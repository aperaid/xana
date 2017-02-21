@extends('layouts.xana.layout')
@section('title')
	Create PO
@stop

@section('content')
	{!! Form::open([
  'route' => 'po.store'
  ]) !!}

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('reference.show', $reference->id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
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
        {!! Form::hidden('id', $reference->id) !!}
        {!! Form::hidden('poid', $last_po+1) !!}
        {!! Form::hidden('Reference', $reference -> Reference) !!}
        <div class="form-group">
          {!! Form::label('POCode', 'Nomor PO') !!}
          {!! Form::text('POCode', null, array('id' => 'POCode', 'class' => 'form-control', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'placeholder' => 'Input PO Number', 'required')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tgl', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '31/12/2000', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Discount', 'Discount(%)') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::number('Discount', null, array('id' => 'Discount', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '15')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Catatan', 'Catatan') !!}
          {!! Form::textarea('Catatan', null, array('id' => 'Catatan', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '5')) !!}
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
  <!-- POITEM BOX -->
  <div class="col-md-9">
    <!-- general form elements -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Input PO Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-hover table-bordered" id="customFields">
          <thead>
            <th width="1%"><a href="javascript:void(0);" id="addCF" class=" glyphicon glyphicon-plus"></a></th>
            <th>Barang</th>
            <th width="9%">ICode</th>
            <th>Type</th>
            <th>J/S</th>
            <th width="7%">Quantity</th>
            <th width="15%">Price/Unit</th>
          </thead>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</div>
<!-- row -->

{!! Form::close() !!}
@stop

@section('script')
<script>
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  todayHighlight: true,
	  autoclose: true,
	  startDate: '{{$min}}',
  }); 
}); 

$(document).ready(function(){
	var max_fields      = 10; //maximum input boxes allowed
	
	var x = 0; //initial text box count
	var y = {{ $maxid }};
	$("#addCF").click(function(){
		if(x < max_fields){ //max input box allowed
			x++; //text box count increment
			y++;
			$("#customFields").append('<tr><td align="center"><a href="javascript:void(0);" class="remCF glyphicon glyphicon-remove"></a></td><td hidden><input type="text" name="transaksiid[]" id="transaksiid" value="'+ y +'"></td><td hidden><input type="text" name="Purchase[]" value="'+ y +'"></td><td>{!! Form::text('Barang[]', null, ['class' => 'form-control Barang', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::text('ICode[]', null, ['class' => 'form-control ICode', 'readonly']) !!}</td><td>{!! Form::select('Type[]', ['NEW' => 'NEW', 'SECOND' => 'SECOND'], null, ['class' => 'form-control Type']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], null, ['class' => 'form-control JS']) !!}</td><td>{!! Form::number('Quantity[]', null, ['class' => 'form-control Quantity', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::text('Amount[]', null, ['class' => 'form-control Amount', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
			
			$(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
		
			var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletebarang.php");?>;
			$( ".Barang" ).autocomplete({
				source: availableTags,
				autoFocus: true
			});
			
			$('.Barang').keyup(function(){
				this.value = this.value.toUpperCase();
			});
			
			$(document).on('click autocompletechange', '.Barang, .ICode, .Type, .Quantity, .JS, .Amount', function(){
				var this2 = this;
				$.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
				.done(function(data){
					result = $.parseJSON(data);
					if($(this2).closest('tr').find(".JS").val() == 'Jual'){
						var price = result.JualPrice
					}else{
						var price = result.Price
					}
					$(this2).closest('tr').find(".Amount").val('Rp '+price.toLocaleString().replace(/\,/g,'.'));
					$(this2).closest('tr').find(".ICode").val(result.Code);
				})
				.fail(function(data){
					if( data.status === 500 ) {
						console.log("Barang tak ditemukan");
					}
				});
			});
			
			$(document).on('keyup', '.Barang, .ICode, .Type, .Quantity, .JS, .Amount', function(e){
				var this2 = this;
				if(e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40){
					$.post("/barang", { "_token": "{{ csrf_token() }}", namabarang: $(this).closest('tr').find(".Barang").val(), tipebarang: $(this).closest('tr').find(".Type").val() }, function(data){})
					.done(function(data){
						result = $.parseJSON(data);
						if($(this2).closest('tr').find(".JS").val() == 'Jual'){
							var price = result.JualPrice
						}else{
							var price = result.Price
						}
						$(this2).closest('tr').find(".Amount").val('Rp '+price.toLocaleString().replace(/\,/g,'.'));
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
				if(parseInt($(this).closest('tr').find(".Quantity").val()) < 0)
					$(this).closest('tr').find(".Quantity").val(0);
				/*if(parseInt($(this).closest('tr').find(".Quantity").val()) > $(this).closest('tr').find(".Stock").val())
					$(this).closest('tr').find(".Quantity").val($(this).closest('tr').find(".Stock").val());
				else
					$(this).closest('tr').find(".Stock").val();*/
			});
		}
	});
	
	$("#customFields").on('click','.remCF',function(){
		$(this).parent().parent().remove();
		x--;
		if($(this).closest('tr').find("#transaksiid").val() < y)
			y-2;
		else
			y--;
	});	
});

$(document).ready(function(){
	//Mask Price
	$("#Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	//Mask Discount
	$(document).on('keyup', '#Discount', function(){
	if(parseInt($(this).val()) > 100)
		 $(this).val(100);
	else if(parseInt($(this).val()) < 0)
		$(this).val(0);
	});
});

function capital() {
	var x = document.getElementById("POCode");
	x.value = x.value.toUpperCase();
}
</script>
@stop