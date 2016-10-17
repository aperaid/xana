@extends('layouts.xana.layout')
@section('title')
	View Reference
@stop

@section('content')
	{!! Form::open([
	'method' => 'delete',
	'route' => ['reference.destroy', $detail->id]
	]) !!}
	
<section class="content">
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
										<small class="pull-right">Date: {{ $detail -> Tgl  }}</small>
									</h2>
								</div>
							</div>
							<!-- info row -->
							<div class="row">
								<div class="col-sm-4">
									Company
									<address>
										<strong>{{ $detail -> Company  }}</strong><br>
										{{ $detail -> custalamat  }}<br>
										{{ $detail -> Kota  }},  {{ $detail -> Zip  }}<br>
										Phone: {{ $detail -> CompPhone  }}<br>
										Email: {{ $detail -> CompEmail  }}
									</address>
								</div>
								<div class="col-sm-4">
									Project
									<address>
										<strong>{{ $detail -> Project  }}</strong><br>
										{{ $detail -> projalamat  }}<br>
										{{ $detail -> Kota  }}<br>
									</address>
								</div>
								<div class="col-sm-4">
									Contact Person
									<address>
										<strong>{{ $detail -> Customer  }}</strong><br>
										Phone: {{ $detail -> CustPhone  }}<br>
										Email: {{ $detail -> CustEmail  }}
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
											@foreach($purchases as $purchase)
											<tr>
												<td>{{ $purchase -> JS  }}</td>
												<td>{{ $purchase -> Barang  }}</td>
												<td>{{ $purchase -> Quantity  }}</td>
												<td>Rp {{ number_format($purchase -> Amount, 2,',', '.') }}</td>
                          @if ($purchase -> JS == "Sewa") <!-- Kalau SEWA -->
                            @if ($purchase -> QSisaKir == $purchase -> Quantity && $purchase -> QSisaKem == 0) <!-- belum dikirim -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-red" style="width:10%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-red">Belum Dikirim</span></td>
                            @elseif (($purchase -> QSisaKir < $purchase -> Quantity) && $purchase -> QSisaKir) != 0) <!-- setengah dikirim -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-yellow" style="width:25%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-yellow">Separuh Terkirim</span></td>
                            @elseif ($purchase -> QSisaKir == 0 && $purchase -> QSisaKem == $purchase -> Quantity) <!-- pengiriman selesai, dalam proses penyewaan -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-blue" style="width:50%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-blue">Pengiriman Selesai, dalam penyewaan</span></td>
                            @elseif (($purchase -> QSisaKem < $purchase -> Quantity) && $purchase -> QSisaKem != 0) <!-- setengah dikembalikan -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-yellow" style="width:75%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-yellow">Separuh Kembali</span></td>
                            @elseif ($purchase -> QSisaKem == 0 && $purchase -> QSisaKir == 0) <!-- selesai dikembalikan -->
                              <td>
                                <div class="progress progress-xs">
                                  <div class="progress-bar progress-bar-green" style="width:100%"></div>
                                </div>
                              </td>
                              <td><span class="badge bg-green">Semua Kembali/Claimed, Transaksi Selesai</span></td>
                            @endif
													@elseif($purchase -> JS == "Jual") <!-- kalau JUAL -->
														@if ($purchase -> QSisaKir == $purchase -> Quantity) <!-- belum dikirim -->
															<td>
																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-red" style="width:10%"></div>
																</div>
															</td>
															<td><span class="badge bg-red">Belum Dikirim</span></td>

														@elseif ($purchase -> QSisaKir < $purchase -> Quantity && $purchase -> QSisaKir != 0) <!-- setengah dikirim -->
															<td>
																<div class="progress progress-xs">
																  <div class="progress-bar progress-bar-yellow" style="width:50%"></div>
																</div>
															</td>
															<td><span class="badge bg-yellow">Separuh Terkirim</span></td>

														@elseif ($purchase -> QSisaKir == 0) <!-- pengiriman selesai, dalam proses penyewaan -->
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
									<a href="{{route('sjkirim.create', $detail->Reference)}}"><button id="SJKirim_button" type="button" style="margin-right: 5px;" @if ($sjkircheck == 0)	class="btn btn-default pull-right" disabled	@else class="btn btn-success pull-right"	@endif	>SJ Kirim</button></a>
									<a href="{{route('sjkembali.create', $detail->Reference)}}"><button id="SJKembali_button" type="button" style="margin-right: 5px;"	@if ($sjkemcheck == 0) class="btn btn-default pull-right" disabled @else class="btn btn-warning pull-right"	@endif	>SJ Kembali</button></a>
									<a href="{{route('claim.create', $detail->Reference)}}">	<button id="claim_button" type="button" style="margin-right: 5px;" @if ($sjkemcheck == 0) class="btn btn-default pull-right" disabled @else class="btn btn-info pull-right" @endif	>Claim</button></a>
									<a href="{{route('reference.edit', $detail->id)}}"><button id="edit_button" type="button" style="margin-right: 5px;"	@if ($pocheck == 1)	class="btn btn-default pull-right" disabled	@else	class="btn btn-primary pull-right"	@endif >Edit</button></a>
									<button id="delete_button" type="button" style="margin-right: 5px;"	@if ($pocheck == 1)	class="btn btn-default pull-right" disabled	@else	class="btn btn-danger pull-right"	@endif onclick="return confirm('Delete PO Customer?')">Delete</button></a>
									<a href="{{route('reference.create')}}"><button id="insertPO_button" type="button" style="margin-right: 5px;" class="btn btn-success pull-right">Insert PO</button></a>
								</div>
							</div>
						</div>
					</div>
					
					
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->
{!! Form::close() !!}
@stop