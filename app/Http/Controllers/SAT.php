<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use DB;
use App\SAT\LAPORAN;
use Carbon\Carbon;
class SAT extends Controller
{
    //

    public function list_data($tahun,$pemda=null,Request $requests){
        $data=DB::table('sat.laporan as l1')->groupBy('l1.pemda_id')
        ->selectRaw("
            max(pdam.pemda_id) as pemda_id,
            max(pdam.name) as name,
            max(pdam.address) as address,
            max(l1.id) as last_id_laporan,
            max(l1.entry_period_year) as last_entry_period_year,
            max(l1.entry_period_month) as last_entry_period_month,
            max(l1.insert_date) as updated_at,
            (select count(*) from sat.laporan as l where l.pemda_id = l1.pemda_id) as jumlah_laporan_diterima
        ")->orderBy(DB::raw('max(l1.entry_period)'),'DESC')
        ->leftJoin('sat.master_pdam as pdam','pdam.pemda_id','=','l1.pemda_id')
         ->where('entry_period_year','<=',$tahun)
        ->where('entry_period_year','>=',$tahun-5);
        ;

        if($pemda){
            $data=$data->where('l1.pemda_id',$pemda);
        }     

        $data=$data->get(); 

          if($data){
            return array('code'=>200,'count'=>count($data),'data'=>$data);
        }else{
            return array('code'=>500,'data'=>[]);

        }

        return $data;

    }

    public function series_laporan($tahun,$pemda,Request $requests){

        $data=LAPORAN::with([
        '_pdam'
        ,'_ketegori'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_ketegori._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_pelayanan'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_pelayanan._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_operasional'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_operasional._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_keuangan'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_keuangan._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_pemerintah_daerah'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_pemerintah_daerah._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        }])
       
        ->selectRaw("
            (id) as id,
            pemda_id as pemda_id,
            (entry_period_year) as entry_period_year,
            (entry_period_month) as entry_period_month,
            (entry_date) as entry_date,
            (insert_date) as insert_date

        ")
        ->orderBy(DB::raw('(entry_period)'),'DESC')
        ->where('pemda_id',$pemda)
        ->where('entry_period_year','<=',$tahun)
        ->where('entry_period_year','>=',$tahun-5);

        if($requests->id_laporan){
            $data=$data->where('id',$requests->id_laporan);
        }

        $data=$data->get()->toArray();

        
         if($data){
            return array('code'=>200,'count'=>count($data),'data'=>static::mapjson($data));


        }else{
            return array('code'=>500,'data'=>[]);

        }

        return $data;

    }

    public function sat($tahun,$pemda=null,Request $requests){

        $data=LAPORAN::with([
        '_pdam'
        ,'_ketegori'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_ketegori._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_pelayanan'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_pelayanan._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_operasional'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_operasional._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_keuangan'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_keuangan._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        },'_pemerintah_daerah'=>function($q){
            return $q->selectRaw("id,id_laporan,id_question,data");
        },'_pemerintah_daerah._question'=>function($q){
            return $q->selectRaw("question,id,uom");
        }])
        ->groupBy('pemda_id')
        ->selectRaw("
            max(id) as id,
            pemda_id as pemda_id,
            max(entry_period_year) as entry_period_year,
            max(entry_period_month) as entry_period_month,
            max(entry_date) as entry_date,
            max(insert_date) as insert_date
        ")
        ->orderBy(DB::raw('max(entry_period)'),'DESC')
        ->where(DB::raw('(entry_period_year)'),'<=',$tahun)
        ->where(DB::raw('(entry_period_year)'),'>=',$tahun-5)
        ;

        if($pemda){
            $data=$data->where('pemda_id',$pemda);
        }

        $data=$data->get()->toArray();

        if($data){
            return array('code'=>200,'count'=>count($data),'data'=>static::mapjson($data));
        }else{
            return array('code'=>500,'data'=>[]);

        }

        return $data;
    }

    public static function con($method='post',$url,$vars=[]){
    	if(!file_exists(storage_path('app/cookies/sipd_cookies.txt')) ){
			Storage::put('cookies/sipd_cookies.txt','');
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

		// basic curl options for all requests
		curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);         
		curl_setopt($ch, CURLOPT_USERAGENT, $agent); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    // curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path('app/cookies/sipd_cookies.txt'));
	    // curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path('app/cookies/sipd_cookies.txt'));

	    $buffer = curl_exec($ch);
	    return $buffer;
	    
    }

    public static function mapjson($dp){
        $data=$dp;


        foreach($dp as $k=>$m){

            foreach ($m as $key => $d) {
                if(is_array($d)){
                    if(in_array($key, ['_operasional','_keuangan','_ketegori','_pemerintah_daerah','_pelayanan'])){

                        foreach ($d as $keyd => $value) {
                            # code...
                            $data[$k][$key][$keyd]=array(
                                'question'=>$value['_question']['question'],
                                'data'=>$value['data'],
                                'uom'=>$value['_question']['uom']
                            );

                        }

                        $data[$k]['_laporan'][$key]=$data[$k][$key];
                        unset($data[$k][$key]);



                    }else if(in_array($key, ['_pdam'])){


                        $data[$k]=[
                            'id_laporan'=>$data[$k]['id'],
                            'name_pemda'=>$d['name'],
                            'name_pdam'=>$d['address']
                        ]+$data[$k];
                                  
                        unset($data[$k]['id']);
                        unset($data[$k]['_pdam']);


                        
                    }
                }
            }
        }

        return $data;
    }

    public static function laporan(){


    	$data=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/DataMaster?token=d25bb055-28ef-46f4-b9b1-97fa8fa19f0f&table=m_pdam',[]);

    	if(!is_array($data)){
    		$data=json_decode($data);
    	}

    	foreach($data as $d){
    		$d=(array)$d;
    		DB::table('sat.master_pdam')->updateOrInsert(

    			[
    				'id'=>$d['id']
	    		],
	    		[
	    			'id'=>$d['id'],
    				'name'=>$d['name'],
    				'address'=>$d['address'],
    				'regencies_id'=>$d['regencies_id'],
    				'provincies_id'=>$d['provincies_id'],
    				'districts_id'=>$d['districts_id'],
    				'pemda_id'=>$d['regencies_id']?$d['regencies_id']:$d['provincies_id']

	    		]
	    	);
    	}




    	$data2=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/DataMaster?token=d25bb055-28ef-46f4-b9b1-97fa8fa19f0f&table=m_question',[]);

    	if(!is_array($data2)){
    		$data2=json_decode($data2);
    	}

    	foreach($data2 as $d){
    		$d=(array)$d;
    		DB::table('sat.master_pertanyaan')->updateOrInsert(
    			[
    				'id'=>$d['id']
	    		],
	    		[
	    			'id'=>$d['id'],
    				'parent_category'=>$d['parent_category'],
    				'question'=>$d['question'],
    				'default_value'=>$d['default_value'],
    				'uom'=>$d['uom'],
    				'remark'=>$d['remark'],
    				'sort'=>$d['sort'],
    				'year'=>$d['year'],
    				'js'=>$d['js'],
    				'commas'=>$d['commas'],
    				'average'=>$d['average'],
    				'max_value'=>$d['max_value'],
    				'readonly'=>(boolean)$d['max_value'],

	    		]
	    	);
    	}

    	$data=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/data?token=UWpNbFlqbHJabTl5YlY4d1h6Wm5NSEl6Ym1jPQ==',[]);

    	if(!is_array($data)){
    		$data=json_decode($data);
    	}

    	foreach($data as $d){
    		$d=(array)$d;
    		$pemda_id=DB::table('sat.master_pdam')->where(['name'=>$d['site_name'],'address'=>$d['address']])->pluck('pemda_id')->first();
    		if($pemda_id){
                $entry_day=Carbon::parse($d['insert_date']);
                $day= Carbon::parse($d['insert_date'])->format('d');
                $entry_date=$entry_day->format('d h:i:s A').'';

                $entry=Carbon::parse($d['entry_period_year'].'-'.$d['entry_period_month'].'-01')->endOfMonth()->format('d');

                if(((int)$entry) <((int)$day) ){
                    $entry_day=Carbon::parse($d['insert_date'])->format($entry.' h:i:s A');
                }

                $entry_day=$entry_day->format('d h:i:s A');


    			DB::table('sat.laporan')->updateOrInsert(

    			[
    				'id'=>$d['id']
	    		],
	    		[
	    			'id'=>$d['id'],
    				'site_name'=>$d['site_name'],
    				'address'=>$d['address'],
    				'entry_date'=>$d['entry_date'],
    				'entry_period_year'=>$d['entry_period_year'],
    				'entry_period_month'=>$d['entry_period_month'],
    				'insert_date'=>$d['insert_date'],
    				'pemda_id'=>$pemda_id,
                    'entry_period'=>Carbon::parse($d['entry_period_year'].'-'.$d['entry_period_month'].'-'.$entry_day),
                    'updated_at'=>Carbon::now()
    				
	    		]
	    	);
    		}else{
    			// dd($d);
    		}
    	}

    	// laporan
    	$data=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/data?token=UWpNbFlqbHJabTl5YlY4d1h6Wm5NSEl6Ym1jPQ==',[]);

    	if(!is_array($data)){
    		$data=json_decode($data);
    	}

    	foreach($data as $d){
    		$d=(array)$d;
    		$laporan_id=DB::table('sat.laporan')->where(['id'=>$d['id']])->pluck('id')->first();
    		foreach($d['detail'] as $l){
    			$l=(array)$l;
    			$question_id=DB::table('sat.master_pertanyaan')->where(['question'=>$l['question']])->pluck('id')->first();

    			if((!empty($laporan_id)) && (!empty($question_id)) ){
    				DB::table('sat.laporan_kategori')->updateOrInsert(
	    			[
	    				'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,

		    		],
		    		[
		    			'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,
	    				'data'=>$l['data'],

		    		]
		    	);
	    		}else{
	    			// dd($d);
	    		}
    		}
    		
    	}


    	$data=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/data?token=UWpNbFlqbHJabTl5YlY4d1h6ZG5NSEl6Ym1jPQ==',[]);

    	if(!is_array($data)){
    		$data=json_decode($data);
    	}

    	foreach($data as $d){
    		$d=(array)$d;
    		$laporan_id=DB::table('sat.laporan')->where(['id'=>$d['id']])->pluck('id')->first();
    		foreach($d['detail'] as $l){
    			$l=(array)$l;
    			$question_id=DB::table('sat.master_pertanyaan')->where(['question'=>$l['question']])->pluck('id')->first();

    			if((!empty($laporan_id)) && (!empty($question_id)) ){
    				DB::table('sat.laporan_pelayanan')->updateOrInsert(
	    			[
	    				'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,

		    		],
		    		[
		    			'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,
	    				'data'=>$l['data'],

		    		]
		    	);
	    		}else{
	    			// dd($d);
	    		}
    		}
    		
    	}


    	$data=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/data?token=UWpNbFlqbHJabTl5YlY4d1h6Vm5NSEl6Ym1jPQ==',[]);

    	if(!is_array($data)){
    		$data=json_decode($data);
    	}

    	foreach($data as $d){
    		$d=(array)$d;
    		$laporan_id=DB::table('sat.laporan')->where(['id'=>$d['id']])->pluck('id')->first();
    		foreach($d['detail'] as $l){
    			$l=(array)$l;
    			$question_id=DB::table('sat.master_pertanyaan')->where(['question'=>$l['question']])->pluck('id')->first();

    			if((!empty($laporan_id)) && (!empty($question_id)) ){
    				DB::table('sat.laporan_operasional')->updateOrInsert(
	    			[
	    				'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,

		    		],
		    		[
		    			'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,
	    				'data'=>$l['data'],

		    		]
		    	);
	    		}else{
	    			// dd($d);
	    		}
    		}
    		
    	}

    	$data=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/data?token=UWpNbFlqbHJabTl5YlY4d1h6aG5NSEl6Ym1jPQ==',[]);

    	if(!is_array($data)){
    		$data=json_decode($data);
    	}

    	foreach($data as $d){
    		$d=(array)$d;
    		$laporan_id=DB::table('sat.laporan')->where(['id'=>$d['id']])->pluck('id')->first();
    		foreach($d['detail'] as $l){
    			$l=(array)$l;
    			$question_id=DB::table('sat.master_pertanyaan')->where(['question'=>$l['question']])->pluck('id')->first();

    			if((!empty($laporan_id)) && (!empty($question_id)) ){
    				DB::table('sat.laporan_keuangan')->updateOrInsert(
	    			[
	    				'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,

		    		],
		    		[
		    			'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,
	    				'data'=>$l['data'],

		    		]
		    	);
	    		}else{
	    			// dd($d);
	    		}
    		}
    		
    	}


    	$data=static::con('get','http://nuwsp.labsgue.com/penilaian/api/generate/data?token=UWpNbFlqbHJabTl5YlY4d1h6bG5NSEl6Ym1jPQ==',[]);

    	if(!is_array($data)){
    		$data=json_decode($data);
    	}

    	foreach($data as $d){
    		$d=(array)$d;
    		$laporan_id=DB::table('sat.laporan')->where(['id'=>$d['id']])->pluck('id')->first();
    		foreach($d['detail'] as $l){
    			$l=(array)$l;
    			$question_id=DB::table('sat.master_pertanyaan')->where(['question'=>$l['question']])->pluck('id')->first();

    			if((!empty($laporan_id)) && (!empty($question_id)) ){
    				DB::table('sat.laporan_pemda')->updateOrInsert(
	    			[
	    				'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,

		    		],
		    		[
		    			'id_laporan'=>$laporan_id,
	    				'id_question'=>$question_id,
	    				'data'=>$l['data'],

		    		]
		    	);
	    		}else{
	    			// dd($d);
	    		}
    		}
    		
    	}






    }
}
