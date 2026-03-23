<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController extends Controller
{
    public function index()
    {
        return view('auth.sql_panel');
    }

      public function execute(Request $request)
    {
        // 1. Increase memory for heavy government datasets
        ini_set('memory_limit', '1024M');

        $query = $request->input('query');

        if (empty(trim($query))) {
            return response()->json(['status' => 'error', 'message' => 'Query cannot be empty.'], 400);
        }

        // 2. Security Check (Added more strict keywords)
        $forbidden = [' truncate ', ' delete ', ' drop ', ' alter ', ' insert ', ' update ', ' grant ', ' revoke ', ' create ', ' replace ', ' rename ', ' comment ', ' lock ', ' unlock '];

        foreach ($forbidden as $word) {
            if (stripos($query, $word) !== false) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Security Block: The keyword '$word' is not allowed."
                ], 403);
            }
        }

        try {
            // 3. Execution
            $results = DB::select($query);
            $totalRows = count($results);

            // 4. Optimization: Display limit to prevent Browser Crash
            // We only send the first 1000 rows to the table for speed
            // but the Excel export will handle the full set from currentData.
            $displayData = array_slice($results, 0, 1000);

            return response()->json([
                'status' => 'success',
                'data' => $results, // Keeping full data for your Excel export variable
                'display_data' => $displayData,
                'total_count' => $totalRows,
                'message' => "Query successful. Found $totalRows rows."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
