<?php

namespace App\Http\Controllers\SIPD\RKPD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
class STOREDATA extends Controller
{
    //

    static $id_bidang;
    static $id_program;
    static $id_kegiatan;
    static $id_sub_kegiatan;
    static $data_ll=[];




    static function non_array($data_ex){
    	
    	$data=[];
    	if(!is_array($data_ex)){
    		dd(static::$data_ll);

    	}
    	static::$data_ll=$data_ex;
    	foreach ($data_ex as $key => $value) {
    		if(!is_array($value)){
    			$data[$key]=$value;
    		}
    		# code...
    	}

    	return $data;
    }

    static function store($data,$kodepemda,$tahun,$transactioncode=null){
    	DB::table('rkpd.'.$tahun.'_status_rkpd')
    	->where([
    		'kodepemda'=>$kodepemda,
    		'tahun'=>$tahun
    	])
    	->update([
    		'attemp'=>DB::raw('(attemp+1)')
    	]);

    	foreach($data as $key => $bd) {
			static::$id_bidang=DB::table('rkpd.'.env('TAHUN').'_bidang')->insertGetId(static::non_array($bd)
			);

			foreach ($bd['program'] as $keyp => $p) {
				# code...
				$dbi=static::non_array($p);
				$dbi['id_bidang']=static::$id_bidang;
				static::$id_program=DB::table('rkpd.'.env('TAHUN').'_program')->insertGetId($dbi);
				foreach ($p['capaian'] as $keyc => $c) {
					$dbi=static::non_array($c);
					$dbi['id_bidang']=static::$id_bidang;
					$dbi['id_program']=static::$id_program;

					DB::table('rkpd.'.env('TAHUN').'_capaian')->insertGetId($dbi);
				}

				foreach ($p['kegiatan'] as $keyk => $k) {
					# code...
					$dbi=static::non_array($k);
					$dbi['id_bidang']=static::$id_bidang;
					$dbi['id_program']=static::$id_program;

					static::$id_kegiatan=DB::table('rkpd.'.env('TAHUN').'_kegiatan')->insertGetId($dbi);
					
				
					foreach ($k['indikator'] as $keyi => $i) {
						$dbi=static::non_array($i);
						$dbi['id_bidang']=static::$id_bidang;
						$dbi['id_program']=static::$id_program;
						$dbi['id_kegiatan']=static::$id_kegiatan;

						DB::table('rkpd.'.env('TAHUN').'_indikator')->insertGetId($dbi);
					}

					foreach ($k['sumberdana'] as $keyksum => $sum) {

						$dbi=static::non_array($sum);
						$dbi['id_bidang']=static::$id_bidang;
						$dbi['id_program']=static::$id_program;
						$dbi['id_kegiatan']=static::$id_kegiatan;

						DB::table('rkpd.'.env('TAHUN').'_sumberdana')->insertGetId($dbi);
						
					}

					
					foreach ($k['subkegiatan'] as $keyks => $ks) {
						# code...
						$dbi=static::non_array($ks);
						$dbi['id_bidang']=static::$id_bidang;
						$dbi['id_program']=static::$id_program;
						$dbi['id_kegiatan']=static::$id_kegiatan;

						static::$id_sub_kegiatan=DB::table('rkpd.'.env('TAHUN').'_sub_kegiatan')->insertGetId($dbi);
					
						foreach ($ks['indikator'] as $keyis => $issub) {
						# code...
							$dbi=static::non_array($issub);
							$dbi['id_bidang']=static::$id_bidang;
							$dbi['id_program']=static::$id_program;
							$dbi['id_kegiatan']=static::$id_kegiatan;
							$dbi['id_sub_kegiatan']=static::$id_sub_kegiatan;


							DB::table('rkpd.'.env('TAHUN').'_indikator_sub')->insertGetId($dbi);
						}


						foreach ($ks['sumberdana'] as $keyssum => $ssum) {
							$dbi=static::non_array($ssum);
							$dbi['id_bidang']=static::$id_bidang;
							$dbi['id_program']=static::$id_program;
							$dbi['id_kegiatan']=static::$id_kegiatan;
							$dbi['id_sub_kegiatan']=static::$id_sub_kegiatan;
							

							DB::table('rkpd.'.env('TAHUN').'_sumberdana_sub_kegiatan')->insertGetId($dbi);
						}
					}

				}
			}
		}

		$approve=false;



		$pagu=(array)DB::table('rkpd.'.$tahun.'_bidang as b')->leftJoin( 'rkpd.'.$tahun.'_kegiatan as k','b.id','=','k.id_bidang')->selectRaw('sum(k.pagu) as total_pagu,min(b.transactioncode) as transactioncode')->where([
			'b.kodepemda'=>$kodepemda,
			'b.tahun'=>$tahun,

		])->first();

		if(!$pagu){
			$pagu=[
				'transactioncode'=>$transactioncode,
				'kodepemda'=>$kodepemda,
				'tahun'=>$tahun,
				'total_pagu'=>0
			];
		}

		$pagu['total_pagu']=(int)($pagu['total_pagu']);
		$pagu['transactioncode']=isset($pagu['transactioncode'])?$pagu['transactioncode']:$transactioncode;





		if($pagu){

			$pagu_rkpd=(array)DB::table('rkpd.'.$tahun.'_status_rkpd')->where([
				'kodepemda'=>$kodepemda,
				'tahun'=>$tahun,
			])->first();

			if($pagu_rkpd){
				$pagu_rkpd['y']=[];
				$pagu_rkpd['y']=$pagu_rkpd['y']+$pagu;
				if((((int)$pagu_rkpd['pagu'])==((int)$pagu['total_pagu'])) and ($pagu_rkpd['transactioncode']==$pagu['transactioncode'])){
					$approve=true;

					$in=DB::table('rkpd.'.$tahun.'_status_data')->updateOrInsert([
						'kodepemda'=>$kodepemda,
						'tahun'=>$tahun,
					],
					[
						'kodepemda'=>$kodepemda,
						'tahun'=>$tahun,
						'transactioncode'=>$pagu_rkpd['transactioncode'],
						'last_date'=>$pagu_rkpd['last_date'],
						'updated_at'=>Carbon::now(),
						'pagu'=>(float)($pagu['total_pagu']?$pagu['total_pagu']:0),
						'matches'=>true,
						'status'=>$pagu_rkpd['status']
					]);

					$in=DB::table('rkpd.'.$tahun.'_status_data')->where(['kodepemda'=>$kodepemda,
						'tahun'=>$tahun,
					])->update(
					[
						'matches'=>true,
					]);
				}
			}


		}

		$in=DB::table('rkpd.'.$tahun.'_status_data')->insertOrIgnore(
		[
				'kodepemda'=>$kodepemda,
				'tahun'=>$tahun,
				'transactioncode'=>$pagu_rkpd['transactioncode'],
				'last_date'=>$pagu_rkpd['last_date'],
				'updated_at'=>Carbon::now(),
				'pagu'=>(float)($pagu['total_pagu']?$pagu['total_pagu']:0),
				'matches'=>false,
				'status'=>$pagu_rkpd['status']
		]);

		if(!$approve){
			$in=DB::table('rkpd.'.$tahun.'_status_data')->where([
				'kodepemda'=>$kodepemda,
				'tahun'=>$tahun,
			])->update(['matches'=>false]);
		}

		return 'ok';

    }
}
