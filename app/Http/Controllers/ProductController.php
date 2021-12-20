<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;

class ProductController extends Controller
{
    //
    public function save_product(Request $request){
        if($request->isMethod('post')){
            $request->flash();
            $data = $request->all();
            // dd($request->all());
            $this->validate($request, [
                'image' => 'image|mimes:jpeg,png,jpg|required',
            ]);


            $image_name = $request->file('image')->getRealPath();
            $disk_name = $request->file('image')->getClientOriginalName();

            // dd($disk_name);
            Cloudder::upload($image_name, null, array(
                "folder" => "cloudi-test",
                "overwrite" => FALSE,
                "resource_type" => "image",
                "responsive" => TRUE,
                "public_id" => $disk_name,
            ));

            // dd('reached here');


            $image_url = Cloudder::show(Cloudder::getPublicId()  );

            $isFound = Product::where('image', $image_url)->first();
            // dd($isFound == null);

            if($isFound == null) {
                $data['image'] = $image_url;
                Product::create($data);

                return back()->with('status', 'Media successfully updated!');
            }
            return back()->with('status', 'Media not successfully updated!');

        }
        $categories = Category::all();//->get();
        // dd($categories);
        // $categories = ['electronics', 'cutleries', 'Computers'];
        return view('product', compact('categories'));
    }

    public function products(Request $request){
        $products = Product::all();
        return response()->json($products);
        
    }
}
