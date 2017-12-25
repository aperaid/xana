@extends('layouts.xana.layout')
@section('title')
	All Supplier
@stop

@section('button')
	<large><a href="{{route('supplier.create')}}"><button class="btn btn-success btn-sm">Create</button></a></large>
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
              <th>Supplier Code</th>
              <th>Company Name</th>
            </tr>
          </thead>
          <tbody>
            @foreach($suppliers as $supplier)
            <tr>
              <td width="5%">{{$supplier->id}}</td>
              <td>{{$supplier->SCode}}</td>
              <td>{{$supplier->Company}}</td>
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
			window.open("supplier/show/"+ data[0],"_self");
		});
	});
</script>
@stop
