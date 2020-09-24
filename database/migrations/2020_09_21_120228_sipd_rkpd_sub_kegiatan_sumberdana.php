<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SipdRkpdSubKegiatanSumberdana extends Migration
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

         if(!Schema::connection('pgsql')->hasTable($schema.env('TAHUN').'_sumberdana_sub_kegiatan')){
              Schema::connection('pgsql')->create($schema.env('TAHUN').'_sumberdana_sub_kegiatan',function(Blueprint $table) use ($schema){
                    $table->bigIncrements('id');
                    $table->text('kodedata')->unique();
                    $table->bigInteger('id_bidang')->unsigned();
                    $table->bigInteger('id_program')->unsigned();
                    $table->bigInteger('id_kegiatan')->unsigned();
                    $table->bigInteger('id_sub_kegiatan')->unsigned();
                    $table->string('kodepemda',4);
                    $table->integer('tahun');
                    $table->string('kodebidang')->nullable();
                    
                    $table->string('kodeprogram')->nullable();
                    $table->string('kodekegiatan')->nullable();
                    $table->string('kodesubkegiatan')->nullable();
                    $table->string('kodesumberdana')->nullable();
                    $table->text('sumberdana')->nullable();
                    $table->double('pagu',25,3)->default(0);
                    $table->bigInteger('transactioncode')->nullable();


                      $table->foreign('id_bidang')
                      ->references('id')->on($schema.env('TAHUN').'_bidang')
                      ->onDelete('cascade')->onUpdate('cascade');

                       $table->foreign('id_program')
                      ->references('id')->on($schema.env('TAHUN').'_program')
                      ->onDelete('cascade')->onUpdate('cascade');


                       $table->foreign('id_kegiatan')
                      ->references('id')->on($schema.env('TAHUN').'_kegiatan')
                      ->onDelete('cascade')->onUpdate('cascade');

                       $table->foreign('id_sub_kegiatan')
                      ->references('id')->on($schema.env('TAHUN').'_sub_kegiatan')
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
        $schema='rkpd.';
        
        Schema::connection('pgsql')->dropIfExists($schema.env('TAHUN').'_sumberdana_sub_kegiatan');
    }
}
