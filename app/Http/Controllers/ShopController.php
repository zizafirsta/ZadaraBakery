<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // Tampilkan Halaman Katalog Utama & Filter Kategori
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        // Filter berdasarkan kategori jika dipilih
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->get();

        return view('shop.index', compact('products', 'categories'));
    }

    // Tampilkan Detail Produk
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('shop.show', compact('product'));
    }
}
