@extends('layouts.xana.layout')
@section('title')
	All Project
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#sewa_tab" data-toggle="tab">Sewa</a></li>
				<li><a href="#sewa_pisah_tab" data-toggle="tab">Sewa Pisah</a></li>
        <li><a href="#jual_tab" data-toggle="tab">Jual</a></li>
				<li><a href="#jual_pisah_tab" data-toggle="tab">Jual Pisah</a></li>
        <li><a href="#claim_tab" data-toggle="tab">Claim</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="sewa_tab">
          <table id="datatabless" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicess as $invoices)
              <tr>
                <td>{{$invoices->id}}</td>
                <td>{{$invoices->Invoice}}</td>
                <td>{{$invoices->Project}}</td>
                <td>{{$invoices->Periode}}</td>
                <td>{{$invoices->Company}}</td>
                <td>
									@if($invoices->TglTerima!='')
										{{date('d/m/Y', strtotime(str_replace('/', '-', $invoices->TglTerima)."+".$invoices->Termin." days"))}}
									@else
										Fill Tgl Surat Terima
									@endif
								</td>
                <td>{{$invoices->Reference}}</td>
                <td width="10%">
									@if($invoices->TglTerima=='')
										<a><button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button></a>
                  @elseif($invoices->Lunas==0)
                    <a href="{{route('invoice.lunas', $invoices->id)}}"><button type="button" class="btn btn-block btn-danger" >Belum Lunas</button></a>
                  @else
                    <a href="{{route('invoice.lunas', $invoices->id)}}"><button type="button" class="btn btn-block btn-success" onclick="return confirm('Pembayaran belum lunas?')" >Lunas</button></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
				<div class="tab-pane" id="sewa_pisah_tab">
          <table id="datatablessp" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicesps as $invoicesp)
              <tr>
                <td>{{$invoicesp->id}}</td>
                <td>{{$invoicesp->Invoice}}</td>
                <td>{{$invoicesp->Project}}</td>
                <td>{{$invoicesp->Periode}}</td>
                <td>{{$invoicesp->Company}}</td>
                <td>
									@if($invoicesp->TglTerima!='')
										{{date('d/m/Y', strtotime(str_replace('/', '-', $invoicesp->TglTerima)."+".$invoicesp->Termin." days"))}}
									@else
										Fill Tgl Surat Terima
									@endif
								</td>
                <td>{{$invoicesp->Reference}}</td>
                <td width="10%">
									@if($invoicesp->TglTerima=='')
										<a><button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button></a>
                  @elseif($invoicesp->Lunas==0)
                    <a href="{{route('invoice.lunas', $invoicesp->id)}}"><button type="button" class="btn btn-block btn-danger" >Belum Lunas</button></a>
                  @else
                    <a href="{{route('invoice.lunas', $invoicesp->id)}}"><button type="button" class="btn btn-block btn-success" onclick="return confirm('Pembayaran belum lunas?')" >Lunas</button></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="jual_tab">
          <table id="datatablesj" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicejs as $invoicej)
              <tr>
                <td>{{$invoicej->id}}</td>
                <td>{{$invoicej->Invoice}}</td>
                <td>{{$invoicej->Project}}</td>
                <td>{{$invoicej->Company}}</td>
                <td>
									@if($invoicej->TglTerima!='')
										{{date('d/m/Y', strtotime(str_replace('/', '-', $invoicej->TglTerima)."+".$invoicej->Termin." days"))}}
									@else
										Fill Tgl Surat Terima
									@endif
								</td>
                <td>{{$invoicej->Reference}}</td>
                <td width="10%">
									@if($invoicej->TglTerima=='')
										<a><button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button></a>
                  @elseif($invoicej->Lunas==0)
                    <a href="{{route('invoice.lunas', $invoicej->id)}}"><button type="button" class="btn btn-block btn-danger" >Belum Lunas</button></a>
                  @else
                    <a href="{{route('invoice.lunas', $invoicej->id)}}"><button type="button" class="btn btn-block btn-success" onclick="return confirm('Pembayaran belum lunas?')" >Lunas</button></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
				<div class="tab-pane" id="jual_pisah_tab">
          <table id="datatablesjp" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicejps as $invoicejp)
              <tr>
                <td>{{$invoicejp->id}}</td>
                <td>{{$invoicejp->Invoice}}</td>
                <td>{{$invoicejp->Project}}</td>
                <td>{{$invoicejp->Company}}</td>
                <td>
									@if($invoicejp->TglTerima!='')
										{{date('d/m/Y', strtotime(str_replace('/', '-', $invoicejp->TglTerima)."+".$invoicejp->Termin." days"))}}
									@else
										Fill Tgl Surat Terima
									@endif
								</td>
                <td>{{$invoicejp->Reference}}</td>
                <td width="10%">
									@if($invoicejp->TglTerima=='')
										<a><button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button></a>
                  @elseif($invoicejp->Lunas==0)
                    <a href="{{route('invoice.lunas', $invoicejp->id)}}"><button type="button" class="btn btn-block btn-danger" >Belum Lunas</button></a>
                  @else
                    <a href="{{route('invoice.lunas', $invoicejp->id)}}"><button type="button" class="btn btn-block btn-success" onclick="return confirm('Pembayaran belum lunas?')" >Lunas</button></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="claim_tab">
          <table id="datatablesc" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicecs as $invoicec)
              <tr>
                <td>{{$invoicec->id}}</td>
                <td>{{$invoicec->Invoice}}</td>
                <td>{{$invoicec->Project}}</td>
                <td>{{$invoicec->Company}}</td>
                <td>
									@if($invoicec->TglTerima!='')
										{{date('d/m/Y', strtotime(str_replace('/', '-', $invoicec->TglTerima)."+".$invoicec->Termin." days"))}}
									@else
										Fill Tgl Surat Terima
									@endif
								</td>
                <td>{{$invoicec->Reference}}</td>
                <td width="10%">
									@if($invoicec->TglTerima=='')
										<a><button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button></a>
                  @elseif($invoicec->Lunas==0)
                    <a href="{{route('invoice.lunas', $invoicec->id)}}"><button type="button" class="btn btn-block btn-danger" >Belum Lunas</button></a>
                  @else
                    <a href="{{route('invoice.lunas', $invoicec->id)}}"><button type="button" class="btn btn-block btn-success" onclick="return confirm('Pembayaran belum lunas?')" >Lunas</button></a>
                  @endif
                </td>
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

	// Invoice Sewa
	var table = $("#datatabless").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
		],
	});
		
	$('#datatabless tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		window.open("invoice/showsewa/" + data[0],"_self");
	} );

	// Invoice Sewa Pisah
	var table2 = $("#datatablessp").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
		],
	});
		
	$('#datatablessp tbody').on('click', 'tr', function () {
		var data2 = table2.row( this ).data();
		window.open("invoice/showsewapisah/" + data2[0],"_self");
	} );

	// Invoice Jual
	var table3 = $("#datatablesj").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
		],
	});
		
	$('#datatablesj tbody').on('click', 'tr', function () {
		var data3 = table3.row( this ).data();
		window.open("invoice/showjual/"+ data3[0],"_self");
	} );
	
	// Invoice Jual Pisah
	var table4 = $("#datatablesjp").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
		],
	});
		
	$('#datatablesjp tbody').on('click', 'tr', function () {
		var data4 = table4.row( this ).data();
		window.open("invoice/showjualpisah/"+ data4[0],"_self");
	} );

	//Invoice Claim
	var table5 = $("#datatablesc").DataTable({
		"processing": true,
    "order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
		],
	});
		
	$('#datatablesc tbody').on('click', 'tr', function () {
		var data5 = table5.row( this ).data();
		window.open("invoice/showclaim/"+ data5[0],"_self");
	} );
});
</script>
@stop