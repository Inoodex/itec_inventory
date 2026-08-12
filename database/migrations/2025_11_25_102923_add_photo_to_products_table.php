<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // $table->string('photo')->nullable()->after('model');
            // multiple photos
            $table->json('photos')->nullable()->after('model');
        });
    }

      public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('photos');
        });
    }
};