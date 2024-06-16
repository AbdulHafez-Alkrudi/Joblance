<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ReportReplyRequest;
use App\Http\Requests\ReportSendRequest;
use App\Mail\AdminReply;
use App\Models\Report\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class ReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::all();

        return $this->sendResponse($reports);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $reportSendRequest = new ReportSendRequest();
        $validator = Validator::make($request->all(), $reportSendRequest->rules());

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $report = Report::create([
            'title' => $request->title,
            'body'  => $request->body,
            'user_id'  => Auth::id(),
        ]);

        return $this->sendResponse($report);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }

    public function newReports()
    {
        $reports = Report::query()->whereNull('read_at')->get();

        if($reports->isEmpty())
        {
            return $this->sendResponse([]);
        }

        $reports->toQuery()->update([
            'read_at' => Carbon::now(),
        ]);

        return $this->sendResponse($reports);
    }

    public function reply(Request $request)
    {
        $replyRequest = new ReportReplyRequest();
        $validator = Validator::make($request->all(), $replyRequest->rules());

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $reply_data = [
            'title' => $request->title,
            'body'  => $request->body,
        ];

        $user = User::query()->find($request->user_id);

        Mail::to($user->email)->send(new AdminReply($reply_data));

        return $this->sendResponse([]);
    }
}
