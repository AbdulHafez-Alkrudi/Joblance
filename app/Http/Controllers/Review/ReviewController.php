<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Users\Company\Company;
use App\Models\Users\Evaluation;
use App\Models\Users\Freelancer\Freelancer;
use App\Models\Review\Review;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            return $this->indexByUserId($request->user_id);
        }
    }

    protected function indexByUserId(string $userId)
    {
        $user = User::find($userId);
        if (is_null($user)) {
            return $this->sendError(['message' => 'There is no user with this ID']);
        }

        if ($user->userable_type != Company::class) {
            return $this->sendError(['message' => 'Userable type is not Company']);
        }

        $reviews = (new Review)->get_all_reviews($user->userable->reviews);
        return $this->sendResponse($reviews);
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
        DB::beginTransaction();
        try {
            $reviewRequest = new ReviewRequest();
            $validator = Validator::make($request->all(), $reviewRequest->rules());

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $user = User::find($request->company_id);
            if (is_null($user)) {
                return $this->sendError('There is no company with thid ID');
            }

            if ($user->userable_type != Company::class) {
                return $this->sendError('This user is not Company');
            }

            if (auth()->user()->hasReview($user->userable_id)) {
                return $this->sendError('This user already reviewed');
            }

            if (Auth::id() == $request->company_id) {
                return $this->sendError('you can not review yourself');
            }

            $review_data = [
                'company_id' => $user->userable_id,
                'user_id'    => Auth::id(),
                'level'      => $request->level,
                'comment'    => $request->comment,
            ];

            $review = Review::create($review_data);
            $review = $review->get_info($review);

            DB::commit();

            return $this->sendResponse($review);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $company_id)
    {
        $user = User::find($company_id);
        if (is_null($user)) {
            return $this->sendError('There is no company with this ID');
        }

        if ($user->userable_type != Company::class) {
            return $this->sendError('This user is not company');
        }

        $review = auth()->user()->reviews()->where('company_id', $user->userable_id)->first();
        if (is_null($review)) {
            return $this->sendError('This user did not reviewed this company');
        }

        $review->delete();
        return $this->sendResponse();
    }
}
