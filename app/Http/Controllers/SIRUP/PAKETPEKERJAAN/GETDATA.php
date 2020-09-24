<?php

namespace App\Http\Controllers\SIRUP\PAKETPEKERJAAN;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;
use DB;

use Carbon\Carbon;
class GETDATA extends Controller
{
    //

	static $data_json=[];


	public function getData($tahun){
		set_time_limit(-1);

		$data=static::con('https://sirup.lkpp.go.id/sirup/caripaketctr/search?tahunAnggaran='.$tahun.'&jenisPengadaan=&metodePengadaan=&minPagu=&maxPagu=&bulan=&draw=3&columns%5B0%5D%5Bdata%5D&columns%5B0%5D%5Bname%5D&columns%5B0%5D%5Bsearchable%5D=false&columns%5B0%5D%5Borderable%5D=false&columns%5B0%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B0%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B1%5D%5Bdata%5D=paket&columns%5B1%5D%5Bname%5D=&columns%5B1%5D%5Bsearchable%5D=true&columns%5B1%5D%5Borderable%5D=true&columns%5B1%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B1%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B2%5D%5Bdata%5D=pagu&columns%5B2%5D%5Bname%5D=&columns%5B2%5D%5Bsearchable%5D=true&columns%5B2%5D%5Borderable%5D=true&columns%5B2%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B2%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B3%5D%5Bdata%5D=jenisPengadaan&columns%5B3%5D%5Bname%5D=&columns%5B3%5D%5Bsearchable%5D=true&columns%5B3%5D%5Borderable%5D=true&columns%5B3%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B3%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B4%5D%5Bdata%5D=metode&columns%5B4%5D%5Bname%5D=&columns%5B4%5D%5Bsearchable%5D=true&columns%5B4%5D%5Borderable%5D=true&columns%5B4%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B4%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B5%5D%5Bdata%5D=pemilihan&columns%5B5%5D%5Bname%5D=&columns%5B5%5D%5Bsearchable%5D=true&columns%5B5%5D%5Borderable%5D=true&columns%5B5%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B5%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B6%5D%5Bdata%5D=kldi&columns%5B6%5D%5Bname%5D=&columns%5B6%5D%5Bsearchable%5D=true&columns%5B6%5D%5Borderable%5D=true&columns%5B6%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B6%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B7%5D%5Bdata%5D=satuanKerja&columns%5B7%5D%5Bname%5D=&columns%5B7%5D%5Bsearchable%5D=true&columns%5B7%5D%5Borderable%5D=true&columns%5B7%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B7%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B8%5D%5Bdata%5D=lokasi&columns%5B8%5D%5Bname%5D=&columns%5B8%5D%5Bsearchable%5D=true&columns%5B8%5D%5Borderable%5D=true&columns%5B8%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B8%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B9%5D%5Bdata%5D=id&columns%5B9%5D%5Bname%5D=&columns%5B9%5D%5Bsearchable%5D=true&columns%5B9%5D%5Borderable%5D=true&columns%5B9%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B9%5D%5Bsearch%5D%5Bregex%5D=false&order%5B0%5D%5Bcolumn%5D=5&order%5B0%5D%5Bdir%5D=DESC&start=0&length=10000&search%5Bvalue%5D&search%5Bregex%5D=false&_=1600762061962','get',[]);

		$data=json_decode($data,true);


		if(is_array($data['data'])){

			if(((int)date('s')%2)!=0){
				$data['data']=array_reverse($data['data']);
			}

			foreach ($data['data'] as $key => $d) {

				
				$data['data'][$key]=[
					"id_bulan"=>$d['idBulan'],
		            "metode"=> strtoupper($d['metode']),
		            "kldi"=>$d['kldi'],
		            "lokasi"=>isset($d['lokasi'])?$d['lokasi']:null,
		            "jenis_pengadaan"=>(isset($d['jenisPengadaan'])?strtoupper($d['jenisPengadaan']):''),
		            "pemilihan"=>$d['pemilihan'],
		            "id_metode"=>$d['idMetode'],
		            "pagu"=>$d['pagu'],
		            "id"=>$d['id'],
		            "id_jenis_pengadaan"=>empty($d['idJenisPengadaan'])?null:$d['idJenisPengadaan'],
		            "satuan_kerja"=>$d['satuanKerja'],
		            "paket"=>$d['paket'],
		            "tahun"=>$tahun,
		            "created_at"=>Carbon::now(),


				];
			}

			$data_json=array_chunk($data['data'], 500);

			foreach ($data_json as $key => $value) {
				DB::table('sirup.'.$tahun.'_paket')->insertOrIgnore(
					$value
				);
			}

			
		}


		return back();


	}

    static function con($url, $method='', $vars=''){

		if(!file_exists(storage_path('app/cookies/sirup_data_cookies.txt')) ){
			Storage::put('cookies/sirup_data_cookies.txt','');
		}

    	$time=((int)microtime(true));

	 	$ch = curl_init();
	    if ($method == 'post') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }else{
	    	$params = '';
    		foreach($vars as $key=>$value){
                $params .= $key.'='.$value.'&';
         
       			$params = trim($params, '&');
       		}

       		$url.='?'.$params;

	        // curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
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
	    curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path('app/cookies/sirup_data_cookies.txt'));
	    curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path('app/cookies/sirup_data_cookies.txt'));
	    

	    $buffer = curl_exec($ch);
	    static::$data_json=json_decode($buffer,true);
	 



	    curl_close($ch);
	    return $buffer;
 	}
}
