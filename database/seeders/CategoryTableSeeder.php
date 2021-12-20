<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        //This resets the table, deleting all the data everytime the function is called.
        Category::truncate();
        Category::create([
            'name' => 'Electronics',
            'rank' => '1',
            'description' => 'Beautiful products for you'
        ]);
        Category::create([
            'name' => 'Phone Accessories',
            'rank' => '3',
            'description' => 'Beautiful products for you'
        ]);
        Category::create([
            'name' => 'Furniture',
            'rank' => '2',
            'description' => 'Beautiful products for you'
        ]);
        Category::create([
            'name' => 'Fashion',
            'rank' => '4',
            'description' => 'Beautiful products for you'
        ]);
    }
}
