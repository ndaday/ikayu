<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $description = $request->input('description');
        $tag = $request->input('tag');
        $categories = $request->input('categories');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if ($id) {
            $product = Product::with(['category', 'galleries'])->find($id);

            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data produk berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data produk tidak ada',
                    404
                );
            }
        }

        $product = Product::with(['category', 'galleries']);

        if ($name) {
            $product->where('name', 'like', '%', $name, '%');
        }
        if ($description) {
            $product->where('description', 'like', '%', $description, '%');
        }
        if ($tag) {
            $product->where('tag', 'like', '%', $tag, '%');
        }
        if ($categories) {
            $product->where('categories', $categories);
        }
        if ($price_from) {
            $product->where('price_from', '>=', $price_from);
        }
        if ($price_to) {
            $product->where('price_to', '<=', $price_to);
        }
        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data produk berhasil diambil'
        );
    }

    public function addProduct(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'price' => 'required',
                'description' => 'required',
                'tag' => 'nullable',
                'categories_id' => 'required',
            ]);

            $product = Product::create($data);

            return ResponseFormatter::success([$product, 'Product berhasil ditambahkan']);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 500);
        }
    }
}
