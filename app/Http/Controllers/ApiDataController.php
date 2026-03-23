<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ApiDataController extends Controller
{
    public function loadApiData()
    {
        try {
            // Increase memory limit for large data sets
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            \Log::info('Starting API data load...');

            // 1️⃣ Fetch API data
            $response = Http::timeout(120)->withHeaders([
                'Authorization' => 'Basic Q0hXRENXOmMjd2Rjd0AyMDI1',
            ])->get('https://cgems.adt-ai.com/Masters/GetMatserListForThirdParty?key=01Octcgem$2025');

            if ($response->failed()) {
                \Log::error('API request failed', ['status' => $response->status()]);
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed',
                    'error' => 'HTTP request failed with status: ' . $response->status()
                ], 500);
            }

            $data = $response->json();
            \Log::info('API response received', ['size' => strlen(json_encode($data))]);

            // Validate data structure
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data received from API'
                ], 400);
            }

            // Convert to array if needed
            $responseData = json_decode(json_encode($data), true);

            // ✅ Handle nested structure: data.Table or data.table
            $dataArray = null;
            if (isset($responseData['data']['Table'])) {
                $dataArray = $responseData['data']['Table'];
            } elseif (isset($responseData['data']['table'])) {
                $dataArray = $responseData['data']['table'];
            } elseif (isset($responseData['Table'])) {
                $dataArray = $responseData['Table'];
            } elseif (isset($responseData['table'])) {
                $dataArray = $responseData['table'];
            } elseif (isset($responseData[0])) {
                // Direct array of objects
                $dataArray = $responseData;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data structure: Could not find data array',
                    'debug_info' => [
                        'available_keys' => is_array($responseData) ? array_keys($responseData) : 'N/A',
                        'response_sample' => is_array($responseData) ? array_slice($responseData, 0, 1) : $responseData
                    ]
                ], 400);
            }

            // Validate array is not empty
            if (empty($dataArray) || !is_array($dataArray)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data array is empty or invalid',
                    'type' => gettype($dataArray)
                ], 400);
            }

            $totalRecords = count($dataArray);
            \Log::info("Processing {$totalRecords} records");

            // Get first element
            $firstElement = $dataArray[0];

            // Validate first element is an array
            if (!is_array($firstElement)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data structure: Each item must be an object/array',
                    'first_element_type' => gettype($firstElement)
                ], 400);
            }

            // 2️⃣ Decide table name
            $tableName = 'api_master_data';

            // 3️⃣ Check if table exists
            if (!Schema::hasTable($tableName)) {
                \Log::info('Creating table: ' . $tableName);
                // 4️⃣ Create table dynamically using keys from first record
                Schema::create($tableName, function (Blueprint $table) use ($firstElement) {
                    $table->id(); // auto increment id
                    foreach (array_keys($firstElement) as $column) {
                        // Sanitize column name
                        $sanitizedColumn = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
                        $table->text($sanitizedColumn)->nullable();
                    }
                    $table->timestamp('inserted_at')->useCurrent();
                });
                \Log::info('Table created successfully');
            } else {
                \Log::info('Deleting existing data from table: ' . $tableName);
                // 5️⃣ Delete all data from table if already exists
                DB::table($tableName)->delete();
                // Reset auto-increment to 1
                DB::statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
            }

            // 6️⃣ Insert data in chunks to avoid memory issues
            $chunkSize = 1000; // Insert 1000 records at a time
            $insertedCount = 0;
            $chunks = array_chunk($dataArray, $chunkSize);

            \Log::info("Inserting data in " . count($chunks) . " chunks");

            foreach ($chunks as $chunkIndex => $chunk) {
                $insertData = [];
                foreach ($chunk as $row) {
                    if (is_array($row)) {
                        // Sanitize column names in row data
                        $sanitizedRow = [];
                        foreach ($row as $key => $value) {
                            $sanitizedKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
                            $sanitizedRow[$sanitizedKey] = $value;
                        }
                        $sanitizedRow['inserted_at'] = now();
                        $insertData[] = $sanitizedRow;
                    }
                }

                if (!empty($insertData)) {
                    DB::table($tableName)->insert($insertData);
                    $insertedCount += count($insertData);
                    \Log::info("Inserted chunk " . ($chunkIndex + 1) . "/" . count($chunks) . " ({$insertedCount}/{$totalRecords} records)");
                }

                // Free memory
                unset($insertData);
            }

            \Log::info("Data load complete. Total records inserted: {$insertedCount}");

            return response()->json([
                'success' => true,
                'message' => 'Data loaded successfully into ' . $tableName,
                'records_inserted' => $insertedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('API Data Load Error:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

