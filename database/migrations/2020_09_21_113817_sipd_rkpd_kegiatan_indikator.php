<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SipdRkpdKegiatanIndikator extends Migration
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

         if(!Schema::connection('pgsql')->hasTable($schema.env('TAHUN').'_indikator')){
              Schema::connection('pgsql')->create($schema.env('TAHUN').'_indikator',function(Blueprint $table) use ($schema){
                    $table->bigIncrements('id');
                    $table->text('kodedata')->unique();
                    $table->bigInteger('id_bidang')->unsigned();
                    $table->bigInteger('id_program')->unsigned();
                    $table->bigInteger('id_kegiatan')->unsigned();
                    $table->string('kodepemda',4);
                    $table->integer('tahun');
                    $table->string('kodeprogram')->nullable();
                    $table->string('kodekegiatan')->nullable();
                    $table->string('kodeindikator')->nullable();
                    $table->longText('tolokukur')->nullable();
                    $table->longText('target')->nullable();
                    $table->longText('satuan')->nullable();
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
        
        Schema::connection('pgsql')->dropIfExists($schema.env('TAHUN').'_indikator');
    }
}
