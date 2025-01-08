<?php

namespace App\Http\Controllers\API;

use App\Models\SlideImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseController as BaseController;

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
       $validated = $request->validate([
           'title' => 'nullable|string|max:255',
           'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
       ]);

       if ($request->hasFile('image')) {
           $imagePath = $request->file('image')->store('slideimages', 'public');

           $slideImage = SlideImage::create([
               'title' => $validated['title'] ?? null,
               'image_path' => $imagePath,
           ]);

           return response()->json(['success' => true, 'data' => $slideImage], 201);
       }

       return response()->json(['success' => false, 'message' => 'Image upload failed.'], 400);
   }

   // Show a specific image
   public function show(SlideImage $slideImage)
   {
       return response()->json($slideImage);
   }

   // Update an existing image
   public function update(Request $request, SlideImage $slideImage)
   {
       $validated = $request->validate([
           'title' => 'nullable|string|max:255',
           'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
       ]);
       Log::info($request->hasFile('image'));
       if ($request->hasFile('image')) {
           // Debugging: Check the uploaded file
           $newImage = $request->file('image');
           if (!$newImage) {
               return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
           }
   
           // Debugging: Check old image path
           if ($slideImage->image_path) {
               if (Storage::disk('public')->exists($slideImage->image_path)) {
                   Storage::disk('public')->delete($slideImage->image_path);
               }
           }
   
           // Store new image
           $imagePath = $newImage->store('slideimages', 'public');
   
           // Debugging: Ensure new image path is valid
           if (!Storage::disk('public')->exists($imagePath)) {
               return response()->json(['success' => false, 'message' => 'Image not saved.'], 500);
           }
   
           $slideImage->image_path = $imagePath;
       }
   
       $slideImage->title = $validated['title'] ?? $slideImage->title;
       $slideImage->save();
   
       return response()->json(['success' => true, 'data' => $slideImage]);
   }
   

   // Delete an image
   public function destroy(SlideImage $slideImage)
   {
       Storage::disk('public')->delete($slideImage->image_path);
       $slideImage->delete();

       return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
   }
}
