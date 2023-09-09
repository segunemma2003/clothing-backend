<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ProductOrder;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user','shipping','products' => function ($query) {
            $query->with('product');
        }])->latest()->get();
        return response()->json([
            "status"=>"success",
            "data"=>$orders
        ]);
    }

    public function indexUser()
    {
        try {
            $userId = Auth::user()->id;

            $orders = Order::with(['user','shipping', 'products' => function ($query) {
                $query->with('product');
            }])
                ->where('user_id', $userId) // Filter orders for a specific user
                ->latest()
                ->get();

            return response()->json([
                "status" => "success",
                "data" => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
                // "Failed to retrieve orders."
            ], 522);
        }
    }

    /**
      * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [

                "total_price" => "required",
                "sub_total" => "required",
                "delivery_fee" => "required",
                "tax_rate" => "required",
                "type_of_delivery" => "required",
                "products" => "required|array",
                "products.*.id" => "required",
                "products.*.quantity" => "required",
                "products.*.total_price" => "required",
                "products.*.discount" => "required",
                "products.*.tax_rate" => "nullable",
                "products.*.size" => [
                    'nullable',
                    'required_unless:products.*.is_measurement,true',
                ],
                "products.*.measurements" => [
                    'nullable',
                    'required_if:products.*.is_measurement,true',
                ],
                "products.*.is_measurement" => "nullable|boolean",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $code = time() . "_" . uniqid();
            $total_price = 0;
            $sub_total = 0;

            foreach ($request->products as $product) {
                $original_product = Product::find($product['id']);
                $tot = (($product['quantity'] * $original_product->price) - (($product['quantity'] * $original_product->price) * $product['discount'])) + $product['tax_rate'];
                $total_price += $tot;
                $sub_total += $product['quantity'] * $original_product->price;
            }

            $order = Order::create([
                "code" => $code,
                "user_id"=> Auth::user()->id,
                "total_price" => $total_price,
                "sub_total" => $sub_total,
                "delivery_fee" => $request->delivery_fee,
                "tax_rate" => $request->tax_rate,
                "type_of_delivery" => $request->type_of_delivery,
            ]);

            foreach ($request->products as $product) {
                $original_product = Product::find($product['id']);
                $product_order = ProductOrder::create([
                    'order_id' => $order->id,
                    "code" => $code,
                    "product_id" => $product['id'],
                    "product_code" => $code,
                    "total_price" => (($product['quantity'] * $original_product->price) - (($product['quantity'] * $original_product->price) * $product['discount'])) + $product['tax_rate'],
                    "sub_total" => ($product['quantity'] * $original_product->price),
                    "delivery_fee" => 0.00,
                    "discount" => $product['discount'],
                    "quantity" => $product['quantity'],
                    "tax_rate" => $product['tax_rate'],
                    "size" => $product['size'],
                    "measurements" => $product['measurements'],
                    "is_measurement" => $product['is_measurement'],
                ]);
            }

            return response()->json([
                "status" => "success",
                "data" => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" =>  "An error occurred while processing the request.",
            ], 522);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $order = Order::with(['shipping','products' => function ($query) {
                $query->with('product');
            }])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $order,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
                // 'An error occurred while fetching the order.',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Find the product by its ID
            $order = Order::findOrFail($id);

            // Delete the product's images (assuming you have a relationship set up)
            $order->products()->delete();

            // Delete the product itself
            $order->delete();

            // Commit the transaction
            DB::commit();

            return response()->json([
                "status" => "success",
                "message" => "Order and other related products have been deleted successfully.",
            ]);
        } catch (\Exception $e) {
            // Something went wrong; roll back the transaction
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while deleting the product and its images.',
            ], 500);
        }
    }
}
