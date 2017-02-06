@extends('layouts.xana.layout')
@section('title')
	Create Claim
@stop

@section('content')
{!! Form::open([
  'route' => 'transaksi.updateclaimcreate'
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <table id="datatables" class="table table-bordered table-striped table-responsive">
          <thead>
            <tr>
              <th>Barang</th>
              <th>Quantity Ditempat</th>
              <th>Quantity Claim</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody>
            {!! Form::hidden('invoiceid', $invoice->maxid+1) !!}
            @foreach($isisjkirims as $key => $isisjkirim)
            <tr>
              {!! Form::hidden('id[]', $isisjkirim->id) !!}
              {!! Form::hidden('Periode', $isisjkirim->Periode) !!}
              {!! Form::hidden('claim[]', $claim->maxid+$key+1) !!}
              {!! Form::hidden('Purchase[]', $isisjkirim->Purchase) !!}
              {!! Form::hidden('IsiSJKir[]', $isisjkirim->IsiSJKir) !!}
              <td>{!! Form::text('Barang[]', $isisjkirim->Barang, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('QSisaKem[]', $isisjkirim->SumQSisaKem, array('class' => 'form-control', 'readonly')) !!}</td>
              <td><input name="QClaim[]" type="number" class="form-control" autocomplete="off" onkeyup="this.value = minmax(this.value, 0, {{ $isisjkirim->SumQSisaKem }})" value="0" required></td>
              <td>{!! Form::text('Amount[]', 'Rp ' . number_format( $isisjkirim -> JualPrice, 0,',', '.' ), array('class' => 'form-control Amount', 'autocomplete' => 'off', 'required')) !!}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="box-footer">
        <table class="table table-hover table-bordered">
          <thead>
            <th>PPN</th>
          </thead>
          <tbody>
            <tr>
              <td>{!! Form::hidden('PPN', 0) !!}{!! Form::checkbox('PPN', 1, null, array('class' => 'minimal')) !!}</td>
            </tr>
    			</tbody>
        </table>
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
        <a href="{{route('transaksi.claimcreate', $id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
      </div>
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->
{!! Form::close() !!}
@stop

@section('script')
<script>
function minmax(value, min, max) 
{
	if(parseInt(value) < min || isNaN(value)) 
    return 0; 
  if(parseInt(value) > max) 
    return parseInt(max); 
  else return value;
}

$(document).ready(function(){
	//Mask Price
	$(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	//iCheck
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		increaseArea: '20%' // optional
	});
});
</script>
@stop