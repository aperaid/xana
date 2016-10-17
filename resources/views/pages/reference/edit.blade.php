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
      <div class="form-horizontal">
        <div class="box-body">
          <div class="form-group">
            {!! Form::label('Project Code', 'Project Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('PCode', $project->PCode, array('class' => 'form-control', 'id' => 'PCode', 'placeholder' => 'Project Code', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
            </div>
          </div>
          
          <div class="form-group">
            {!! Form::label('Project Name', 'Project Name', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
              {!! Form::text('Project', $project->Project, array('class' => 'form-control', 'id' => 'Project', 'placeholder' => 'Project Name', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'required')) !!}
            </div>
          </div>
          
          <div class="form-group">
            {!! Form::label('Project Address', 'Project Address', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
              {!! Form::text('Alamat', $project->Alamat, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'required')) !!}
            </div>
          </div>
          
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
              {!! Form::text('CCode', $project->CCode, array('class' => 'form-control', 'id' => 'CCode', 'placeholder' => 'Company Code', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
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
{!! Form::close() !!}

</div>
<!-- col -->

</div>
<!-- row -->

@stop

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