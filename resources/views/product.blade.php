@extends('layouts.app')

@section('content')
<div class="container">


                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


    <form action="" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="col-lg-6">
                <input type="text" name='name' placeholder="name"  value="{{ old('name') }}" required></br>
                <!-- <input type="text" name ='item_sold' placeholder="item_sold"></br> -->

                <input type="text" name='current_price' placeholder="current_price" required value="{{ old('current_price') }}"></br>
                <input type="text" name='discount' placeholder="discount" value="{{ old('discount') }}"></br>

                <!-- <input type="text" name ='former_price' placeholder="former_price"></br> -->
                <input type="text" name='color' placeholder="color" value="{{ old('color') }}"></br>
                <input type="text" name='short_description' placeholder="short_description" value="{{ old('short_description') }}"></br>
                <input type="text" name='long_description' placeholder="long_description" value="{{ old('long_description') }}" ></br>

                <select name="category" id="">
                    <option value="">Choose the category</option>
                    @foreach($categories as $category)
                    <option value="{{$category['name']}}">{{$category['name']}}</option>
                    @endforeach
                </select></br>

                <!-- <input type="text" name ='category' placeholder="category"></br> -->

                <input type="text" name='tags' placeholder="Enter tags separated by comma" size="40" value="{{ old('tags') }}"></br>
                <!-- <input type="text" name ='details_id' placeholder="details_id"></br> -->

                <textarea name="features" id="" cols="40" rows="5" placeholder="Enter one-line features separated by '*'" value="">{{ old('features') }}</textarea></br>

                <!-- </br> -->

            </div>
            <div class="col-lg-6">
                <label for="">Chose the image to upload</label> </br><input type="file" name='image'> </br></br>
                <!-- <input type="text" name ='sm_images_id' placeholder="sm_images_id"></br> -->
                <input type="text" name='size' placeholder="size"  value="{{ old('size') }}"></br>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_featured" id="inlineRadio1" value="Yes">
                    <label class="form-check-label" for="inlineRadio1">Featured</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" checked name="is_featured" id="inlineRadio2" value="No">
                    <label class="form-check-label" for="inlineRadio2">Not featured</label>
                </div></br>
                <!-- <input type="text" name ='is_featured' placeholder="is_featured"></br> -->

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_latest" id="inlineRadio1" value="Yes">
                    <label class="form-check-label" for="inlineRadio1">Latest</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" checked name="is_latest" id="inlineRadio2" value="No">
                    <label class="form-check-label" for="inlineRadio2">Not latest</label>
                </div></br>
                <!-- <input type="text" name ='is_latest' placeholder="is_latest"></br> -->


                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_unique" id="inlineRadio1" value="Yes">
                    <label class="form-check-label" for="inlineRadio1">Unique</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"checked  name="is_unique" id="inlineRadio2" value="No">
                    <label class="form-check-label" for="inlineRadio2">Not Unique</label>
                </div></br>
                <!-- <input type="text" name ='is_unique' placeholder="is_unique"></br> -->


                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_trending" id="inlineRadio1" value="Yes">
                    <label class="form-check-label" for="inlineRadio1">Trending</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" checked name="is_trending" id="inlineRadio2" value="No">
                    <label class="form-check-label" for="inlineRadio2">Not Trending</label>
                </div></br>
                <!-- <input type="text" name='is_trending' placeholder="is_trending"></br> -->

                <input type="text" name='brand' placeholder="brand"   value="{{ old('brand') }}"></br>
                <input type="text" name='code' placeholder="code"  value="{{ old('code') }}"></br>
                <input type="text" name='type' placeholder="type"  value="{{ old('type') }}"></br>


            </div>
            <div class="text-center">
                <input type="submit" value="Submit" style="width:10rem">
            </div>


        </div>
    </form>

</div>
@endsection
