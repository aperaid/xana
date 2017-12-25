@extends('layouts.xana.layout')
@section('title')
	Adjust Inventory
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-body with-border">
        <table id="datatables" class="table table-hover table-bordered">
          <thead>
            <tr>
							@if(env('APP_TYPE')=='Sewa')
								<th>Id</th>
								<th>Code</th>
								<th>Barang</th>
								<th>Type</th>
							@else
								<th>Id</th>
								<th>Code</th>
								<th>Barang</th>
								<th>Kategori</th>
							@endif
            </tr>
          </thead>
          <tbody>
            @foreach($adjusts as $adjust)
            <tr>
              <td>{{$adjust->id}}</td>
              <td>{{$adjust->Code}}</td>
              <td>{{$adjust->Barang}}</td>
              <td>{{$adjust->Type}}</td>
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
			window.open("editadjustinventory/"+ data[0],"_self");
		});
	});
</script>
@stop
