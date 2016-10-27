@extends('layouts.xana.layout')
@section('title')
	All SJ Kembali
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-body">
        <table id="datatables" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>id</th>
              <th>QTerima</th>
              <th>SJ Code</th>
              <th>Tgl Tertanda</th>
              <th>Customer</th>
              <th>Project</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sjkembalis as $sjkembali)
            <tr>
              <td>{{$sjkembali->id}}</td>
              <td>{{$sjkembali->qtrima}}</td>
              <td>{{$sjkembali->SJKem}}</td>
              <td>{{$sjkembali->Tgl}}</td>
              <td>{{$sjkembali->Customer}}</td>
              <td>{{$sjkembali->Project}}</td>
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
			window.open("sjkembali/"+ data[0],"_self");
		});
	});
</script>
@stop