<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OtpHelper
{

    public static function generateAndStoreOtp($type, $identifier, $length = 6, $expiry = 5)
    {
        $otp = 654321; // Default OTP for testing purposes
        // $otp = random_int(pow(10, $length - 1), pow(10, $length) - 1);

        if (config('app.env') == 'production')
            $otp = random_int(pow(10, $length - 1), pow(10, $length) - 1);

        $expiryTime = Carbon::now()->addMinutes($expiry);

        DB::table('tbl_otp_verification')->insert([
            'type' => $type,
            'identifier' => $identifier ? $identifier : 0,
            'otp' => $otp,
            'is_verified' => 0,
            'expires_at' => $expiryTime,
        ]);

        session(['current_OTP' => $otp]);
        return $otp;
    }


    public function sendSMS($mobileno, $otp)
    {
        $message = "Your OTP is $otp. -from Vectre";
        $authkey = "c8be251538c003cbc28d8279dfcb4a6c";
        $senderid = "VCTREA";
        $route = "4";
        $template_id = '1407175274379900883';

        $response = Http::get('http://shubhsms.com/apiv2', [
            'authkey' => $authkey,
            'senderid' => $senderid,
            'numbers' => $mobileno,
            'message' => $message,
            'route' => $route,
            'template_id' => $template_id
        ]);
        // dd($response->json());
        return $response->json();
    }



    public function sendOtp($identifier, $otp)
    {
        // $this->sendSMS($identifier, $otp);
        if (config('app.env') == 'production')
            // dd('hi');
            $this->sendSMS($identifier, $otp);
        Log::info("OTP sent to $identifier: $otp"); // Use the Log facade
    }



    public static function verifyOtp($type, $identifier, $otp)
    {
        // Check if the OTP exists and is not already verified
        $record = DB::table('tbl_otp_verification')
            ->where('type', $type)
            ->where('identifier', $identifier)
            // ->where('otp', $otp)
            ->where('is_verified', 0) // Check if not already verified
            ->orderByDesc('id')
            ->first();



        // If no record is found, return an error for incorrect OTP
        if (!$record) {
            return response()->json([
                'status' => 'error',
                'message' => 'गलत OTP या OTP की समय सीमा समाप्त हो गई है। कृपया सही OTP दर्ज करें।'
            ]);
        }

        // Check if the OTP has expired
        if (Carbon::now()->greaterThan(Carbon::parse($record->expires_at))) {
            DB::table('tbl_otp_verification')
                ->where('identifier', $identifier)
                ->where('otp', $otp)
                ->update(['is_verified' => 2]); // Mark as expired

            return response()->json([
                'status' => 'error',
                'message' => 'OTP की समय सीमा समाप्त हो गई है। कृपया दोबारा प्रयास करें।'
            ]);
        }


        // $session_OTP = Session::get('current_OTP');
        // dd($session_OTP);
        if ($record->otp == $otp) {
            // If OTP is valid and not expired, mark it as verified
            $affectedRows = DB::table('tbl_otp_verification')
                ->where('id', $record->id)
                ->update(['is_verified' => 1]); // Mark as verified

            if ($affectedRows > 0) {
                session(['is_verified' => 1]);
                session(['sess_mobile' => $record->identifier]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP सत्यापित हो गया है।'
                ]);
            }
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'OTP सत्यापन में त्रुटि हुई। कृपया सही OTP दर्ज करें।'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'OTP सत्यापन में त्रुटि हुई। कृपया सही OTP दर्ज करें।'
        ]);
    }
}
