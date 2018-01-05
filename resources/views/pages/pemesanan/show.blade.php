@extends('layouts.xana.layout')
@section('title')
	View Pemesanan
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
        <li class="active"><a href="#pemesanan_tab" data-toggle="tab">Pemesanan</a></li>
				@if($pemesanan -> TerimaCode!='')
					<li><a href="#penerimaan_tab" data-toggle="tab">Penerimaan</a></li>
				@else
					<li class="disabled"><a>Penerimaan</a></li>
				@endif
				@if($pemesanan -> ReturCode!='')
					<li><a href="#retur_tab" data-toggle="tab">Retur</a></li>
				@else
					<li class="disabled"><a>Retur</a></li>
				@endif
        <li><a href="#invoice_tab" data-toggle="tab">Invoice</a></li>
			</ul>
				<div class="tab-content">
					<!-- PEMESANAN TAB -->
					<div class="active tab-pane" id="pemesanan_tab">
						<div class="box-body">
							<!-- title row -->
							<div class="row">
								<div class="col-md-12">
									<h2 class="page-header">
										<small class="pull-right">Date: {{ $pemesanan -> Tgl }}</small><br>
										<i class="fa fa-globe"></i> PT. {{env('APP_COMPANY')}} | {{ $pemesanan -> PesanCode }}
										<small class="pull-right">Transport: {{ 'Rp '. number_format( $pemesanan -> Transport, 2,',', '.' ) }}</small>
									</h2>
								</div>
							</div>
							<!-- info row -->
							<div class="row">
								<div class="col-sm-3">
									Company
									<address>
										<strong>{{ $pemesanan -> Company }}</strong><br>
										{{ $pemesanan -> CompAlamat }}<br>
										{{ $pemesanan -> CompKota }},  {{ $pemesanan -> CompZip }}<br>
										Phone: {{ $pemesanan -> CompPhone }}<br>
										Email: {{ $pemesanan -> CompEmail }}
									</address>
								</div>
								@if($pemesanan -> Supplier!='')
								<div class="col-sm-3">
									Contact Person
									<address>
										<strong>{{ $pemesanan -> Supplier }}</strong><br>
										Phone: {{ $pemesanan -> SupPhone }}<br>
										Email: {{ $pemesanan -> SupEmail }}
									</address>
								</div>
								@endif
								@if($pemesanan -> Supplier2!='')
								<div class="col-sm-3">
									Contact Person
									<address>
										<strong>{{ $pemesanan -> Supplier2 }}</strong><br>
										Phone: {{ $pemesanan -> SupPhone2 }}<br>
										Email: {{ $pemesanan -> SupEmail2 }}
									</address>
								</div>
								@endif
								@if($pemesanan -> Supplier3!='')
								<div class="col-sm-3">
									Contact Person
									<address>
										<strong>{{ $pemesanan -> Supplier3 }}</strong><br>
										Phone: {{ $pemesanan -> SupPhone3 }}<br>
										Email: {{ $pemesanan -> SupEmail3 }}
									</address>
								</div>
								@endif
							</div>
							<!-- Table row -->
							<div class="row">
								<div class="col-md-12 table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>ICode</th>
												<th>Item Name</th>
												@if(env('APP_TYPE')=='Jual')
													<th>Kategori</th>
												@endif
												<th>Quantity</th>
												<th>Price/Unit</th>
												<th>Progress</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											@foreach( $pemesananlists as $pemesananlist )
											<tr>
												<td>{{ $pemesananlist -> ICode }}</td>
												<td>{{ $pemesananlist -> Barang }}</td>
												@if(env('APP_TYPE')=='Jual')
													<td>{{ $pemesananlist -> Type }}</td>
												@endif
												<td>{{ $pemesananlist -> QTerima or '0'.'/'.$pemesananlist -> Quantity }}</td>
												<td>Rp {{ number_format( $pemesananlist -> Amount, 2,',', '.' ) }}</td>
													@if ( !$terimacheck ) <!-- belum dikirim -->
														<td>
															<div class="progress progress-xs">
																<div class="progress-bar progress-bar-red" style="width:10%"></div>
															</div>
														</td>
														<td><span class="badge bg-red">Barang Dipesan</span></td>

													@elseif ( $pemesananlist -> Quantity > $pemesananlist -> QTerima ) <!-- setengah diterima -->
														<td>
															<div class="progress progress-xs">
																<div class="progress-bar progress-bar-yellow" style="width:50%"></div>
															</div>
														</td>
														<td><span class="badge bg-yellow">Separuh Barang Diterima</span></td>

													@elseif ( $pemesananlist -> Quantity == $pemesananlist -> QTerima ) <!-- penerimaan selesai  -->
														<td>
															<div class="progress progress-xs">
																<div class="progress-bar progress-bar-green" style="width:100%"></div>
															</div>
														</td>
														<td><span class="badge bg-green">Barang Telah Diterima</span></td>
													@endif
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<a href="{{route('pemesanan.index')}}"><button type="button" class="btn btn-default pull-left" style="margin-right: 5px;">Back</button></a>
									<a href="{{route('penerimaan.create', 'id='.$pemesanan->idPesan)}}"><button type="button" style="margin-right: 5px;" @if ( $terimacheck )	class="btn btn-default pull-right" disabled	@else class="btn btn-success pull-right" @endif>Penerimaan</button></a>
									<a href="{{route('pemesanan.edit', $pemesanan->idPesan)}}"><button type="button" style="margin-right: 5px;" @if ( $terimacheck )	class="btn btn-default pull-right" disabled	@else class="btn btn-primary pull-right" @endif>Edit</button></a>
									<button type="button" style="margin-right: 5px;" id="delete" @if ( $terimacheck )	class="btn btn-default pull-right" disabled	@else class="btn btn-danger pull-right" @endif>Delete</button>
								</div>
							</div>
						</div>
					</div>
					
					<!-- penerimaan TAB -->
					<div class="tab-pane" id="penerimaan_tab">
						<div class="box-body">
							<div class="row">
								<div class="col-md-3">
									<div class="box box-primary">
										<div class="box-header with-border">
											<h3 class="box-title">Penerimaan Detail</h3>
										</div>
										<div class="box-body">
											<div class="form-group">
												{!! Form::label('TerimaCode', 'Terima Code') !!}
												<input type="text" id="TerimaCode" value="{{$pemesanan->TerimaCode}}" class="form-control" readonly>
											</div>
											<div>
												<a href="{{route('penerimaan.show', $pemesanan->idTerima)}}"><button type="button" class="btn btn-success btn-block">View</button></a>
											</div>
											<div class="form-group">
												{!! Form::label('TglTerima', 'Date') !!}
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													{!! Form::text('TglTerima', $pemesanan -> TglTerima, array('class' => 'form-control', 'readonly')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('TransportTerima', 'Biaya Transport') !!}
												{!! Form::text('TransportTerima', 'Rp '. number_format( $pemesanan -> TransportTerima, 2,',', '.' ), array('class' => 'form-control', 'readonly')) !!}
											</div>
											<div class="form-group">
												{!! Form::label('PesanCode', 'Pesan Code') !!}
												{!! Form::text('PesanCode', $pemesanan->PesanCode, array('class' => 'form-control', 'id' => 'PesanCode', 'readonly')) !!}
											</div>
										</div>
										<!-- box-body -->
									</div>
									<!-- box -->
								</div>
								<!-- col -->
								<div class="col-md-9">
									<div class="box box-primary">
										<div class="box-header with-border">
											<h3 class="box-title">Penerimaan Item</h3>
										</div>
										<div class="box-body">
											<table class="table table-bordered table-striped table-responsive">
												<thead>
													<tr>
														<th>Barang</th>
														<th>ICode</th>
														<th width="10%">QTerima</th>
														@if(env('APP_TYPE')=='Jual')
															<th width="30%">Kategori</th>
														@endif
													</tr>
												</thead>
												<tbody>
													@foreach($pemesananlists as $pemesananlist)
													<tr>
														<td>{{ $pemesananlist -> Barang }}</td>
														<td>{{ $pemesananlist -> ICode }}</td>
														<td>{{ $pemesananlist -> QTerima }}</td>
														@if(env('APP_TYPE')=='Jual')
															<td>{{ $pemesananlist -> Type }}</td>
														@endif
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
									<!-- box -->
								</div>
									<!-- col -->
							</div>
							<!-- row -->
							<div class="row">
								<div class="col-md-12">
									<a href="{{route('pemesanan.index')}}"><button type="button" class="btn btn-default pull-left" style="margin-right: 5px;">Back</button></a>
									<a href="{{route('retur.create', 'id='.$pemesanan->idPesan)}}">	<button type="button" style="margin-right: 5px;" @if ( $returcheck || ($qreturcheck->SumQuantity!=$qreturcheck->SumQTerima))	class="btn btn-default pull-right" disabled	@else class="btn btn-warning pull-right" @endif>Retur</button></a>
									<a href="{{route('penerimaan.edit', $pemesanan->idTerima)}}"><button type="button" style="margin-right: 5px;" @if ( $returcheck )	class="btn btn-default pull-right" disabled	@else class="btn btn-primary pull-right" @endif>Edit</button></a>
								</div>
							</div>
						</div>
					</div>
					
					<!-- retur TAB -->
					<div class="tab-pane" id="retur_tab">
						<div class="box-body">
							<div class="row">
								<div class="col-md-3">
									<div class="box box-primary">
										<div class="box-header with-border">
											<h3 class="box-title">Retur Detail</h3>
										</div>
										<div class="box-body">
											<div class="form-group">
												{!! Form::label('ReturCode', 'Retur Code') !!}
												<input type="text" id="ReturCode" value="{{$pemesanan->ReturCode}}" class="form-control" readonly>
											</div>
											<div>
												<a href="{{route('retur.show', $pemesanan->idRetur)}}"><button type="button" class="btn btn-success btn-block">View</button></a>
											</div>
											<div class="form-group">
												{!! Form::label('TglRetur', 'Date') !!}
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													{!! Form::text('TglRetur', $pemesanan -> TglRetur, array('class' => 'form-control', 'readonly')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('TransportRetur', 'Biaya Transport') !!}
												{!! Form::text('TransportRetur', 'Rp '. number_format( $pemesanan -> TransportRetur, 2,',', '.' ), array('class' => 'form-control', 'readonly')) !!}
											</div>
											<div class="form-group">
												{!! Form::label('PesanCode', 'Pesan Code') !!}
												{!! Form::text('PesanCode', $pemesanan->PesanCode, array('class' => 'form-control', 'id' => 'PesanCode', 'readonly')) !!}
											</div>
										</div>
										<!-- box-body -->
									</div>
									<!-- box -->
								</div>
								<!-- col -->
								<div class="col-md-9">
									<div class="box box-primary">
										<div class="box-header with-border">
											<h3 class="box-title">Retur Item</h3>
										</div>
										<div class="box-body">
											<table class="table table-bordered table-striped table-responsive">
												<thead>
													<tr>
														<th>Barang</th>
														<th>ICode</th>
														<th width="10%">QRetur</th>
														@if(env('APP_TYPE')=='Jual')
															<th width="30%">Kategori</th>
														@endif
													</tr>
												</thead>
												<tbody>
													@foreach($pemesananlists as $pemesananlist)
													<tr>
														<td>{{ $pemesananlist -> Barang }}</td>
														<td>{{ $pemesananlist -> ICode }}</td>
														<td>{{ $pemesananlist -> QRetur }}</td>
														@if(env('APP_TYPE')=='Jual')
															<td>{{ $pemesananlist -> Type }}</td>
														@endif
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
									<!-- box -->
								</div>
									<!-- col -->
							</div>
							<!-- row -->
							<div class="row">
								<div class="col-md-12">
									<a href="{{route('pemesanan.index')}}"><button type="button" class="btn btn-default pull-left" style="margin-right: 5px;">Back</button></a>
									<a href="{{route('retur.edit', $pemesanan->idRetur)}}"><button type="button" style="margin-right: 5px;" class="btn btn-primary pull-right">Edit</button></a>
								</div>
							</div>
						</div>
					</div>
					
					<!-- invoice TAB -->
					<div class="tab-pane" id="invoice_tab">
						<div class='form-horizontal'>
							<div class="box-header with-border">
								<h3 class="box-title">Purchase Invoice Detail</h3>
							</div>
							<div class="box-body with-border">
								<div class="col-sm-9">
									<div class="form-group">
										{!! Form::label('Invoice', 'No. Invoice', ['class' => "col-sm-4 control-label"]) !!}
										<div class="col-sm-8">
											{!! Form::text('Invoice', $pemesanan->PurchaseInvoice, array('class' => 'form-control', 'readonly')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('Supplier', 'Supplier', ['class' => "col-sm-4 control-label"]) !!}
										<div class="col-sm-8">
											{!! Form::text('Supplier', $pemesanan->Company, array('class' => 'form-control', 'readonly')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('PesanCode', 'Pesan Code', ['class' => "col-sm-4 control-label"]) !!}
										<div class="col-sm-8">
											{!! Form::text('PesanCode', $pemesanan->PesanCode, array('class' => 'form-control', 'readonly')) !!}
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<a href="{{route('purchaseinvoice.edit', $pemesanan->idInvoice)}}"><button type="button" class="btn btn-success btn-block">View</button></a>
								</div>
								<table id="datatables" class="table table-bordered table-striped table-responsive">
									<thead>
										<tr>
											<th>Type</th>
											<th>Item</th>
											<th>Quantity</th>
											<th>Price/Unit</th>
											<th>Jumlah</th>
										</tr>
									</thead>
									<tbody>
										@foreach($pemesananlists as $pemesananlists)
										<tr>
											<td>{{$pemesananlists->Type}}</td>
											<td>{{$pemesananlists->Barang}}</td>
											<td>{{$pemesananlists->QTerima}}</td>
											<td>Rp {{ number_format($pemesananlists->Amount, 2, ',', '.') }}</td>
											<td>Rp {{ number_format($pemesananlists->QTerima*$pemesananlists->Amount, 2,',','.') }}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								<hr>
								<!-- Total -->
								<div class="form-group">
									{!! Form::label('Total', 'Total', ['class' => "col-sm-2 control-label"]) !!}
									<div class="col-sm-8">
										{!! Form::text('Total', 'Rp '.number_format($Total, 2, ',','.'), array('id' => 'Total', 'class' => 'form-control', 'readonly')) !!}
									</div>
								</div>
								<!-- Termin Input -->
								<div class="form-group ">
									{!! Form::label('TglTerima', 'Tgl Terima Surat', ['class' => "col-sm-2 control-label"]) !!}
									<div class="col-sm-2">
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											{!! Form::text('TglTerima', $pemesanan->TglTerima, ['class' => 'form-control', 'readonly']) !!}
										</div>
									</div>
									{!! Form::label('Termin', 'Termin', ['class' => "col-sm-1 control-label"]) !!}
									<div class="col-sm-2">
										{!! Form::number('Termin', $pemesanan->Termin, array('class' => 'form-control', 'readonly')) !!}
									</div>
									{!! Form::label('DueDate', 'Due Date', ['class' => "col-sm-1 control-label"]) !!}
									<div class="col-sm-2">
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											@if(isset($pemesanan->TglTerima))
												{!! Form::text('DueDate', $duedate, array('class' => 'form-control', 'readonly')) !!}
											@else
												{!! Form::text('DueDate', null, array('class' => 'form-control', 'readonly')) !!}
											@endif
										</div>
									</div>
								</div>
								<!-- Catatan Input -->
								<div class="form-group">
									{!! Form::label('Catatan', 'Catatan', ['class' => "col-sm-2 control-label"]) !!}
									<div class="col-sm-8">
										{!! Form::textarea('Catatan', $pemesanan->Catatan, array('class' => 'form-control', 'readonly', 'rows' => '4')) !!}
									</div>
								</div>
								<!-- Discount & Pembulatan Input -->
								<div class="form-group">
									{!! Form::label('Discount', 'Inv Discount (-)', ['class' => "col-sm-2 control-label"]) !!}
									<div class="col-sm-2">
										<input id="Discount" name="Discount" type="text" value="{{'Rp '.number_format($pemesanan->Discount, 2, ',','.')}}" class="form-control" readonly>
									</div>
									{!! Form::label('Pembulatan', 'Pembulatan (-)', ['class' => "col-sm-1 control-label"]) !!}
									<div class="col-sm-2">
										<input id="Pembulatan" name="Pembulatan" type="text" value="{{'Rp '.number_format($pemesanan->Pembulatan, 2, ',','.')}}" class="form-control" readonly>
									</div>
									{!! Form::label('TransportRetur', 'Transport Retur (-)', ['class' => "col-sm-1 control-label"]) !!}
									<div class="col-sm-2">
										{!! Form::text('TransportRetur', 'Rp '.number_format($pemesanan->TransportRetur, 2, ',','.'), ['class' => 'form-control', 'readonly']) !!}
									</div>
								</div>
								<!-- Grand Total Input -->
								<div class="form-group">
									{!! Form::label('Transport', 'Transport', ['class' => "col-sm-2 control-label"]) !!}
									<div class="col-sm-3">
										{!! Form::text('Transport', 'Rp '.number_format($pemesanan->TransportTerima, 2, ',','.'), ['class' => 'form-control', 'readonly']) !!}
									</div>
									{!! Form::label('GrandTotal', 'Grand Total', ['class' => "col-sm-2 control-label"]) !!}
									<div class="col-sm-3">
										{!! Form::text('GrandTotal', 'Rp '.number_format($GrandTotal, 2, ',','.'), array('class' => 'form-control', 'readonly')) !!}
									</div>
								</div>
								<div class="box-footer">
									<!-- Back Button -->
									<a href="{{route('pemesanan.index')}}"><button type="button" class="btn btn-default">Back</button></a>
									<a href="{{route('invoice.Invj', $pemesanan->id)}}" button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print Invoice</a>
								</div>
								<!-- box-footer -->
							</div>
						</div>
					</div>
					
				</div>
				<!-- /.tab-content -->
		</div>
    <!-- /.tab-custom -->
	</div>
  <!-- col -->
</div>
<!-- row -->
<div class="clearfix"></div>
@stop

<div class="modal fade" id="deletemodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="deleteform" name="deleteform" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal"><h4> Are you sure you want to delete this Pemesanan? (Delete Permanently)</h4></label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger pull-right">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

@section('script')
<script>
//When delete button is clicked
$("#delete").click(function(){
  //Toggle the modal
  $('#deletemodal').modal('toggle');
});

//When delete form is submitted
$("#deleteform").submit(function(event){
  $.post("delete", { "_token": "{{ csrf_token() }}", PesanCode: $("#PesanCode").val() }, function(data){})
  .done(function(data){
		window.location.replace("../pemesanan");
  });
});
</script>
@stop