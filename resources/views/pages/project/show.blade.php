@extends('layouts.xana.layout')
@section('title')
	View Project
@stop

@section('content')
{!! Form::open([
  'method' => 'delete',
  'route' => ['project.destroy', $project->proid]
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
              {!! Form::text('Project', $project->Project, array('class' => 'form-control', 'readonly')) !!}
            </div>
						{!! Form::label('Sales', 'Sales', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              {!! Form::text('Sales', $project->Sales, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Project Address', 'Project Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('ProjAlamat', $project->ProjAlamat, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('City', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('ProjKota', $project->ProjKota, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('Zip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('ProjZip', $project->ProjZip, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CCode', $project->CCode, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company Name', 'Company Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Company', $project->Company, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('Phone', 'Phone', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CompPhone', $project->CompPhone, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Contact Person', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer', $project->Customer, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('Phone CP', 'Phone CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CustPhone', $project->CustPhone, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
        </div>
        <!-- box body -->
      </div>
      <!-- form-horizontal -->
      <div class="box-footer">
        <a href="{{route('project.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
        <button type="submit" style="margin-right: 5px;" class="btn btn-danger pull-left" onclick="return confirm('Delete Project?')">Delete</button>
        <a href="{{route('project.edit', $project->proid)}}"><button type="button" class="btn btn-info pull-right">Edit</button></a>
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