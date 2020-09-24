<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SatMasterPdam extends Migration
{
    
    public function up()
    {
        //
        $schema='sat.';

         if(!Schema::connection('pgsql')->hasTable($schema.'master_pdam')){
              Schema::create($schema.'master_pdam', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('name')->unique();
                    $table->string('address')->nullable();
                    $table->string('regencies_id',4)->nullable();
                    $table->string('provincies_id',2)->nullable();
                    $table->string('districts_id',11)->nullable();
                    $table->string('pemda_id',4)->unique();
                    $table->timestamps();
                });
          }
    }

 
    public function down()
    {
        $schema='sat.';
        
        Schema::connection('pgsql')->dropIfExists($schema.'master_pdam');

    }
}
