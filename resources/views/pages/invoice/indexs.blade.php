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
                <th>id</th>
                <th>Reference</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Periode</th>
                <th>Company</th>
                <th>Invoice Date</th>
                <th>Reference</th>
                <th width="10%">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicess as $invoices)
              <tr>
                <td>{{$invoices->id}}</td>
                <td>{{$invoices->Reference}}</td>
                <td>{{$invoices->Invoice}}</td>
                <td>{{$invoices->Project}}</td>
                <td>{{$invoices->Periode}}</td>
                <td>{{$invoices->Company}}</td>
                <td>{{$invoices->Tgl}}</td>
                <td>{{$invoices->Reference}}</td>
                <td>
                  @if($invoices->Lunas==0)
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
        <div class="tab-pane" id="jual_tab">
          <table id="datatablesj" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>Reference</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Invoice Date</th>
                <th>Reference</th>
                <th width="10%">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicejs as $invoicej)
              <tr>
                <td>{{$invoicej->id}}</td>
                <td>{{$invoicej->Reference}}</td>
                <td>{{$invoicej->Invoice}}</td>
                <td>{{$invoicej->Project}}</td>
                <td>{{$invoicej->Company}}</td>
                <td>{{$invoicej->Tgl}}</td>
                <td>{{$invoicej->Reference}}</td>
                <td>
                  @if($invoicej->Lunas==0)
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
        <div class="tab-pane" id="claim_tab">
          <table id="datatablesc" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>id</th>
                <th>Reference</th>
                <th>No. Invoice</th>
                <th>Project</th>
                <th>Company</th>
                <th>Invoice Date</th>
                <th>Reference</th>
                <th width="10%">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoicecs as $invoicec)
              <tr>
                <td>{{$invoicec->id}}</td>
                <td>{{$invoicec->Reference}}</td>
                <td>{{$invoicec->Invoice}}</td>
                <td>{{$invoicec->Project}}</td>
                <td>{{$invoicec->Company}}</td>
                <td>{{$invoicec->Tgl}}</td>
                <td>{{$invoicec->Reference}}</td>
                <td>
                  @if($invoicec->Lunas==0)
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
      {
				"targets": [1],
				"visible": false
			}
		],
	});
		
	$('#datatabless tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		window.open("invoice/showsewa/" + data[0],"_self");
	} );


	// Invoice Jual
	var table2 = $("#datatablesj").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
      {
				"targets": [1],
				"visible": false
			}
		],
	});
		
	$('#datatablesj tbody').on('click', 'tr', function () {
		var data2 = table2.row( this ).data();
		window.open("invoice/showjual/"+ data2[0],"_self");
	} );

	//Invoice Claim
	var table3 = $("#datatablesc").DataTable({
		"processing": true,
    "order": [0, "desc"],
		"columnDefs":[
			{
				"targets": [0],
				"visible": false
			},
      {
				"targets": [1],
				"visible": false
			}
		],
	});
		
	$('#datatablesc tbody').on('click', 'tr', function () {
		var data3 = table3.row( this ).data();
		window.open("invoice/showclaim/"+ data3[0],"_self");
	} );
});
</script>
@stop