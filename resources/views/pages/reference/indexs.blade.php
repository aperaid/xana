@extends('layouts.xana.layout')
@section('title')
	All Reference
@stop

@section('button')
	<large><a href="{{route('reference.create')}}"><button class="btn btn-success btn-sm">New Reference</button></a></large>
@stop

@section('content')
<div class="row">
<div class="col-md-12">
<div class="box box-info">
<div class="box-body with-border">
<table id="datatables" class="table table-hover table-bordered">
	<thead>
		<tr>
			<th>Reference</th>
			<th>Date</th>
      <th>Company</th>
      <th>Project</th>
      <th>Price</th>
		</tr>
	</thead>
	<tbody>
		@foreach($reference as $reference)
		<tr>
			<td>{{$reference->Reference}}</td>
			<td>{{$reference->Tgl}}</td>
      <td>{{$reference->Company}}</td>
      <td>{{$reference->Project}}</td>
      <td>{{$reference->Price}}</td>
		</tr>
		@endforeach
	</tbody>
</table>

</div>
<!-- box body -->

<div class="box-footer">

</div>
<!-- footer -->
</div>
<!-- box -->
</div>
<!-- col -->

</div>
<!-- row -->
@stop

@section('script')
<script>
$(function (){
	var table = $("#datatables").DataTable();
})
</script>
@stop