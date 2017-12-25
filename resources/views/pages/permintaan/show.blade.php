@extends('layouts.xana.layout')
@section('title')
	View Permintaan
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('permintaan.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
        <a href="{{route('permintaan.edit', $permintaan -> id )}}"><button type="button" class="btn btn-primary pull-right">Edit</button></a>
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
        <h3 class="box-title">Permintaan Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('MintaCode', 'Permintaan Code') !!}
          <input type="text" id="MintaCode" value="{{$permintaan->MintaCode}}" class="form-control" readonly>
        </div>
        <div class="form-group">
          {!! Form::label('Tgl', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $permintaan -> Tgl, array('class' => 'form-control', 'readonly')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('SCode', 'Supplier Code') !!}
          {!! Form::text('SCode', $permintaan -> SCode, array('class' => 'form-control', 'readonly')) !!}
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
        <h3 class="box-title">Permintaan Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-striped table-responsive">
          <thead>
            <tr>
              <th>Barang</th>
              <th width="20%">ICode</th>
							<th width="5%">Quantity</th>
							<th width="15%">Price/Unit</th>
							@if(env('APP_TYPE')=='Jual')
								<th width="20%">Kategori</th>
							@endif
            </tr>
          </thead>
          <tbody>
            @foreach($permintaanlists as $permintaanlist)
            <tr>
              <td>{{ $permintaanlist -> Barang }}</td>
              <td>{{ $permintaanlist -> ICode }}</td>
              <td>{{ $permintaanlist -> Quantity }}</td>
              <td>{{ 'Rp '. number_format( $permintaanlist -> Amount, 2,',', '.' ) }}</td>
							@if(env('APP_TYPE')=='Jual')
								<td>{{ $permintaanlist -> Type }}</td>
							@endif
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
          <label class="text-default" data-toggle="modal"><h4> Are you sure you want to delete this Permintaan? (Delete Permanently)</h4></label>
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
  $.post("delete", { "_token": "{{ csrf_token() }}", MintaCode: $("#MintaCode").val() }, function(data){})
  .done(function(data){
		window.location.replace("../permintaan");
  });
});
</script>
@stop