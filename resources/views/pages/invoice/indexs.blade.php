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
				<li><a href="#sewa_kirim_tab" data-toggle="tab">Sewa Kirim</a></li>
        <li><a href="#jual_tab" data-toggle="tab">Jual</a></li>
				<!--<li><a href="#jual_pisah_tab" data-toggle="tab">Jual Pisah</a></li>-->
				<li><a href="#jual_kirim_tab" data-toggle="tab">Jual Kirim</a></li>
        <li><a href="#claim_tab" data-toggle="tab">Claim</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="sewa_tab">
          <table id="datatabless" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
								<th hidden>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicess as $invoices)
              <tr>
                <td hidden>{{$invoices->id}}</td>
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
										<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
                  @elseif($invoices->Lunas==0)
                    <button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
                  @else
                    <button type="button" class="btn btn-block btn-success lunas">Lunas</button>
                  @endif
                </td>
								<td hidden>Gabung</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
				<div class="tab-pane" id="sewa_pisah_tab">
          <table id="datatablessp" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
								<th hidden>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicesps as $invoicesp)
              <tr>
                <td hidden>{{$invoicesp->id}}</td>
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
										<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
                  @elseif($invoicesp->Lunas==0)
                    <button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
                  @else
                    <button type="button" class="btn btn-block btn-success lunas">Lunas</button>
                  @endif
                </td>
								<td hidden>Pisah</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
				<div class="tab-pane" id="sewa_kirim_tab">
          <table id="datatablessk" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
								<th hidden>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicesks as $invoicesk)
              <tr>
                <td hidden>{{$invoicesk->id}}</td>
                <td>{{$invoicesk->Invoice}}</td>
                <td>{{$invoicesk->Project}}</td>
                <td>{{$invoicesk->Periode}}</td>
                <td>{{$invoicesk->Company}}</td>
                <td>
									@if($invoicesk->TglTerima!='')
										{{date('d/m/Y', strtotime(str_replace('/', '-', $invoicesk->TglTerima)."+".$invoicesk->Termin." days"))}}
									@else
										Fill Tgl Surat Terima
									@endif
								</td>
                <td>{{$invoicesk->Reference}}</td>
                <td width="10%">
									@if($invoicesk->TglTerima=='')
										<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
                  @elseif($invoicesk->Lunas==0)
                    <button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
                  @else
                    <button type="button" class="btn btn-block btn-success lunas">Lunas</button>
                  @endif
                </td>
								<td hidden>Pisah</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="jual_tab">
          <table id="datatablesj" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
								<th hidden>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicejs as $invoicej)
              <tr>
                <td hidden>{{$invoicej->id}}</td>
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
										<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
                  @elseif($invoicej->Lunas==0)
                    <button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
                  @else
                    <button type="button" class="btn btn-block btn-success lunas">Lunas</button>
                  @endif
                </td>
								<td hidden>Gabung</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
				<div class="tab-pane" id="jual_pisah_tab">
          <table id="datatablesjp" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
								<th hidden>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicejps as $invoicejp)
              <tr>
                <td hidden>{{$invoicejp->id}}</td>
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
										<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
                  @elseif($invoicejp->Lunas==0)
                    <button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
                  @else
                    <button type="button" class="btn btn-block btn-success lunas">Lunas</button>
                  @endif
                </td>
								<td hidden>Pisah</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
				<div class="tab-pane" id="jual_kirim_tab">
          <table id="datatablesjk" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
								<th hidden>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicejks as $invoicejk)
              <tr>
                <td hidden>{{$invoicejk->id}}</td>
                <td>{{$invoicejk->Invoice}}</td>
                <td>{{$invoicejk->Project}}</td>
                <td>{{$invoicejk->Periode}}</td>
                <td>{{$invoicejk->Company}}</td>
                <td>
									@if($invoicejk->TglTerima!='')
										{{date('d/m/Y', strtotime(str_replace('/', '-', $invoicejk->TglTerima)."+".$invoicejk->Termin." days"))}}
									@else
										Fill Tgl Surat Terima
									@endif
								</td>
                <td>{{$invoicejk->Reference}}</td>
                <td width="10%">
									@if($invoicejk->TglTerima=='')
										<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
                  @elseif($invoicejk->Lunas==0)
                    <button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
                  @else
                    <button type="button" class="btn btn-block btn-success lunas">Lunas</button>
                  @endif
                </td>
								<td hidden>Pisah</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="claim_tab">
          <table id="datatablesc" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Due Date</th>
                <th>Reference</th>
                <th>Status</th>
								<th hidden>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicecs as $invoicec)
              <tr>
                <td hidden>{{$invoicec->id}}</td>
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
										<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
                  @elseif($invoicec->Lunas==0)
                    <button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
                  @else
                    <button type="button" class="btn btn-block btn-success lunas">Lunas</button>
                  @endif
                </td>
								<td hidden>Gabung</td>
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
				"visible": true,
				"searchable": false
			},
		],
	});
		
	$('#datatabless tbody').on('click', 'td', function () {
		var data = table.row( $(this).closest('tr') ).data();
		if ($(this).index() == 7)
			return;
		else
			window.open("invoice/showsewa/" + data[0],"_self");
	});

	// Invoice Sewa Pisah
	var table2 = $("#datatablessp").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": true,
				"searchable": false
			},
		],
	});
		
	$('#datatablessp tbody').on('click', 'td', function () {
		var data2 = table2.row( $(this).closest('tr') ).data();
		if ($(this).index() == 7)
			return;
		else
			window.open("invoice/showsewapisah/" + data2[0],"_self");
	});
	
	// Invoice Sewa Kirim
	var table3 = $("#datatablessk").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": true,
				"searchable": false
			},
		],
	});
		
	$('#datatablessk tbody').on('click', 'td', function () {
		var data3 = table3.row( $(this).closest('tr') ).data();
		if ($(this).index() == 7)
			return;
		else
			window.open("invoice/showsewakirim/" + data3[0],"_self");
	});

	// Invoice Jual
	var table4 = $("#datatablesj").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": true,
				"searchable": false
			},
		],
	});
		
	$('#datatablesj tbody').on('click', 'td', function () {
		var data4 = table4.row( $(this).closest('tr') ).data();
		if ($(this).index() == 6)
			return;
		else
			window.open("invoice/showjual/"+ data4[0],"_self");
	});
	
	// Invoice Jual Pisah
	var table5 = $("#datatablesjp").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": true,
				"searchable": false
			},
		],
	});
		
	$('#datatablesjp tbody').on('click', 'td', function () {
		var data5 = table5.row( $(this).closest('tr') ).data();
		if ($(this).index() == 6)
			return;
		else
			window.open("invoice/showjualpisah/"+ data5[0],"_self");
	});
	
	// Invoice Jual Kirim
	var table6 = $("#datatablesjk").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": true,
				"searchable": false
			},
		],
	});
		
	$('#datatablesjk tbody').on('click', 'td', function () {
		var data6 = table6.row( $(this).closest('tr') ).data();
		if ($(this).index() == 7)
			return;
		else
			window.open("invoice/showjualkirim/" + data6[0],"_self");
	});

	//Invoice Claim
	var table7 = $("#datatablesc").DataTable({
		"processing": true,
    "order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": true,
				"searchable": false
			},
		],
	});
		
	$('#datatablesc tbody').on('click', 'td', function () {
		var data7 = table7.row( $(this).closest('tr') ).data();
		if ($(this).index() == 6)
			return;
		else
			window.open("invoice/showclaim/"+ data7[0],"_self");
	});
});

$(".lunas").click(function(){
  $.post("invoice/updatelunas", {"_token":"{{csrf_token()}}", id: $(this).parent().siblings(":first").text(), LunasType: $(this).parent().siblings(":last").text()}, function(){})
  .done(function(data){
    location.reload();
  })
  .fail(function(data){
    console.log('fail');
  });
});

</script>
@stop