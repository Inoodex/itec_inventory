<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Purchase;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name','brand_id', 'category_id' , 'sub_category_id', 'description','image', 'quantity', 'price', 'offer_price','offer_from','offer_to','book_type','subject_id','writer_id','publisher_id','is_book_or_product','is_package', 'package_item_ids',  'is_size', 'is_size_wise_price', 'status', 'created_by'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}


