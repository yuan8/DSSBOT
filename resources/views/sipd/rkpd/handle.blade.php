@extends('adminlte::page',['layoutBuild'=>['menuBuild'=>'RKPD','tahun'=>$tahun]])

@section('content')

<div class="row">
	<div class="col-md-12">
		<h5 class="text-center"><b>RKPD {{$tahun}}</b></h5>
	</div>
	<div class="col-md-4">
	<div class="card">
		<div class="card-header with-border">
			<h5>RKPD {{number_format($data_count->rk_count)}}</h5>
		</div>
	</div>
	</div>
	<div class="col-md-4">
	<div class="card">
		<div class="card-header with-border">
			<h5>RKPD STORED {{number_format($data_count->d_count)}}</h5>
		</div>
	</div></div>
	<div class="col-md-4">
	<div class="card">
		<div class="card-header with-border">
			<h5>RKPD MATCH {{number_format($data_count->match_count)}}</h5>
		</div>
	</div></div>
</div>
<div class="card">
	
	<div class="card-header with-border">
		<div  class="float-left">
		<h5><b>DATA RKPD TAHUN {{$tahun}} NEED HANDLE</b> </h5>
		@isset($last_list_date)
			<small>UPDATED REKAP {{\Carbon\Carbon::parse($last_list_date)->format('d F Y H:i A')}}</small>
		@endisset
		</div>

		
		<div class="input-group float-right col-md-4">
			<select class="form-control "  onchange="window.location.href=(this.value)" >
				@php
					for($i=(\Carbon\Carbon::now()->format('Y')+1);$i>=(\Carbon\Carbon::now()->format('Y')-1);$i--){

				@endphp
				<option value="{{route('sipd.rkpd.handle',['tahun'=>$i])}}" {{$i==$tahun?'selected':''}}>{{$i}}</option>
				@php	

				}

				@endphp
			</select>

			<div class="input-group-append">
			<a {{isset(($page_block))?'area-disabled="true"':''}} href="{{route('sipd.rkpd.list.update',['tahun'=>$tahun])}}" class="btn btn-success {{isset(($page_block))?'disabled':''}}">UPDATE REKAP RKPD {{$tahun}}</a>
			</div>

		</div>



		
	</div>
	<div class="card-body table-responsive table-fix">
				<table class="table table-bordered">
			<tbody>
				@foreach($data as $d)
				@php
					$bg='';
					if($d->pagu_store){
						$bg='bg-maroon';
					}else if(!$d->rkpd_match){
						$bg='bg-danger';
					}

					if($bg=='bg-danger'){
						if($d->stored){
							$bg='bg-maroon';
						}
					}


				@endphp
				<tr class="{{$d->rkpd_match?'':'bg '.$bg}}">
					<td>{{$d->kodepemda}}</td>
					<td><b>{{$d->nama_pemda}}</b></td>
					<td>{{$d->status}}</td>
					<td> Rp. {{number_format($d->pagu)}} 

						@if((!$d->rkpd_match))
						/ Rp. {{number_format($d->pagu_store)}}
						@endif
					</td>
					<td>{{\Carbon\Carbon::parse($d->last_date)->format('d F Y h:i A')}}</td>
					<td>{{$d->rkpd_match?'MATCH':'UNMATCH'}}</td>
					<td>
						<div class="btn-group">
						@if(!$d->rkpd_match)
							<a href="{{route('sipd.rkpd.data.update',['tahun'=>$tahun,'kodepemda'=>$d->kodepemda,'status'=>$d->status,'transactioncode'=>$d->transactioncode])}}" class="btn btn-success btn-sm" >
							UPDATE
						</a>
						@if($d->pagu_store)
								<a href="{{route('sipd.rkpd.json',['tahun'=>$tahun,'json_id'=>$d->kodepemda.'.'.$d->status.'.'.$d->transactioncode])}}" class="btn btn-warning  btn-sm">JSON</a>
						@endif	
						@elseif($d->rkpd_match)
						<button onclick="force('{{route('sipd.rkpd.data.update',['tahun'=>$tahun,'kodepemda'=>$d->kodepemda,'status'=>$d->status,'transactioncode'=>$d->transactioncode])}}','')" class="btn btn-warning btn-sm" >
							UPDATE FORCE 
						</button>

						@endif
						</div>
					</td>
					<td>
						{{number_format($d->attemp)}}
					</td>


				</tr>
				@endforeach
			</tbody>
			<thead class="bg-lime">
				<tr>
				<th>KODEPEMDA</th>
				<th>NAMAPEMDA</th>
				<th>STATUS</th>
				<th>PAGU</th>
				<th>UPDATED RKPD AT</th>
				<th>MATCH</th>
				<th>ACTION</th>
				<th>ATTEMP</th>


			</tr>
			</thead>
		</table>
	</div>
</div>

@stop