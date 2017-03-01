@extends('layouts.xana.layout')
@section('title')
	Create Claim
@stop

@section('content')
	{!! Form::open([
  'route' => ['transaksi.claimcreate2', $reference->id]
  ]) !!}
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Transaksi Claim Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Tgl', 'Claim Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}
          </div>
        </div>
				<div class="form-group">
					{!! Form::label('Discount', 'Discount(%)') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::number('Discount', null, array('id' => 'Discount', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '15')) !!}
          </div>
				</div>
        <div class="form-group">
          {!! Form::label('Reference', 'Reference Code') !!}
          {!! Form::text('Reference', $reference->Reference, array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="box-footer">
          <a href="{{route('reference.show', $reference->id)}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
          {!! Form::submit('Next',  array('class' => 'btn btn-primary pull-right')) !!}
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

@section('script')
<script>
var Min = '{{ $TglMin->S }}';
var Max = '{{ $TglMax->E }}';
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  todayHighlight: true,
	  autoclose: true,
	  startDate: Min,
	  endDate: Max,
  }); 
});

$(document).ready(function(){
	//Mask Discount
	$(document).on('keyup', '#Discount', function(){
	if(parseInt($(this).val()) > 100)
		 $(this).val(100);
	else if(parseInt($(this).val()) < 0)
		$(this).val(0);
	});
});
</script>
@stop