@extends('layouts.xana.layout')
@section('title')
	All Project
@stop

@section('button')
	<large><a href="{{route('project.create')}}"><button class="btn btn-success btn-sm">Create</button></a></large>
@stop

@section('content')
<div class="row">
<div class="col-md-12">
<div class="box box-info">
<div class="box-body with-border">
<table id="datatables" class="table table-hover table-bordered">
	<thead>
		<tr>
			<th>Project Code</th>
			<th>Project Name</th>
		</tr>
	</thead>
	<tbody>
		@foreach($project as $project)
		<tr>
			<td>{{$project->PCode}}</td>
			<td>{{$project->Project}}</td>
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