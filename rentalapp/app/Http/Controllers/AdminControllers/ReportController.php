<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\PartnerAccountDetails;
use App\PartnerProfitLoss;
use App\TenantProfitLoss;
use Auth;
use GuzzleHttp\Psr7\Request;

class ReportController extends Controller
{
    public function partnerProfitLoss()
    {
        return view('company_portal.reports.partner_profit_loss');
    }
    public function partnerAccountDetails()
    {
        return view('company_portal.reports.partner_account_detail');
    }
    public function tenantProfitloss()
    {
        return view('company_portal.reports.tenant_profit_loss');
    }
    public function tenantAccountDetails()
    {
        return view('company_portal.reports.tenant_account_details');
    }
}
