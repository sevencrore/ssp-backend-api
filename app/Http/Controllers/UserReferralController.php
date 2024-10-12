<?php

namespace App\Http\Controllers\API;

use App\Models\UserReferral;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class UserReferralController extends BaseController
{
    public function index()
    {
        return UserReferral::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'reg_user_id' => 'required|exists:users,id',
            'referral_id' => 'required|exists:users,id',
        ]);

        return UserReferral::create($request->all());
    }

    public function show($id)
    {
        return UserReferral::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'reg_user_id' => 'sometimes|required|exists:users,id',
            'referral_id' => 'sometimes|required|exists:users,id',
        ]);

        $userReferral = UserReferral::findOrFail($id);
        $userReferral->update($request->all());

        return $userReferral;
    }

    public function destroy($id)
    {
        $userReferral = UserReferral::findOrFail($id);
        $userReferral->delete();

        return response()->noContent();
    }
}
