@extends('layouts.xana.layout')
@section('title')
	View Reference
@stop

@section('content')
{!! Form::open([
  'method' => 'delete',
  'route' => ['reference.destroy', $detail->pocusid]
]) !!}
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
        <li class="active"><a href="#overall_tab" data-toggle="tab">Overall</a></li>
        <li><a href="#po_tab" data-toggle="tab">PO</a></li>
        <li><a href="#sjkirim_tab" data-toggle="tab">SJKirim</a></li>
        <li><a href="#sjkembali_tab" data-toggle="tab">SJKembali</a></li>
        <li><a href="#sewa_tab" data-toggle="tab">Sewa</a></li>
        <li><a href="#jual_tab" data-toggle="tab">Jual</a></li>
        <li><a href="#claim_tab" data-toggle="tab">Claim</a></li>
			</ul>
				<div class="tab-content">
					<!-- OVERALL TAB -->
					<div class="active tab-pane" id="overall_tab">
						<div class="box-body">
							<!-- title row -->
							<div class="row">
								<div class="col-md-12">
									<h2 class="page-header">
										<i class="fa fa-globe"></i> PT. BDN | {{ $detail -> Reference }}
										<small class="pull-right">Date: {{ $detail -> Tgl }}</small>
									</h2>
								</div>
							</div>
							<!-- info row -->
							<div class="row">
								<div class="col-sm-4">
									Company
									<address>
										<strong>{{ $detail -> Company }}</strong><br>
										{{ $detail -> custalamat }}<br>
										{{ $detail -> Kota }},  {{ $detail -> Zip }}<br>
										Phone: {{ $detail -> CompPhone }}<br>
										Email: {{ $detail -> CompEmail }}
									</address>
								</div>
								<div class="col-sm-4">
									Project
									<address>
										<strong>{{ $detail -> Project }}</strong><br>
										{{ $detail -> projalamat }}<br>
										{{ $detail -> Kota }}<br>
									</address>
								</div>
								<div class="col-sm-4">
									Contact Person
									<address>
										<strong>{{ $detail -> Customer }}</strong><br>
										Phone: {{ $detail -> CustPhone }}<br>
										Email: {{ $detail -> CustEmail }}
									</address>
								</div>
							</div>
							<!-- Table row -->
							<div class="row">
								<div class="col-md-12 table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>J/S</th>
												<th>Item Name</th>
												<th>Quantity</th>
												<th>Price</th>
												<th>Progress</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											@foreach( $purchases as $purchase )
											<tr>
												<td>{{ $purchase -> JS }}</td>
												<td>{{ $purchase -> Barang }}</td>
												<td>{{ $purchase -> Quantity }}</td>
												<td>Rp {{ number_format( $purchase -> Amount, 2,',', '.' ) }}</td>
                          @if ( $purchase -> JS == "Sewa" ) <!-- Kalau SEWA -->
                            @if ( $purchase -> QSisaKir == $purchase -> Quantity && $purchase -> QSisaKem == 0 ) <!-- belum dikirim -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-red" style="width:10%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-red">Belum Dikirim</span></td>
                            @elseif (( $purchase -> QSisaKir < $purchase -> Quantity ) && $purchase -> QSisaKir ) != 0 ) <!-- setengah dikirim -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-yellow" style="width:25%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-yellow">Separuh Terkirim</span></td>
                            @elseif ( $purchase -> QSisaKir == 0 && $purchase -> QSisaKem == $purchase -> Quantity ) <!-- pengiriman selesai, dalam proses penyewaan -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-blue" style="width:50%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-blue">Pengiriman Selesai, dalam penyewaan</span></td>
                            @elseif (( $purchase -> QSisaKem < $purchase -> Quantity) && $purchase -> QSisaKem != 0 ) <!-- setengah dikembalikan -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-yellow" style="width:75%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-yellow">Separuh Kembali</span></td>
                            @elseif ( $purchase -> QSisaKem == 0 && $purchase -> QSisaKir == 0 ) <!-- selesai dikembalikan -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-green" style="width:100%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-green">Semua Kembali/Claimed, Transaksi Selesai</span></td>
                            @endif
													@elseif( $purchase -> JS == "Jual" ) <!-- kalau JUAL -->
														@if ( $purchase -> QSisaKir == $purchase -> Quantity ) <!-- belum dikirim -->
															<td>
																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-red" style="width:10%"></div>
																</div>
															</td>
															<td><span class="badge bg-red">Belum Dikirim</span></td>

														@elseif ( $purchase -> QSisaKir < $purchase -> Quantity && $purchase -> QSisaKir != 0 ) <!-- setengah dikirim -->
															<td>
																<div class="progress progress-xs">
																  <div class="progress-bar progress-bar-yellow" style="width:50%"></div>
																</div>
															</td>
															<td><span class="badge bg-yellow">Separuh Terkirim</span></td>

														@elseif ( $purchase -> QSisaKir == 0 ) <!-- pengiriman selesai, dalam proses penyewaan -->
															<td>
																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-green" style="width:100%"></div>
																</div>
															</td>
															<td><span class="badge bg-green">Selesai Dikirim, Penjualan Selesai</span></td>
                            @endif
                          @endif
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<a href="#" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
									<a href="{{route('reference.index')}}"><button type="button" class="btn btn-default pull-left" style="margin-right: 5px;">Back</button></a>
									<a href="{{route('sjkirim.create', $detail->Reference)}}"><button id="SJKirim_button" type="button" style="margin-right: 5px;" @if ( $sjkircheck == 0 )	class="btn btn-default pull-right" disabled	@else class="btn btn-success pull-right"	@endif	>SJ Kirim</button></a>
									<a href="{{route('sjkembali.create', $detail->Reference)}}"><button id="SJKembali_button" type="button" style="margin-right: 5px;"	@if ( $sjkemcheck == 0 ) class="btn btn-default pull-right" disabled @else class="btn btn-warning pull-right"	@endif	>SJ Kembali</button></a>
									<a href="{{route('claim.create', $detail->Reference)}}">	<button id="claim_button" type="button" style="margin-right: 5px;" @if ( $sjkemcheck == 0 ) class="btn btn-default pull-right" disabled @else class="btn btn-info pull-right" @endif	>Claim</button></a>
									<a href="{{route('reference.edit', $detail->pocusid)}}"><button id="edit_button" type="button" style="margin-right: 5px;"	@if ( $pocheck == 1 )	class="btn btn-default pull-right" disabled	@else	class="btn btn-primary pull-right"	@endif >Edit</button></a>
									<button id="delete_button" type="submit" style="margin-right: 5px;"	@if ( $pocheck == 1 )	class="btn btn-default pull-right" disabled	@else	class="btn btn-danger pull-right"	@endif onclick="return confirm('Delete PO Customer?')">Delete</button>
									<a href="{{route('po.create', 'id=' .$detail -> pocusid)}}"><button id="insertPO_button" type="button" style="margin-right: 5px;" class="btn btn-success pull-right">Insert PO</button></a>
								</div>
							</div>
						</div>
					</div>
					
					<!-- PO TAB -->
					<div class="tab-pane" id="po_tab">
						<div class="box-body">
							<table id="tb_po" class="table table-condensed">
								<thead>
									<tr>
										<th>PO Code</th>
										<th>Tgl</th>
										<th width="10%">View</th>
									</tr>
								</thead>
								<tbody>
									@foreach( $pos as $po )
									<tr>
										<td>{{ $po -> POCode }}</td>
										<td>{{ $po -> Tgl }}</td>
										<td><a href="{{route('po.show', $po -> POCode)}}"><button type="button" class="btn btn-primary btn-block btn-sm">View</button></a></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					
					<!-- SJKIRIM TAB -->
					<div class="tab-pane" id="sjkirim_tab">
						<div class="box-body">
							<table class="table table-condensed">
								<tr>
									<th>SJKir</th>
									<th>Tanggal</th>
									<th>Progress</th>
									<th>Status</th>
									<th>View</th>
								</tr>
								@foreach( $sjkirims as $sjkirim )
								<tr>
									<td>{{ $sjkirim -> SJKir }}</td>
									<td>{{ $sjkirim -> Tgl }}</td>
									@if( $sjkirim -> QTertanda != 0 )
										<td>
											<div class="progress progress-xs">
												<div class="progress-bar progress-bar-success" style="width: 100%"></div>
											</div>
										</td>                    
										<td><span class="badge bg-green">Selesai Dikirim</span></td>
									@else
										<td>
											<div class="progress progress-xs">
											  <div class="progress-bar progress-bar-yellow" style="width: 50%"></div>
											</div>
										</td>                    
										<td><span class="badge bg-yellow">Dalam Pengiriman</span></td>
									@endif
									<td><a href="{{route('sjkirim.show', $sjkirim -> SJKir)}}"><button class="btn btn-primary btn-block btn-sm">View</button></a></td>
								</tr>
								@endforeach
							</table>
						</div>
					</div>

					<!-- SJKEMBALI TAB-->
					<div class="tab-pane" id="sjkembali_tab">
						<div class="box-body">
							<table class="table table-condensed">
								<thead>
									<tr>
										<th>SJKem</th>
										<th>Tanggal</th>
										<th>Progress</th>
										<th>Status</th>
										<th>View</th>
									</tr>
								</thead>
								
								@foreach( $sjkembalis as $sjkembali )
								<tr>
									<td>{{ $sjkembali -> SJKem }}</td>
									<td>{{ $sjkembali -> Tgl }}</td>
									@if( $sjkembali -> QTerima != 0 )
										<td>
											<div class="progress progress-xs">
												<div class="progress-bar progress-bar-success" style="width: 100%"></div>
											</div>
										</td>                    
										<td><span class="badge bg-green">Selesai Dikembalikan</span></td>
									@else
										<td>
											<div class="progress progress-xs">
												<div class="progress-bar progress-bar-yellow" style="width: 50%"></div>
											</div>
										</td>
										<td><span class="badge bg-yellow">Dalam Pengambilan</span></td>
									@endif
									<td><a href="{{route('sjkembali.show', $sjkembali -> SJKem)}}"><button class="btn btn-primary btn-block btn-sm">View</button></a></td>
								</tr>
								@endforeach
							</table>
						</div>
					</div>
					
					<!-- Sewa TAB -->
					<div class="tab-pane" id="sewa_tab">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Invoice</th>
									<th>Periode</th>
									<th>End</th>
									<th width="10%">View</th>
									<th width="10%">Extend</th>
								</tr>
							</thead>
							<tbody>
								@foreach( $sewas as $sewa )
									<tr>
										<td>{{ $sewa -> Invoice }}</td>
										<td>{{ $sewa -> Periode }}</td>
										<td>{{ $sewa -> E }}</td>
										<td>
											<a href="{{route('invoice.show', array($sewa -> Reference, $sewa->Invoice, 'Sewa', $sewa -> Periode))}}"><button class="btn btn-primary btn-block btn-sm">Invoice</button></a>
										</td>
										<td>
											@if ( $sewa -> id == $sewa -> maxid )
												<a href="{{route('invoice.show', array($sewa -> Reference, $sewa -> Periode))}}"><button class="btn btn-success btn-block btn-sm">Extend</button></a>
											@else
												<button class="btn btn-default btn-block btn-sm" disabled>Extend</button>
											@endif
										</td>
								@endforeach
							</tbody>
						</table>
					</div>

					<!-- Jual TAB -->
					<div class="tab-pane" id="jual_tab">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Invoice</th>
									<th width="10%">View</th>
								</tr>
							</thead>
							<tbody>
								@foreach( $juals as $jual )
									<tr>
										<td>{{ $jual -> Invoice }}</td>
										<td>
											<a href="{{route('invoice.show', array($jual -> Reference, $jual->Invoice, 'Jual'))}}"><button class="btn btn-primary btn-block btn-sm">Invoice</button></a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<!-- Claim TAB -->
					<div class="tab-pane" id="claim_tab">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No. Invoice</th>
									<th>Periode</th>
									<th>Tanggal Claim</th>
									<th>Project</th>
									<th width="10%">View</th>
									<th width="10%">Batal Claim</th>
								</tr>
							</thead>
							<tbody>
								@foreach( $claims as $claim )
									<tr>
										<td>{{ $claim -> Invoice }}</td>
										<td>{{ $claim -> Periode }}</td>
										<td>{{ $claim -> Tgl }}</td>
										<td>{{ $claim -> Project }}</td>
										<td>
										<button class="btn btn-primary btn-block">Invoice</button></td>
										<td>
                      @if ( $claim -> periodeclaim == $claim -> periodeextend )
												<button class="btn btn-danger btn-block btn-sm">Batal</button>
											@else
												<button class="btn btn-default btn-block btn-sm" disabled>Batal</button>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
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
{!! Form::close() !!}
@stop