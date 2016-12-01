@extends('layouts.xana.layout')
@section('title')
	All Penawaran
@stop

@section('button')
	<large><a href="{{route('penawaran.create')}}"><button class="btn btn-success btn-sm">Create</button></a></large>
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
              <th>Penawaran Code</th>
              <th>Tgl</th>
              <th>PCode</th>
            </tr>
          </thead>
          <tbody>
            @foreach($penawarans as $penawaran)
            <tr>
              <td>{{$penawaran->id}}</td>
              <td>{{$penawaran->Penawaran}}</td>
              <td>{{$penawaran->Tgl}}</td>
              <td>{{$penawaran->PCode}}</td>
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
        }
      ],
      "order": [[0, "desc"]]
		});
		
		$('#datatables tbody').on('click', 'tr', function () {
			var data = table.row( this ).data();
			window.open("penawaran/"+ data[0],"_self");
		});
	});
</script>
@stop
