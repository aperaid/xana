@extends('layouts.xana.layout')
@section('title')
	Create Project
@stop

@section('content')
{!! Form::open([
  'route' => 'project.store'
]) !!}
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Project Detail</h3>
      </div>
      <!-- box-header -->
      <div class="form-horizontal">
        <div class="box-body">
          {!! Form::hidden('id', $project->id+1) !!}
          <div class="form-group">
            {!! Form::label('Project Code', 'Project Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('PCode', null, array('class' => 'form-control', 'id' => 'PCode', 'placeholder' => 'ABC01', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Project Name', 'Project Name', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
              {!! Form::text('Project', null, array('class' => 'form-control', 'id' => 'Project', 'placeholder' => 'Project Name', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Project Address', 'Project Address', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
              {!! Form::text('ProjAlamat', null, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
              {!! Form::text('CCode', null, array('class' => 'form-control', 'id' => 'CCode', 'placeholder' => 'Company Code', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
            </div>
          </div>
        </div>
        <!-- box body -->
      </div>
      <!-- form-horizontal -->
      <div class="box-footer">
        {!! Form::submit('Create',  array('class' => 'btn btn-info pull-right')) !!}
        <a href="{{route('project.index')}}"><button type="button" class="btn btn-default pull-Left">Cancel</button></a>
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
  function capital() {
    var x = document.getElementById("PCode");
    x.value = x.value.toUpperCase();
    var x = document.getElementById("Project");
    x.value = x.value.toUpperCase();
    var x = document.getElementById("CCode");
    x.value = x.value.toUpperCase();
  }
</script>
<script>
  $(function() {
    var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompleteccode.php");?>;
    $( "#tx_insertproject_CCode" ).autocomplete({
      source: availableTags,
      autoFocus: true
    });
  });
</script>
@stop