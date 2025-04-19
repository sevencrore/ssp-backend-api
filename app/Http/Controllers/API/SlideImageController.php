<?php

namespace App\Http\Controllers\API;

use App\Models\SlideImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Validation\ValidationException;

class SlideImageController extends BaseController
{
    // Display all images
    public function index()
    {
        $images = SlideImage::all();
        return response()->json($images);
    }

    // Store a new image
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'navigate_url' => 'required|string|max:255',
                'image_text' => 'nullable|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('slideimages', 'public');

                $slideImage = SlideImage::create([
                    'title' => $validated['title'],
                    'navigate_url' => $validated['navigate_url'],
                    'image_text' => $validated['image_text'] ?? null,
                    'image_path' => $imagePath,
                ]);

                return response()->json(['success' => true, 'data' => $slideImage], 201);
            }

            return response()->json(['success' => false, 'message' => 'Image upload failed.'], 400);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Show a specific image
    public function show(SlideImage $slideImage)
    {
        return response()->json($slideImage);
    }

    // Update an existing image
    public function update(Request $request, SlideImage $slideImage)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'navigate_url' => 'nullable|string|max:255',
                'image_text' => 'nullable|string|max:255',
                'navigate_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            // Handle new image upload if present
            if ($request->hasFile('image')) {
                $newImage = $request->file('image');
    
                if (!$newImage) {
                    return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
                }
    
                $imagePath = $newImage->store('slideimages', 'public');
    
                if (!Storage::disk('public')->exists($imagePath)) {
                    return response()->json(['success' => false, 'message' => 'Image not saved.'], 500);
                }
    
                $slideImage->image_path = $imagePath;
            }
    
            // Update other fields if present
            $slideImage->title = $validated['title'] ?? $slideImage->title;
            $slideImage->navigate_url = $validated['navigate_url'] ?? $slideImage->navigate_url;
            $slideImage->image_text = $validated['image_text'] ?? $slideImage->image_text;
    
            $slideImage->save();
    
            return response()->json(['success' => true, 'data' => $slideImage]);
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }


    // Delete an image
    public function destroy(SlideImage $slideImage)
    {
        Storage::disk('public')->delete($slideImage->image_path);
        $slideImage->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
    }
}
