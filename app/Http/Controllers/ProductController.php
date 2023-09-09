<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use DB;
use App\Models\ProductImage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $products = Product::with('images')->latest()->get();
        if(!is_null($q)){
            $products = Product::with('images')->where('category', $q)->latest()->get();
        }


        return response()->json([
            "status"=>"success",
            "data"=>$products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    public function upload_cloudinary($mfile){
        $uploadedFileUrl = cloudinary()->uploadFile($mfile->getRealPath())->getSecurePath();
        return  $uploadedFileUrl;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "name"=>"required|string|min:3|unique:products",
            "category"=>"required|in:native,english,kaftan",
            "quantity"=>"required",
            "price" => "required",
            "availability"=>"required|in:in_stock,out_of_stock",
            "discount"=>"nullable",
            "description"=>"required",
            "product_images"=>"required",
            "product_images.*.image"=> "required|image|mimes:jpeg,jpg,png",
            "product_images.*.make_main"=>"required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'failed',
                'errors' => $validator->errors()],
                 422);
        }

        $product =  Product::create([
            "name"=>$request->name,
            "category"=>$request->category,
            "quantity"=>$request->quantity,
            "price" => $request->price,
            "availability"=>$request->availability,
            "discount"=> $request->discount,
            "description"=>$request->description,
        ]);

        foreach($request->product_images as $images){

                $image_save = new ProductImage([
                        "image" => $this->upload_cloudinary($images['image']),
                        "make_main"=> $images['make_main']
                ]);

                $product->images()->save($image_save);

        }
        return response()->json([
            "status"=>"success",
            "data"=> $product
        ]);
    }




    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Find the existing product by its ID
    $product = Product::findOrFail($id);
    // var_dump($request->all());
    // Validate the request data
    $validator = Validator::make($request->all(), [
        "name" => "required|string|min:3|unique:products,name,$id",
        "category" => "required|in:native,english,kaftan",
        "quantity" => "required",
        "price" => "required",
        "availability" => "required|in:in_stock,out_of_stock",
        "discount" => "nullable",
        "description" => "required",
        "product_images" => "required|array",
        "product_images.*.id" => "nullable|integer", // Optional: for existing images
        "product_images.*.image" => "required|image|mimes:jpeg,jpg,png|max:2048",
        "product_images.*.make_main" => "required",
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Start a database transaction
        DB::beginTransaction();

        // Update the product attributes
        $product->update([
            "name" => $request->name,
            "category" => $request->category,
            "quantity" => $request->quantity,
            "price" => $request->price,
            "availability" => $request->availability,
            "discount" => $request->discount,
            "description" => $request->description,
        ]);

        // Handle product images update
        $updatedImages = [];
        foreach ($request->product_images as $imageData) {
            if (isset($imageData['id'])) {
                // If the image has an ID, it's an existing image; update it
                $productImage = ProductImage::where('product_id', $product->id)
                    ->findOrFail($imageData['id']);

                $productImage->update([
                    "image" => $this->upload_cloudinary($imageData['image']),
                    "make_main" => $imageData['make_main'],
                ]);
            } else {
                // If there's no ID, it's a new image; create it
                $productImage = new ProductImage([
                    "image" => $this->upload_cloudinary($imageData['image']),
                    "make_main" => $imageData['make_main'],
                ]);

                $product->images()->save($productImage);
            }
            $updatedImages[] = $productImage->id;
        }

        // Delete images that are no longer associated with the product
        $product->images()->whereNotIn('id', $updatedImages)->delete();

        // Commit the transaction
        DB::commit();

        return response()->json([
            "status" => "success",
            "data" => $product,
        ]);
    } catch (\Exception $e) {
        // Something went wrong; roll back the transaction
        DB::rollBack();
        return response()->json([
            'status' => 'failed',
            'message' => 'An error occurred while updating the product.',
        ], 500);
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function deleteProduct($id)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Find the product by its ID
            $product = Product::findOrFail($id);

            // Delete the product's images (assuming you have a relationship set up)
            $product->images()->delete();

            // Delete the product itself
            $product->delete();

            // Commit the transaction
            DB::commit();

            return response()->json([
                "status" => "success",
                "message" => "Product and related images have been deleted successfully.",
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
