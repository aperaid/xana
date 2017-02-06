@extends('layouts.xana.layout')
@section('title')
	Create Claim
@stop

@section('content')
{!! Form::open([
  'route' => ['transaksi.claimcreate3', $id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <table id="datatables" class="table table-bordered table-striped table-responsive">
          <thead>
          <tr>
            <th>Pilih</th>
            <th>Extend Date</th>
            <th>Barang</th>
            <th>Quantity Ditempat</th>
            <th>SJ Kirim Code</th>
          </tr>
          </thead>
          <tbody>
            @foreach($isisjkirims as $isisjkirim)
              <tr>
                <td>
                @if($check < $checks)
                {!! Form::checkbox('checkbox[]', $isisjkirim->Purchase, null, ['class' => 'minimal',  'disabled' ]) !!}
                @elseif($check > $checke)
                {!! Form::checkbox('checkbox[]', $isisjkirim->Purchase, null, ['class' => 'minimal',  'disabled' ]) !!}
                @elseif($isisjkirim->SumQSisaKem == 0)
                {!! Form::checkbox('checkbox[]', $isisjkirim->Purchase, null, ['class' => 'minimal',  'disabled' ]) !!}
                @else
                {!! Form::checkbox('checkbox[]', $isisjkirim->Purchase, null, ['class' => 'minimal']) !!}
                @endif</td>
                <td>{{$isisjkirim->S}}</td>
                <td>{{$isisjkirim->Barang}}</td>
                <td>{{$isisjkirim->SumQSisaKem}}</td>
                <td>{{$isisjkirim->SJKir}}</td>
              </tr>
            @endforeach
            <p>{!! Form::checkbox('SelectAll', null, null, ['id' => 'SelectAll', 'class' => 'minimal']) !!}{!! Form::label('SelectAll', 'Check All') !!}
          </tbody>
        </table>
      </div>
      <div class="box-footer">
        {!! Form::submit('Choose',  array('class' => 'btn btn-info pull-right', 'disabled')) !!}
        <a href="{{route('transaksi.claimcreate', $id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
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
var checkboxes = $("input[type='checkbox']"), submitButt = $("input[type='submit']");

checkboxes.click(function() {
    submitButt.attr("disabled", !checkboxes.is(":checked"));
});

$('#SelectAll').click(function () {
    var checked_status = this.checked;
    $('input[type=checkbox]').not(":disabled").prop('checked', checked_status);
});

$(document).ready(function(){
	//iCheck
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		increaseArea: '20%' // optional
	});
});
</script>
@stop