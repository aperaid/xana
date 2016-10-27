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
        {!! Form::hidden('id', $reference->id+1) !!}
        <div class="form-group">
          {!! Form::label('Reference', 'Reference') !!}
          {!! Form::text('Reference', str_pad($reference->id+1, 5, "0", STR_PAD_LEFT).'/'.date("dmy"), array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Date', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', null, array('class' => 'form-control pull-right date', 'id' => 'Tgl', 'required')) !!}
          </div>
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
  $(document).ready(function() {
    src = "{{ route('searchajax') }}";
      $("#CCode").autocomplete({
        source: function(request, response) {
          $.ajax({
            url: src,
            dataType: "json",
            data: {
              term : request.term
            },
            success: function(data) {
              response(data);
               
            }
          });
        },
        min_length: 3,
    });
  });
</script>
@stop