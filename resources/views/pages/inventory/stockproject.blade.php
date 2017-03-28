@extends('layouts.xana.layout')
@section('title')
	Stock Project
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-body with-border">
        <table id="datatables" class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Id</th>
              <th>Project Code</th>
              <th>Project Name</th>
            </tr>
          </thead>
          <tbody>
            @foreach($project as $project)
            <tr>
              <td>{{$project->id}}</td>
              <td>{{$project->PCode}}</td>
              <td>{{$project->Project}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- box body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->
@stop

@section('script')
  <script>
    $(document).ready(function () {
		var table = $("#datatables").DataTable({
      "processing": true,
      "scrollY": "100%",
      "columnDefs":[
        {
          "targets": [0],
          "visible": false,
          "searchable": false
        },
      ],
      "order": [[0, "asc"]]
		});
		
		$('#datatables tbody').on('click', 'tr', function () {
			var data = table.row( this ).data();
			window.open("viewstockproject/"+ data[0],"_self");
		});
	});
  </script>
@stop
