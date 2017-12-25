@extends('layouts.xana.layout')
@section('title')
	All Permintaan
@stop

@section('button')
	<large><a href="{{route('permintaan.create')}}"><button class="btn btn-success btn-sm">Create</button></a></large>
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
              <th>Minta Code</th>
              <th>Date</th>
              <th>SCode</th>
            </tr>
          </thead>
          <tbody>
            @foreach($permintaans as $permintaan)
            <tr>
              <td>{{$permintaan->id}}</td>
              <td>{{$permintaan->MintaCode}}</td>
              <td>{{$permintaan->Tgl}}</td>
              <td>{{$permintaan->SCode}}</td>
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
			window.open("permintaan/"+ data[0],"_self");
		});
	});
</script>
@stop
