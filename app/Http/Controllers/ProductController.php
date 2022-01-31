<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\Favourite;
use App\Models\Product;
use App\Models\Question;
use App\Models\Shopex;
use App\Models\Transaction;
use App\Models\Transaction_item;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JD\Cloudder\Facades\Cloudder;
use PhpParser\Node\Stmt\TryCatch;

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
                "public_id" =>  substr($disk_name, 0, -4),

            ));

            // dd('reached here');


            $image_url = Cloudder::show(Cloudder::getPublicId()  );

            $isFound = Product::where('image', $image_url)->first();
            // dd($isFound == null);

            if($isFound == null) {
                $data['image'] = $image_url;
                $data['former_price'] = $data['current_price'] * (($data['discount']/100) +1);
                // dd($data);
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



        try{
            $user = new UserController;
            $user_id = $user->getAuthenticatedUser()->original['user']->id;
            $user_liked = Favourite::where('user_id', $user_id)->select('isFavourite', 'product_id');

            $products = Product::leftJoinSub($user_liked,'user_liked', function($leftJoin){
                $leftJoin->on('products.id', '=', 'user_liked.product_id');
            });
        }catch(Exception $e){
            // $products = Product::all();
            $products = DB::table('products');
        }

        //this will be updated if the search has category
        $brandResponse = [];
        $totalHits = $products->count();

        //Arrange the url search params to make them arrays
        foreach($request->all() as $key=>$item){
            if($item != null){
                $data[$key] = explode(",", $item);
            }else{
                $data[$key] = [];
            }
        }
        // return response()->json($data);

        // $data = $request->all()['queryParams'];//for postman

        $categories = $data['category'];
        if (count($categories) !== 0){
            $products = $products->where(function ($query) use ($categories) {
                $qry = $query->where('category', $categories[0]);
                foreach($categories as $key=>$category){
                    if($key!=0){
                        $qry = $qry->orWhere('category', $category);
                    }
                }
            });

            $brandResponse = array_values(array_unique($products->pluck('brand')->all()));

            //get the brands
            $brands = $data['brand'];
            // return response()->json($brands);
            if (count($brands) !== 0){
                $products = $products->where(function ($query) use ($brands){
                    $qry = $query->where('brand', $brands[0]);
                    foreach($brands as $key=> $brand){
                        if($key!=0){
                            $qry = $qry->orWhere('brand', $brand);
                        }
                    }
                    // return response()->json($products->get());
                });
            }
            // return response()->json($products->paginate(10));
        }else{
            // return response()->json($products->paginate(10));
        }

        // return response()->json($products->paginate(10));

        //the price query will happen here

        //now get the categories
        $catResponse = Category::select('name')->pluck('name');
        //now get the brands//
        // brandResponse;
        //get the totalHits
        $totalHits = $products->count();


        $products = $products->paginate(10);

        foreach($products as $product){

            $product->features = explode("*", $product->features);

            $newArray = [];
            foreach($product->features as $feature){
                array_push($newArray, trim($feature) );
            }
            $product->features = $newArray;
        }

        // $products = $products->get();
        $addedInfo['brandResponse'] = $brandResponse;
        $addedInfo['catResponse'] = $catResponse;
        $addedInfo['totalHits'] = $totalHits;

        //
        // $products[0]['item_sold'] = 400;
        return response()->json(compact('products', 'addedInfo'));

    }








    public function user_favourite(Request $request){
        $data = $request->all();
        $product_id = $data['product_id'];

        $user = new UserController;
        $user_id = $user->getAuthenticatedUser()->original['user']->id;

        // return response()->json($data);
        //chech if user has favourite before
        $userFave = Favourite::where('user_id', $user_id)->where('product_id', $product_id);
        $isPresent= $userFave->count();
        if($isPresent > 0){
            if($userFave->first()->isFavourite == "true"){
                $userFave->update(['isFavourite' => "false"]);
                return response()->json(['msg'=>"favourite removed", 'isFavourite'=> false]);
            }else{
                $userFave->update(['isFavourite' => "true"]);
                return response()->json(['msg'=>"favourite added", 'isFavourite'=> true]);
            }
        }else{
            Favourite::create(["user_id"=>$user_id, "product_id"=>$product_id, 'isFavourite' => "true"]);
            return response()->json(['msg'=>"favourite added", 'isFavourite'=> "true"]);
        }

        // return response()->json(compact(['user_id', 'product_id']));
    }


    public function check_contact(Request $request){
        try{
            $user = new UserController;
            $user_id = $user->getAuthenticatedUser()->original['user']->id;

            // $contact_saved = Contact::where('user_id', $user_id)->first()->owner;
            // return response()->json(["status"=> true, "message"=>"User exists", "data"=>$contact_saved]);


            $contact_saved = Contact::with(['owner'])->where('user_id', $user_id)->first();
            if ($contact_saved != null) return response()->json(["status"=> true, "message"=>"User exists", "data"=>$contact_saved]);
            else return response()->json(["status"=> false, "message"=>"User doesnt exist"]) ;
        }catch(Exception $e){
            return response()->json(["status"=> false, "message"=>"Login  or try again"]) ;
        }
    }

    public function save_contact(Request $request){
        try{
            $user = new UserController;
            $user_id = $user->getAuthenticatedUser()->original['user']->id;
        }catch(Exception $e){
            return response()->json(["message"=>"Login again", "status"=>'failed'], 400);
        }
        if ($request->isMethod('GET')){
            $contact = Contact::find($user_id);
            return response()->json(["message"=>"OK", "status"=>'OK', "data"=>$contact], 400);
        }

        $body = ($request->all());
        $validator = Validator::make( $body,[
            'phone'=>'required',
            'firstname'=>'required',
            'lastname'=>'required',
            'address'=>'required',
            'suit_no'=>'required',
            'city'=>'required',
            'country'=>'required',
            'postal_code'=>'required',
            'contact_me'=>'required',
        ]);

        if ( $validator->fails() )
        {
            return response()->json( [
                'status' => "failed",
                'message' => $validator->errors()->first()
            ]);
        }



        $contact_saved = Contact::where('user_id', $user_id)->count();
        if ($contact_saved){
            $update = Contact::find($user_id)->update($body);
            return response()->json(["status"=> true, "message"=>"Contact updated","data"=>$body]);
        }

        $body['user_id'] = $user_id;
        $created = Contact::create($body);
        if ($created != null){
            $resp['status'] = 'ok';
            $resp['message'] = 'Your contact is saved';
            return response()->json([$resp]);
        }else{
            $resp['status'] = 'failed';
            $resp['message'] = 'Your contact is not saved';
            return response()->json([$resp]);
        }
    }

    public function payment(Request $request_){
        $request = (object)$request_->all();

        // return response()->json($request);

        $trans_data = [];
        $user_id = $request->user_id;
        $total = $request->total;

        $trans_data['trans_ref'] = $request->reference;
        $trans_data['total'] = $total;
        $trans_data['user_id'] = $user_id;
        $trans_data['status'] = 'pending';
        // $trans_data['status'] = $request->reference['status'];
        // return response()->json($user_id);

        $trans_id = Transaction::create($trans_data)->id;


        forEach($request->cartData as $item){
            $trans_item['transaction_id'] = $trans_id;
            $trans_item['user_id'] = $user_id;
            $trans_item['size'] = $item['size'];


            $trans_item['color'] = $item['color'];
            $trans_item['name'] = $item['name'];

            $trans_item['product_id'] = $item['id'];
            $trans_item['price'] = $item['currentPrice'];


            $trans_item['quantity'] = $item['quantity'];
            $trans_item['total'] = $item['quantity'] * $item['currentPrice'];


            Transaction_item::create($trans_item);
            // return response()->json(['status'=>'success', 'message'=> 'Transaction saved']);
        }

        return response()->json(['status'=>'success', 'message'=> 'Transaction saved']);

    }
    public function updatepayment(Request $request){
        // return response()->json($request->all()['reference']);
        Transaction::where('trans_ref', $request->all()['reference'])
                    ->update(['status'=>'completed']);
        return response()->json(['status'=>'success']);
    }



    public function single_product(Request $request, $id){
        $product = Product::find($id);

        $product->features = explode("*", $product->features);
        $newArray = [];
        foreach($product->features as $feature){
            array_push($newArray, trim($feature) );
        }
        $product->features = $newArray;

        $related = Product::where('category', $product->category)
            ->where('id','<>', $product->id)
            ->take(4)->select('id','image', 'name', 'current_price')->get();
        return response()->json(compact('product', 'related') );
    }


    public function homepage(Request $request){



        try{
            $user = new UserController;
            $user_id = $user->getAuthenticatedUser()->original['user']->id;
            $user_liked = Favourite::where('user_id', $user_id)->select('isFavourite', 'product_id');

            $products = Product::leftJoinSub($user_liked,'user_liked', function($leftJoin){
                $leftJoin->on('products.id', '=', 'user_liked.product_id');
            });
            // return response()->json($products);
        }catch(Exception $e){
            // $products = Product::all();
            $products = DB::table('products');
        }



        $featured = $products->where("is_featured", "Yes")->inRandomOrder()->limit(4)->get();
        $trending = Product::where("is_trending", "Yes")->inRandomOrder()->limit(9)->get();
        $unique = Product::where("is_unique", "Yes")->inRandomOrder()->limit(4)->get();
        $latest = Product::where("is_latest", "Yes")->inRandomOrder()->limit(6)->get();

        $discount = Product::where("discount", ">", '20')->inRandomOrder()->limit(1)->get()[0];
        $discount->features = explode("*", $discount->features);
        $newArray = [];
        foreach($discount->features as $feature){
            array_push($newArray, trim($feature) );
        }
        $discount->features = $newArray;

        $shopexes = Shopex::all();

        $uflt = Product::where([
                ['is_unique', '=', 'Yes'],
                ['is_latest', '=', 'Yes'],
                ['is_trending', '=', 'Yes'],
            ])->inRandomOrder()->limit(1)->get()[0];

        $uflt->features = explode("*", $uflt->features);
        $newArray = [];
        foreach($uflt->features as $feature){
            array_push($newArray, trim($feature) );
        }
        $uflt->features = $newArray;

        $topcat = Category::orderBy('rank')->first()->name;
        $topcat = Product::where('category', $topcat)->inRandomOrder()->limit(4)->get();

        $blogs = Blog::inRandomOrder()->limit(4)->get();

        $data = compact('featured', 'trending', 'unique', 'latest', 'shopexes', 'uflt', 'discount', 'topcat', 'blogs');

        // return response()->json('hello');
        return response()->json(["status"=>"OK", 'data'=>$data], 200);
    }

    public function blogs(Request $request){
        $blogs = Blog::paginate(3);
        foreach($blogs as $blog){

            $blog->content = explode("*", $blog->content);
            $newArray = [];
            foreach($blog->content as $content){
                array_push($newArray, trim($content) );
            }
            $blog->content = $newArray;
        }

        return response()->json($blogs);
    }

    public function singleblog(Request $request, $id){
        $blog = Blog::find($id);
        if($blog != null){
            $blog->content = explode("*", $blog->content);
            $newArray = [];
            foreach($blog->content as $content){
                array_push($newArray, trim($content) );
            }
            $blog->content = $newArray;
            return response()->json(['blog'=>$blog, 'status'=>'success'], 200);
        }
        return response()->json(['status'=>'fail'], 404);
    }


    public function add_question(request $request){
        Question::create($request->all());
        return response()->json(200);
    }

    public function faqs(){
        $faqs = Faq::all();
        return response()->json($faqs);
    }



// ============================================================================================================
    public function greet(Request $request){
        // return response()->json( [
        //     'msg'=>'request successful',
        //     'data'=>'How are you sir?'
        // ]);

        $body = $request->getContent();
        $data = json_decode($body);

        $password = $data->password;
        $email = $data->email;
        $resp = $password.$email;

        return response()->json(
            $resp
        );

    }

    public function register(Request $request){
        $body = json_decode($request->getContent());
        $body = (array) $body;

        try {
            $validator = Validator::make( $body,[
                'email' => 'email|required|unique:users',
                'password' => 'string|required|min:4',
            ]);

            if ( $validator->fails() )
            {
                return response()->json( [
                    'status' => "failed",
                    'msg' => $validator->errors()->first()
                ]);
            }

            $body = (object) $body;
            $hashed = Hash::make($body->password);

            $created = User::create(['email'=>$body->email, 'password'=>$hashed]);
            if ($created != null){
                $resp['status'] = 'ok';
                $resp['msg'] = 'You have been registered';
                return response()->json([$resp]);
            }else{
                $resp['status'] = 'fail';
                $resp['msg'] = 'You have not been registered';
                return response()->json([$resp]);
            }
        } catch (Exception $e) {
            $resp['status'] = 'fail';
            $resp['msg'] = "Perhaps a server error: please try again";
            return response()->json($resp);
        }

    }



    public function login(Request $request){

        if ($request->isMethod('post')){
            $request->validate([
                "email" => 'required',
                'password' => 'required',
            ]);
            $credentials = $request->only('email', 'password');
            if(Auth::attempt($credentials)){
                return response()->json('You are signed in');
            }return response()->json('Fuvk off');
        }
        return response()->json($request->all());
    }

}
