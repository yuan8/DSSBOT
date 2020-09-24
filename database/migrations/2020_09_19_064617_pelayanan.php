<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pelayanan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        //
        $schema='sat.';

         if(!Schema::connection('pgsql')->hasTable($schema.'laporan_pelayanan')){
              Schema::connection('pgsql')->create($schema.'laporan_pelayanan',function(Blueprint $table) use ($schema){
                    $table->bigIncrements('id');

                    $table->string('id_laporan');
                    $table->bigInteger('id_question')->unsigned();
                    
                    $table->double('data',25,3)->nullable();
                    $table->unique(['id_laporan','id_question']);

                
                    $table->foreign('id_laporan')
                      ->references('id')->on($schema.'laporan')
                      ->onDelete('cascade')->onUpdate('cascade');

                    $table->foreign('id_question')
                      ->references('id')->on($schema.'master_pertanyaan')
                      ->onDelete('cascade')->onUpdate('cascade');
                   
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
        $schema='sat.';
        
        Schema::connection('pgsql')->dropIfExists($schema.'laporan_pelayanan');

    }
}
