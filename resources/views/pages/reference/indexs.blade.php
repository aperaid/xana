@extends('layouts.xana.layout')
@section('title')
	All Reference
@stop

@section('button')
	<large><a href="{{route('reference.create')}}"><button class="btn btn-success btn-sm">New Reference</button></a></large>
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
              <th>Reference</th>
              <th>Date</th>
              <th>Company</th>
              <th>Project</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reference as $reference)
            <tr>
              <td>{{$reference->Id}}</td>
              <td>{{$reference->Reference}}</td>
              <td>{{$reference->Tgl}}</td>
              <td>{{$reference->Company}}</td>
              <td>{{$reference->Project}}</td>
              <td>{{$reference->Price}}</td>
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
        }
      ],
      "order": [[0, "desc"]]
		});
		
		$('#datatables tbody').on('click', 'tr', function () {
			var data = table.row( this ).data();
			window.open("reference/"+ data[0],"_self");
		});
	});
</script>
@stop