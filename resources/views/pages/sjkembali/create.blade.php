@extends('layouts.xana.layout')
@section('title')
	Create SJKembali
@stop

@section('content')
{!! Form::open([
  'route' => ['sjkembali.create2', $reference->id]
]) !!}
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">SJ Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('SJKem', 'No. Surat Jalan') !!}
          {!! Form::text('SJKem', str_pad($sjkembali->maxid+1, 3, "0", STR_PAD_LEFT).'/SI/'.date("mY"), array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tgl', 'Return Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Reference', 'Reference Code') !!}
          {!! Form::text('Reference', $reference->Reference, array('class' => 'form-control', 'readonly')) !!}
          <p class="help-block">Enter the beginning of the Reference Code, then pick from the dropdown</p>
        </div>
      </div>
      <div class="box-footer">
        <a href="{{route('reference.show', $reference->id)}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        {!! Form::submit('Next',  array('class' => 'btn btn-info pull-right')) !!}
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
    startDate: Min,
    endDate: Max,
	  todayHighlight: true,
	  autoclose: true,
  }); 
}); 
</script>
@stop