<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:10|unique:products,sku',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer',
            'weight_kg' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'stock.integer' => 'The stock quantity must be a whole number (integers only, no decimals allowed).',
            'sku.unique' => 'This SKU is already in use.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $allowedCategories = [
            'Tools', 'Stationery', 'Sports', 'Pets', 'Outdoors', 'Misc', 
            'Kitchen', 'Home & Office', 'Health', 'Gifts', 'Games', 
            'Footwear', 'Food & Beverage', 'Electronics', 'Clothing', 
            'Books', 'Beauty', 'Accessories'
        ];

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'sku' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('products', 'sku')->ignore($product->id)],
            'description' => 'nullable|string',
            'category' => ['sometimes', 'required', Rule::in($allowedCategories)],
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'weight_kg' => 'nullable|numeric|min:0',
        ]);

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }

    public function bulkDelete(Request $request)
    {
        $validatedData = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:products,id',
        ]);

        Product::whereIn('id', $validatedData['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Products deleted successfully'
        ], Response::HTTP_OK);
    }

    public function importCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
            'update_existing' => 'nullable|boolean', // Recibe la decisión del usuario (true/false)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->getRealPath();
        $updateExisting = filter_var($request->input('update_existing', true), FILTER_VALIDATE_BOOLEAN);

        if (($handle = fopen($path, 'r')) !== FALSE) {
            $header = fgetcsv($handle, 1000, ',');
            
            $createdCount = 0;
            $updatedCount = 0;
            $ignoredCount = 0; // Cambiamos el conteo de rechazados por ignorados si ya existen y se eligió ignorar
            $rejectedCount = 0;
            $errors = [];
            $rowNumber = 1;

            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $rowNumber++;

                if (count($header) !== count($row)) {
                    $rejectedCount++;
                    continue;
                }

                $data = array_combine($header, $row);

                if (empty(trim($data['name'] ?? '')) || empty(trim($data['sku'] ?? ''))) {
                    $rejectedCount++;
                    continue;
                }

                $cleanName = strip_tags(trim($data['name']));
                $cleanSku = strtoupper(trim($data['sku']));
                $cleanDescription = isset($data['description']) ? strip_tags(trim($data['description'])) : null;
                $cleanCategory = trim($data['category'] ?? 'Misc');
                $price = $data['price'] ?? null;
                $stock = $data['stock'] ?? null;
                $weight_kg = $data['weight_kg'] ?? 0;

                if (!is_numeric($price) || $price < 0 || !is_numeric($stock) || intval($stock) < 0) {
                    $rejectedCount++;
                    $errors[] = "Row {$rowNumber} (SKU: {$data['sku']}): Invalid price or stock value.";
                    continue;
                }

                try {
                    $existingProduct = Product::where('sku', $cleanSku)->first();

                    if ($existingProduct) {
                        if ($updateExisting) {
                            // Actualizar el existente
                            $existingProduct->update([
                                'name' => $cleanName,
                                'description' => $cleanDescription,
                                'category' => $cleanCategory,
                                'price' => $price,
                                'stock' => intval($stock),
                                'weight_kg' => (is_numeric($weight_kg) && $weight_kg >= 0) ? $weight_kg : 0,
                            ]);
                            $updatedCount++;
                        } else {
                            // Ignorar si ya existe
                            $ignoredCount++;
                        }
                    } else {
                        // Crear nuevo
                        Product::create([
                            'sku' => $cleanSku,
                            'name' => $cleanName,
                            'description' => $cleanDescription,
                            'category' => $cleanCategory,
                            'price' => $price,
                            'stock' => intval($stock),
                            'weight_kg' => (is_numeric($weight_kg) && $weight_kg >= 0) ? $weight_kg : 0,
                        ]);
                        $createdCount++;
                    }
                } catch (\Exception $e) {
                    $rejectedCount++;
                    $errors[] = "Row {$rowNumber} (SKU: {$cleanSku}) failed: " . $e->getMessage();
                }
            }
            fclose($handle);

            return response()->json([
                'success' => true,
                'message' => "Import complete: {$createdCount} created, {$updatedCount} modified, {$ignoredCount} ignored, {$rejectedCount} rejected.",
                'stats' => [
                    'created' => $createdCount,
                    'modified' => $updatedCount,
                    'ignored' => $ignoredCount,
                    'rejected' => $rejectedCount
                ],
                'errors' => $errors
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Could not read the uploaded file.'
        ], 500);
    }
    public function bulkCheckout(Request $request)
    {
        $items = $request->input('items', []); // Array de [ 'id' => productId, 'quantity' => qty ]

        foreach ($items as $item) {
            $product = Product::find($item['id']);
            if ($product && $product->stock >= $item['quantity']) {
                $product->stock -= $item['quantity'];
                $product->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart transaction processed successfully!'
        ]);
    }
}