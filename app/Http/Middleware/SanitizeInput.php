<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Global input sanitizer middleware.
 * - Recursively sanitizes string inputs by removing HTML tags and dangerous protocols
 * - Skips files and a small allowlist of fields that intentionally contain HTML (e.g. WYSIWYG content)
 */
class SanitizeInput
{
    /**
     * Fields that should NOT be sanitized because they intentionally contain HTML
     * (for example, CKEditor content). Add route-specific fields here as needed.
     * @var array
     */
    protected $except = [
        '_token',
        'rules',
        'guidelines',
        'description',
        'content'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $all = $request->all();
            $sanitized = $this->sanitizeArray($all);

            // merge sanitized input back into request
            $request->merge($sanitized);
        } catch (\Exception $e) {
            // Fail safe: on error do not block request; log if needed
            // logger()->error('SanitizeInput middleware error: ' . $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Recursively sanitize array values
     */
    protected function sanitizeArray(array $data)
    {
        foreach ($data as $key => $value) {
            // Skip excepted fields
            if (in_array($key, $this->except, true)) {
                continue;
            }

            // Skip file uploads
            if ($value instanceof UploadedFile) {
                continue;
            }

            if (is_string($value)) {
                $data[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            }
            // other types (int, bool) left unchanged
        }

        return $data;
    }

    /**
     * Sanitize a single string value: remove scripts, tags and dangerous protocols
     */
    protected function sanitizeString(string $value): string
    {
        // Remove null bytes
        $value = preg_replace('/\x00+/', '', $value);

        // Decode HTML entities to normalize
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove any <script>...</script>, <iframe>, <object>, <embed>
        $value = preg_replace('#<(script|iframe|object|embed)[\s\S]*?>[\s\S]*?<\/\1>#i', '', $value);

        // Remove on* event handlers if present inside tags (defensive) - strip tags later anyway
        $value = preg_replace('/on[a-zA-Z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $value);

        // Remove dangerous protocols like javascript: and data:
        $value = preg_replace('/(javascript|data):/i', '', $value);

        // Strip all HTML tags
        $value = strip_tags($value);

        // Remove remaining angle brackets just in case
        $value = str_replace(['<', '>'], '', $value);

        // Trim whitespace
        $value = trim($value);

        return $value;
    }
}
