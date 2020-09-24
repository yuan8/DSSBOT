<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterDaerah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         $schema='public.';

         if(!Schema::connection('pgsql')->hasTable($schema.'master_daerah')){
               Schema::connection('pgsql')->create($schema.'master_daerah',function(Blueprint $table) use ($schema){
                    $table->string('id')->primary();
                    $table->integer('kode_daerah_parent')->nullable();
                    $table->string('nama');
                    $table->string('logo');
                });
                    
         }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

         $schema='public.';
        
        Schema::connection('pgsql')->dropIfExists($schema.'master_daerah');
    }
}
