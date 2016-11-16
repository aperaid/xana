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
              <th>Id</th>
              <th>Code</th>
              <th>Barang</th>
              <th>Type</th>
              <th>Edit</th>
            </tr>
          </thead>
          <tbody>
            @foreach($adjusts as $adjust)
            <tr>
              <td>{{$adjust->id}}</td>
              <td>{{$adjust->Code}}</td>
              <td>{{$adjust->Barang}}</td>
              <td>{{$adjust->Type}}</td>
              <td><a href="{{route('inventory.editadjustinventory', $adjust->id)}}"><button class="btn btn-primary">Edit</button></a></td>
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
