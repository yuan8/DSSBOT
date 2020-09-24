<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SipdRkpdBidang extends Migration
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

         if(!Schema::connection('pgsql')->hasTable($schema.env('TAHUN').'_bidang')){
              Schema::connection('pgsql')->create($schema.env('TAHUN').'_bidang',function(Blueprint $table) use ($schema){
                    $table->bigIncrements('id');
                    $table->text('kodedata')->unique();
                    $table->string('kodepemda',4);
                    $table->integer('tahun');
                    $table->string('kodebidang')->nullable();
                    $table->string('uraibidang')->nullable();
                    $table->bigInteger('id_urusan')->nullable();
                    $table->string('kodeskpd')->nullable();
                    $table->string('uraiskpd')->nullable();
                    $table->bigInteger('transactioncode')->nullable();
              });
          }

        $schema='rkpd.';

        // if(!Schema::connection('pgsql')->hasTable($schema.env('TAHUN').'_status_data')){
        //       Schema::connection('pgsql')->create($schema.env('TAHUN').'_status_data',function(Blueprint $table) use ($schema){
        //             $table->bigIncrements('id');

        //             $table->string('kodepemda',4)->unique();
        //             $table->integer('tahun');
        //             $table->integer('status');
        //             $table->double('pagu',25,3)->default(0);
        //             $table->dateTime('last_date')->nullable();
        //             $table->bigInteger('transactioncode')->nullable();
        //             $table->boolean('matches')->nullable();
        //             $table->timestamps();
                   
                   
        //       });
        // }
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
        
        Schema::connection('pgsql')->dropIfExists($schema.env('TAHUN').'_bidang');
    }
}
