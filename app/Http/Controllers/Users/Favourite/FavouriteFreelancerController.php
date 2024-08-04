<?php

namespace App\Http\Controllers\Users\Favourite;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\Users\Favourite\FavouriteFreelancer;
use App\Models\Users\Freelancer\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavouriteFreelancerController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            $favourite_freelancers = Auth::user()->favourite_freelancers()->with('freelancer')->get();
            $favourite_freelancers = (new FavouriteFreelancer)->get_favourite_freelancers($favourite_freelancers);
            return $this->sendResponse($favourite_freelancers);
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
        $validator = Validator::make($request->all(), [
            'freelancer_id' => ['required', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user = User::find($request->freelancer_id);
        if (auth()->user()->hasFavouriteFreelancer($user->userable_id))
        {
            return $this->sendError('This freelancer already favourited');
        }

        $favourite_freelancer = FavouriteFreelancer::create([
            'freelancer_id' => $user->userable_id,
            'user_id' => Auth::id()
        ]);

        return $this->sendResponse($favourite_freelancer);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $favourite_freelancer = FavouriteFreelancer::with('freelancer')->find($id);
        if (is_null($favourite_freelancer)) {
            return $this->sendError('There is no favourite_freelancer with this ID');
        }

        $favourite_freelancer = $favourite_freelancer->get_favourite_freelancer($favourite_freelancer);
        return $this->sendResponse($favourite_freelancer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FavouriteFreelancer $favouriteFreelancer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FavouriteFreelancer $favouriteFreelancer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $freelancer = Freelancer::find($id);
        if (is_null($freelancer)) {
            return $this->sendError('There is no freelancer with this ID');
        }

        $freelancer->delete();
        return $this->sendResponse();
    }
}
