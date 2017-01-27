@extends('layouts.xana.layout')
@section('title')
	Create SJKembali
@stop

@section('content')
{!! Form::open([
  'route' => ['sjkembali.create3', $id]
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
            <th>Quantity Sisa Kembali</th>
            <th>SJ Kirim Code</th>
          </tr>
          </thead>
          <tbody>
            @foreach($isisjkembalis as $isisjkembali)
              <tr>
                <td>      
                @if($check < $checks)
                {!! Form::checkbox('checkbox[]', $isisjkembali->Purchase, null, ['class' => 'minimal',  'disabled' ]) !!}
                @elseif($check > $checke)
                {!! Form::checkbox('checkbox[]', $isisjkembali->Purchase, null, ['class' => 'minimal',  'disabled' ]) !!}
                @elseif($isisjkembali->SumQSisaKemInsert == 0)
                {!! Form::checkbox('checkbox[]', $isisjkembali->Purchase, null, ['class' => 'minimal',  'disabled' ]) !!}
                @else
                {!! Form::checkbox('checkbox[]', $isisjkembali->Purchase, null, ['class' => 'minimal']) !!}
                @endif</td>
                <td>{{$isisjkembali->S}}</td>
                <td>{{$isisjkembali->Barang}}</td>
                <td>{{$isisjkembali->SumQSisaKemInsert}}</td>
                <td>{{$isisjkembali->SJKir}}</td>
              </tr>
            @endforeach
            <p>{!! Form::checkbox('SelectAll', null, null, ['id' => 'SelectAll', 'class' => 'minimal']) !!}{!! Form::label('SelectAll', 'Check All') !!}
          </tbody>
        </table>
      </div>
      <div class="box-footer">
        {!! Form::submit('Choose',  array('class' => 'btn btn-info pull-right', 'disabled')) !!}
        <a href="{{route('sjkembali.create', 'id='.$id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
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