<?php

namespace App\SAT;

use Illuminate\Database\Eloquent\Model;
use App\SAT\LAPORANKATEGORI;
use App\SAT\LAPORANPELAYANAN;
use App\SAT\LAPORANKEUANGAN;
use App\SAT\LAPORANOPERASIONAL;
use App\SAT\LAPORANPEMDA;
use App\SAT\MASTERPDAM;



class LAPORAN extends Model
{
    //

    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

    protected $table='sat.laporan';

    public function _ketegori(){
    	return $this->hasMany(LAPORANKATEGORI::class,'id_laporan');
    }

    public function _pdam(){
    	return $this->belongsTo(MASTERPDAM::class,'pemda_id','pemda_id');
    }

    public function _pelayanan(){
    	return $this->hasMany(LAPORANPELAYANAN::class,'id_laporan');

    }

    public function _operasional(){
    	return $this->hasMany(LAPORANOPERASIONAL::class,'id_laporan');

    }

    public function _keuangan(){
    	return $this->hasMany(LAPORANKEUANGAN::class,'id_laporan');

    }

     public function _pemerintah_daerah(){
    	return $this->hasMany(LAPORANPEMDA::class,'id_laporan');

    }
}
