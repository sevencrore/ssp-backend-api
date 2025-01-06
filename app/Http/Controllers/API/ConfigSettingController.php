<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfigSetting;
use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;

class ConfigSettingController extends BaseController
{
    // Display all ConfigSettings (Read)
    public function index( Request $request)
    {   $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }
        $settings = ConfigSetting::all();
        return response()->json($settings);
    }

    // Display a specific ConfigSetting by ID (Read)
    public function show($id , Request $request)
    {   $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }
        $setting = ConfigSetting::find($id);
        if ($setting) {
            return response()->json($setting);
        } else {
            return response()->json(['message' => 'ConfigSetting not found'], 404);
        }
    }

    // Create a new ConfigSetting
    public function store(Request $request)
    {    $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }
        $request->validate([
            'referal_incentive' => 'required|integer|min:0',
            'max_level' => 'required|integer|min:0',
            'default_vendor_id' => 'nullable|integer',
            'vendor_comission' => 'nullable|numeric',
        ]);

        $setting = ConfigSetting::create($request->all());
        return response()->json($setting, 201);
    }

    // Update an existing ConfigSetting
    public function update(Request $request, $id)
    {   $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }
        $setting = ConfigSetting::find($id);

        if (!$setting) {
            return response()->json(['message' => 'ConfigSetting not found'], 404);
        }

        $request->validate([
            'referal_incentive' => 'sometimes|integer|min:0',
            'max_level' => 'sometimes|integer|min:0',
            'default_vendor_id' => 'nullable|integer',
            'vendor_comission' => 'nullable|numeric',
        ]);

        $setting->update($request->all());
        return response()->json($setting);
    }
}
