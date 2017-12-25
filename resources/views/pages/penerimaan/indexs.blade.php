@extends('layouts.xana.layout')
@section('title')
	All Penerimaan
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
              <th>Terima Code</th>
              <th width="10%">Date</th>
              <th width="20%">Supplier Company</th>
            </tr>
          </thead>
          <tbody>
            @foreach($penerimaans as $penerimaan)
            <tr>
              <td>{{$penerimaan->id}}</td>
              <td>{{$penerimaan->TerimaCode}}</td>
              <td>{{$penerimaan->Tgl}}</td>
              <td>{{$penerimaan->Company}}</td>
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
			window.open("penerimaan/"+ data[0],"_self");
		});
	});
</script>
@stop