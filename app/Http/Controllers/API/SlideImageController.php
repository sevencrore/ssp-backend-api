<?php

namespace App\Http\Controllers\API;

use App\Models\SlideImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ConfigSetting;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class SlideImageController extends BaseController
{
    // Display all images
    public function index()
    {
        $images = SlideImage::all();
        return response()->json($images);
    }

    public function getforntpageImages()
    {
        try {
            $configSetting = ConfigSetting::first();

            // If configSetting is null, use default limit
            $limit = $configSetting->slideImage_displayCount ?? 5;

            $images = SlideImage::orderBy('order_number', 'asc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Front page images fetched successfully.',
                'data' => $images
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch front page images.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getALLPaginated(Request $request): JsonResponse
    {
        try {
            // Get query parameters
            $title = $request->query('title');
            $perPage = $request->query('per_page', 10); // Default to 10 items per page

            // Query builder
            $query = SlideImage::query();

            // Apply filters if provided
            if (!empty($title)) {
                $query->where('title', 'LIKE', "%$title%");
            }

            // Paginate results and append query parameters
            $data = $query->paginate($perPage)->appends([
                'title' => $title,
                'per_page' => $perPage
            ]);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve slide Images',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new image

    public function store(Request $request)
    {
        try {
            // Log the raw incoming request
            Log::info('Slide Image Store Request:', [
                'payload' => $request->all(),
                'has_image' => $request->hasFile('image')
            ]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'navigate_url' => 'required|string|max:255',
                'image_text' => 'nullable|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'order_number' => 'nullable|integer',
            ]);

            // Log validated data
            Log::info('Validated Slide Image Data:', $validated);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('slideimages', 'public');

                // Log image path
                Log::info('Image Uploaded Successfully:', ['image_path' => $imagePath]);

                $slideImage = SlideImage::create([
                    'title' => $validated['title'],
                    'navigate_url' => $validated['navigate_url'],
                    'image_text' => $validated['image_text'] ?? null,
                    'image_path' => $imagePath,
                    'order_number' => $validated['order_number'] ?? null,
                ]);

                // Log DB insert success
                Log::info('Slide Image Created:', $slideImage->toArray());

                return response()->json(['success' => true, 'data' => $slideImage], 201);
            }

            Log::warning('Image upload failed – no image found in request.');

            return response()->json(['success' => false, 'message' => 'Image upload failed.'], 400);

        } catch (ValidationException $e) {
            Log::error('Validation Error in Slide Image Store:', $e->errors());

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Unexpected Error in Slide Image Store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
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
            // Log the incoming update request
            Log::info('Slide Image Update Request:', [
                'id' => $slideImage->id,
                'payload' => $request->all(),
                'has_image' => $request->hasFile('image')
            ]);

            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'navigate_url' => 'nullable|string|max:255',
                'image_text' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'order_number' => 'nullable|integer',
            ]);

            // Log validated data
            Log::info('Validated Slide Image Update Data:', $validated);

            // Handle new image upload if present
            if ($request->hasFile('image')) {
                $newImage = $request->file('image');

                if (!$newImage) {
                    Log::warning('Update failed: image key present but no file uploaded');
                    return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
                }

                $imagePath = $newImage->store('slideimages', 'public');

                if (!Storage::disk('public')->exists($imagePath)) {
                    Log::error('Image save failed, path missing after upload', ['image_path' => $imagePath]);
                    return response()->json(['success' => false, 'message' => 'Image not saved.'], 500);
                }

                // Log successful image upload
                Log::info('New Image Uploaded Successfully:', ['image_path' => $imagePath]);

                $slideImage->image_path = $imagePath;
            }

            // Log before updating fields
            Log::info('Updating Slide Image Record:', [
                'id' => $slideImage->id,
                'before_update' => $slideImage->toArray()
            ]);

            // Update fields if present
            $slideImage->title = $validated['title'] ?? $slideImage->title;
            $slideImage->navigate_url = $validated['navigate_url'] ?? $slideImage->navigate_url;
            $slideImage->image_text = $validated['image_text'] ?? $slideImage->image_text;
            $slideImage->order_number = $validated['order_number'] ?? $slideImage->order_number;

            $slideImage->save();

            // Log after update
            Log::info('Slide Image Updated Successfully:', [
                'id' => $slideImage->id,
                'after_update' => $slideImage->toArray()
            ]);

            return response()->json(['success' => true, 'data' => $slideImage]);

        } catch (ValidationException $e) {

            Log::error('Validation Error in Slide Image Update:', $e->errors());

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            Log::error('Unexpected Error in Slide Image Update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
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
