@extends('layouts.xana.layout')
@section('title')
	Purchase Invoice
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class="box-body with-border">
				<table id="datatable" class="table table-hover table-bordered">
					<thead>
						<tr>
							<th hidden>id</th>
							<th>No. Invoice</th>
							<th>Supplier</th>
							<th>Due Date</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						@foreach($purchaseinvoices as $purchaseinvoice)
						<tr>
							<td hidden>{{$purchaseinvoice->id}}</td>
							<td>{{$purchaseinvoice->PurchaseInvoice}}</td>
							<td>{{$purchaseinvoice->Company}}</td>
							<td>
								@if($purchaseinvoice->TglTerima!='')
									{{date('d/m/Y', strtotime(str_replace('/', '-', $purchaseinvoice->TglTerima)."+".$purchaseinvoice->Termin." days"))}}
								@else
									Fill Tgl Surat Terima
								@endif
							</td>
							<td width="10%">
								@if($purchaseinvoice->TglTerima=='')
									<button type="button" class="btn btn-block btn-danger" disabled>Belum Lunas</button>
								@elseif($purchaseinvoice->Lunas==0)
									<button type="button" class="btn btn-block btn-danger lunas">Belum Lunas</button>
								@else
									<button type="button" class="btn btn-block btn-success lunas">Lunas</button>
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
  </div>
  <!-- col -->
</div>
<!-- row -->
@stop

@section('script')
<script>
$(document).ready(function () {
	var table1 = $("#datatable").DataTable({
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
	
	$('#datatable tbody').on('click', 'td', function () {
		var data1 = table1.row( $(this).closest('tr') ).data();
		if ($(this).index() == 4)
			return;
		else
			window.open("purchaseinvoice/"+ data1[0]+"/edit","_self");
	});
});

$(".lunas").click(function(){
  $.post("purchaseinvoice/updatelunas", {"_token":"{{csrf_token()}}", id: $(this).parent().siblings(":first").text(), LunasType: $(this).parent().siblings(":last").text()}, function(){})
  .done(function(data){
    location.reload();
  })
  .fail(function(data){
    console.log('fail');
  });
});

</script>
@stop