<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultProductViewToConfigsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->string('home_product_view')->default('by_category');
         $table->boolean('is_pic_order')->default(false);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->dropColumn('home_product_view');
         $table->dropColumn('is_pic_order');
      });
   }
}
