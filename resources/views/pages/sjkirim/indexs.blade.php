@extends('layouts.xana.layout')
@section('title')
	All SJ Kirim
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-body with-border">
        <table id="datatables" class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>id</th>
              <th>QTertanda</th>
              <th>SJ Code</th>
              <th>Tgl Kirim</th>
              <th>Customer</th>
              <th>Project</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sjkirims as $sjkirim)
            <tr>
              <td>{{$sjkirim->id}}</td>
              <td>{{$sjkirim->qttd}}</td>
              <td>{{$sjkirim->SJKir}}</td>
              <td>{{$sjkirim->Tgl}}</td>
              <td>{{$sjkirim->Customer}}</td>
              <td>{{$sjkirim->Project}}</td>
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
      "paging": false,
      "processing": true,
      "scrollY": "100%",
      "columnDefs":[
        {
          "targets": [0],
          "visible": false,
          "searchable": false
        },
        {
          "targets": [1],
          "visible": false,
          "searchable": false
        }
      ],
      "order": [[0, "desc"]]
		});
		
		$('#datatables tbody').on('click', 'tr', function () {
			var data = table.row( this ).data();
			window.open("sjkirim/"+ data[0],"_self");
		});
	});
</script>
@stop