<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\PaymentHistory;
use App\Models\SampleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Get counts for dashboard widgets
        $stats = [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'total_requests' => SampleRequest::count(),
            'pending_requests' => SampleRequest::pending()->count(),
            'total_jobs' => Job::count(),
            'admin_users' => User::adminUsers()->count(),
            'customers' => User::customers()->count(),
        ];

        // Get recent requests
        $recentRequests = SampleRequest::with(['user', 'company'])
            ->orderBy('request_date', 'desc')
            ->limit(10)
            ->get();

        // Get recent jobs
        $recentJobs = Job::with(['user', 'company'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRequests', 'recentJobs'));
    }
}
