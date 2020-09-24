<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SatMasterPertanyaan extends Migration
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

         if(!Schema::connection('pgsql')->hasTable($schema.'master_pertanyaan')){
              Schema::connection('pgsql')->create($schema.'master_pertanyaan',function(Blueprint $table) use ($schema){
                    $table->bigIncrements('id');
                    $table->bigInteger('parent_category')->nullable();
                    $table->text('question')->nullable();
                    $table->string('default_value')->nullable();
                    $table->text('uom')->nullable();
                    $table->text('remark')->nullable();
                    $table->float('sort')->nullable();
                    $table->integer('year')->nullable();
                    $table->text('js')->nullable();
                    $table->integer('commas')->nullable();
                    $table->float('average')->nullable();
                    $table->float('max_value')->nullable();
                    $table->boolean('readonly')->nullable()->default(0);
                    $table->timestamps();
                   
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
        
        Schema::connection('pgsql')->dropIfExists($schema.'master_pertanyaan');

    }
}
