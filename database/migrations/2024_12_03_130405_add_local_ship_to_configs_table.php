<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocalShipToConfigsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->boolean('is_local_shipping_active')->default(0);
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
         $table->dropColumn('is_local_shipping_active');
      });
   }
}
