<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('configs', function (Blueprint $table) {
         $table->id();
         $table->string('theme')->nullable()->default('default');
         $table->string('theme_color')->nullable()->default('#1bb90d');
         $table->string('accent_color', 20)->default('#1bb90d');
         $table->string('secondary_color', 20)->default('#ec0d0d');
         $table->string('primary_color', 20)->default('#1bb90d');
         $table->string('home_view_mode')->nullable()->default('grid');
         $table->integer('home_product_limit')->default(10);
         $table->string('home_product_sort')->default('DESC');
         $table->string('product_view_mode')->nullable()->default('grid');
         $table->integer('catalog_product_limit')->default(10);
         $table->string('catalog_product_sort')->default('DESC');
         $table->boolean('is_cod_payment')->default(false);
         $table->boolean('is_shipping_active')->default(false);
         $table->boolean('review_auto_approved')->default(false);
         $table->text('cod_list')->nullable();
         $table->boolean('is_notifypro')->default(false);
         $table->tinyInteger('notifypro_interval')->default(20);
         $table->tinyInteger('notifypro_timeout')->default(4);
         $table->string('rajaongkir_type')->nullable()->default('starter');
         $table->string('rajaongkir_apikey')->nullable();
         $table->text('rajaongkir_couriers')->nullable();
         $table->integer('warehouse_id')->nullable();
         $table->text('warehouse_address')->nullable();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('configs');
   }
}
