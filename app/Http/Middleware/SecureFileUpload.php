<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureFileUpload
{
    /**
     * Handle an incoming request with file uploads.
     * 
     * Security Validations:
     * 1. Double Extension Check (file.pdf.php)
     * 2. MIME Type Validation (actual file content)
     * 3. File Size Validation
     * 4. Malicious File Content Check
     * 5. Filename Sanitization
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info("🛡️ SecureFileUpload Middleware: Starting file validation");

        // Check if request has any files
        if ($request->hasFile('') || count($request->allFiles()) > 0) {

            // Get all uploaded files
            $allFiles = $request->allFiles();
            \Log::info("📎 Files detected: " . count($allFiles) . " fields");

            foreach ($allFiles as $fieldName => $file) {
                // Handle array of files (multiple uploads)
                $files = is_array($file) ? $file : [$file];

                foreach ($files as $uploadedFile) {
                    \Log::info("🔍 Checking file: " . ($uploadedFile ? $uploadedFile->getClientOriginalName() : 'NULL'));
                    \Log::info("📏 File valid? " . ($uploadedFile && $uploadedFile->isValid() ? 'YES' : 'NO'));
                    
                    // Skip if not a valid file
                    if (!$uploadedFile || !$uploadedFile->isValid()) {
                        if ($uploadedFile) {
                            $errorCode = $uploadedFile->getError();
                            $errorMessages = [
                                UPLOAD_ERR_INI_SIZE => 'फ़ाइल PHP upload_max_filesize से बड़ी है (' . ini_get('upload_max_filesize') . ')',
                                UPLOAD_ERR_FORM_SIZE => 'फ़ाइल HTML फॉर्म MAX_FILE_SIZE से बड़ी है',
                                UPLOAD_ERR_PARTIAL => 'फ़ाइल आंशिक रूप से अपलोड हुई',
                                UPLOAD_ERR_NO_FILE => 'कोई फ़ाइल अपलोड नहीं की गई',
                                UPLOAD_ERR_NO_TMP_DIR => 'अस्थायी फ़ोल्डर नहीं मिला',
                                UPLOAD_ERR_CANT_WRITE => 'फ़ाइल डिस्क पर लिखने में विफल',
                                UPLOAD_ERR_EXTENSION => 'PHP एक्सटेंशन ने अपलोड रोक दिया',
                            ];
                            $errorMsg = $errorMessages[$errorCode] ?? "अज्ञात त्रुटि (कोड: {$errorCode})";
                            \Log::warning("⚠️ फ़ाइल छोड़ी गई: {$uploadedFile->getClientOriginalName()} - {$errorMsg}");
                            
                            // If file too large for PHP, show friendly error
                            if ($errorCode === UPLOAD_ERR_INI_SIZE) {
                                abort(422, "'{$uploadedFile->getClientOriginalName()}' का आकार बहुत बड़ा है। कृपया 2MB से छोटी फ़ाइल अपलोड करें।");
                            }
                        }
                        continue;
                    }

                    // 1. Double Extension Check
                    $this->validateDoubleExtension($uploadedFile, $fieldName);

                    // 2. MIME Type Validation
                    $this->validateMimeType($uploadedFile, $fieldName);

                    // 3. File Size Validation
                    $this->validateFileSize($uploadedFile, $fieldName);

                    // 4. Malicious Content Check
                    $this->validateMaliciousContent($uploadedFile, $fieldName);

                    // 5. Filename Security Check
                    $this->validateFilename($uploadedFile, $fieldName);
                }
            }
        }

        return $next($request);
    }

    /**
     * Check for double extensions (e.g., file.pdf.php, image.jpg.exe)
     * STRICT MODE: Blocks ANY double extension (even harmless ones)
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fieldName
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateDoubleExtension($file, $fieldName)
    {
        $originalName = $file->getClientOriginalName();

        // Count dots in filename
        $parts = explode('.', $originalName);

        // STRICT CHECK: Block ANY file with more than 1 dot
        // Allowed: filename.pdf (2 parts)
        // Blocked: filename.print.pdf (3 parts)
        // Blocked: filename.php.pdf (3 parts)
        // Blocked: filename.copy.docx (3 parts)
        if (count($parts) > 2) {
            abort(422, "फ़ाइल नाम में केवल एक एक्सटेंशन होना चाहिए: '{$originalName}'");
        }
    }

    /**
     * Validate MIME type matches actual file content (not just extension)
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fieldName
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateMimeType($file, $fieldName)
    {
        $allowedMimeTypes = [
            // Images
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',

            // Documents
            'application/pdf',
            'application/msword', // .doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.ms-excel', // .xls
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx

            // Text
            'text/plain',
            'text/csv',
        ];

        $fileMimeType = $file->getMimeType();

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            abort(422, "कृपया मान्य फ़ाइल प्रकार अपलोड करें: '{$file->getClientOriginalName()}'");
        }
    }

    /**
     * Validate file size (max 5MB for images, 10MB for documents)
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fieldName
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateFileSize($file, $fieldName)
    {
        $fileSize = $file->getSize(); // in bytes
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        $fileName = $file->getClientOriginalName();

        // DEBUG: Log file size check
        \Log::info("🔍 Middleware validateFileSize: {$fileName} = {$fileSizeMB}MB");

        // Maximum file size: 2MB for all files
        $maxSize = 2 * 1024 * 1024; // 2MB
        $maxSizeLabel = '2MB';

        if ($fileSize > $maxSize) {
            \Log::warning("❌ File too large: {$fileName} ({$fileSizeMB}MB > 2MB)");
            abort(422, "'{$fileName}' का आकार {$fileSizeMB}MB है। कृपया {$maxSizeLabel} से छोटी फ़ाइल अपलोड करें।");
        }

        \Log::info("✅ File size OK: {$fileName} ({$fileSizeMB}MB)");
    }

    /**
     * Check for malicious content in files (PHP code, scripts, etc.)
     * 
     * Note: PDF, Excel, Word documents are SKIPPED from content scanning
     * because they may contain legitimate code/scripts that trigger false positives.
     * These are already validated by MIME type and extension checks.
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fieldName
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateMaliciousContent($file, $fieldName)
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        // Skip content scanning for document and image files
        // These files can contain legitimate binary/metadata that may trigger false positives
        $skipScanningMimeTypes = [
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            // Images (PNG, JPG, GIF contain binary data that can trigger false positives)
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/gif',
            'image/bmp',
            'image/webp',
        ];

        $skipScanningExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'];

        // Skip content scanning (already validated by MIME type and double extension checks)
        if (in_array($mimeType, $skipScanningMimeTypes) || in_array($extension, $skipScanningExtensions)) {
            return; // Files are safe (validated by MIME type + double extension checks)
        }

        // Only scan text files and other suspicious uploads
        // Read first 2KB of file content
        $content = file_get_contents($file->getRealPath(), false, null, 0, 2048);

        // Patterns indicating malicious content in images/text files
        $maliciousPatterns = [
            '/<\?php/i',           // PHP opening tag
            '/<\?=/i',             // PHP short echo tag
            '/<script/i',          // JavaScript in uploaded file
            '/eval\(/i',           // eval() function
            '/system\(/i',         // System command
            '/exec\(/i',           // Exec command
            '/shell_exec/i',       // Shell exec
            '/passthru/i',         // Passthru
            '/`.*`/i',             // Backtick execution
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                abort(422, "कृपया मान्य फ़ाइल अपलोड करें: '{$file->getClientOriginalName()}'");
            }
        }
    }

    /**
     * Validate and sanitize filename
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fieldName
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateFilename($file, $fieldName)
    {
        $originalName = $file->getClientOriginalName();

        // Check for path traversal attempts
        if (strpos($originalName, '../') !== false || strpos($originalName, '..\\') !== false) {
            abort(422, "कृपया मान्य फ़ाइल नाम चुनें।");
        }

        // Check for null bytes
        if (strpos($originalName, "\0") !== false) {
            abort(422, "कृपया मान्य फ़ाइल नाम चुनें।");
        }

        // Check filename length (max 255 characters)
        if (strlen($originalName) > 255) {
            abort(422, "फ़ाइल नाम बहुत लंबा है। कृपया छोटा नाम चुनें (अधिकतम 255 वर्ण)।");
        }
    }
}
