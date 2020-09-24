@extends('adminlte::page')

@section('content')
	
<form action="{{route('test.post')}}" method="post">
	@csrf
	<label>USERNAME</label>

	<input type="text" name="username" class="form-control">
	<label>PASSWORD</label>
	<input type="password" name="password" class="form-control">
	<button type="submit" class="bt btn-primary">SUBMIT</button>
</form>

@stop