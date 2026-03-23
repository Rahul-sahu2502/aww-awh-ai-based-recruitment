<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DscController extends Controller
{
    function dcs_register_ajax(Request $request)
    {
        // dd($request->ip(), $request->dsc_type);
        $dsc_data = $request->all();
        $res = array();

        if ($dsc_data['hasPrivateKey'] == true) {
            // $query = "SELECT COUNT(1) cnt FROM tbl_dsc_details t WHERE t.algorithm = ? AND t.certificatePEM=?";
            // $count = DB::select($query, array($dsc_data['algorithm'], $dsc_data['certificatePEM']))[0]->cnt;

            session()->put('dsc_serial_no', $dsc_data['serialNumber']);
            $query = "SELECT * FROM tbl_dsc_details t WHERE t.algorithm = ? AND t.certificatePEM = ? LIMIT 1";
            $dscExist = DB::select($query, array($dsc_data['algorithm'], $dsc_data['certificatePEM']));
            $userDataExist = DB::table("tbl_dsc_details")->where('created_by', session()->get('uid'))->first();

            if (!empty($dscExist) && !empty($userDataExist)) {
                $res['status'] = 3;
                $res['msg'] = "DSC पहले से ही पंजीकृत है|";
            } 
            else if (!empty($dscExist) && empty($userDataExist) || empty($dscExist) && !empty($userDataExist)) {
                $res['status'] = 2;
                $res['msg'] = "यह DSC पहले से ही किसी अन्य उपयोगकर्ता द्वारा पंजीकृत है| कृपया स्वयं का DSC लगायें|";
            } 
            else if (empty($dscExist) && empty($userDataExist)) {
                $dsc_data['created_by'] = session()->get('uid');
                $dsc_data["created_ipaddress"] = $request->ip();
                $sts = DB::table("tbl_dsc_details")->insert($dsc_data);

                if ($sts) {
                    $res['status'] = 1;
                    $res['msg'] = "DSC सफलतापूर्वक पंजीकृत किया गया है।";
                } else {
                    $res['status'] = 2;
                    $res['msg'] = "पुन: प्रयास करें|";
                }
            }
        } else {
            $res['status'] = 4;
            $res['msg'] = "कृपया मशीन में DSC लगाये|";
        }
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function dsc_add_sign(Request $request)
    {
        // dd($request->all(), $request->hasFile('advertisement_document'));

        //Step 1: Validate PDF file
        if (!$request->hasFile('advertisement_document')) {
            return response()->json([
                'status' => 0,
                'msg' => 'PDF फ़ाइल नहीं मिली।'
            ]);
        }

        $pdf = $request->file('advertisement_document');
        $originalName = $pdf->getClientOriginalName();
        $tempPath = public_path('temp_dsc');

        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

        // Save the uploaded file -- meeting_signed_
        $fileName = $originalName . time() . '.pdf';
        $pdf->move($tempPath, $fileName);

        $fullPath = $tempPath . '/' . $fileName;

        if (!file_exists($fullPath)) {
            return response()->json([
                'status' => 4,
                'msg' => 'File does not exist.'
            ]);
        }

        //Step 2: Prepare for signing
        $binaryData = base64_encode(file_get_contents($fullPath));

        if (!$binaryData) {
            return response()->json([
                'status' => 3,
                'msg' => "Failed to read the file as binary data."
            ]);
        }

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $userId = session()->get('uid');
        // $roleId = session()->get('sess_role');
        $dscData = DB::table('tbl_dsc_details')->where('created_by', $userId)->first();
        
        if (!$dscData) {
            return response()->json([
                'status' => 2,
                'msg' => "DSC details not found."
            ]);
        }
        
        $document_id = str_replace(".", "", uniqid("", true)) . rand(100, 999);
        $json_data = [
            "documentType" => "PDF",
            "registeredCertificateSubject" => $dscData->subject,
            "documentId" => $document_id,
            "documentTextData" => null,
            "documentBinaryData" => $binaryData,
            "locationOfSigning" => null,
            "dataHash" => null,
            "addScannedSign" => false,
            "scannedSignData" => null,
            "signLeftPosition" => 400,
            "signTopPosition" => 80,
            "signRightPosition" => 550,
            "signBottomPosition" => 30,
            "padStandardCompliant" => false,
            "signatureDisplayOn" => 2,
            "isFinalSignature" => false
        ];

        return response()->json($json_data);
    }

    public function dsc_save_sign(Request $request)
    {
        // dd($request->input('signedDocumentData'),$request->all());    
        try {

            if (null == ($request->input('signedDocumentData'))) {
                DB::table('master_advertisement')->where('Advertisement_ID', $request->advertisement_id)->delete();
                return response()->json([
                    'status' => 4,
                    'msg' => "कृपया मशीन में DSC लगाये|"
                ]);
            }

            // $allow_insert = false;
            // $signedBinary = base64_decode($request->input('signedDocumentData'));

            // $signedFileName = 'meeting_notice_' . time() . '_DSC_Signed.pdf';
            // $spacesPath = 'dsc_signed_uploads/' . $signedFileName;

            // $directory = public_path('public/sign_dsc'); 
            // if (!file_exists($directory)) {
            //     mkdir($directory, 0777, true); // recursive create
            // }

            // $filePath = $directory . DIRECTORY_SEPARATOR . 'signed_document'. time() . '.pdf';

            // // write the file
            // if (file_put_contents($filePath, $signedBinary) === false) {
            //     DB::table('master_advertisement')->where('Advertisement_ID', $request->advertisement_id)->delete();
            //     throw new \Exception("Failed to save signed document at: " . $filePath);
            // }

            // $save = DB::table("tbl_dsc_pdf_signed_data")->updateOrInsert(
            //     ['advertisement_id' => $request->advertisement_id],
            //     [
            //         'document_id' => $request->documentId,
            //         // 'document_path' => $filePath,
            //         'dsc_sign_data' => $request->signedDocumentData,
            //         'created_by' => $request->session()->get('uid'),
            //         'created_ipaddress' => $request->ip()
            //     ]
            // );
            
            $save =   DB::table('tbl_dsc_pdf_signed_data')->insert([
                'advertisement_id'   => $request->advertisement_id,
                'document_id'        => $request->documentId,
                'dsc_sign_data'      => $request->signedDocumentData,
                'created_by'         => $request->session()->get('uid'),
                'created_ipaddress'  => $request->ip()
            ]);

            if ($save) {
                DB::table('master_advertisement')->where('Advertisement_ID', $request->advertisement_id)->update([
                    'is_dsc' => 1,
                    'Last_Updated_dttime' => now(),
                    'IP_Address' => $request->ip(),
                ]);
            } else {
                DB::table('master_advertisement')->where('Advertisement_ID', $request->advertisement_id)->delete();
                
                return response()->json([
                    'status' => 3,
                    'msg' => "Signed document data could not be saved. Please try again."
                ]);
            }

            return response()->json([
                'status' => 1,
                'msg' => 'Success',
            ]);

        } catch (\Exception $e) {
            // Handle any outer exceptions
            DB::table('master_advertisement')->where('Advertisement_ID', $request->advertisement_id)->delete();
            
            return response()->json([
                'status' => 3,
                'msg' => "Error occurred: " . $e->getMessage()
            ]);
        }
    }
}
