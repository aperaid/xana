@extends('layouts.xana.layout')
@section('title')
	All Transaksi
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#sewa_tab" data-toggle="tab">Sewa</a></li>
        <li><a href="#jual_tab" data-toggle="tab">Jual</a></li>
        <li><a href="#claim_tab" data-toggle="tab">Claim</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="sewa_tab">
          <table id="datatabless" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>maxid</th>
                <th>Reference</th>
                <th>Invoice</th>
                <th>Periode</th>
                <th>E</th>
                <th>Project</th>
                <th>Extend</th>
              </tr>
            </thead>
            <tbody>
              @foreach($transaksiss as $transaksis)
              <tr>
                <td>{{$transaksis->id}}</td>
                <td>{{$transaksis->maxid}}</td>
                <td>{{$transaksis->Reference}}</td>
                <td>{{$transaksis->Invoice}}</td>
                <td>{{$transaksis->Periode}}</td>
                <td>{{$transaksis->E}}</td>
                <td>{{$transaksis->Project}}</td>
                <td><a href="{{route('extend.create', 'Reference=' .$transaksis -> Reference . '&Periode=' .$transaksis -> Periode)}}"><button class="btn btn-block btn-info btn-sm">Extend</button></a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="jual_tab">
          <table id="datatablesj" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Invoice</th>
                <th>Project</th>
              </tr>
            </thead>
            <tbody>
              @foreach($transaksijs as $transaksij)
              <tr>
                <td>{{$transaksij->Reference}}</td>
                <td>{{$transaksij->Invoice}}</td>
                <td>{{$transaksij->Project}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="claim_tab">

          <table id="datatablesc" class="table table-hover table-bordered">
            <thead>
              <th>periodeclaim</th>
              <th>periodeextend</th>
              <th>Reference</th>
              <th>No. Invoice</th>
              <th>Periode</th>
              <th>Tanggal Claim</th>
              <th>Project</th>
              <th>Batal Claim</th>
            </thead>
            <tbody>
              @foreach($transaksics as $transaksic)
              <tr>
                <td>{{$transaksic->periodeclaim}}</td>
                <td>{{$transaksic->periodeextend}}</td>
                <td>{{$transaksic->Reference}}</td>
                <td>{{$transaksic->Invoice}}</td>
                <td>{{$transaksic->Periode}}</td>
                <td>{{$transaksic->Tgl}}</td>
                <td>{{$transaksic->Project}}</td>
                <td><a href="{{route('extend.create')}}"><button class="btn btn-block btn-danger btn-sm">Batal</button></a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- nav-tabs-custom -->
  </div>
  <!-- col -->
</div>
<!-- row -->
@stop

@section('script')
<script>
	$(document).ready(function () {
		var table = $("#datatabless").DataTable({
      "paging": false,
      "processing": true,
      "scrollY": "100%",
      "order": [2, "desc"],
      "columnDefs":[
				{
					"targets" : 0,
					"visible" : false,
          "searchable": false
				},
				{
					"targets" : 1,
					"visible" : false,
          "searchable": false
				}
      ],
		});
		
		$('#datatabless tbody').on('click', 'tr', function () {
			var data = table.row( this ).data();
      window.open("invoice/showsewa?Reference=" + data[2] + "&Invoice=" + data[3] + "&JS=Sewa&Periode=" + data[4], "_self");
		});
	});
</script>
<script>
	$(document).ready(function () {
		var table = $("#datatablesj").DataTable({
      "paging": false,
      "processing": true,
      "order": [0, "desc"],
		});
		
		$('#datatablesj tbody').on('click', 'tr', function () {
			var data = table.row( this ).data();
      window.open("invoice/showjual?Reference=" + data[0] + "&Invoice=" + data[1] + "&JS=Jual", "_self");
		});
	});
</script>
<script>
	$(document).ready(function () {
		var table = $("#datatablesc").DataTable({
      "paging" : false,
      "processing" : true,
      "order": [2, "desc"],
      "columnDefs": [{
        "targets": 0,
        "visible": false,
        "searchable": false
      },{
        "targets": 1,
        "visible": false,
        "searchable": false
      }]
    });
		
		$('#datatablesc tbody').on('click', 'tr', function () {
			var data = table.row( this ).data();
      window.open("invoice/showclaim?Reference=" + data[0] + "&Invoice=" + data[1] + "&Periode=" + data[2] + "&JS=Claim", "_self");
		});
	});
</script>
@stop