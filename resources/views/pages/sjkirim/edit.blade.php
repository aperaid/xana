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
              <th>Stock</th>
              <th>Q Sisa Kirim</th>
              <th>Q Kirim</th>
              <th>Q Tertanda</th>
            </tr>
          </thead>
          <tbody>
            @foreach($isisjkirims as $isisjkirim)
            <tr>
              {!! Form::hidden('isisjkirimid[]', $isisjkirim->isisjkirimid) !!}
              {!! Form::hidden('transaksiid[]', $isisjkirim->transaksiid) !!}
              {!! Form::hidden('Purchase[]', $isisjkirim->Purchase) !!}
              {!! Form::hidden('IsiSJKir[]', $isisjkirim->IsiSJKir) !!}
              {!! Form::hidden('ICode[]', $isisjkirim->ICode) !!}
              {!! Form::hidden('QKirim2[]', $isisjkirim->QKirim) !!}
              <td hidden>{!! Form::text('Type[]', $isisjkirim->Type, array('class' => 'form-control Type')) !!}</td>
              <td>{!! Form::text('JS[]', $isisjkirim->JS, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('Barang[]', $isisjkirim->Barang, array('class' => 'form-control Barang', 'readonly')) !!}</td>
              <td>{!! Form::select('Warehouse[]', ['Kumbang'=>'Kumbang', 'BulakSereh'=>'Bulak Sereh', 'Legok'=>'Legok', 'CitraGarden'=>'Citra Garden'], $isisjkirim->Warehouse, ['class' => 'form-control Warehouse']) !!}</td>
              <td>{!! Form::number('Stock[]', null, ['class' => 'form-control Stock', 'readonly']) !!}</td>
              <td>{!! Form::number('QSisaKirInsert[]', $isisjkirim->QSisaKirInsert, array('class' => 'form-control', 'readonly')) !!}</td>
              <td><input name="QKirim[]" type="number" class="form-control QKirim" placeholder="1000" autocomplete="off" onkeyup="this.value = minmax(this.value, 0, {{ $isisjkirim->QSisaKirInsert+$isisjkirim->QKirim }})" value="{{ $isisjkirim->QKirim }}" required></td>
              <td>{!! Form::text('QTertanda[]', $isisjkirim->QTertanda, array('class' => 'form-control', 'readonly')) !!}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- box-body -->
      <table id="datatables" class="table table-bordered table-striped table-responsive">
        <thead>
          <tr>
            <th>No Polisi</th>
            <th>Sopir</th>
            <th>Kenek</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{!! Form::text('NoPolisi', $sjkirim->NoPolisi, array('id' => 'NoPolisi', 'class' => 'form-control', 'autocomplete' => 'off', 'onkeyup' => 'capital()')) !!}</td>
            <td>{!! Form::text('Sopir', $sjkirim->Sopir, array('class' => 'form-control', 'autocomplete' => 'off')) !!}</td>
            <td>{!! Form::text('Kenek', $sjkirim->Kenek, array('class' => 'form-control', 'autocomplete' => 'off')) !!}</td>
          </tr>
        </tbody>
      </table>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Keterangan', 'Keterangan', ['class' => "col-sm-1 control-label"]) !!}
          <div class="col-sm-5">
            {!! Form::textarea('Keterangan', $sjkirim->Keterangan, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Keterangan', 'rows' => '3')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('FormMuat', 'Form Muat', ['class' => "col-sm-1 control-label"]) !!}
          <div class="col-sm-5">
            {!! Form::textarea('FormMuat', $sjkirim->FormMuat, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Form Muat', 'rows' => '3')) !!}
          </div>
        </div>
      </div>
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

<script>
  $(document).on('click autocompletechange mouseenter mouseleave', '.QKirim, .Warehouse', function(){
    var this2 = this;
    $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
    .done(function(data){
      result = $.parseJSON(data);
      if($(this2).closest('tr').find(".Warehouse").val() == 'Kumbang'){
        var jumlah = result.Kumbang
      }else if($(this2).closest('tr').find(".Warehouse").val() == 'BulakSereh'){
        var jumlah = result.BulakSereh
      }else if($(this2).closest('tr').find(".Warehouse").val() == 'Legok'){
        var jumlah = result.Legok
      }else if($(this2).closest('tr').find(".Warehouse").val() == 'CitraGarden'){
        var jumlah = result.CitraGarden
      }
      $(this2).closest('tr').find(".Stock").val(jumlah);
    })
    .fail(function(data){
      if( data.status === 500 ) {
        console.log("Barang tak ditemukan");
      }
    });
  });
  
  $(document).on('keyup', '.QKirim, .Warehouse', function(e){
      var this2 = this;
      if(e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40){
        $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
        .done(function(data){
          result = $.parseJSON(data);
          if($(this2).closest('tr').find(".Warehouse").val() == 'Kumbang'){
            var jumlah = result.Kumbang
          }else if($(this2).closest('tr').find(".Warehouse").val() == 'BulakSereh'){
            var jumlah = result.BulakSereh
          }else if($(this2).closest('tr').find(".Warehouse").val() == 'Legok'){
            var jumlah = result.Legok
          }else if($(this2).closest('tr').find(".Warehouse").val() == 'CitraGarden'){
            var jumlah = result.CitraGarden
          }
          $(this2).closest('tr').find(".Stock").val(jumlah);
        })
        .fail(function(data){
          if( data.status === 500 ) {
            console.log("Barang tak ditemukan");
          }
        });
      }
    });
</script>
@stop