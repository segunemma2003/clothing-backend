<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShippingOrderRequest;
use App\Http\Requests\UpdateShippingOrderRequest;
use App\Models\ShippingOrder;
use App\Models\ProductOrder;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class ShippingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shipping = ShippingOrder::latest()->get();
        return response()->json([
            "status"=>"success",
            "data"=>$shipping
        ]);
    }


    public function indexUser()
    {
        try {
            $userId = Auth::user()->id;

            $orders = ShippingOrder::where('user_id', $userId)
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
                "order_id" => "required",
                "name"=> "required",
                "country" => "required",
                "state" => "required",
                "phone" => "required",
                "email" => "required|email",
                "address" => "required",
                "postal_code" => "required"
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();
            $code = time() . "_" . uniqid();
            $shipping =  new ShippingOrder([
                "name"=>$request->name,
                "user_id"=>Auth::user()->id,
                "code"=> $code,
                "category"=>$request->category,
                "country"=>$request->country,
                "state" => $request->state,
                "phone"=>$request->phone,
                "email"=> $request->email,
                "address"=>$request->address,
                "postal_code"=> $request->postal_code
            ]);

            $order = Order::where('id',$request->order_id)->first();
            $order->shipping()->save($shipping);
            DB::commit();

            return response()->json([
                "status" => "success",
                "message" => "shipping created",
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
    public function show(ShippingOrder $shippingOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingOrder $shippingOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShippingOrderRequest $request, ShippingOrder $shippingOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingOrder $shippingOrder)
    {
        //
    }
}
