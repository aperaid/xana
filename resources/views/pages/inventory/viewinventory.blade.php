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
              <th>Id</th>
              <th>Code</th>
              <th>Barang</th>
              <th>Harga</th>
              <th>Jumlah</th>
              <th>Type</th>
              <th>Warehouse</th>
              <th>Image</th>
            </tr>
          </thead>
          <tbody>
          @foreach($inventorys as $inventory)
            <tr>
              <td>{{$inventory->id}}</td>
              <td>{{$inventory->Code}}</td>
              <td>{{$inventory->Barang}}</td>
              <td>Rp {{ number_format( $inventory -> Price, 2,',', '.' ) }}</td>
              <td>{{$inventory->Jumlah}}</td>
              <td>{{$inventory->Type}}</td>
              <td>{{$inventory->Warehouse}}</td>
              <td><img src="{{ asset('/inventory/'. $inventory->id .'.jpg') }}"></td>
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
    $(function (){
      var table = $("#datatables").DataTable();
    })
  </script>
@stop
