<?php

namespace App\Http\Controllers\API;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ImageResource;
use App\Http\Controllers\API\BaseController as BaseController;


class ImageController extends BaseController
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'nullable|string|max:255', 
        ]);
    
        $path = $request->file('image')->store('images', 'public');
            $image = Image::create([
            'image_path' => $path,
            'title' => $request->input('title', 'Untitled') 
        ]);
    
        return response()->json(new ImageResource($image), 201);
    }
    
    public function index()
    {
        $images = Image::all();
        return ImageResource::collection($images);
    }
}