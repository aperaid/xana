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
          {{$x = 1}}
					@foreach($isisjkirims as $isisjkirim)
						<tr>
              {!! Form::hidden('id', $isisjkirim->id) !!}
              {!! Form::hidden('QSisaKir2', $isisjkirim->QSisaKir, ['id' => 'QSisaKir2'.$x]) !!}
              {!! Form::hidden('IsiSJKir', $isisjkirim->IsiSJKir) !!}
              {!! Form::hidden('Purchase', $isisjkirim->Purchase) !!}
              {!! Form::hidden('QTertanda2', $isisjkirim->QTertanda) !!}
              <td>{!! Form::text('JS', $isisjkirim->JS, ['class' => 'form-control', 'readonly']) !!}</td>
							<td>{!! Form::text('Barang', $isisjkirim->Barang, ['class' => 'form-control', 'readonly']) !!}</td>
              <td>{!! Form::text('Warehouse', $isisjkirim->Warehouse, ['class' => 'form-control', 'readonly']) !!}</td>
              <td>{!! Form::text('QKirim', $isisjkirim->QKirim, ['class' => 'form-control', 'readonly']) !!}</td>
              <td>{!! Form::text('QSisaKir', $isisjkirim->QSisaKir, ['id' => 'QSisaKir'.$x, 'readonly']) !!}</td>
              <td>{!! Form::text('QTertanda', $isisjkirim->QTertanda, array('id' => 'QSisaKir'.$x, 'class' => 'form-control', 'autocomplete' => 'off', 'onkeyup' => 'this.value = minmax(this.value, 0, $isisjkirim->QKirim)', 'onkeyup' => 'sisa()', 'required')) !!}</td>
            </tr>
            {{$x++}}
          @endforeach
					</tbody>
				</table>
			</div>
      <!-- box-body -->
      <div class="box-footer">
        <label>Tanggal Barang Sampai Tujuan/Tanggal Mulai Penghitungan</label>
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          {!! Form::text('Tgl', $Tgl->S, ['id' => 'QSisaKir'.$x, 'class' => 'form-control', 'autocomplete' => 'off', 'required']) !!}
				</div>
				<br>
				<a href="{{route('sjkirim.show', $sjkirim->id)}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
				<button type="submit" name="bt_editsjkirimquantity_submit" id="bt_editsjkirimquantity_submit" class="btn btn-success pull-right">Update</button>
			</div>
      <!-- box-footer -->
    </div>
    <!-- box -->
	</div>
  <!-- col -->
</div>
<!-- row -->
{!! Form::close() !!}
@stop

@section('script')
<script language="javascript">
  function sisa() {
  for(x = 1; x < 11; x++){
    var txtFirstNumberValue = document.getElementById('hd_editsjkirimquantity_QSisaKir2'+x).value;
    var txtSecondNumberValue = document.getElementById('tx_editsjkirimquantity_QTertanda'+x).value;
	var result = parseInt(txtFirstNumberValue) - parseInt(txtSecondNumberValue);
	  if (!isNaN(result)) {
		document.getElementById('tx_editsjkirimquantity_QSisaKir'+x).value = result;
      }
   }
   }
</script>

<script type="text/javascript">
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
var Min = '{{ $sjkirim->Tgl }}';
var Max = '{{ $Tgl->E }}';
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  startDate: Min,
	  endDate: Max,
	  todayHighlight: true,
	  autoclose: true
  }); 
});
</script>
@stop