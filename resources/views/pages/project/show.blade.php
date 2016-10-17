@extends('layouts.xana.layout')
@section('title')
	View Project
@stop

@section('content')
	{!! Form::open([
	'method' => 'delete',
	'route' => ['project.destroy', $project->id]
	]) !!}
	
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="form-horizontal">
        <div class="box-body">
          <div class="form-group">
            {!! Form::label('Project Code', 'Project Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('PCode', $project->PCode, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('Project Name', 'Project Name', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('Project', $project->Project, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('Project Address', 'Project Address', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('Alamat', $project->Alamat, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('CCode', $project->CCode, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          
          <div class="form-group">
            {!! Form::label('Company Name', 'Company Name', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('Company', $project->Company, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('Telp', 'Telp', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-3">
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
            {!! Form::label('CP', 'CP', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer', $project->Customer, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('Telp', 'Telp', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-3">
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
        {!! Form::submit('Delete',  array('class' => 'btn btn-danger pull-left')) !!}
        <a href="{{route('project.edit', $project->id)}}"><button type="button" class="btn btn-info pull-right">Edit</button></a>
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