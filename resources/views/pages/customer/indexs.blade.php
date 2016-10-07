@extends('layouts.xana.layout')
@section('title')
	All Customer
@stop

@section('button')
	<large><a href="{{route('customer.create')}}"><button class="btn btn-success btn-sm">Create</button></a></large>
@stop

@section('content')
<div class="row">
<div class="col-md-12">
<div class="box box-info">
<div class="box-body with-border">
<table id="datatables" class="table table-hover table-bordered">
	<thead>
		<tr>
			<th>Customer Code</th>
			<th>Company Name</th>
		</tr>
	</thead>
	<tbody>
		@foreach($customers as $customer)
		<tr>
			<td>{{$customer->CCode}}</td>
			<td>{{$customer->Company}}</td>
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
	$("#datatables").DataTable();
})
</script>
@stop
