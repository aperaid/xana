@extends('layouts.xana.layout')
@section('title')
	Edit PO
@stop

@section('content')
	{!! Form::model($po, [
	'method' => 'patch',
	'route' => ['po.update', $po->id]
	]) !!}
	
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('po.show', $po -> id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        <button type="submit" class="btn btn-success pull-right">Update</button>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->

<!-- CONTENT ROW -->
<div class="row">
  <!-- PO INFO BOX -->
  <div class="col-md-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Detail</h3>
      </div>
      <div class="box-body">
        {!! Form::hidden('poid', $po->id) !!}
        {!! Form::hidden('Reference', $transaksi -> Reference) !!}
        <div class="form-group">
          {!! Form::label('Nomor PO', 'Nomor PO') !!}
          {!! Form::text('POCode', $po -> POCode, array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tanggal', 'Tanggal') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $po -> Tgl, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Date', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Catatan', 'Catatan') !!}
          {!! Form::textarea('Catatan', $po -> Catatan, array('id' => 'Catatan', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '5', 'required')) !!}
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
  <!-- POITEM BOX -->
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-hover table-bordered" id="customFields">
          <thead>
            <th><a id="addCF" class=" glyphicon glyphicon-plus"></a></th>
            <th>Barang</th>
            <th>Type</th>
            <th>J/S</th>
            <th width="10%">Stock</th>
            <th width="10%">Quantity</th>
            <th>Price/Unit</th>
          </thead>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
    <!-- /.col -->
</div>
<!-- /.row -->

@stop

@section('script')
<script>
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  todayHighlight: true,
	  autoclose: true,
	  startDate: '-7d',
	  endDate: '+7d'
  }); 
}); 
</script>
<script>
    $(document).ready(function(){
		var max_fields      = 10; //maximum input boxes allowed
		
    var x = 0; //initial text box count
		var y = {{ $transaksi -> id }};
		var z = y;
    
		@foreach($transaksis as $transaksi)
      $("#customFields").append('<tr><td><a class="remCF glyphicon glyphicon-remove"></a></td>{!! Form::hidden('transaksiid[]', $transaksi->id) !!}{!! Form::hidden('Purchase[]', $transaksi->Purchase) !!}<td>{!! Form::text('Barang[]', $transaksi->Barang, ['class' => 'form-control Barang', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::select('Type[]', ['Baru' => 'Baru', 'Lama' => 'Lama'], $transaksi->Type, ['class' => 'form-control Type']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], $transaksi->JS, ['class' => 'form-control']) !!}</td><td>{!! Form::number('Stock[]', null, ['class' => 'form-control Stock', 'readonly']) !!}</td><td>{!! Form::number('Quantity[]', $transaksi->Quantity, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::text('Amount[]', 'Rp '. number_format( $transaksi -> Amount, 0,',', '.' ), ['id' => 'Amount', 'class' => 'form-control Amount', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
		@endforeach
    
    $(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
      
    var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletebarang.php");?>;
    $( ".Barang" ).autocomplete({
      source: availableTags,
      autoFocus: true
    });
    
    $('.Barang').keyup(function(){
      this.value = this.value.toUpperCase();
    });
    
    $(document).on('click autocompletechange', '.Barang, .Type', function(){
      var this2 = this;
      $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
      .done(function(data){
        result = $.parseJSON(data);
        $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
        $(this2).closest('tr').find(".Stock").val(result.Jumlah);
      })
      .fail(function(data){
        if( data.status === 500 ) {
          console.log("Barang tak ditemukan");
        }
      });
    });
    
    $(document).on('keydown', '.Barang, .Type', function(e){
      var this2 = this;
      if(e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40){
        $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
        .done(function(data){
          result = $.parseJSON(data);
          $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
          $(this2).closest('tr').find(".Stock").val(result.Jumlah);
        })
        .fail(function(data){
          if( data.status === 500 ) {
            console.log("Barang tak ditemukan");
          }
        });
      }
    });
		
		$("#addCF").click(function(){
			if(x < max_fields){ //max input box allowed
				x++; //text box count increment
				z++;
        var id = x + y + 1;
				$("#customFields").append('<tr><td><a href="javascript:void(0);" class="remCF glyphicon glyphicon-remove"></a></td><input type="hidden" name="transaksiid[]" value="'+ id +'"><input type="hidden" name="Purchase[]" value="'+ z +'"><td>{!! Form::text('Barang[]', null, ['class' => 'form-control Barang', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::select('Type[]', ['Baru' => 'Baru', 'Lama' => 'Lama'], null, ['class' => 'form-control Type']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], null, ['class' => 'form-control']) !!}</td><td>{!! Form::number('Stock[]', null, ['class' => 'form-control Stock', 'readonly']) !!}</td><td>{!! Form::number('Quantity[]', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::text('Amount[]', null, ['class' => 'form-control Amount', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
        
        $(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
      
        var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletebarang.php");?>;
        $( ".Barang" ).autocomplete({
          source: availableTags,
          autoFocus: true
        });
        
        $('.Barang').keyup(function(){
          this.value = this.value.toUpperCase();
        });
        
        $(document).on('click autocompletechange', '.Barang, .Type', function(){
          var this2 = this;
          $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
          .done(function(data){
            result = $.parseJSON(data);
            $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
            $(this2).closest('tr').find(".Stock").val(result.Jumlah);
          })
          .fail(function(data){
            if( data.status === 500 ) {
              console.log("Barang tak ditemukan");
            }
          });
        });
        
        $(document).on('keydown', '.Barang, .Type', function(e){
          var this2 = this;
          if(e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40){
            $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
            .done(function(data){
              result = $.parseJSON(data);
              $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
              $(this2).closest('tr').find(".Stock").val(result.Jumlah);
            })
            .fail(function(data){
              if( data.status === 500 ) {
                console.log("Barang tak ditemukan");
              }
            });
          }
        });
			}
		});
		
		$("#customFields").on('click','.remCF',function(){
			$(this).parent().parent().remove();
			x--;
		});	
	});
</script>
<script>
  $(document).ready(function(){
		//Mask Price
    $("#Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
</script>
@stop