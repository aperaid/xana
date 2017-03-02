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
              <th>Price/Unit</th>
            </tr>
          </thead>
          <tbody>
            {!! Form::hidden('invoiceid', $invoice->maxid+1) !!}
						<!--{!! Form::hidden('exchangeid[]', 0) !!}-->
            @foreach($isisjkirims as $key => $isisjkirim)
            <tr>
              {!! Form::hidden('id[]', $isisjkirim->id) !!}
              {!! Form::hidden('Periode', $isisjkirim->Periode) !!}
              {!! Form::hidden('claim[]', $claim->maxid+$key+1) !!}
              {!! Form::hidden('Purchase[]', $isisjkirim->Purchase) !!}
              {!! Form::hidden('IsiSJKir[]', $isisjkirim->IsiSJKir) !!}
              <td>{!! Form::text('Barang[]', $isisjkirim->Barang, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('QSisaKem[]', $isisjkirim->SumQSisaKem, array('class' => 'form-control', 'readonly')) !!}</td>
							<td><input name="QClaim[]" type="number" class="form-control QClaim" placeholder="1000" autocomplete="off" onkeyup="this.value = minmax(this.value, 1, {{ $isisjkirim->SumQSisaKem }})" required></td>
              <td>{!! Form::text('Amount[]', 'Rp ' . number_format( $isisjkirim -> JualPrice, 0,',', '.' ), array('class' => 'form-control Amount', 'autocomplete' => 'off', 'required')) !!}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
			<div class="box-body">
        <table class="table table-hover table-bordered" id="customFields">
          <thead>
            <th width="1%"><a href="javascript:void(0);" id="addCF" class=" glyphicon glyphicon-plus"></a></th>
            <th>Barang Exchange</th>
            <th>QExchange</th>
            <th>Price/Unit</th>
          </thead>
        </table>
      </div>
      <div class="box-footer">
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
$(document).ready(function(){
	var max_fields      = 10; //maximum input boxes allowed
	
	var x = 0; //initial text box count
	var y = {{$last_exchangeid}};
	//Every time Add CF button is clicked:
	$("#addCF").click(function(){
		if(x < max_fields){ //max input box allowed
			x++; //text box count increment
			y++;
			$("#customFields").append('<tr><td align="center"><a href="javascript:void(0);" class="remCF glyphicon glyphicon-remove"></a></td><td hidden><input type="text" name="exchangeid[]" id="exchangeid" value="'+ y +'"></td><td>{!! Form::text('BExchange[]', null, ['class' => 'form-control BExchange', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::number('QExchange[]', null, ['class' => 'form-control QExchange', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::text('PExchange[]', null, ['class' => 'form-control PExchange', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
		
			$(".PExchange").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});

			$(document).on('keyup', '.QExchange', function(){
				if(parseInt($(this).closest('tr').find(".QExchange").val()) < 1)
					$(this).closest('tr').find(".QExchange").val(1);
			});
		}
	});
	
	$("#customFields").on('click','.remCF',function(){
		$(this).parent().parent().remove();
		x--;
		if($(this).closest('tr').find("#penawaranid").val() < y)
			y-2;
		else
			y--;
	});	
});
	
function minmax(value, min, max) 
{
	if(parseInt(value) < min || isNaN(value)) 
    return 1; 
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