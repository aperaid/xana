@extends('layouts.xana.layout')
@section('title')
	Edit SJ Kirim
@stop

@section('content')
{!! Form::model($sjkirim, [
  'method' => 'patch',
  'route' => ['sjkirim.update', $sjkirim->id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body no-padding">
        <table id="datatables" class="table table-bordered">
          <thead>
            <tr>
              <th>J/S</th>
              <th>Barang</th>
              <th>Warehouse</th>
              <th>Q Kirim</th>
              <th>Q Tertanda</th>
            </tr>
          </thead>
          <tbody>
            @foreach($isisjkirims as $isisjkirim)
            <tr>
              {!! Form::hidden('id', $isisjkirim->id) !!}
              {!! Form::hidden('Purchase', $isisjkirim->Purchase) !!}
              {!! Form::hidden('IsiSJKir', $isisjkirim->IsiSJKir) !!}
              <td>{!! Form::text('JS', $isisjkirim->JS, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('Barang', $isisjkirim->Barang, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('Warehouse', $isisjkirim->Warehouse, array('class' => 'form-control', 'autocomplete' => 'off')) !!}</td>
              <td>{!! Form::number('QKirim', $isisjkirim->QKirim, array('class' => 'form-control', 'autocomplete' => 'off', 'onkeyup' => 'this.value = minmax(this.value, 0, $isisjkirim->Quantity)', 'required')) !!}</td>
              <td>{!! Form::text('QTertanda', $isisjkirim->QTertanda, array('class' => 'form-control', 'readonly')) !!}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- box-body -->
      <div class="box-footer">
        {!! Form::label('Send Date', 'Send Date', ['class' => "control-label"]) !!}
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          {!! Form::text('Tgl', $sjkirim->Tgl, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}
        </div>
        <br>
        <a href="{{route('sjkirim.show', $sjkirim->id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
        {!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
      </div>
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
{!! Form::close() !!}
@stop

@section('script')
<script>
function minmax(value, min, max) 
{
	if(parseInt(value) < min || isNaN(value)) 
    return 0; 
  if(parseInt(value) > max) 
    return parseInt(max); 
  else return value;
}
</script>

<script>
var Min = '{{ $TglMin->Tgl }}';
$(function() {
  $('#Tgl').datepicker({
  format: "dd/mm/yyyy",
  startDate: Min,
  todayHighlight: true,
  autoclose: true
  }); 
}); 
</script>
@stop