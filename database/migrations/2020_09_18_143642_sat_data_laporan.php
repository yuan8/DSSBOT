<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SatDataLaporan extends Migration
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

         if(!Schema::connection('pgsql')->hasTable($schema.'laporan')){
              Schema::connection('pgsql')->create($schema.'laporan',function(Blueprint $table) use ($schema){
                    $table->string('id')->primary();
                    $table->string('site_name')->nullable();
                    $table->string('address')->nullable();
                    $table->string('pemda_id',4)->nullable();
                    $table->dateTime('entry_date')->nullable();
                    $table->integer('entry_period_year')->nullable();
                    $table->integer('entry_period_month')->nullable();
                    $table->dateTime('entry_period')->nullable();
                    $table->dateTime('insert_date')->nullable();
                    $table->timestamps();
                    $table->foreign('pemda_id')
                      ->references('pemda_id')->on($schema.'master_pdam')
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
        
        Schema::connection('pgsql')->dropIfExists($schema.'laporan');

    }
}
