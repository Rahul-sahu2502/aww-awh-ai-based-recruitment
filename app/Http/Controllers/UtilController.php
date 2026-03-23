<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;

class UtilController extends Controller
{




    /* File Uplaod Related Function */
    static function upload_file_previous($file, $upload_path, $disk, $allowed_extensions, $allowed_types)
    {

        // Original file name
        $originalName = $file->getClientOriginalName();

        // 🔐 Double extension security check
        if (self::hasDoubleExtension($originalName)) {
            // Hindi message as per your requirement
            throw new \Exception("कृपया $originalName के लिए वैध एक्सटेंशन वाला दस्तावेज़ अपलोड करें");
        }

        $file_extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $file_mime_type = $file->getClientMimeType();
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = preg_replace('/\s+/', '_', trim($filename)) . '_' . time() . '.' . $file_extension;

        // Check if file extension is allowed
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new \Exception("कृपया मान्य फ़ाइल प्रकार अपलोड करें: '{$originalName}'");
        }
        // Check if file mime type is allowed
        else if (!in_array($file_mime_type, $allowed_types)) {
            throw new \Exception("कृपया मान्य फ़ाइल प्रकार अपलोड करें: '{$originalName}'");
        } else {
            // $image_name = preg_replace('/\s+/', '_', trim($filename)) . '_' . time() . '.' . $file_extension;

            if (in_array($file_extension, ['jpeg', 'jpg', 'png'])) {
                // Compress image files (JPEG/PNG)
                $temp_image_path = sys_get_temp_dir() . '/' . $filename;

                if ($file_extension === 'jpg' || $file_extension === 'jpeg') {
                    $image = imagecreatefromjpeg($file->getRealPath());
                } elseif ($file_extension === 'png') {
                    $image = imagecreatefrompng($file->getRealPath());
                }

                // Get the current width and height of the image
                list($original_width, $original_height) = getimagesize($file->getRealPath());

                // Set the new width
                $new_width = 800;

                // Calculate the new height to maintain aspect ratio
                $new_height = ($new_width / $original_width) * $original_height;

                // Create a new true color image with the new dimensions
                $new_image = imagecreatetruecolor($new_width, $new_height);

                // Preserve transparency for PNG
                if ($file_extension === 'png') {
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                }

                // Resample the image to the new dimensions
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

                // Save the resized image to a temporary file
                if ($file_extension === 'jpg' || $file_extension === 'jpeg') {
                    imagejpeg($new_image, $temp_image_path, 90); // Adjust quality as needed
                } elseif ($file_extension === 'png') {
                    imagepng($new_image, $temp_image_path, 8); // Compression level (0-9)
                }

                // Free up memory
                imagedestroy($image);
                imagedestroy($new_image);

                // Use the compressed file for upload
                // $file_content = file_get_contents($temp_image_path);
                unlink($temp_image_path); // Remove temporary file
            } elseif ($file_extension === 'pdf') {
                // Handle PDF compression (if applicable)
                $tmp_file = $file->getRealPath();
                // $compressedFile = self::compressPdf($tmp_file);

                // Clean up the temporary file (after processing)
                // unlink($compressedFile);
            }

            if (config('app.env') === 'production') {
                $filePath = config('custom.s3_bucket_folder') . '/' . $upload_path . '/' . $filename;
                $upload_sts = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                if ($upload_sts)
                    return $filePath;
                else
                    return false;
            } else {
                // Save the file using storeAs at the end
                return $file->storeAs($upload_path, $filename, $disk);
            }
        }
    }

    private static function compressPdf($file_path)
    {
        // Create FPDI instance
        $pdf = new Fpdi();

        // Set source file
        $pdf->setSourceFile($file_path);

        // Create a temporary file for the compressed PDF
        $tmpFile = tempnam(sys_get_temp_dir(), 'compressed_pdf_');
        $pdf->AddPage();

        // Loop through each page in the original PDF
        $pageCount = $pdf->setSourceFile($file_path);
        for ($i = 1; $i <= $pageCount; $i++) {
            // Import each page from the original PDF
            $template = $pdf->importPage($i);
            $pdf->useTemplate($template);
        }

        // Output the compressed PDF to the temporary file
        $pdf->Output('F', $tmpFile);

        // Return the path to the temporary file for further processing (e.g., storing)
        return $tmpFile;
    }

    // Double extension check function
    protected static function hasDoubleExtension(string $originalName): bool
    {
        // file name
        $name = basename($originalName);

        // Parts split by dot
        $parts = explode('.', $name);

        // Extra safety: remove empty parts (e.g. "file..pdf")
        $parts = array_filter($parts, 'strlen');

        // if dots are more than 1 (e.g. name.test.pdf, img.png.pdf, abc..pdf)
        // to use double/multiple extension maanenge
        return count($parts) > 2;
    }





    static function upload_file($file, $upload_path, $disk, $allowed_extensions, $allowed_types)
    {
        // Original file name
        $originalName = $file->getClientOriginalName();

        // 🔐 Double extension security check
        if (self::hasDoubleExtension($originalName)) {
            throw new \Exception("कृपया $originalName के लिए वैध एक्सटेंशन वाला दस्तावेज़ अपलोड करें");
        }

        $file_extension = $file->getClientOriginalExtension();
        $file_mime_type = $file->getClientMimeType();

        // ### Generate unique filename using Laravel's hashName()
        $unique_filename = $file->hashName(); // e.g., "5f8a9b3c1d2e4f6a7b8c9d0e1f2a3b4c5d6e7f8a.jpg"

        // Check if file extension is allowed
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new \Exception("कृपया मान्य फ़ाइल प्रकार अपलोड करें: '{$originalName}'");
        } elseif (!in_array($file_mime_type, $allowed_types)) {
            throw new \Exception("कृपया मान्य फ़ाइल प्रकार अपलोड करें: '{$originalName}'");
        } else {
            if (in_array($file_extension, ['jpeg', 'jpg', 'png'])) {
                // Compress image files (JPEG/PNG)
                $temp_image_path = sys_get_temp_dir() . '/' . $unique_filename;

                // ### Check if file exists before processing
                if (!$file->isValid()) {
                    throw new \Exception("फ़ाइल मान्य नहीं है");
                }

                // Get the actual file path
                $realPath = $file->getRealPath();
                if (!file_exists($realPath)) {
                    throw new \Exception("फ़ाइल नहीं मिली");
                }

                // Create image from file
                if ($file_extension === 'jpg' || $file_extension === 'jpeg') {
                    $image = imagecreatefromjpeg($realPath);
                } elseif ($file_extension === 'png') {
                    $image = imagecreatefrompng($realPath);
                }

                if (!$image) {
                    throw new \Exception("इमेज बनाने में त्रुटि");
                }

                // Get the current width and height of the image
                list($original_width, $original_height) = getimagesize($realPath);

                // Set the new width
                $new_width = 800;

                // Calculate the new height to maintain aspect ratio
                $new_height = ($new_width / $original_width) * $original_height;

                // Create a new true color image with the new dimensions
                $new_image = imagecreatetruecolor($new_width, $new_height);

                // Preserve transparency for PNG
                if ($file_extension === 'png') {
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                }

                // Resample the image to the new dimensions
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

                // Save the resized image to a temporary file
                if ($file_extension === 'jpg' || $file_extension === 'jpeg') {
                    imagejpeg($new_image, $temp_image_path, 90);
                } elseif ($file_extension === 'png') {
                    imagepng($new_image, $temp_image_path, 8);
                }

                // Free up memory
                imagedestroy($image);
                imagedestroy($new_image);

                // ### Verify temp file was created
                if (!file_exists($temp_image_path)) {
                    throw new \Exception("टेम्परेरी फ़ाइल नहीं बन सकी");
                }

                if (config('app.env') === 'production') {
                    $filePath = config('custom.s3_bucket_folder') . '/' . $upload_path . '/' . $unique_filename;

                    // Read the temp file content
                    $fileContent = file_get_contents($temp_image_path);
                    if ($fileContent === false) {
                        throw new \Exception("फ़ाइल पढ़ने में त्रुटि");
                    }

                    // Upload to S3
                    $upload_sts = Storage::disk('s3')->put($filePath, $fileContent, 'public');

                    // Clean up temporary file
                    if (file_exists($temp_image_path)) {
                        unlink($temp_image_path);
                    }

                    if ($upload_sts) {
                        return $filePath;
                    } else {
                        return false;
                    }
                } else {
                    // Create a new UploadedFile instance from the temporary file
                    $compressed_file = new \Illuminate\Http\UploadedFile(
                        $temp_image_path,
                        $unique_filename,
                        $file_mime_type,
                        null,
                        true // Mark as test mode to avoid moving issues
                    );

                    // Use storeAs to save the compressed file
                    $path = $compressed_file->storeAs($upload_path, $unique_filename, $disk);

                    // Clean up temporary file after upload
                    if (file_exists($temp_image_path)) {
                        unlink($temp_image_path);
                    }

                    return $path;
                }
            } elseif ($file_extension === 'pdf') {
                if (config('app.env') === 'production') {
                    $filePath = config('custom.s3_bucket_folder') . '/' . $upload_path . '/' . $unique_filename;
                    $upload_sts = Storage::disk('s3')->put($filePath, file_get_contents($file->getRealPath()), 'public');

                    if ($upload_sts)
                        return $filePath;
                    else
                        return false;
                } else {
                    // Save PDF file using storeAs with unique filename
                    return $file->storeAs($upload_path, $unique_filename, $disk);
                }
            }
        }
    }
}
