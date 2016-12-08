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
              <th>Hapus</th>
            </tr>
          </thead>
          <tbody>
            @foreach($removes as $remove)
            <tr>
              <td>{{$remove->id}}</td>
              <td width="10%">{{substr($remove->Code, 0, -1)}}</td>
              <td>{{$remove->Barang}}</td>
              <td width="10%"><a href="{{route('inventory.getremoveinventory', $remove->id)}}"><button class="btn btn-block btn-danger btn-sm" onclick="return confirm('Delete Inventory?')">Delete</button></a></td>
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
