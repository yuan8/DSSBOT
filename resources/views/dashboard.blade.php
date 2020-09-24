@extends('adminlte::page')

@section('content')
    <div class="btn-group">
        <a href="" class="btn btn-primary">UPDATE LOG</a>
        <button class="btn btn-info" disabled="">JUMLAH PEMDA TEREKAP : {{$jumlah_pemda}}</button> 
        <button class="btn btn-info" disabled="">JUMLAH PEMDA PERLU PERBARUAN : {{count($data)}}</button> 

    </div>
    <br>
    <div class="card-solid card card-primary">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">AKSI</th>
                        <th rowspan="2">KODEPEMDA</th>
                        <th rowspan="2">PEMDA</th>
                        <th colspan="2">LOG</th>
                        <th colspan="2" >EXISTING</th>


                    </tr>
                    <tr>
                        <th>STATUS</th>
                        <th>ANGGARAN</th>
                         <th>STATUS</th>
                        <th>ANGGARAN</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                    <tr>
                        <td>
                            <a href="" class=" btn btn-primary btn-xs">UPDATE</a>
                        </td>
                        <td>{{$d->kodepemda}}</td>
                        <td>{{$d->nama_daerah}}</td>
                        <td>{{$d->status}}</td>
                        <td>{{number_format($d->status_anggaran)}}</td>
                         <td>{{$d->data_status}}</td>
                        <td>{{number_format($d->data_anggaran)}}</td>



                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
@endpush