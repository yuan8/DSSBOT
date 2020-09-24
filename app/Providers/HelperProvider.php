<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

use DB;
use Auth;
use App\MASTER\SATUAN;
class HelperProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    public static function tag_ind($tag){
        switch ($tag) {
            case 1:
                $r='RPJMN';
                break;

            case 2:
                $r='RKP';
                break;

             case 3:
                $r='PEMDA';
                break;

             case 4:
                $r='LAIN-LAIN';
                # code...
                break;
            
            default:
                # code...
$r='LAIN-LAIN';
                break;
        }

        return $r;
    }

    public static function satuanCreateOrignore($satuan){
            $data=SATUAN::where('kode','ilike',$satuan)->first();

            if(!$data){
                $data=SATUAN::create(['kode'=>$satuan]);
            }
    }

    public static function pre_ind($tag){
        switch ($tag) {
            case 1:
            $p=static::fokus_urusan()['singkat'].'.RPJMN.IND.';
                # code...
                break;
            case 2:
            $p=static::fokus_urusan()['singkat'].'.RKP.IND.'.static::fokus_tahun().'.';
                # code...
                break;
            case 3:
            $p=static::fokus_urusan()['singkat'].'.PD.IND.'.static::fokus_tahun().'.';
                # code...
                break;
                case 4:
            $p=static::fokus_urusan()['singkat'].'.IND.'.static::fokus_tahun().'.';
                # code...
                break;
            default:
                # code...
                break;
        }
        return $p;
    
    }


    static function get_tahun_rpjmn($tahun=null){
        if($tahun==null){
            $tahun=static::fokus_tahun();
        }

        $tahun=(int)$tahun;

        $poin_start=2020;
        $point_finish=$poin_start+4;

        // 2020 - 2024
        // 2025 - 2029

        if(($poin_start+4)>=$tahun){
            $index=($poin_start - $tahun)+1;
            
        }else{
              do{
                $poin_start+=5;
                $point_finish=$poin_start+4;
                if(($poin_start<=$tahun)and($point_finish>=$tahun)){
                    $ok=false;

                }

            }while($ok);

        }
        $index=($poin_start - $tahun)+1;


        return [
            'tahun_akses'=>$tahun,
            'index'=>$index,
            'start'=>$poin_start,
            'finish'=>$point_finish,
            'table'=>static::get_rpjmn_table(null,$tahun),
            'table_indikator'=>static::get_rpjmn_table('indikator',$tahun),
        ];
        
    }

    static function get_rpjmn_table($tambahan=null,$tahun=null){
        if($tahun==null){
            $tahun=static::fokus_tahun();
        }

        $tahun=(int)$tahun;

        $poin_start=2020;
        $point_finish=$poin_start+4;

        // 2020 - 2024
        // 2025 - 2029

        if(($poin_start+4)>=$tahun){

             return ('master_'.(($poin_start)).'_'.$point_finish.'_rpjmn'.(!empty($tambahan)?'_'.$tambahan:'') );

        }else{
              do{
                $poin_start+=5;
                $point_finish=$poin_start+4;
                if(($poin_start<=$tahun)and($point_finish>=$tahun)){
                    $ok=false;

                }

            }while($ok);

        }

        return ('master_'.(($poin_start)).'_'.$point_finish.'_rpjmn'.(!empty($tambahan)?'_'.$tambahan:'') );

       
       
    }


    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    static function DSS_TOKEN(){
      return 'g'.md5(date('y-mm-ymm'));
    }


    static function ToObject($Array) { 
      
    // Create new stdClass object 
    $object = new \stdClass(); 
      
    // Use loop to convert array into 
    // stdClass object 
    foreach ($Array as $key => $value) { 
        if (is_array($value)) { 
            $value = static::ToObject($value); 
        } 
        $object->$key = $value; 
    } 
    return $object; 
    } 

    static function checked(){
        if(empty(session('fokus_tahun'))){
            // dd((session('fokus_tahun')));

            header("Location: ".url('meta-login-user'));
            exit();
        }else{
            
            // dd((session('fokus_tahun')));
        }


        $urusan=(session('fokus_urusan'));
        if(empty($urusan)){
            header("Location: ".url('meta-login-user?tahun='.session('fokus_tahun')));
            exit();
        }else{
            // Auth::logout();
            return [];
        }
    }

    static function fokus_tahun(){
       if(Auth::User()){
        static::checked();
        $tahun= session('fokus_tahun');
            // dd('u_empty');

            if($tahun){
                // $request->session(['fokus_tahun'=>$tahun]);
                return $tahun;
            }else{
                Auth::logout();
                return 0;
            }
        }else{
            return '';
        }
    }
    static function fokus_urusan(){
       if(Auth::User()){
        
        static::checked();

         $urusan=(array)(session('fokus_urusan'));
        if(!empty($urusan)){
            if(!isset($urusan['nama'])){
                Auth::logout();
            }
            $urusan=(array) $urusan;
            // $request->session(['fokus_urusan'=>$urusan]);
            return $urusan;
        }else{
                Auth::logout();
            return [];
        }


       }else{
        return ['nama'=>''];
       }
    }

    static function pilihan_urusan(){
         $data=session('route_access');
         $fokus=session('fokus_urusan');
         return array('data'=>$data,'id_fokus'=>$fokus['id_urusan']);
    }


    static function error($msg){
        return array_values(array_values((array)$msg->messages())[0])[0];
    }


    static function checkdb($tahun=null){
        return true;
        if($tahun==null){
            $tahun=date('Y');
        }

        $pro=(array)DB::select("select exists (select * FROM information_schema.tables where table_name = 'map_nomen_pro_".$tahun."') as exist")[0];

        if(!$pro['exist']){

            DB::statement("
                CREATE TABLE public.map_nomen_pro_".$tahun." (
                id bigserial NOT NULL,
                id_nomen bigserial NOT NULL,
                kode_daerah varchar(4) NOT NULL,
                nomen varchar(100)  NULL,
                tahun int8 NOT NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                CONSTRAINT map_nomen_pro_".$tahun."_pkey PRIMARY KEY (id),
                CONSTRAINT map_nomen_pro_".$tahun."_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES master_daerah(id) ON UPDATE CASCADE,
                CONSTRAINT map_nomen_pro_".$tahun."_id_nomen_foreign FOREIGN KEY (id_nomen) REFERENCES master_nomenklatur_provinsi(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT map_nomen_pro_".$tahun."_tahun_kode_daerah_id_nomen_unique UNIQUE (tahun, kode_daerah, id_nomen)

                );
               
            ");
            DB::statement(" CREATE INDEX map_nomen_pro_".$tahun."_id_nomen_index ON public.map_nomen_pro_".$tahun." USING btree (id_nomen);");
            DB::statement(" CREATE INDEX map_nomen_pro_".$tahun."_kode_daerah_index ON public.map_nomen_pro_".$tahun." USING btree (kode_daerah);");
            DB::statement(" CREATE INDEX map_nomen_pro_".$tahun."_tahun_kode_daerah_id_nomen_index  ON  public.map_nomen_pro_".$tahun." USING btree (tahun,kode_daerah, id_nomen);");
        }

         $kab=(array)DB::select("select exists (select * FROM information_schema.tables where table_name = 'map_nomen_kab_".$tahun."') as exist")[0];

        if(!$kab['exist']){

            DB::statement("
                CREATE TABLE public.map_nomen_kab_".$tahun." (
                id bigserial NOT NULL,
                id_nomen bigserial NOT NULL,
                kode_daerah varchar(4) NOT NULL,
                nomen varchar(100)  NULL,
                tahun int8 NOT NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                CONSTRAINT map_nomen_kab_".$tahun."_pkey PRIMARY KEY (id),
                CONSTRAINT map_nomen_kab_".$tahun."_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES master_daerah(id) ON UPDATE CASCADE,
                CONSTRAINT map_nomen_kab_".$tahun."_id_nomen_foreign FOREIGN KEY (id_nomen) REFERENCES master_nomenklatur_kabkota(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT map_nomen_kab_".$tahun."_tahun_kode_daerah_id_nomen_unique UNIQUE (tahun, kode_daerah, id_nomen)

                );
               
            ");
            DB::statement(" CREATE INDEX map_nomen_kab_".$tahun."_id_nomen_index ON public.map_nomen_kab_".$tahun." USING btree (id_nomen);");
            DB::statement(" CREATE INDEX map_nomen_kab_".$tahun."_kode_daerah_index ON public.map_nomen_kab_".$tahun." USING btree (kode_daerah);");
            DB::statement(" CREATE INDEX map_nomen_kab_".$tahun."_tahun_kode_daerah_id_nomen_index  ON  public.map_nomen_kab_".$tahun." USING btree (tahun,kode_daerah, id_nomen);");
        }

    }


    public static function checkDBProKeg($tahun=null){
        return true;
        if($tahun==null){
            $tahun==date('Y');
        }

        $db_main='public';
        $schema='prokeg';

        $mastter_ind= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_master_indikator') as exist")[0];
       if(!$mastter_ind->exist){
          DB::connection('sink_prokeg')->statement("
            CREATE TABLE ".$schema."."."tb_".$tahun."_master_indikator (
                kode varchar(225) NOT NULL,
                uraian text NOT NULL,
                id_urusan int8 NULL,
                id_sub_urusan int8 NULL,
                pelaksana text NULL,
                target text NULL,
                target_max text NULL,
                satuan varchar(200) NULL,
                jenis varchar(100) NULL,
                data_type varchar(20) NULL,
                cal_type varchar(20) NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                CONSTRAINT "."tb_".$tahun."_master_indikator_pkey PRIMARY KEY (kode),
                CONSTRAINT "."tb_".$tahun."_mastre_indkator_id_urusan_foreign FOREIGN KEY (id_urusan) REFERENCES ".$db_main.".master_urusan(id) ON UPDATE CASCADE ON DELETE CASCADE,
                 CONSTRAINT "."tb_".$tahun."_mastre_indkator_id_sub_urusan_foreign FOREIGN KEY (id_sub_urusan) REFERENCES ".$db_main.".master_sub_urusan(id) ON UPDATE CASCADE ON DELETE CASCADE
            )
        ");

       }


       $pro= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_program') as exist")[0];

       if(!$pro->exist){
        DB::connection('sink_prokeg')->statement("
            CREATE TABLE ".$schema."."."tb_".$tahun."_program (
                id bigserial NOT NULL,
                tahun int4 NOT NULL,
                kode_daerah varchar(4) NOT NULL,
                kode_program varchar(255) NOT NULL,
                uraian text NOT NULL,
                id_urusan int8 NULL,
                id_sub_urusan int8 NULL,
                pelaksana text NULL,
                id_psn int8 NULL,
                id_spm int8 NULL,
                id_sdgs int8 NULL,
                id_nspk int8 NULL,
                tag varchar(255) NULL,
                kode_skpd varchar(50) NULL,
                kode_bidang varchar(50) NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                CONSTRAINT "."tb_".$tahun."_program_pkey PRIMARY KEY (id),

                CONSTRAINT "."tb_".$tahun."_program_id_psn_foreign FOREIGN KEY (id_psn) REFERENCES ".$db_main.".master_psn(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_program_id_sdgs_foreign FOREIGN KEY (id_sdgs) REFERENCES ".$db_main.".master_sdgs(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_program_id_spm_foreign FOREIGN KEY (id_spm) REFERENCES ".$db_main.".master_spm(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_program_id_sub_urusan_foreign FOREIGN KEY (id_sub_urusan) REFERENCES ".$db_main.".master_sub_urusan(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_program_id_urusan_foreign FOREIGN KEY (id_urusan) REFERENCES ".$db_main.".master_urusan(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_program_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES ".$db_main.".master_daerah(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_program_tahun_kode_daerah_kode_program_unique UNIQUE (tahun, kode_daerah, kode_program,kode_skpd,kode_bidang)
            )
        ");
       }

        $keg= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_kegiatan') as exist")[0];

        if(!$keg->exist){

            DB::connection('sink_prokeg')->statement("
            CREATE TABLE ".$schema."."."tb_".$tahun."_kegiatan (
                id bigserial NOT NULL,
                tahun int4 NOT NULL,
                kode_daerah varchar(4) NOT NULL,
                kode_kegiatan varchar(255) NOT NULL,
                id_program int8 NOT NULL,
                uraian text NOT NULL,
                anggaran float8 NULL,
                id_urusan int8 NULL,
                id_sub_urusan int8 NULL,
                pelaksana text NULL,
                id_psn int8 NULL,
                id_spm int8 NULL,
                id_sdgs int8 NULL,
                id_nspk int8 NULL,
                tag varchar(255) NULL,
                kode_skpd varchar(50) NULL,
                kode_bidang varchar(50) NULL,
                status int4 NULL DEFAULT 0,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                CONSTRAINT "."tb_".$tahun."_kegiatan_pkey PRIMARY KEY (id),
                CONSTRAINT "."tb_".$tahun."_kegiatan_id_psn_foreign FOREIGN KEY (id_psn) REFERENCES ".$db_main.".master_psn(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_kegiatan_id_sdgs_foreign FOREIGN KEY (id_sdgs) REFERENCES ".$db_main.".master_sdgs(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_kegiatan_id_spm_foreign FOREIGN KEY (id_spm) REFERENCES ".$db_main.".master_spm(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_kegiatan_id_sub_urusan_foreign FOREIGN KEY (id_sub_urusan) REFERENCES ".$db_main.".master_sub_urusan(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_kegiatan_id_urusan_foreign FOREIGN KEY (id_urusan) REFERENCES ".$db_main.".master_urusan(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_kegiatan_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES ".$db_main.".master_daerah(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_kegiatan_id_program_foreign FOREIGN KEY (id_program) REFERENCES ".$schema.".tb_".$tahun."_program(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT "."tb_".$tahun."_kegiatan_tahun_kode_daerah_kode_kegiatan_unique UNIQUE (tahun, kode_daerah, kode_kegiatan,kode_skpd,kode_bidang)
            )
        ");
        }


         $ind_keg= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_ind_kegiatan') as exist")[0];

         if(!$ind_keg->exist){
            DB::connection('sink_prokeg')->statement("
                CREATE TABLE ".$schema."."."tb_".$tahun."_ind_kegiatan (
                    id bigserial NOT NULL,
                    tahun int4 NOT NULL,
                    kode_daerah varchar(4) NOT NULL,
                    kode_ind varchar NULL,
                    id_kegiatan int8 NOT NULL,
                    indikator text NOT NULL,
                    target_awal text NULL,
                    target_ahir text NULL,
                    satuan varchar(255) NULL,
                    anggaran float8 NULL,
                    pelaksana text NULL,
                    keterangan text NULL,
                    kode_skpd varchar(50) NULL,
                    kode_bidang varchar(50) NULL,
                    created_at timestamp NULL,
                    updated_at timestamp NULL,
                    CONSTRAINT "."tb_".$tahun."_ind_kegiatan_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES ".$db_main.".master_daerah(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_ind_kegiatan_pkey PRIMARY KEY (id),
                    CONSTRAINT "."tb_".$tahun."_kegiatan_id_kegiatan_foreign FOREIGN KEY (id_kegiatan) REFERENCES ".$schema.".tb_".$tahun."_kegiatan(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_ind_kegiatan_tahun_kode_daerah_kode_ind_id_kegiatan_unique UNIQUE (tahun, kode_daerah,kode_ind,id_kegiatan,kode_skpd,kode_bidang)
                );
            ");

         }


         $ind_pro= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_ind_program') as exist")[0];

         if(!$ind_pro->exist){
            DB::connection('sink_prokeg')->statement("
                CREATE TABLE ".$schema."."."tb_".$tahun."_ind_program (
                   id bigserial NOT NULL,
                    tahun int4 NOT NULL,
                    kode_daerah varchar(4) NOT NULL,
                    kode_ind varchar NULL,
                    id_program int8 NOT NULL,
                    indikator text NOT NULL,
                    target_awal varchar(255) NULL,
                    target_ahir varchar(255) NULL,
                    satuan varchar(255) NULL,
                    anggaran float8 NULL,
                    pelaksana text NULL,
                    keterangan text NULL,
                    kode_skpd varchar(50) NULL,
                    kode_bidang varchar(50) NULL,
                    created_at timestamp NULL,
                    updated_at timestamp NULL,
                    CONSTRAINT "."tb_".$tahun."_ind_program_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES ".$db_main.".master_daerah(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_ind_program_pkey PRIMARY KEY (id),
                    CONSTRAINT "."tb_".$tahun."_ind_program_id_program_foreign FOREIGN KEY (id_program) REFERENCES ".$schema."."."tb_".$tahun."_program(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_ind_program_tahun_kode_daerah_kode_ind_id_program_unique UNIQUE (tahun, kode_daerah,kode_ind,id_program,kode_skpd,kode_bidang)
                );
            ");

         }

          $ind_pro= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_map_urusan') as exist")[0];

         if(!$ind_pro->exist){
            DB::connection('sink_prokeg')->statement("
                CREATE TABLE ".$schema."."."tb_".$tahun."_map_urusan (
                    id bigserial NOT NULL,
                    kode_daerah varchar(4) NOT NULL,
                    kode_skpd varchar(255)  NULL,
                    kode_bidang varchar(255)  NULL,
                    uraian_urusan_daerah text NOT NULL,
                    kode_urusan varchar NULL,
                    id_urusan int8 NULL,
                    created_at timestamp NULL,
                    updated_at timestamp NULL,
                    CONSTRAINT "."tb_".$tahun."_map_urusan_pkey PRIMARY KEY (id),
                    CONSTRAINT "."tb_".$tahun."_map_urusan_id_urusan_foreign FOREIGN KEY (id_urusan) REFERENCES ".$db_main."."."master_urusan(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_map_urusan_kode_daerah_kode_urusan_unique UNIQUE (kode_daerah, kode_urusan,kode_skpd,kode_bidang)
                );
            ");

         }

          $ind_pro= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_status_file_daerah') as exist")[0];

         if(!$ind_pro->exist){
            DB::connection('sink_prokeg')->statement("
                CREATE TABLE ".$schema."."."tb_".$tahun."_status_file_daerah (
                    id bigserial NOT NULL,
                    kode_daerah varchar(4) NOT NULL,
                    status int4 NULL DEFAULT 0,
                    last_date varchar(255) NULL ,
                    created_at timestamp NULL,
                    updated_at timestamp NULL,
                    CONSTRAINT "."tb_".$tahun."_map_urusan_pkey PRIMARY KEY (id),
                    CONSTRAINT "."tb_".$tahun."_status_file_daerah_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES ".$db_main.".master_daerah(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_status_file_daerah_kode_daerah_unique UNIQUE (kode_daerah)
                );
            ");

         }

          $map_ind= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_map_kegiatan_indikator_pusat') as exist")[0];

           if(!$map_ind->exist){
            DB::connection('sink_prokeg')->statement("
                CREATE TABLE ".$schema."."."tb_".$tahun."_map_kegiatan_indikator_pusat (
                    kode_kegiatan varchar(100) NOT NULL,
                    kode_indikator varchar(100) NOT NULL,
                    kode_daerah varchar(4) NOT NULL,
                    created_at timestamp NULL,
                    updated_at timestamp NULL,
                    CONSTRAINT "."tb_".$tahun."_map_kegiatan_indikator_pusat_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES ".$db_main.".master_daerah(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_map_kegiatan_indikator_pusat_kode_ind_kode_kode_kegiatan_kode_daerah_unique UNIQUE (kode_daerah,kode_kegiatan,kode_indikator)
                );
            ");

            }


            $map_ind= DB::connection('sink_prokeg')->select("select exists (select * FROM information_schema.tables where table_schema='".$schema."' and table_name = '"."tb_".$tahun."_map_integarsi') as exist")[0];

           if(!$map_ind->exist){
            DB::connection('sink_prokeg')->statement("
                CREATE TABLE ".$schema."."."tb_".$tahun."_map_integarsi (
                    kode_indikator varchar(100) NOT NULL,
                    target_daerah text NULL,
                    kode_daerah varchar(4) NOT NULL,
                    catatan text NULL,
                    persetujuan timestamp NULL,
                    pembuat varchar(255) NULL,
                    desk varchar(100) NULL,
                    created_at timestamp NULL,
                    updated_at timestamp NULL,
                    CONSTRAINT "."tb_".$tahun."_map_integarsi_kode_daerah_foreign FOREIGN KEY (kode_daerah) REFERENCES ".$db_main.".master_daerah(id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT "."tb_".$tahun."_map_integarsi_kode_ind_kode_kode_kegiatan_kode_daerah_unique UNIQUE (kode_daerah,kode_indikator)
                );
            ");


         }



    }


    static public function pilihan_tahun(){
        $sekarang=(int)date('Y');
        $awal=2020;
        $mulai=0;
        $ahir=0;

        $range=$sekarang-$awal;
        // 0
        $range=ceil($range / 4);
  
        if($range!=0){
        $ahir=((int)$range * 4 )+$awal+($range-1);
        $awal=$ahir-5;
        }else{
            $ahir=$awal+4;
            $awal=$ahir-4;
        }
        // 2024
        
        // 2020

        $data=[];

        for($i=$awal;$i<=$ahir;$i++){
            $data[]=$i;
        }

        return $data;
        

    }

    static public function q($q){
       if($q){
            $q=explode(' ',$q);
            $data=$q;
            $match='';
            foreach ($q as $key => $value) {
                if($match==''){
                    $match.=$value;

                }else{
                    $match.=' '.$value;
                $data[]=$match;

                }

            }
            $data=array_reverse($data);

             return ("'%".implode("%'||'%", $data)."%'");
       }else{
        return '';
       }

    }
}
