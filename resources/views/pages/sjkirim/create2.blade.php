@extends('layouts.xana.layout')
@section('title')
	Create SJKirim
@stop

@section('content')
{!! Form::open([
  'route' => ['sjkirim.create3', $referenceid->id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <table id="datatables" class="table table-bordered table-striped table-responsive">
          <thead>
          <tr>
            <th>Choose</th>
            <th>J/S</th>
            <th>Barang</th>
            <th>Quantity Sisa Kirim</th>
          </tr>
          </thead>
          <tbody>
            @foreach($transaksis as $transaksi)
              <tr>
                <td>@if($transaksi->QSisaKirInsert == 0)
                {!! Form::checkbox('checkbox[]', $transaksi->Purchase, null, ['class' => 'minimal',  'disabled' ]) !!}
                @else
                {!! Form::checkbox('checkbox[]', $transaksi->Purchase, null, ['class' => 'minimal']) !!}
                @endif</td>
                <td>{{$transaksi->JS}}</td>
                <td>{{$transaksi->Barang}}</td>
                <td>{{$transaksi->QSisaKirInsert}}</td>
              </tr>
            @endforeach
            <p>{!! Form::checkbox('SelectAll', null, null, ['id' => 'SelectAll', 'class' => 'minimal']) !!}{!! Form::label('Check All', 'Check All') !!}
          </tbody>
        </table>
      </div>
      <div class="box-footer">
        {!! Form::submit('Choose',  array('class' => 'btn btn-info pull-right', 'disabled')) !!}
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
var checkboxes = $("input[type='checkbox']"),
    submitButt = $("input[type='submit']");

checkboxes.click(function() {
    submitButt.attr("disabled", !checkboxes.is(":checked"));
});
</script>
<script>
$('#SelectAll').click(function () {
    var checked_status = this.checked;

    $('input[type=checkbox]').not(":disabled").prop('checked', checked_status);
});
</script>
@stop