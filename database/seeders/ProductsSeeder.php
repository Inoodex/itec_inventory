<?php

namespace Database\Seeders;

use App\Models\Admin\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                "name" => "Level 20 RGB Cherry",
                "category_id" => 2,
                "description" => "<p>With pepperoni love An incredible gift - a heart-shaped pizza</p>",
                "image" => "20240216161024_uRwm7SUzkm.jpg",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Caprese",
                "category_id" => 2,
                "description" => "<p>Ranch sauce, mozzarella, cherry tomatoes, pesto, rucola</p>",
                "image" => "20240216161230_Wx8soqwXY0.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Meaty Mix",
                "category_id" => 2,
                "description" => "<p>Pizza-kaste, mozzarella juust, chorizo vorst, pepperoni vorst, peekon, mortadella</p>",
                "image" => "20240216161313_t8azPsaJjX.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Julienne",
                "category_id" => 2,
                "description" => "<p>Mushroom sauce, cheddar cheese, mozzarella cheese, red onion, chicken, champignons, ranch kaste, grated italian hard cheese</p>",
                "image" => "20240216161357_ivm8frbnEj.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Mortadella&Mushrooms",
                "category_id" => 2,
                "description" => "<p>Ranch sauce, boletus mushrooms, mozzarella cheese, mortadella, truffle oil</p>",
                "image" => "20240216161441_xqonUbcEyR.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Fiesta",
                "category_id" => 2,
                "description" => "<p>Ranch sauce, chicken, mozzarella cheese, onion, garlic, spicy chorizo, paprika, tomatoes, chipotle sauce</p>",
                "image" => "20240216162354_MfOayy3Slw.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Dodster",
                "category_id" => 3,
                "description" => "<p>Legendary hot snack with chicken, tomatoes, mozzarella, ranch sauce in a thin wheat tortilla</p>",
                "image" => "20240216162539_BY1akhTzFu.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Chilli Dodster 🌶️",
                "category_id" => 3,
                "image" => "20240216163039_Nf46NvsSsA.webp",
                "description" => "<p>Legendary hot snack with chicken, tomatoes, mozzarella, ranch sauce in a thin wheat tortilla</p>",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Cheese starter 🌱",
                "category_id" => 3,
                "description" => "<p>A hot snack with a very cheesy filling. Mozzarella, Italian hard cheese, cheddar and ranch dressing in a thin wheat tortilla</p>",
                "image" => "20240216163133_bqkSstS77Z.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Mushrooms starter",
                "category_id" => 3,
                "description" => "<p>Hot snack with champignons, mozzarella and ranch sauce in a thin wheat tortilla</p>",
                "image" => "20240216163212_RJ53dAle3j.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Sweet pineapple cinnamon rolls",
                "category_id" => 4,
                "description" => "<p>Dough, sugar, cinnamon, pineapples, condensed milk</p>",
                "image" => "20240216163321_GLwvOGqmui.webp",
                "status" => "1",
                "created_by" => 1,
            ],
            [
                "name" => "Cinnamon-apple rolls",
                "category_id" => 4,
                "description" => "<p>These are incredibly sweet rolls filled with a mix of apple, cinnamon and condensed milk.</p>",
                "image" => "20240216163404_4xnOzJnBdu.webp",
                "status" => "1",
                "created_by" => 1,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
