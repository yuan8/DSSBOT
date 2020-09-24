<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterUrusan extends Migration
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

         if(!Schema::connection('pgsql')->hasTable('public.master_urusan')){
                Schema::create('public.master_urusan', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('nama');
                    $table->string('nomenklatur')->nullable();
                    $table->string('singkat')->nullable();
                    $table->string('kode')->nullable();

                    $table->timestamps();
                });
        }

        if(!Schema::connection('pgsql')->hasTable('public.master_sub_urusan')){
             Schema::create('public.master_sub_urusan', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('id_urusan')->unsigned();
                $table->string('nama');
                $table->string('nomeklatur')->nullable();
                
                $table->timestamps();

                $table->foreign('id_urusan')
                ->references('id')->on('public.master_urusan')
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
        $schema='public.';
        
        Schema::connection('pgsql')->dropIfExists($schema.'master_sub_urusan');
        Schema::connection('pgsql')->dropIfExists($schema.'master_urusan');

    }
}
