@extends('layouts.xana.layout')
@section('title')
	Create Reference
@stop

@section('content')
{!! Form::open([
  'route' => 'reference.store'
]) !!}
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">PO Detail</h3>
			</div>
      <!-- box header -->
      <div class="box-body">
        {!! Form::hidden('id', $reference->maxid+1) !!}
        <div class="form-group">
          {!! Form::label('Reference', 'Reference') !!}
          {!! Form::text('Reference', str_pad($reference->maxid+1, 5, "0", STR_PAD_LEFT).'/'.date("dmy"), array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Date', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Transport', 'Transport') !!}
          {!! Form::text('Transport', null, array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100.000', 'required')) !!}
        </div>
        <div class="form-group">
        @if(Auth::user()->access == 'Admin')
          {!! Form::hidden('PPNT', 0) !!}
          {!! Form::checkbox('PPNT', 1, null, ['class' => 'minimal']) !!}
          {!! Form::label('Transport included in PPN', 'Transport included in PPN') !!}
        @else
        @endif
        </div>
        <div class="form-group">
          {!! Form::label('Project Code', 'Project Code') !!}
          {!! Form::text('PCode', null, array('class' => 'form-control', 'id' => 'PCode', 'placeholder' => 'ABC01', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
          <p class="help-block">Enter the beginning of the Project Code, then pick from the dropdown</p>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
        {!! Form::submit('Create',  array('class' => 'btn btn-info pull-right')) !!}
        <a href="{{route('reference.index')}}"><button type="button" class="btn btn-default pull-Left">Cancel</button></a>
      </div>
      <!-- box footer -->
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
  $(function() {
    $('#Tgl').datepicker({
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true,
      startDate: '-7d',
      endDate: '+7d'
    }); 
  }); 
</script>
<script>
  function capital() {
    var x = document.getElementById("PCode");
    x.value = x.value.toUpperCase();
  }
</script>
<script>
  $(function() {
    var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletepcode.php");?>;
    $( "#PCode" ).autocomplete({
      source: availableTags,
      autoFocus: true
    });
  });
</script>
<script>
  $(document).ready(function(){
		//Mask Transport
		$("#Transport").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
</script>
@stop