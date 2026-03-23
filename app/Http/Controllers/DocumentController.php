<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DocumentController extends Controller
{

    public function analyzeDocument(Request $request)
    {
        // Accept either base64 string or file path
        $pdfBase64 = $request->input('pdf_base64');
        if (!$pdfBase64) {
            return response()->json([
                'status' => 'error',
                'message' => 'No PDF content provided',
            ]);
        }

        try {
            // Example: call Gemini AI or any free API
            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . env('GEMINI_API_KEY'),
                [
                    'json' => [
                        'contents' => [[
                            'parts' => [[
                                'text' => "You are a document authenticity checker. 
                                       Given the attached PDF (base64 encoded), determine if it looks like an original certificate or tampered. 
                                       Reply strictly with either 'Original' or 'Not Original'.",
                            ], [
                                'inlineData' => [
                                    'mimeType' => 'application/pdf',
                                    'data' => $pdfBase64,
                                ]
                            ]]
                        ]]
                    ]
                ]
            );

            $data = json_decode($response->getBody(), true);

            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Not Original';
            $reply = strip_tags(trim($reply));

            // Only allow 'Original' or 'Not Original'
            if (!in_array($reply, ['Original', 'Not Original'])) {
                $reply = 'Not Original';
            }

            return response()->json(['status' => $reply]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'AI verification failed',
                'errors' => ['server' => [$e->getMessage()]],
            ]);
        }
    }
   }
