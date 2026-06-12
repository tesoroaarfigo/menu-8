<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function menu()
    {
        $products = Product::where('is_active', true)
            ->orderBy('category')
            ->get();

        $zones = DeliveryZone::where('is_active', true)->get();

        return view('menu', compact('products', 'zones'));
    }

    public function index()
    {
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => Product::where('is_active', true)
                    ->orderBy('category')
                    ->get(),
                'message' => 'Productos obtenidos exitosamente'
            ]);
        }

        $products = Product::orderBy('category')->get();

        return view('admin.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    protected function normalizeProductData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_usd' => 'required|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'price_bs' => 'nullable|numeric|min:0',
            'category' => 'required|string|max:100',
            'tag' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:100',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('price')) {
            $validated['price_usd'] = $validated['price'];
            unset($validated['price']);
        }

        return $validated;
    }

    public function store(Request $request)
    {
        $validated = $this->normalizeProductData($request);

        $product = Product::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Producto creado exitosamente'
            ], 201);
        }

        return redirect()->back()->with('success', 'Producto creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            return redirect()->back()->withErrors(['Producto no encontrado']);
        }

        $validated = $this->normalizeProductData($request);

        $product->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Producto actualizado exitosamente'
            ]);
        }

        return redirect()->back()->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            return redirect()->back()->withErrors(['Producto no encontrado']);
        }

        $product->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);
        }

        return redirect()->back()->with('success', 'Producto eliminado exitosamente');
    }

    public function byCategory($category)
    {
        return response()->json([
            'success' => true,
            'data' => Product::where('category', $category)
                ->where('is_active', true)
                ->get(),
            'message' => "Productos de la categoría '{$category}' obtenidos"
        ]);
    }
}
