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
            @foreach($pemesanans as $key => $pemesanan)
            <tr>
              <td>{{$pemesanan->id}}</td>
              <td>{{$pemesanan->PesanCode}}</td>
              <td>{{$pemesanan->Tgl}}</td>
              <td>{{$pemesanan->Company}}</td>
              <td>Rp {{ number_format( $pemesanan->Price+$transports[$key], 2,',', '.' ) }}</td>
              @if ( $pemesanan -> SumQTTerima == 0 ) <!-- belum dikirim -->
								<td><span class="badge bg-red">Pesan</span></td>
							@elseif ( $pemesanan -> SumQuantity > $pemesanan -> SumQTTerima ) <!-- setengah diterima -->
								<td><span class="badge bg-orange">Kirim</span></td>
							@elseif ( ($pemesanan -> SumQuantity == $pemesanan -> SumQTTerima) && $pemesanan -> TglTerima == '') <!-- penerimaan selesai/tgl terima  -->
								<td><span class="badge bg-yellow">Tgl Terima</span></td>
							@elseif ( $pemesanan -> Lunas == 0) <!-- Jatuh Tempo  -->
								<td><span class="badge bg-blue">Jatuh Tempo</span></td>
							@elseif ( $pemesanan -> Lunas == 1) <!-- Lunas  -->
								<td><span class="badge bg-green">Lunas</span></td>
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