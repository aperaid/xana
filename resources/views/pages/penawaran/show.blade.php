@extends('layouts.xana.layout')
@section('title')
	View Penawaran
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
				<input type="hidden" name="id" id="id" value="{{$penawaran->id}}">
        <a href="{{route('penawaran.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
        <a href="{{route('penawaran.edit', $penawaran -> id )}}"><button type="button" class="btn btn-primary pull-right">Edit</button></a>
        <button type="button" id="delete" class="btn btn-danger pull-right" style="margin-right: 5px;">Delete</button>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->

<div class="row">
  <div class="col-md-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Penawaran Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Penawaran', 'Penawaran Code') !!}
          <input type="text" id="Penawaran" value="{{$penawaran->Penawaran}}" class="form-control" readonly>
        </div>
        <div class="form-group">
          {!! Form::label('Tgl', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $penawaran -> Tgl, array('class' => 'form-control', 'readonly')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('PCode', 'Project Code') !!}
          {!! Form::text('PCode', $penawaran -> PCode, array('class' => 'form-control', 'readonly')) !!}
        </div>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Penawaran Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-striped table-responsive">
          <thead>
            <tr>
              <th>ICode</th>
              <th>Barang</th>
              <th>J/S</th>
              <th>Quantity</th>
              <th>Price/Unit</th>
            </tr>
          </thead>
          <tbody>
            @foreach($penawarans as $penawaran)
            <tr>
              <td>{{ $penawaran -> ICode }}</td>
              <td>{{ $penawaran -> Barang }}</td>
              <td>{{ $penawaran -> JS }}</td>
              <td>{{ $penawaran -> Quantity }}</td>
              <td>{{ 'Rp '. number_format( $penawaran -> Amount, 2,',', '.' ) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
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
          <label class="text-default" data-toggle="modal"><h4> Are you sure you want to delete this Penawaran? (Delete Permanently)</h4></label>
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
//When delete button is clicked
$("#delete").click(function(){
  //Toggle the modal
  $('#deletemodal').modal('toggle');
});

//When delete form is submitted
$("#deleteform").submit(function(event){
  $.post("delete", { "_token": "{{ csrf_token() }}", id: $("#id").val(), Penawaran: $("#Penawaran").val() }, function(data){})
  .done(function(data){
		window.location.replace("../penawaran");
  });
});
</script>
@stop