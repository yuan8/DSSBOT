@extends('adminlte::page',['layoutBuild'=>['menuBuild'=>'RKPD','tahun'=>$tahun]])

@section('content')

		<div class="col-md-12">
			<H5>EXECUTE TIME <span id="execute_time">0 s</span></H5>

			<div class="card">
				<div class="card-body">
					
					<H5><B>NAMA PEMDA : {!!($data['nama_pemda'])!!}</B></H5>
					<H5><B>KODE PEMDA : {!!($data['kodepemda'])!!}</B></H5>
					<H5><B>TAHUN : {!!($tahun)!!}</B></H5>
					<H5><B>STATUS : {!!($data['status'])!!}</B></H5>



				</div>
			</div>
		</div>
@stop


@section('js')
	<script type="text/javascript">
		var connectionStart=false;
		var countTime=0;
		function coundUp(){
			setTimeout(function(){
				countTime+=1;
				$('#execute_time').html(countTime+' s');
				if((countTime>=0)&&(!connectionStart)){
					connectionStart=true;
					window.location.href='{{route('sipd.rkpd.data.masive',['tahun'=>$tahun,'getdata'=>'tue'])}}';
				}
			 	coundUp();

			},1000);


		}

		 coundUp()


	</script>

@stop