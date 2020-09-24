<?php

namespace App\Http\Controllers\SIPD\RKPD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;
use App\Http\Controllers\SIPD\RKPD\STOREDATA;
use DB;
use Illuminate\Support\Facades\Schema;

class GETDATA extends Controller
{
    //
	static $token='d1d1ab9140c249e34ce356c91e9166a6';
	static $data_json=[];

	static $data_urusan=[
		'nama'=>'',
		'id'=>''
	];

	static $data_sub_urusan=[
		'nama'=>'',
		'id'=>''
	];

	static $tahun='';
	static $kodepemda='';
	static $uraibidang='';
	static $kodebidang='';
	static $kodeprogram='';
	static $kodeskpd='';

	static $kodekegiatan='';
	static $kodesubkegiatan='';
	static $kodecapaian='';
	static $kodeindikator='';
	static $kodesumberdana='';
	static $kodesumberdana_sub='';
	static $id_urusan=null;
	static $pagutotal=0;
	static $listingcode=[];
	static $kodeindikatorsubkegiatan='';
	static $transactioncode='102020030417';


   static function  host(){
		if( strpos($_SERVER['HTTP_HOST'], '192.168.123.190') !== false) {

			return 'http://192.168.123.195/';

		}else{

			return 'https://sipd.go.id/';
		}

	}


	 static function store_masive($tahun,Request $request){
    	$page=(int) (isset($request->page)?$request->page:0);

    	 static::$tahun=$tahun??date('Y');

        if(!Schema::connection('pgsql')->hasTable('rkpd.'.$tahun.'_status_rkpd')){
            return view('sipd.rkpd.handle')->with(['data'=>[],'page_block'=>true,'tahun'=>$tahun]);
        }

        $data=DB::table('rkpd.'.$tahun.'_status_rkpd as rk')
        ->leftJoin('rkpd.'.$tahun.'_status_data as d',[['d.kodepemda','=','rk.kodepemda'],['d.tahun','=','rk.tahun'],['d.status','=','rk.status'],['d.transactioncode','=','rk.transactioncode']])
        ->selectRaw('rk.*,d.matches as rkpd_match,d.pagu as pagu_store,
            (select nama from public.master_daerah as ld where ld.id=rk.kodepemda) as nama_pemda,
                rk.attemp as attemp
            ')
        ->where([['d.matches','=',false],['rk.attemp','<',1],['rk.status','=',5]])
        ->orWhere([['d.matches','=',null],['rk.attemp','<',1],['rk.status','=',5]])
        ->orderBy(DB::raw('(rk.attemp)'),'ASC')->limit(1)->get();


    	$d=[];
    	if(isset($data[0])){
    		$d=(array)$data[0];
    		if($request->getdata){
    			return redirect()->route('sipd.rkpd.data.update',['tahun'=>$tahun,'kodepemda'=>$d['kodepemda'],'status'=>$d['status'],'transactioncode'=>$d['transactioncode']]);
    			}
    	}else{
    		return 'done';
    	}

    	return view('sipd.rkpd.masive')->with(['data'=>$d,'tahun'=>$tahun,'page'=>$page]);
    }






	public function getData($tahun=2020,$kodepemda=11,$status=5,$transactioncode=111){
    	set_time_limit(-1);
    	static::$transactioncode=$transactioncode;

		try {
        	// Hp::checkDBProKeg($tahun);
			$file=null;

			if(strpos($kodepemda, '00')!==false){
				$kodepemda=str_replace('00', '', $kodepemda);
			}

			if(strlen($kodepemda)<4){
				$kode_daerah=$kodepemda.'00';
			}else{
				$kode_daerah=$kodepemda;
			}

			
			if($status==null){
				$status=0;
			}
			
			if(($status==null)OR($status<=2)){
				$path=static::host().'run/serv/ranwal.php?tahun='.($tahun).'&kodepemda='.$kode_daerah;
			}else{
				$path=static::host().'run/serv/get.php?tahun='.($tahun).'&kodepemda='.$kode_daerah;
			}

	    	$opts = [
			    "http" => [
			        "method" => "GET",
			          "header" => "Authorization: bearer ".static::$token

			    ]
			];

			$context = static::con($path,'get',[]);

			if(file_exists(storage_path('app/BOT/SIPD/RKPD/'.$tahun.'/JSON-SIPD/'.$kodepemda.'.'.$status.'.'.$transactioncode.'.json'))){
				static::$data_json=json_decode(file_get_contents(storage_path('app/BOT/SIPD/RKPD/'.$tahun.'/JSON-SIPD/'.$kodepemda.'.'.$status.'.'.$transactioncode.'.json')),true);
				$data=static::buildData();

				Storage::put('BOT/SIPD/RKPD/'.$tahun.'/JSON-DATA/'.$kodepemda.'.'.$status.'.'.$transactioncode.'.json',json_encode(['pagu'=>static::$pagutotal,'status'=>$status,'transactioncode'=>$transactioncode,'via'=>'api','data'=>$data],true));


			}else{

				$data=static::buildData();
				Storage::put('BOT/SIPD/RKPD/'.$tahun.'/JSON-SIPD/'.$kodepemda.'.'.$status.'.'.$transactioncode.'.json',json_encode(static::$data_json));
				Storage::put('BOT/SIPD/RKPD/'.$tahun.'/JSON-DATA/'.$kodepemda.'.'.$status.'.'.$transactioncode.'.json',json_encode(['pagu'=>static::$pagutotal,'status'=>$status,'transactioncode'=>$transactioncode,'via'=>'api','data'=>$data],true));


			}

			DB::table('rkpd.'.env('TAHUN').'_bidang')->where('kodepemda',$kodepemda)->where('tahun',$tahun)->delete();

			DB::table('rkpd.'.env('TAHUN').'_status_data')->where('kodepemda',$kodepemda)->where('tahun',$tahun)->update(['matches'=>false]);

			$store=STOREDATA::store($data,$kodepemda,$tahun,$transactioncode);


	
			return back();



		}catch(exception $e){
				dd($e);
		}

	}


	static function buildData(){
		$data_return=[];
		if((!is_array(static::$data_json))){
			static::$data_json=[];
		}

		foreach(static::$data_json as $key => $bd) {

			$data_return[]=static::bidang($bd,$key);

			if((!is_array($bd['program']))){
				$bd['program']=[];
			}
		
			foreach ($bd['program'] as $keyp => $p) {
				# code...
				$data_return[$key]['program'][]=static::program($p,$keyp);
				if((!is_array($p['capaian']))){
						$p['capaian']=[];
				}
				foreach ($p['capaian'] as $keyc => $c) {
					$data_return[$key]['program'][$keyp]['capaian'][]=static::capaian($c,$keyc);
				}

				if((!is_array($p['kegiatan']))){
						$p['kegiatan']=[];
				}

				foreach ($p['kegiatan'] as $keyk => $k) {
					# code...
					$data_return[$key]['program'][$keyp]['kegiatan'][]=static::kegiatan($k,$keyk);
					
				// entity
					if((!is_array($k['indikator']))){
							$k['indikator']=[];
					}
					foreach ($k['indikator'] as $keyi => $i) {
						$data_return[$key]['program'][$keyp]['kegiatan'][$keyk]['indikator'][]=static::indikator($i,$keyi);
					}

					if((!is_array($k['sumberdana'])) OR (!isset($k['sumberdana'][0]))){
							$k['sumberdana']=[];
					}
					$k['sumberdana']=array_values($k['sumberdana']);

					foreach ($k['sumberdana'] as $keyksum => $sum) {
						$data_return[$key]['program'][$keyp]['kegiatan'][$keyk]['sumberdana'][]=static::kegiatan_sumberdana($sum,$keyksum);
					}

					if((!is_array($k['subkegiatan']))){
							$k['subkegiatan']=[];
					}

					foreach ($k['subkegiatan'] as $keyks => $ks) {
						# code...
						$data_return[$key]['program'][$keyp]['kegiatan'][$keyk]['subkegiatan'][]=static::sub_kegiatan($ks,$keyks);

						 // entity
						if((!is_array($ks['indikator']))){
								$ks['indikator'] =[];
						}
						foreach ($ks['indikator'] as $keyis => $is) {
						# code...
							$data_return[$key]['program'][$keyp]['kegiatan'][$keyk]['subkegiatan'][$keyks]['indikator'][]=static::indikator_sub_kegiatan($is,$keyis);
						}

						if((!is_array($ks['sumberdana']))OR(!isset($ks['sumberdana'][0])) ){
								$ks['sumberdana'] =[];
						}

						$ks['sumberdana']=array_values($ks['sumberdana']);

						foreach ($ks['sumberdana'] as $keyssum => $ssum) {
						# code...
							$data_return[$key]['program'][$keyp]['kegiatan'][$keyk]['subkegiatan'][$keyks]['sumberdana'][]=static::sub_kegiatan_sumberdana($ssum,$keyssum);
						}
					}

				}
			}
		}

		return ($data_return);

	}


	static function bidang($data,$key){

		static::$kodepemda=str_replace('00','' , $data['kodepemda']);
		static::$kodebidang=($data['kodebidang']);
		static::$tahun=($data['tahun']);
		static::$uraibidang=$data['uraibidang'];
		static::$kodeskpd=$data['kodeskpd'];


		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd;

		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}

		static::$listingcode[]=$kodedata;

		$data_return=[
			'kodedata'=>$kodedata,
			'kodepemda'=>static::$kodepemda,
			'tahun'=>(static::$tahun),
			'kodebidang'=>static::$kodebidang,
			'uraibidang'=>static::$uraibidang,
			'kodeskpd'=>static::$kodeskpd,
			'uraiskpd'=>$data['uraiskpd'],
			'transactioncode'=>static::$transactioncode,
			'id_urusan'=>static::$id_urusan,
			'program'=>[],
		];


		return $data_return;

	}


	static function program($data,$key){
		static::$kodeprogram=$data['kodeprogram'];

		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram;
		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}
		static::$listingcode[]=$kodedata;



		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>static::$kodepemda,
			'kodebidang'=>static::$kodebidang,
			'uraibidang'=>static::$uraibidang,
			'id_urusan'=>static::$id_urusan,
			'kodeprogram'=>static::$kodeprogram,
			'uraiprogram'=>$data['uraiprogram'],
			'id_urusan'=>0,
			'id_sub_urusan'=>0,
			'transactioncode'=>static::$transactioncode,
			'capaian'=>[],
			'kegiatan'=>[],

		];


		return $data_return;

	}

	static function kegiatan($data,$key){
		static::$kodekegiatan=$data['kodekegiatan'];

		if(is_numeric($data['pagu'])){
			static::$pagutotal+=(float)$data['pagu'];
		}

		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram.static::$kodekegiatan;

		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}

		static::$listingcode[]=$kodedata;


		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>static::$kodepemda,
			'kodebidang'=>static::$kodebidang,
			'id_urusan'=>static::$id_urusan,
			'id_sub_urusan'=>null,
			'kodeprogram'=>static::$kodeprogram,
			'kodekegiatan'=>static::$kodekegiatan,
			'uraikegiatan'=>$data['uraikegiatan'],
			'pagu'=>(float)$data['pagu'],
			'transactioncode'=>static::$transactioncode,
			'id_urusan'=>0,
			'sumberdana'=>[],
			'indikator'=>[],
			'subkegiatan'=>[],


		];


		return $data_return;

	}


	static function kegiatan_sumberdana($data,$key){
		

		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram.static::$kodekegiatan.'.S.'.($key+1);
		
		static::$kodesumberdana=$kodedata;

		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}

		static::$listingcode[]=$kodedata;


		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>static::$kodepemda,
			'kodebidang'=>static::$kodebidang,
			'kodeprogram'=>static::$kodeprogram,
			'kodekegiatan'=>static::$kodekegiatan,
			'kodesumberdana'=>(isset($data['kodesumberdana'])?$data['kodesumberdana']:null),
			'sumberdana'=>(isset($data['sumberdana'])?$data['sumberdana']:null),
			'pagu'=>(float)(isset($data['pagu'])?$data['pagu']:NULL),
			'transactioncode'=>static::$transactioncode,
		];


		return $data_return;

	}

	static function sub_kegiatan_sumberdana($data,$key){


		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram.static::$kodekegiatan.static::$kodesubkegiatan.'.S.'.($key+1);
		
		static::$kodesumberdana_sub=$kodedata;

		if(in_array($kodedata,static::$listingcode)){

			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}

		static::$listingcode[]=$kodedata;


		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>static::$kodepemda,
			'kodebidang'=>static::$kodebidang,
			'kodeprogram'=>static::$kodeprogram,
			'kodekegiatan'=>static::$kodekegiatan,
			'kodesubkegiatan'=>static::$kodesubkegiatan,
			'kodesumberdana'=>(isset($data['kodesumberdana'])?$data['kodesumberdana']:null),
			'sumberdana'=>(isset($data['sumberdana'])?$data['sumberdana']:null),
			'pagu'=>(float)(isset($data['pagu'])?$data['pagu']:NULL),
			'transactioncode'=>static::$transactioncode,
		];


		return $data_return;

	}

	static function sub_kegiatan($data,$key){
		static::$kodesubkegiatan=$data['kodesubkegiatan'];

		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram.static::$kodekegiatan.static::$kodesubkegiatan;
		

		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}
		
		static::$listingcode[]=$kodedata;

		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>static::$kodepemda,
			'id_urusan'=>static::$id_urusan,
			'id_sub_urusan'=>null,
			'kodebidang'=>static::$kodebidang,
			'kodeprogram'=>static::$kodeprogram,
			'kodekegiatan'=>static::$kodekegiatan,
			'kodesubkegiatan'=>static::$kodesubkegiatan,
			'uraisubkegiatan'=>$data['uraisubkegiatan'],
			'pagu'=>(float)$data['pagu'],
			'transactioncode'=>static::$transactioncode,
			'indikator'=>[],
			'sumberdana'=>[],


		];


		return $data_return;

	}

	static function capaian($data,$key){
		

		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram.'.'.($key+1);
		
		static::$kodecapaian=$kodedata;

		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}
		
		static::$listingcode[]=$kodedata;

		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>(static::$kodepemda),
			'kodeindikator'=>$data['kodeindikator'],
			'kodeprogram'=>static::$kodeprogram,
			'tolokukur'=>$data['tolokukur'],
			'target'=>$data['target'],
			'satuan'=>$data['satuan'],
			'pagu'=>(float)$data['pagu'],
			'transactioncode'=>static::$transactioncode,

		];

		return $data_return;


	}


	static function indikator($data,$key){

		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram.static::$kodekegiatan.static::$kodesubkegiatan.'.'.($key+1);
		
		static::$kodeindikator=$kodedata;

		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}
		
		static::$listingcode[]=$kodedata;

		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>(static::$kodepemda),
			'kodeindikator'=>$data['kodeindikator'],
			'tolokukur'=>$data['tolokukur'],
			'kodeprogram'=>static::$kodeprogram,
			'kodekegiatan'=>static::$kodekegiatan,
			'target'=>$data['target'],
			'satuan'=>$data['satuan'],
			'pagu'=>(float)$data['pagu'],
			'transactioncode'=>static::$transactioncode,


		];

		return $data_return;


	}

	static function indikator_sub_kegiatan($data,$key){

		$kodedata=static::$tahun.'.'.static::$kodepemda.'.'.static::$kodebidang.'.'.static::$kodeskpd.static::$kodeprogram.static::$kodekegiatan.static::$kodesubkegiatan.'.'.($key+1);
		
		static::$kodeindikatorsubkegiatan=$kodedata;

		if(in_array($kodedata,static::$listingcode)){
			$ch_key=($key+1).'';
			do {
				$ch_key.='X';
				# code...
			} while (in_array($ch, static::$listingcode));

			$kodedata=$kodedata.'.['.($ch_key).']';
		}
		
		static::$listingcode[]=$kodedata;

		$data_return=[
			'kodedata'=>$kodedata,
			'tahun'=>(static::$tahun),
			'kodepemda'=>(static::$kodepemda),
			'kodeindikator'=>$data['kodeindikator'],
			'kodeprogram'=>static::$kodeprogram,
			'kodekegiatan'=>static::$kodekegiatan,
			'kodesubkegiatan'=>static::$kodesubkegiatan,
			'tolokukur'=>$data['tolokukur'],
			'target'=>$data['target'],
			'satuan'=>$data['satuan'],
			'pagu'=>(float)$data['pagu'],
			'transactioncode'=>static::$transactioncode,

		];
		return $data_return;


	}

    static function con($url, $method='', $vars=''){

		if(!file_exists(storage_path('app/cookies/sipd_data_cookies.txt')) ){
			Storage::put('cookies/sipd_data_cookies.txt','');
		}

    	$time=((int)microtime(true));

	 	$ch = curl_init();
	    if ($method == 'post') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }else{
	        // curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }

	    curl_setopt($ch, CURLOPT_URL, $url);
	    $agent  = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36";


		$headers[] = "Accept: */*";
		$headers[] = "Connection: Keep-Alive";
		$headers[] = "Authorization: bearer ".static::$token;


		// basic curl options for all requests
		curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);         
		curl_setopt($ch, CURLOPT_USERAGENT, $agent); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path('app/cookies/sipd_data_cookies.txt'));
	    curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path('app/cookies/sipd_data_cookies.txt'));
	    

	    $buffer = curl_exec($ch);
	    static::$data_json=json_decode($buffer,true);
	    // dd($buffer);
	    $prefix = preg_quote('run/');
        $suffix = preg_quote('/');

        $matches=[];
	    preg_match_all("!$prefix(.*?)$suffix!", (string)$buffer, $matches);

	    
	    if((count($matches)>0)and(isset($matches[0]))){
	    	foreach ($matches[1] as $uk=>$u) {
                $temp=(trim(str_replace('/','', str_replace('"','', $u))));
                if($temp!=''){
                    $data_to_bobol=array('url'=>$matches[0][$uk],'time'=>$time);
                    Storage::put(('cookies/sipd_data_micro.json'),json_encode($data_to_bobol));
                }
                # code...
            }
	    }


	    curl_close($ch);
	    return $buffer;
 	}



}
