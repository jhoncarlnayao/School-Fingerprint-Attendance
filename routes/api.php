<?php

use App\Http\Controllers\Api\AttendanceScanController;
use Illuminate\Support\Facades\Route;

// Called by the fingerprint scanner's local app/SDK (ZKTeco, SecuGen, etc.)
// Protected by a shared bearer token — see App\Http\Middleware\VerifyScannerToken
// and SCANNER_API_TOKEN in .env.
Route::middleware('scanner.token')->post('/attendance/scan', [AttendanceScanController::class, 'store']);
