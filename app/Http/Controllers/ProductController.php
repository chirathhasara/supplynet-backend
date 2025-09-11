<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;


class ProductController extends Controller
{

    public function index()
    {
    $products = Product::all();
    return response()->json(['data' => $products], 200);
    }


    public function store(Request $request)

    {
    $fields = $request->validate([
        'name'   => 'required|string|max:255',
        'image'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'price'  => 'required|numeric|min:0',
        'units'  => 'required|integer|min:0',
    ]);

    if ($request->hasFile('image')) {
        $fields['image'] = $request->file('image')->store('products', 'public');
    }

    $product = Product::create($fields);

    return response()->json([
        'message' => 'Product created successfully!',
        'data' => $product
    ], 201);}



    public function show(Product $product)
    {
    return response()->json(['data' => $product], 200);
    }


    public function update(Request $request, Product $product)
    {
        $fields = $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'image'  => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'price'  => 'sometimes|required|numeric|min:0',
            'units'  => 'sometimes|required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $fields['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($fields);

        return response()->json([
            'message' => 'Product updated successfully!',
            'data' => $product
        ], 200);
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully!'
        ], 200);
    }
}
