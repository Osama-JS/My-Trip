<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightApiLog;
use App\Models\FlightSearchLog;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * View API Logs (Travelopro)
     */
    public function apiLogs(Request $request)
    {
        if ($request->ajax()) {
            $logs = FlightApiLog::with('user')->latest()->limit(200)->get();
            return response()->json([
                'data' => $logs->map(function($log) {
                    return [
                        'id' => $log->id,
                        'endpoint' => $log->endpoint,
                        'user' => $log->user->name ?? 'Guest',
                        'status' => $log->status_code == 200 ? '<span class="badge badge-success">200</span>' : '<span class="badge badge-danger">'.$log->status_code.'</span>',
                        'time' => $log->created_at->format('Y-m-d H:i:s'),
                        'action' => '<button class="btn btn-xs btn-info" onclick="viewLogPayload('.$log->id.')">View</button>'
                    ];
                })
            ]);
        }
        return view('admin.reports.api_logs');
    }

    /**
     * View Search Logs
     */
    public function searchLogs(Request $request)
    {
         if ($request->ajax()) {
            $logs = FlightSearchLog::with('user')->latest()->limit(500)->get();
            return response()->json([
                'data' => $logs->map(function($log) {
                    return [
                        'user' => $log->user->name ?? 'Guest/App',
                        'origin' => $log->origin,
                        'destination' => $log->destination,
                        'date' => $log->departure_date,
                        'pax' => $log->adults + $log->children + $log->infants,
                        'created_at' => $log->created_at->format('Y-m-d H:i')
                    ];
                })
            ]);
        }
        return view('admin.reports.search_logs');
    }
}
