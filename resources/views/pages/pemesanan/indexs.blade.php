@extends('layouts.xana.layout')
@section('title')
	All Pemesanan
@stop

@section('button')
	<large><a href="{{route('pemesanan.create')}}"><button class="btn btn-success btn-sm">Create</button></a></large>
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
              <th width="10%">Pesan Code</th>
              <th width="10%">Date</th>
              <th>Supplier Company</th>
              <th width="10%">Total Price</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pemesanans as $pemesanan)
            <tr>
              <td>{{$pemesanan->id}}</td>
              <td>{{$pemesanan->PesanCode}}</td>
              <td>{{$pemesanan->Tgl}}</td>
              <td>{{$pemesanan->Company}}</td>
              <td>Rp {{ number_format( $pemesanan->Price+$pemesanan->Transport, 2,',', '.' ) }}</td>
              @if ( $pemesanan->TerimaCode == NULL ) <!-- belum dikirim -->
								<td><span class="badge bg-red">Pesan</span></td>
							@elseif ( $pemesanan -> SumQuantity > $pemesanan -> SumQTerima ) <!-- setengah diterima -->
								<td><span class="badge bg-yellow">Kirim</span></td>
							@elseif ( $pemesanan -> SumQuantity == $pemesanan -> SumQTerima ) <!-- penerimaan selesai  -->
								<td><span class="badge bg-green">Terima</span></td>
							@endif
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
			window.open("pemesanan/"+ data[0],"_self");
		});
	});
</script>
@stop