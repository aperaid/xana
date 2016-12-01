@extends('layouts.xana.layout')
@section('title')
	Create PO
@stop

@section('content')
	{!! Form::open([
  'route' => ['po.create3', $id]
  ]) !!}
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Penawaran Code', 'Penawaran Code') !!}
          {!! Form::text('Penawaran', null, array('id' => 'Penawaran', 'class' => 'form-control', 'required')) !!}
        </div>
        <div class="box-footer">
          <a href="{{route('reference.show', $id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
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
  $(function() {
    var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletepenawaran.php");?>;
    $( "#Penawaran" ).autocomplete({
      source: availableTags,
      autoFocus: true
    });
  });
</script>
@stop