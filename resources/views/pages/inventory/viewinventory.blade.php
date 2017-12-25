@extends('layouts.xana.layout')
@section('title')
	All Inventory
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
								<th width='5%'>Code</th>
								<th width='20%'>Barang</th>
								<th width='10%'>Beli</th>
								<th width='10%'>Jual</th>
								<th width='7%'>Sewa</th>
								<th width='5%'>Type</th>
								<th width='12%'>Kumbang</th>
								<th width='12%'>Bulak Sereh</th>
								<th width='12%'>Legok</th>
								<th width='12%'>Citra Garden</th>
							@else
								<th>Id</th>
								<th width='5%'>Code</th>
								<th width='20%'>Barang</th>
								<th width='5%'>Quantity</th>
								<th width='10%'>Beli</th>
								<th width='10%'>Jual</th>
								<th width='20%'>Kategori</th>
							@endif
              <!--<th>Image</th>-->
            </tr>
          </thead>
          <tbody>
          @foreach($inventorys as $inventory)
            <tr>
							@if(env('APP_TYPE')=='Sewa')
								<td>{{$inventory->id}}</td>
								<td>{{$inventory->Code}}</td>
								<td>{{$inventory->Barang}}</td>
								<td>Rp {{ number_format( $inventory -> BeliPrice, 2,',', '.' ) }}</td>
								<td>Rp {{ number_format( $inventory -> JualPrice, 2,',', '.' ) }}</td>
								<td>Rp {{ number_format( $inventory -> Price, 2,',', '.' ) }}</td>
								<td>{{$inventory->Type}}</td>
								<td>{{$inventory->Kumbang}}</td>
								<td>{{$inventory->BulakSereh}}</td>
								<td>{{$inventory->Legok}}</td>
								<td>{{$inventory->CitraGarden}}</td>
							@else
								<td>{{$inventory->id}}</td>
								<td>{{$inventory->Code}}</td>
								<td>{{$inventory->Barang}}</td>
								<td>{{$inventory->Warehouse}}</td>
								<td>Rp {{ number_format( $inventory -> BeliPrice, 2,',', '.' ) }}</td>
								<td>Rp {{ number_format( $inventory -> JualPrice, 2,',', '.' ) }}</td>
								<td>{{$inventory->Type}}</td>
							@endif
              <!--<td><img src="{{ asset('/inventory/'. $inventory->id .'.jpg') }}"></td>-->
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
	});
  </script>
@stop
