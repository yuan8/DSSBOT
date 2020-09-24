<?php

namespace App\Http\Controllers\SIPD\RKPD;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Schema;


class LISTDATA extends Controller
{
    static $tahun=2020;

    public function needHandle($tahun=nul){
        static::$tahun=$tahun??date('Y');
        $tahun=static::$tahun;

        if(!Schema::connection('pgsql')->hasTable('rkpd.'.$tahun.'_status_rkpd')){
            return view('sipd.rkpd.handle')->with(['data'=>[],'page_block'=>true,'tahun'=>$tahun]);
        }

         $data_count=DB::table('rkpd.'.$tahun.'_status_rkpd as rk')
        ->leftJoin('rkpd.'.$tahun.'_status_data as d',[['d.kodepemda','=','rk.kodepemda'],['d.tahun','=','rk.tahun'],['d.status','=','rk.status'],['d.transactioncode','=','rk.transactioncode']])
        ->selectRaw('sum(case when rk.status > 0 then 1 else 0 end) as rk_count, count(d.id) as d_count,
            sum(case when d.matches then 1 else 0 end) match_count
            ')->first();



        $data=DB::table('rkpd.'.$tahun.'_status_rkpd as rk')
        ->leftJoin('rkpd.'.$tahun.'_status_data as d',[['d.kodepemda','=','rk.kodepemda'],['d.tahun','=','rk.tahun'],['d.status','=','rk.status'],['d.transactioncode','=','rk.transactioncode']])
        ->selectRaw('rk.*,d.matches as rkpd_match,d.pagu as pagu_store,
            (select nama from public.master_daerah as ld where ld.id=rk.kodepemda) as nama_pemda,
                rk.attemp as attemp,
                d.id as stored
            ')
        ->where('d.matches',false)
        ->orWhere('d.matches',null)
        ->orderBy(DB::raw('(rk.attemp)'),'ASC')->limit(7)->get();


        $last_list_date=DB::table('rkpd.'.$tahun.'_status_rkpd as rk')->orderBy('updated_at','DESC')->pluck('updated_at')->first();

        return view('sipd.rkpd.handle')
        ->with(['data'=>$data,'tahun'=>$tahun,'last_list_date'=>$last_list_date,'data_count'=>$data_count]);
    }

    public function getJson($tahun=null,$transactioncode){
        static::$tahun=$tahun??date('Y');
       
        if(file_exists(storage_path('app/BOT/SIPD/RKPD/'.static::$tahun.'/JSON-DATA/'.$transactioncode.'.json'))){
            return file_get_contents(storage_path('app/BOT/SIPD/RKPD/'.static::$tahun.'/JSON-DATA/'.$transactioncode.'.json'));
        }else{
            return abort('404');
        }
    }
	public function index($tahun){

		if(!Schema::connection('pgsql')->hasTable('rkpd.'.$tahun.'_status_rkpd')){
			return view('sipd.rkpd.index')->with(['data'=>[],'page_block'=>true,'tahun'=>$tahun]);
		}

        $last_list_date=DB::table('rkpd.'.$tahun.'_status_rkpd as rk')->orderBy('updated_at','DESC')->pluck('updated_at')->first();


		$data=DB::table('rkpd.'.$tahun.'_status_rkpd as rk')
		->leftJoin('rkpd.'.$tahun.'_status_data as d',[['d.kodepemda','=','rk.kodepemda'],['d.tahun','=','rk.tahun'],['d.status','=','rk.status'],['d.transactioncode','=','rk.transactioncode']])
		->selectRaw('rk.*,d.matches as rkpd_match,d.pagu as pagu_store,
			(select nama from public.master_daerah as ld where ld.id=rk.kodepemda) as nama_pemda,
                 rk.attemp as attemp,
                d.id as stored
			')

		->orderBy(DB::raw('(rk.kodepemda)'),'ASC')->paginate(7);

		return view('sipd.rkpd.index')->with(['data'=>$data,'tahun'=>$tahun,'last_list_date'=>$last_list_date]);
	}

    public static  function getData($tahun=2020){

    	$time=((int)microtime(true));
    	$schema='prokeg';
     	// Hp::checkDBProKeg($tahun);

    	$login_url='https://sipd.go.id/run/'.md5($time).'/?m=dashboard';
    	$uid='subpkp@bangda.kemendagri.go.id';
    	$pass='bangdapkp';


    	$connection = static::con($login_url, 'post', array(
    		'userX'=>$uid,
    		'pass'=>md5(md5($pass)),
    		'app'=>'rkpd',
    		'submit'=>1,
    		'user'=>md5(md5(strtolower(trim($uid)))),
    		'tahun'=>$tahun
    	));


    	if($connection){
    		$con=file_get_contents(storage_path('app/cookies/sipd_micro.json'));
     		$con=json_decode($con,true);
    		$time=((int)microtime(true));


    		$list_get='https://sipd.go.id/'.$con['url'].'?m=pusat_rkpd_dashboard&f=ajax_list_pemda&tipe=murni&_='.$time;
    		$data=static::con($list_get,'GET','');
    		
    		 $data=json_decode($data,true);
    		 // return $data;

    		 $data_return=[];

    		 foreach ($data['data'] as $key => $d) {
    		 	$status=0;

    		 	switch (1) {
    		 		case (int)$d['final']:
    		 			$status=5;
    		 			break;
    		 		case (int)$d['rankhir']:
    		 			$status=4;
    		 			break;
    		 		case (int)$d['ranrkpd']:
    		 			$status=3;
    		 			break;
    		 		case (int)$d['ranwal']:
    		 			$status=2;
    		 			break;
    		 		default:
    		 			# code...
    		 			$status=0;
    		 			break;
    		 	}

    		 	$tanggal=explode(',', $d['lastpost']);

    		 	if(is_array($tanggal)){
    		 		if(isset($tanggal[1])){
    		 			$tanggal=strtoupper(trim($tanggal[1]));
	    		 		$tanggal=static::bulanToMonth($tanggal);
	    		 		$tanggal=Carbon::parse($tanggal);
    		 		}else{
    		 			$tanggal=Carbon::parse('2020-01-01');

    		 		}
    		 		
    		 	}else{
    		 		$tanggal=Carbon::parse('2020-01-01');
    		 	}

                $pagu=$d['pagu'];
                if($pagu){
                     $pagu=str_replace('.', '', $pagu);
                        $pagu=str_replace(',', '.', $pagu);
                }


    		 	$kodar=str_replace('00', '', $d['kodepemda']);
                $reco=array('kodepemda'=>$kodar,'tahun'=>$tahun,'status'=>$status,'updated_at'=>Carbon::now(),'last_date'=>$tanggal,'pagu'=>(float)$pagu,'matches'=>0,'transactioncode'=>'1'.$status.Carbon::parse($tanggal)->format('Ymdh'),'attemp'=>0);

    		 	$data_return[]=$reco;

                DB::table('rkpd.'.$tahun.'_status_rkpd')->updateOrInsert(
                	[
                	'kodepemda'=>$kodar,
                	'tahun'=>$tahun
                	]
                	,$reco);
    		 }

    		 if(count($data_return)>0){
    		 	Storage::put('BOT/SIPD/RKPD/'.$tahun.'/list-data.json',json_encode($data_return));

    		 	Storage::put('BOT/SIPD/RKPD/'.$tahun.'/PEMETAAN-DATA-TAMBAHAN/index.text','DIGUNAKAN UNTUK RECORD DATA TAMBAHAN');

    		 	Storage::put('BOT/SIPD/RKPD/'.$tahun.'/JSON-SIPD/index.text','BERISI DATA MASTER DARI SIPD');
    		 	Storage::put('BOT/SIPD/RKPD/'.$tahun.'/JSON-DATA/index.text','BERISI DATA MASTER DARI SIPD YANG TELAH DILAKUKAN PERUBAHAN DATA (+ DATA TAMBAHAN) DENGAN FORMAT YANG TELAH DIPERBAIKI');

                
    		 }


    	}

    	return back();
        return 'success';
    }


  
    static function bulanToMonth($data){
    	$bul=[
    		'JANUARI'=>'JANUARY',
    		'FEBRUARI'=>'FEBRUARY',
    		'MARET'=>'MARCH',
    		'APRIL'=>'APRIL',
    		'MEI'=>'MEY',
    		'JUNI'=>'JUNE',
    		'JULI'=>'JULY',
    		'AGUSTUS'=>'AUGUST',
    		'OKTOBER'=>'OCTOBER',
    		'NOPEMBER'=>'NOVEMBER',
    		'DESEMBER'=>'DECEMBER'
    	];
    	foreach ($bul as $key => $value) {
    		$data=str_replace($key, $value, $data);
    	}

    	return $data;
    }

    	static function con($url, $method='', $vars=''){

		if(!file_exists(storage_path('app/cookies/sipd_cookies.txt')) ){
			Storage::put('cookies/sipd_cookies.txt','');
		}

    	$time=((int)microtime(true));

	 	$ch = curl_init();
	    if ($method == 'post') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }else{
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }

	    curl_setopt($ch, CURLOPT_URL, $url);
	    $agent  = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36";


		$headers[] = "Accept: */*";
		$headers[] = "Connection: Keep-Alive";

		// basic curl options for all requests
		curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);         
		curl_setopt($ch, CURLOPT_USERAGENT, $agent); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path('app/cookies/sipd_cookies.txt'));
	    curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path('app/cookies/sipd_cookies.txt'));
	    

	    $buffer = curl_exec($ch);
	    $prefix = preg_quote('run/');
        $suffix = preg_quote('/');

        $matches=[];
	    preg_match_all("!$prefix(.*?)$suffix!", (string)$buffer, $matches);

	    
	    if((count($matches)>0)and(isset($matches[0]))){
	    	foreach ($matches[1] as $uk=>$u) {
                $temp=(trim(str_replace('/','', str_replace('"','', $u))));
                if($temp!=''){
                    $data_to_bobol=array('url'=>$matches[0][$uk],'time'=>$time);
                    Storage::put(('cookies/sipd_micro.json'),json_encode($data_to_bobol));
                }
                # code...
            }
	    }


	   

	    curl_close($ch);
	    return $buffer;
 	}

}
