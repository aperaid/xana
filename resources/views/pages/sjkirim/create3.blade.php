@extends('layouts.xana.layout')
@section('title')
	Create SJKirim
@stop

@section('content')
{!! Form::open([
  'route' => 'sjkirim.store'
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <table id="datatables" class="table table-bordered table-striped table-responsive">
          <thead>
            <tr>
              <th>J/S</th>
              <th>Barang</th>
              <th>Warehouse</th>
              <th>Stock</th>
              <th>Q Sisa Kirim</th>
              <th>Q Kirim</th>
            </tr>
          </thead>
          <tbody>
            {!! Form::hidden('sjkirimid', $last_sjkirim+1) !!}
            {!! Form::hidden('tglE', $tglE) !!}
            {!! Form::hidden('Periode', $periode) !!}
            @foreach($transaksis as $key => $transaksi)
            <tr>
              {!! Form::hidden('id[]', $transaksi->id) !!}
              {!! Form::hidden('isisjkirimid[]', $last_isisjkirim+$key+1) !!}
              {!! Form::hidden('IsiSJKir[]', $maxisisjkir+$key+1) !!}
              {!! Form::hidden('periodeid[]', $maxperiode+$key+1) !!}
              {!! Form::hidden('Reference[]', $transaksi->Reference) !!}
              {!! Form::hidden('Purchase[]', $transaksi->Purchase) !!}
              {!! Form::hidden('ICode[]', $transaksi->ICode) !!}
              <td hidden>{!! Form::text('Type[]', $transaksi->Type, array('class' => 'form-control Type')) !!}</td>
              <td>{!! Form::text('JS[]', $transaksi->JS, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('Barang[]', $transaksi->Barang, array('class' => 'form-control Barang', 'readonly')) !!}</td>
              <td>{!! Form::select('Warehouse[]', ['Kumbang'=>'Kumbang', 'BulakSereh'=>'Bulak Sereh', 'Legok'=>'Legok', 'CitraGarden'=>'Citra Garden'], null, ['class' => 'form-control Warehouse']) !!}</td>
              <td>{!! Form::number('Stock[]', $transaksi->Kumbang, ['class' => 'form-control Stock', 'readonly']) !!}</td>
              <td>{!! Form::text('QSisaKirInsert[]', $transaksi->QSisaKirInsert, array('class' => 'form-control', 'readonly')) !!}</td>
              <td><input name="QKirim[]" type="number" class="form-control QKirim" placeholder="1000" autocomplete="off" onkeyup="this.value = minmax(this.value, 0, {{ $transaksi->QSisaKirInsert }})" value="{{ $transaksi->QSisaKirInsert }}" required></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
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
            <td>{!! Form::text('NoPolisi', null, array('id' => 'NoPolisi', 'class' => 'form-control', 'autocomplete' => 'off', 'onkeyup' => 'capital()')) !!}</td>
            <td>{!! Form::text('Sopir', null, array('class' => 'form-control', 'autocomplete' => 'off')) !!}</td>
            <td>{!! Form::text('Kenek', null, array('class' => 'form-control', 'autocomplete' => 'off')) !!}</td>
          </tr>
        </tbody>
      </table>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Keterangan', 'Keterangan', ['class' => "col-sm-1 control-label"]) !!}
          <div class="col-sm-5">
            {!! Form::textarea('Keterangan', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Keterangan', 'rows' => '3')) !!}
          </div>
          {!! Form::label('FormMuat', 'Form Muat', ['class' => "col-sm-1 control-label"]) !!}
          <div class="col-sm-5">
            {!! Form::textarea('FormMuat', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Form Muat', 'rows' => '3')) !!}
          </div>
        </div>
      </div>
      <div class="box-footer">
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
        <a href="{{route('sjkirim.create', 'id='.$referenceid->id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
      </div>
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
  function capital() {
    var x = document.getElementById("NoPolisi");
    x.value = x.value.toUpperCase();
  }
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