<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SipdRkpdProgram extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        //

        $schema='rkpd.';

         if(!Schema::connection('pgsql')->hasTable($schema.env('TAHUN').'_program')){
              Schema::connection('pgsql')->create($schema.env('TAHUN').'_program',function(Blueprint $table) use ($schema){
                    $table->bigIncrements('id');
                    $table->text('kodedata')->unique();
                    $table->bigInteger('id_bidang')->unsigned();
                    $table->string('kodepemda',4);
                    $table->integer('tahun');
                    $table->string('kodebidang')->nullable();
                    $table->string('uraibidang')->nullable();
                    $table->bigInteger('id_urusan')->nullable();
                    $table->bigInteger('id_sub_urusan')->nullable();
                    $table->string('kodeprogram')->nullable();
                    $table->text('uraiprogram')->nullable();
                    $table->bigInteger('transactioncode')->nullable();
                    $table->foreign('id_bidang')
                  ->references('id')->on($schema.env('TAHUN').'_bidang')
                  ->onDelete('cascade')->onUpdate('cascade');
                      });
          }

        $schema='rkpd.';

       

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $schema='rkpd.';
        
        Schema::connection('pgsql')->dropIfExists($schema.env('TAHUN').'_program');
    }

}
