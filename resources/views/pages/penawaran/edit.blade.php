@extends('layouts.xana.layout')
@section('title')
	Edit Penawaran
@stop

@section('content')
	{!! Form::model($penawarans, [
	'method' => 'patch',
	'route' => ['penawaran.update', $id]
	]) !!}
	
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('penawaran.show', $id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        <button type="submit" class="btn btn-success pull-right">Update</button>
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
          {!! Form::label('Penawaran Code', 'Penawaran Code') !!}
          {!! Form::text('Penawaran', $penawaran -> Penawaran, array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tanggal', 'Tanggal') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $penawaran->Tgl, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '31/12/2000', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Project Code', 'Project Code') !!}
          {!! Form::text('PCode', $penawaran -> PCode, array('class' => 'form-control', 'id' => 'PCode', 'placeholder' => 'ABC01', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
          <p class="help-block">Enter the beginning of the Project Code, then pick from the dropdown</p>
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
        <h3 class="box-title">PO Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-hover table-bordered" id="customFields">
          <thead>
            <th width="1%"><a id="addCF" class=" glyphicon glyphicon-plus"></a></th>
            <th>Barang</th>
            <th>ICode</th>
            <th>Type</th>
            <th>J/S</th>
            <th width="10%">Stock</th>
            <th width="10%">Quantity</th>
            <th>Price/Unit</th>
          </thead>
        </table>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
    <!-- col -->
</div>
<!-- row -->

@stop

@section('script')
<script>
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  todayHighlight: true,
	  autoclose: true,
	  startDate: new Date(),
  }); 
}); 
</script>
<script>
    $(document).ready(function(){
		var max_fields      = 10; //maximum input boxes allowed
		
    var x = 0; //initial text box count
		var y = {{ $penawaran -> id }};
		var z = y;
    
		@foreach($penawarans as $penawaran)
      $("#customFields").append('<tr><td align="center"><a class="remCF glyphicon glyphicon-remove"></a></td>{!! Form::hidden('penawaranid[]', $penawaran->id) !!}{!! Form::hidden('Purchase[]', $penawaran->Purchase) !!}<td>{!! Form::text('Barang[]', $penawaran->Barang, ['class' => 'form-control Barang', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::text('ICode[]', $penawaran->ICode, ['class' => 'form-control ICode', 'readonly']) !!}</td><td>{!! Form::select('Type[]', ['Baru' => 'Baru', 'Lama' => 'Lama'], $penawaran->Type, ['class' => 'form-control Type']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], $penawaran->JS, ['class' => 'form-control']) !!}</td><td>{!! Form::number('Stock[]', null, ['class' => 'form-control Stock', 'readonly']) !!}</td><td>{!! Form::number('Quantity[]', $penawaran->Quantity, ['class' => 'form-control Quantity', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::text('Amount[]', 'Rp '. number_format( $penawaran -> Amount, 0,',', '.' ), ['id' => 'Amount', 'class' => 'form-control Amount', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
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
    
    $(document).on('click autocompletechange', '.Barang, .Type, .Quantity', function(){
      var this2 = this;
      $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
      .done(function(data){
        result = $.parseJSON(data);
        $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
        $(this2).closest('tr').find(".Stock").val(result.Jumlah);
        $(this2).closest('tr').find(".ICode").val(result.Code);
      })
      .fail(function(data){
        if( data.status === 500 ) {
          console.log("Barang tak ditemukan");
        }
      });
    });
    
    $(document).on('keyup', '.Barang, .Type, .Quantity', function(e){
      var this2 = this;
      if(e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40){
        $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
        .done(function(data){
          result = $.parseJSON(data);
          $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
          $(this2).closest('tr').find(".Stock").val(result.Jumlah);
          $(this2).closest('tr').find(".ICode").val(result.Code);
        })
        .fail(function(data){
          if( data.status === 500 ) {
            console.log("Barang tak ditemukan");
          }
        });
      }
    });
    
    $(document).on('keyup', '.Quantity', function(){
      if(parseInt($(this).closest('tr').find(".Quantity").val()) < 0 || isNaN($(this).closest('tr').find(".Stock").val()))
        $(this).closest('tr').find(".Quantity").val(0);
      if(parseInt($(this).closest('tr').find(".Quantity").val()) > $(this).closest('tr').find(".Stock").val())
        $(this).closest('tr').find(".Quantity").val($(this).closest('tr').find(".Stock").val());
      else
        $(this).closest('tr').find(".Stock").val();
    });
		
		$("#addCF").click(function(){
			if(x < max_fields){ //max input box allowed
				x++; //text box count increment
				z++;
        var id = x + y + 1;
				$("#customFields").append('<tr><td align="center"><a href="javascript:void(0);" class="remCF glyphicon glyphicon-remove"></a></td><input type="hidden" name="penawaranid[]" value="'+ id +'"><input type="hidden" name="Purchase[]" value="'+ z +'"><td>{!! Form::text('Barang[]', null, ['class' => 'form-control Barang', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::text('ICode[]', null, ['class' => 'form-control ICode', 'readonly']) !!}</td><td>{!! Form::select('Type[]', ['Baru' => 'Baru', 'Lama' => 'Lama'], null, ['class' => 'form-control Type']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], null, ['class' => 'form-control']) !!}</td><td>{!! Form::number('Stock[]', null, ['class' => 'form-control Stock', 'readonly']) !!}</td><td>{!! Form::number('Quantity[]', null, ['class' => 'form-control Quantity', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::text('Amount[]', null, ['class' => 'form-control Amount', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
        
        $(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
      
        var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletebarang.php");?>;
        $( ".Barang" ).autocomplete({
          source: availableTags,
          autoFocus: true
        });
        
        $('.Barang').keyup(function(){
          this.value = this.value.toUpperCase();
        });
        
        $(document).on('click autocompletechange', '.Barang, .Type, .Quantity', function(){
          var this2 = this;
          $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
          .done(function(data){
            result = $.parseJSON(data);
            $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
            $(this2).closest('tr').find(".Stock").val(result.Jumlah);
            $(this2).closest('tr').find(".ICode").val(result.Code);
          })
          .fail(function(data){
            if( data.status === 500 ) {
              console.log("Barang tak ditemukan");
            }
          });
        });
        
        $(document).on('keyup', '.Barang, .Type, .Quantity', function(e){
          var this2 = this;
          if(e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40){
            $.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
            .done(function(data){
              result = $.parseJSON(data);
              $(this2).closest('tr').find(".Amount").val('Rp '+result.Price.toLocaleString().replace(',', '.'));
              $(this2).closest('tr').find(".Stock").val(result.Jumlah);
              $(this2).closest('tr').find(".ICode").val(result.Code);
            })
            .fail(function(data){
              if( data.status === 500 ) {
                console.log("Barang tak ditemukan");
              }
            });
          }
        });
        
        $(document).on('keyup', '.Quantity', function(){
          if(parseInt($(this).closest('tr').find(".Quantity").val()) < 0 || isNaN($(this).closest('tr').find(".Stock").val()))
            $(this).closest('tr').find(".Quantity").val(0);
          if(parseInt($(this).closest('tr').find(".Quantity").val()) > $(this).closest('tr').find(".Stock").val())
            $(this).closest('tr').find(".Quantity").val($(this).closest('tr').find(".Stock").val());
          else
            $(this).closest('tr').find(".Stock").val();
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
<script>
  $(function() {
    var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletepcode.php");?>;
    $( "#PCode" ).autocomplete({
      source: availableTags,
      autoFocus: true
    });
  });
</script>
@stop