<?php

namespace App\Http\Controllers\API;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    public function index()
    {
        $data = OrderItem::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'product_id' => 'required|integer',
            'user_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $orderItem = OrderItem::create($request->all());
        return response()->json(['success' => true, 'data' => $orderItem], 201);
    }

    public function show($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        return response()->json(['success' => true, 'data' => $orderItem]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_id' => 'sometimes|required|integer',
            'product_id' => 'sometimes|required|integer',
            'user_id' => 'sometimes|required|integer',
            'quantity' => 'sometimes|required|integer',
            'price' => 'sometimes|required|integer',
        ]);

        $orderItem = OrderItem::findOrFail($id);
        $orderItem->update($request->all());
        return response()->json(['success' => true, 'data' => $orderItem]);
    }

    public function destroy($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();
        return response()->json(['success' => true, 'data' => null], 204);
    }
}
