<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Company;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
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
        elseif ($request->has('review_id')) {
            return $this->show($request->project_id);
        }
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

            $user = User::find($request->user_id);
            $review_data = [
                'company_id' => $user->userable_id,
                'level'      => $request->level,
                'comment'    => $request->comment,
            ];

            $review = Review::create($review_data);

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
        $review = Review::find($id);

        if (is_null($review)) {
            return $this->sendError(['message' => 'There is no project with this ID']);
        }

        return $this->sendResponse($review);
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

        return $this->sendResponse($user->userable->reviews);
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
        DB::beginTransaction();
        try {
            $updateReviewRequest = new UpdateReviewRequest();
            $validator = Validator::make($request->all(), $updateReviewRequest->rules());

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $review = Review::find($id);
            $review->update($request->all());

            DB::commit();

            return $this->sendResponse($review);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);

        if (is_null($review)) {
            return $this->sendError(['message' => 'There is no review with this ID']);
        }

        $review->delete();
        return $this->sendResponse();
    }
}
