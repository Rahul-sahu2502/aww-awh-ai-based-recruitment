<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class logController extends Controller
{
    protected $logFile;

    public function __construct()
    {
        $date = now()->format('Y-m-d');
        $this->logFile = storage_path("logs/laravel-{$date}.log");

        // अगर date वाली log file नहीं है, तो fallback करें default पर
        if (!File::exists($this->logFile)) {
            $this->logFile = storage_path("logs/laravel.log");
        }
    }

    public function index()
    {
        if (!File::exists($this->logFile)) {
            $logContent = null;
            $logMissing = true;
        } else {
            $logContent = File::get($this->logFile);
            $logMissing = false;
        }

        return view('logs.logView', compact('logContent', 'logMissing'));
    }

    public function clear()
    {
        if (File::exists($this->logFile)) {
            File::put($this->logFile, '');
        }

        return redirect()->route('log.viewer')->with('status', 'Log file cleared successfully.');
    }
}
