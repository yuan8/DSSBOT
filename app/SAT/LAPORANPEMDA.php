<?php

namespace App\SAT;

use Illuminate\Database\Eloquent\Model;
use App\SAT\MASTERPERTANYAAN;

class LAPORANPEMDA extends Model
{
    //

     protected $table='sat.laporan_pemda';


    public function _question(){
    	return $this->belongsTo(MASTERPERTANYAAN::class,'id_question');
    }
}
