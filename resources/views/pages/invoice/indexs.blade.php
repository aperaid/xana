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
        <li><a href="#jual_tab" data-toggle="tab">Jual</a></li>
        <li><a href="#claim_tab" data-toggle="tab">Claim</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="sewa_tab">
          <table id="datatabless" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Reference</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Tgl Invoice</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicess as $invoices)
              <tr>
                <td>{{$invoices->Reference}}</td>
                <td>{{$invoices->Invoice}}</td>
                <td>{{$invoices->Project}}</td>
                <td>{{$invoices->Periode}}</td>
                <td>{{$invoices->Company}}</td>
                <td>{{$invoices->Tgl}}</td>
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
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Tgl Invoice</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicejs as $invoicej)
              <tr>
                <td>{{$invoicej->Reference}}</td>
                <td>{{$invoicej->Invoice}}</td>
                <td>{{$invoicej->Project}}</td>
                <td>{{$invoicej->Company}}</td>
                <td>{{$invoicej->Tgl}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="claim_tab">
          <table id="datatablesc" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Reference</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Tgl Invoice</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicecs as $invoicec)
              <tr>
                <td>{{$invoicec->Reference}}</td>
                <td>{{$invoicec->Invoice}}</td>
                <td>{{$invoicec->Project}}</td>
                <td>{{$invoicec->Company}}</td>
                <td>{{$invoicec->Tgl}}</td>
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
		"paging": false,
		"processing": true,
		"order": [5, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			}
		],
	});
		
	$('#datatabless tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		window.open("invoice/showsewa?Reference="+ data[0] + "&Invoice=" + data[1] +"&JS=Sewa&Periode=" + data[3],"_self");
	} );


	// Invoice Jual
	var table2 = $("#datatablesj").DataTable({
		"paging": false,
		"processing": true,
		"order": [4, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			}
		],
	});
		
	$('#datatablesj tbody').on('click', 'tr', function () {
		var data2 = table2.row( this ).data();
		window.open("viewinvoicejual.php?Reference="+ data2[0] + "&JS=Jual&Invoice=" + data2[1],"_self");
	} );

	//Invoice Claim
	var table3 = $("#datatablesc").DataTable({
		"paging": false,
		"processing": true,
    "order": [4, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
		],
	});
		
	$('#datatablesc tbody').on('click', 'tr', function () {
		var data3 = table3.row( this ).data();
		window.open("viewinvoiceclaim.php?Reference="+ data3[0] + "&JS=Claim&Invoice=" + data3[1] + "&Periode=" + data3[5],"_self");
	} );
});
</script>
@stop