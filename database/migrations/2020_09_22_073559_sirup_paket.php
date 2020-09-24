<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SirupPaket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

          $schema='sirup.';

         if(!Schema::connection('pgsql')->hasTable($schema.env('TAHUN').'_paket')){
              Schema::connection('pgsql')->create($schema.env('TAHUN').'_paket',function(Blueprint $table) use ($schema){
                    $table->bigIncrements('id');
                    $table->integer('tahun')->nullable();
                    $table->string('kodepemda',4)->nullable();
                    $table->integer('id_bulan')->nullable();
                    $table->string('metode')->nullable();
                    $table->string('kldi')->nullable();
                    $table->text('lokasi')->nullable();
                    $table->string('jenis_pengadaan')->nullable();
                    $table->string('pemilihan')->nullable();
                    $table->integer('id_metode')->nullable();
                    $table->double('pagu',25,3)->default(0);
                    $table->integer('id_jenis_pengadaan')->nullable();
                    $table->string('satuan_kerja')->nullable();
                    $table->text('paket')->nullable();
                    $table->dateTime('created_at')->nullable();

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
         $schema='sirup.';
        
        Schema::connection('pgsql')->dropIfExists($schema.env('TAHUN').'_paket');
    }
}
