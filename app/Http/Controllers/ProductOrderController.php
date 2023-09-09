<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductOrderRequest;
use App\Http\Requests\UpdateProductOrderRequest;
use App\Models\ProductOrder;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;



class ProductOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = ProductOrder::with(['order', 'product'])->latest()->get();
        return response()->json([
            "status"=>"success",
            "data"=>$orders
        ]);
    }


    public function indexUser()
    {
        try {
            $userId = Auth::user()->id;

            $orders = ProductOrder::with(['order'=>function($query) use ($userId  ) {
                $query->where('user_id', $userId);
            }, 'product'])
                // Filter orders for a specific user
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
                "order_id" => "required",
                "product_id" => "required",
                "quantity" => "required",
                "discount" => "required",
                "size" => [
                    'nullable',
                    'required_unless:is_measurement,true',
                ],
                "measurements" => [
                    'nullable',
                    'required_if:is_measurement,true',
                ],
                "is_measurement" => "nullable|boolean",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

     DB::beginTransaction();
        $order = Order::findOrFail($request->order_id);
        $total_price = $order->total_price ?? 0;
        $original_product =Product::findOrFail($request->product_id);
        $tot = (($request->quantity * $original_product->price) - (($request->quantity * $original_product->price) * $request->discount)) + $request->tax_rate;
        $total_price  += $tot;
        $order->total_price = $total_price;
        $order->sub_total += ($request->quantity * $original_product->price);
        $order->save();
        $product_is_present = ProductOrder::where('product_id', $request->product_id)
        ->where('order_id', $request->order_id)
        ->exists();
        $product_order = ProductOrder::where('product_id', $request->product_id)
            ->where('order_id', $request->order_id)->first();
        if($product_is_present &&  $product_order->size == $request->size ){
            // $product_order = ProductOrder::where('product_id', $request->product_id)
            // ->where('order_id', $request->order_id)->();
            // var_dump($product_order);
            $product_order->quantity += $request->quantity;
            $product_order->total_price += $tot;
            $product_order->sub_total += ($request->quantity * $original_product->price);
            $product_order->delivery_fee = 0.00;
            $product_order->discount +=  $request->discount;
            $product_order->quantity += $request->quantity;
            $product_order->tax_rate += $request->tax_rate;
            $product_order->size =$request->size;
            $product_order->measurements =$request->measurements;
            $product_order->is_measurement = $request->is_measurement;
            $product_order->save();
        }else{
            $product_order = ProductOrder::create([
                'order_id' => $order->id,
                "code" => $order->code,
                "product_id" => $request->product_id,
                "product_code" => $order->code,
                "total_price" => $tot,
                "sub_total" => ($request->quantity * $original_product->price),
                "delivery_fee" => 0.00,
                "discount" => $request->discount,
                "quantity" => $request->quantity,
                "tax_rate" => $request->tax_rate,
                "size" => $request->size,
                "measurements" => $request->measurements,
                "is_measurement" => $request->is_measurement,
            ]);
        }
        DB::commit();

        return response()->json([
            "status" => "success",
            "data" =>  $product_order,
        ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => "error",
                "message" =>  $e->getMessage()
                // "An error occurred while processing the request.",
            ], 522);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            $orders = ProductOrder::with(['order','product'])
                // Filter orders for a specific user
                ->where('id', $id)
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
     * Show the form for editing the specified resource.
     */
    public function edit(ProductOrder $productOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductOrderRequest $request, ProductOrder $productOrder)
    {
        //
    }


    public function updateOrderStatus(Request $request, $id){
        try {
            // Start a database transaction
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            $order->status = $request->status;
            $order->save();
            $order->products()->update([
                "status"=>$request->status
            ]);

            DB::commit();

            return response()->json([
                "status" => "success",
                "message" => "Order and other related products have been updated successfully.",
            ]);
        } catch (\Exception $e) {
            // Something went wrong; roll back the transaction
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
                // 'An error occurred while deleting the product and its images.',
            ], 500);
        }
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
            $order = ProductOrder::findOrFail($id);

            // Delete the product's images (assuming you have a relationship set up)

            $p_order = Order::findOrFail($order->order_id);
            $original_product =Product::findOrFail($order->product_id);
            $total_price = $p_order->total_price;
            $total_price -=$order->total_price;
            $p_order->total_price = $total_price;
            $p_order->sub_total -= $order->sub_total;
            $p_order->save();

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
                'message' => $e->getMessage()
                // 'An error occurred while deleting the product and its images.',
            ], 500);
        }
    }
}
