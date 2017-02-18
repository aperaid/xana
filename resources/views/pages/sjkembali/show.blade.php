@extends('layouts.xana.layout')
@section('title')
	View SJ Kembali
@stop

@section('content')
{!! Form::open([
  'method' => 'delete',
  'route' => ['sjkembali.destroy', $sjkembali->id]
]) !!}
<section class="invoice">
  <div class="row">
    <div class="col-xs-12">
      <h2 class="page-header">
        <i class="fa fa-globe"></i> SJ Kembali | {{ $sjkembali -> SJKem }}
        <small class="pull-right">Date: {{ $sjkembali -> Tgl }}</small>
      </h2>
    </div>
  </div>
  
  <div class="row invoice-info">
    <div class="col-sm-3 invoice-col">
      Company
      <address>
        <strong>{{ $isisjkembali -> Company }}</strong><br>
        {{ $isisjkembali -> CompAlamat }}<br>
        {{ $isisjkembali -> CompKota }},  {{ $isisjkembali -> CompZip }}<br>
        Phone: {{ $isisjkembali -> CompPhone }}<br>
        Email: {{ $isisjkembali -> CompEmail }}
      </address>
    </div>
    <div class="col-sm-3 invoice-col">
      Project
      <address>
        <strong>{{ $isisjkembali -> Project }}</strong><br>
        {{ $isisjkembali -> ProjAlamat }}<br>
        {{ $isisjkembali -> ProjKota }},  {{ $isisjkembali -> ProjZip }}<br>
				Sales: {{ $isisjkembali -> Sales }}
      </address>
    </div>
    <div class="col-sm-3 invoice-col">
      Contact Person
      <address>
        <strong>{{ $isisjkembali -> Customer }}</strong><br>
        Phone: {{ $isisjkembali -> CustPhone }}<br>
        Email: {{ $isisjkembali -> CustEmail }}
      </address>
    </div>
		<div class="col-sm-3 invoice-col">
      No Polisi
      <address>
        <strong>{{ $sjkembali -> NoPolisi }}</strong><br>
        Sopir: {{ $sjkembali -> Sopir }}<br>
        Kenek: {{ $sjkembali -> Kenek }}<br>
				Periode: {{ $isisjkembali -> Periode }}
      </address>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table id="datatables" class="table table-striped">
        <thead>
          <tr>
            <th>Tanggal Kirim</th>
            <th>Barang</th>
            <th>Warehouse</th>
            <th>Q Pengambilan</th>
            <th>Q Terima</th>
          </tr>
        </thead>
        <tbody>
          @foreach($isisjkembalis as $isisjkembali)
          <tr>
            <td>{{ $isisjkembali->Tgl }}</td>
            <td>{{ $isisjkembali->Barang }}</td>
            <td>{{ $isisjkembali->Warehouse }}</td>
            <td>{{ $isisjkembali->SumQTertanda }}</td>
            <td>{{ $isisjkembali->SumQTerima }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
        
    <div class="box-footer">
      <a href="{{route('sjkembali.index')}}"><button type="button" class="btn btn-default">Back</button></a>
			@if($isisjkembali->SumQTertanda!=$isisjkembali->SumQTerima)
				<a button type="button" class="btn btn-danger hilang">Barang Hilang</a>
			@elseif(count($transaksihilang)!=0)
				<a button type="button" class="btn btn-danger cancel">Cancel Barang Hilang</a>
      @endif
      
      <a href="{{route('sjkembali.qterima', $sjkembali->id)}}"><button type="button" @if($periodecheck == 0 && count($transaksihilang)==0) style="margin-right: 5px" class="btn btn-success pull-right" @else style="margin-right: 5px" class="btn btn-default pull-right" disabled @endif >Quantity Terima</button></a>
      <a href="{{route('sjkembali.edit', $sjkembali->id)}}"><button type="button" @if($qtrimacheck == 0) style="margin-right: 5px" class="btn btn-primary pull-right" @else style="margin-right: 5px" class="btn btn-default pull-right" disabled @endif >Edit Pengembalian</button></a>
      <button type="submit" class="btn btn-danger pull-right" style="margin-right: 5px;" @if($qtrimacheck == 1) disabled @endif onclick="return confirm('Delete SJ Kembali?')">Delete</button>
    </div>
    <!-- box footer -->
  </div>
  <!-- row -->
</section>
<!-- invoice -->
{!! Form::close() !!}

<div class="modal fade" id="hilangmodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="hilangform" name="hilangform">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Barang Hilang</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal"><h4> Penyebab Barang Hilang</h4></label>
					<div class="form-group">
						<input type="hidden" id="sjtype" value='Kembali'>
						<input type="hidden" id="hilangid" value={{$sjkembali->id}}>
						<textarea class="form-control" id="hilangtext" rows="5" placeholder="Barang Hilang"></textarea>
					</div>
					<div class="form-group">
						{!! Form::label('Tgl', 'Lost Date') !!}
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							{!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '31/12/2000', 'required')) !!}
						</div>
					</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger pull-right">Hilang</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="cancelmodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="cancelform" name="cancelform">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Cancel Barang Hilang</h4>
        </div>
        <div class="modal-body">
					@if(count($transaksihilang)!=0)
						<table id="datatables" class="table table-striped">
							<input type="hidden" id="sjtype" value='Kembali'>
							<input type="hidden" id="sjkem" value={{$transaksihilang->first()->SJ}}>
							<thead>
								<tr>
									<th>Barang Hilang</th>
									<th>Quantity Hilang</th>
								</tr>
							</thead>
							<tbody>
								@foreach($transaksihilang as $transaksihilang)
								<tr>
									<td>{{ $transaksihilang->Barang }}</td>
									<td>{{ $transaksihilang->QHilang }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<hr>
						<label class="text-default" data-toggle="modal"><h4> Penyebab Barang Hilang</h4></label>
						<div class="form-group">
							<textarea class="form-control" rows="5" readonly>{{ $transaksihilang->first()->HilangText }}</textarea>
						</div>
					@endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger pull-right">Cancel Hilang</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('script')
<script>
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  todayHighlight: true,
	  autoclose: true,
	  startDate: '{{$sjkembali->Tgl}}',
  }); 
});
//When hilang button is clicked
$(".hilang").click(function(){
	$('#hilangmodal').modal('toggle');
});
//When hilang form is submitted
$("#hilangform").submit(function(event){
  $(".loading").show();
  $.post("../transaksi/hilang", { "_token": "{{ csrf_token() }}", sjtype: $("#sjtype").val(), id: $("#hilangid").val(), HilangText: $("#hilangtext").val(), Tgl: $("#Tgl").val() }, function(data){})
  .done(function(data){
    location.reload();
    $('#hilangmodal').modal('toggle');
  })
  .fail(function(data){
    console.log('fail');
  });
});
//When cancel button is clicked
$(".cancel").click(function(){
	$('#cancelmodal').modal('toggle');
});
//When cancel form is submitted
$("#cancelform").submit(function(event){
  $(".loading").show();
  $.post("../transaksi/cancelhilang", { "_token": "{{ csrf_token() }}", sjtype: $("#sjtype").val(), SJKem: $("#sjkem").val() }, function(data){})
  .done(function(data){
    location.reload();
    $('#cancelmodal').modal('toggle');
  })
  .fail(function(data){
    console.log('fail');
  });
});
</script>
@stop