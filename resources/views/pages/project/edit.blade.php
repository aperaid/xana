@extends('layouts.xana.layout')
@section('title')
	Edit Project
@stop

@section('content')
{!! Form::model($project, [
  'method' => 'patch',
  'route' => ['project.update', $project->id]
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
          <div class="form-group">
            {!! Form::label('Project Code', 'Project Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('PCode', $project->PCode, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Project Name', 'Project Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Project', $project->Project, array('class' => 'form-control', 'id' => 'Project', 'placeholder' => 'Project Name', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Project Address', 'Project Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('ProjAlamat', $project->ProjAlamat, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota', 'autocomplete' => 'off', 'onKeyUp' => 'capital()')) !!}
            </div>
            {!! Form::label('City', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('ProjKota', $project->ProjKota, array('class' => 'form-control', 'placeholder' => 'Jakarta', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('Zip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::number('ProjZip', $project->ProjZip, array('class' => 'form-control', 'placeholder' => '10203', 'autocomplete' => 'off')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CCode', $project->CCode, array('class' => 'form-control', 'id' => 'CCode', 'placeholder' => 'COM01', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
            </div>
          </div>
        </div>
        <!-- box body -->
      </div>
      <!-- form-horizontal -->
      <div class="box-footer">
        <a href="{{route('project.show', $project->id)}}"><button type="button" class="btn btn-default pull-left">cancel</button></a>
        {!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
      </div>
      <!-- footer -->
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
    var x = document.getElementById("Project");
    x.value = x.value.toUpperCase();
    var x = document.getElementById("CCode");
    x.value = x.value.toUpperCase();
  }
</script>
<script>
  $(function() {
    var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompleteccode.php");?>;
    $( "#CCode" ).autocomplete({
      source: availableTags,
      autoFocus: true
    });
  });
</script>
@stop