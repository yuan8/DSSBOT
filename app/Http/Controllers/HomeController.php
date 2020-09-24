<?php

namespace App\Http\Controllers;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {   
        // $tahun=date('Y');

        //  $data_rekap= DB::table("rkpd.master_".$tahun."_kegiatan")
        //  ->groupby('kodepemda')
        //  ->count();


        // $sql="select 
        //     max(st.kodepemda) as kodepemda,
        //     max(st.anggaran) as status_anggaran,
        //     sum(k.pagu) as data_anggaran,
        //     max(st.status) as status,
        //     max(k.status) as data_status,
        //     max(st.last_date) as status_date,
        //     max(d.nama) as nama_daerah
        //     from 
        //     rkpd.master_".$tahun."_status as st
        //     left join rkpd.master_".$tahun."_kegiatan as k on k.kodepemda = st.kodepemda
        //     left join public.master_daerah as d on d.id = st.kodepemda
        //     group by st.id
        // ";

        // $data= DB::table(DB::raw("(".$sql.") as gg"))
        // ->whereRaw("
        //     (data_anggaran <> status_anggaran) OR (status <> data_status)
        // ")
        // ->orderBy('kodepemda','asc')->get();


        // return view('dashboard')->with([
        //     'data'=>$data,
        //     'jumlah_pemda'=>$data_rekap
        // ]);

        return view('index');
    }
}
