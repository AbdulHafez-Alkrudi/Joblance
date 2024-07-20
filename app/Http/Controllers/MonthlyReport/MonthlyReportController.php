<?php

namespace App\Http\Controllers\MonthlyReport;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\Users\Company\AcceptedJobs;
use App\Models\Users\Company\Company;
use App\Models\Users\Company\JobDetail;
use App\Models\Users\Freelancer\Freelancer;
use App\Models\Users\Freelancer\JobApplication;
use App\Models\Users\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyReportController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $report = array();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // 1- total number of companies
        $report['total_companies'] = Company::count();

        // 2- total number of freelancers
        $report['total_freelancers'] = Freelancer::count();

        // 3- hired freelancers this month
        $report['hired_freelancers'] = AcceptedJobs::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // 4- posted tasks this month
        $report['posted_tasks'] = Task::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // 5- posted jobs this month
        $report['posted_jobs'] = JobDetail::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // 6- num of freelancers for each weeek in this month
        $weeks = [];
        $currentWeekStart = $startOfMonth->copy();
        $cnt = 1;
        while ($currentWeekStart->lte($endOfMonth)) {
            $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

            // Ensure the week end does not go beyond the end of the month
            if ($currentWeekEnd->gt($endOfMonth)) {
                $currentWeekEnd = $endOfMonth->copy();
            }

            $weeks[] = [
                'week' => $cnt++,
                'num_of_freelancers' => Freelancer::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count()
            ];

            // Move to the next week
            $currentWeekStart = $currentWeekEnd->copy()->addDay();
        }
        $report['freelancers_in_each_week'] = $weeks;

        // 7- num of companies for each weeek in this month
        $weeks = [];
        $currentWeekStart = $startOfMonth->copy();
        $cnt = 1;
        while ($currentWeekStart->lte($endOfMonth)) {
            $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

            // Ensure the week end does not go beyond the end of the month
            if ($currentWeekEnd->gt($endOfMonth)) {
                $currentWeekEnd = $endOfMonth->copy();
            }

            $weeks[] = [
                'week' => $cnt++,
                'num_of_companies' => Company::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count()
            ];

            // Move to the next week
            $currentWeekStart = $currentWeekEnd->copy()->addDay();
        }
        $report['companies_in_each_week'] = $weeks;

        // 8- the company that posted the most jobs
        $companyWithMostJobs = Company::select('companies.*', DB::raw('COUNT(job_details.id) as job_count'))
            ->join('job_details', 'job_details.company_id', '=', 'companies.id')
            ->groupBy('companies.id')
            ->orderBy('job_count', 'desc')
            ->first();

        if ($companyWithMostJobs) {
            $report['company_with_most_jobs'] = (new Company)->get_info($companyWithMostJobs, request('lang'));
        }
        else {
            $report['company_with_most_jobs'] = 'No jobs found';
        }

        return $this->sendResponse($report);
    }
}
