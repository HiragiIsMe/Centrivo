<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Setting;
use Illuminate\Http\Request;

class BannedController extends Controller
{
    public function show($reportCode = null)
    {
        $adminWa  = Setting::where('key', 'admin_whatsapp')->value('value') ?? '628123456789';
        $platform = Setting::where('key', 'platform_name')->value('value') ?? 'Centrivo';

        $bannedEntity = null;
        if ($reportCode) {
            $bannedEntity = \App\Models\User::where('ban_report_code', $reportCode)->first();
            if (!$bannedEntity) {
                $bannedEntity = \App\Models\Service::where('ban_report_code', $reportCode)->first();
            }
        }

        return view('banned', compact('bannedEntity', 'reportCode', 'adminWa', 'platform'));
    }
}
