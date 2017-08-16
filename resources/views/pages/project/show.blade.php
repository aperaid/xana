@extends('layouts.xana.layout')
@section('title')
	View Project
@stop

@section('content')
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
					<input type="hidden" name="id" id="id" value="{{$project->proid}}">
            {!! Form::label('PCode', 'Project Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              <input type="text" id="PCode" value="{{$project->PCode}}" class="form-control" readonly>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Project', 'Project Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Project', $project->Project, array('class' => 'form-control', 'readonly')) !!}
            </div>
						{!! Form::label('Sales', 'Sales', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              {!! Form::text('Sales', $project->Sales, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('ProjAlamat', 'Project Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('ProjAlamat', $project->ProjAlamat, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('ProjKota', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('ProjKota', $project->ProjKota, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('ProjZip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('ProjZip', $project->ProjZip, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('CCode', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CCode', $project->CCode, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company', 'Company Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Company', $project->Company, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('CompPhone', 'Phone', ['class' => "col-md-1 control-label"]) !!}
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
            {!! Form::label('Customer', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer', $project->Customer, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('CustPhone', 'Phone CP', ['class' => "col-md-1 control-label"]) !!}
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
        <button type="button" style="margin-right: 5px;" id="delete" @if ( $checkproj == 1 || $checkpen == 1 )	class="btn btn-default pull-right" disabled	@else	class="btn btn-danger pull-right"	@endif>Delete</button>
        <a href="{{route('project.edit', $project->proid)}}"><button type="button" class="btn btn-info pull-right">Edit</button></a>
      </div>
      <!-- box footer -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->
@stop

<div class="modal fade" id="deletemodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="deleteform" name="deleteform" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal"><h4> Are you sure you want to delete this Project? (Delete Permanently)</h4></label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger pull-right">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

@section('script')
<script>
$(document).ready(function(){
	//iCheck
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		increaseArea: '20%' // optional
	});
});

//When delete button is clicked
$("#delete").click(function(){
  //Toggle the modal
  $('#deletemodal').modal('toggle');
});

//When delete form is submitted
$("#deleteform").submit(function(event){
  $.post("delete", { "_token": "{{ csrf_token() }}", id: $("#id").val(), PCode: $("#PCode").val() }, function(data){})
  .done(function(data){
		window.location.replace("../project");
  });
});
</script>
@stop