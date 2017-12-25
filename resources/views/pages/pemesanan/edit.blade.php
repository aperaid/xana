@extends('layouts.xana.layout')
@section('title')
	Edit Pemesanan
@stop

@section('content')
	{!! Form::model($pemesanan, [
	'method' => 'patch',
	'route' => ['pemesanan.update', $pemesanan->id]
	]) !!}
	
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('pemesanan.show', $pemesanan->id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
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
        <h3 class="box-title">Pemesanan Detail</h3>
      </div>
      <div class="box-body">
				<input type="hidden" name="OldPemesanan" value="{{$pemesanan->PesanCode}}">
        <div class="form-group">
          {!! Form::label('PesanCode', 'Pesan Code') !!}
          {!! Form::text('PesanCode', $pemesanan -> PesanCode, array('id' => 'PesanCode', 'class' => 'form-control', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'placeholder' => 'Input Code', 'required')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tgl', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $pemesanan->Tgl, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '31/12/2000', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Supplier Code', 'Supplier Code') !!}
          {!! Form::text('SCode', $pemesanan -> SCode, array('class' => 'form-control', 'id' => 'SCode', 'placeholder' => 'ABC01', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
          <p class="help-block">Enter the beginning of the Supplier Code, then pick from the dropdown</p>
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
						<tr>
							<th width="1%"><a href="javascript:void(0);" id="addmore" class=" glyphicon glyphicon-plus"></a></th>
							<th>Barang</th>
							<th width="20%">ICode</th>
							<th width="10%">Quantity</th>
							<th width="15%">Price/Unit</th>
							@if(env('APP_TYPE')=='Jual')
								<th width="20%">Kategori</th>
							@endif
						</tr>
					</thead>
					<tbody>
						<tr class='tr_input'>
							<td><a href='javascript:void(0);' class='glyphicon glyphicon-remove'></a></td>
							<td><input type='text' name='Barang[]' id='Barang_0' value='{{$pemesananlists[0]->Barang}}' class='form-control input-sm Barang' placeholder='Main Frame' autocomplete required></td>
							<td><input type='text' name='ICode[]' id='ICode_0' value='{{$pemesananlists[0]->ICode}}' class='form-control input-sm ICode' readonly></td>
							<td><input type='number' name='Quantity[]' id='Quantity_0' value='{{$pemesananlists[0]->Quantity}}' class='form-control input-sm Quantity' placeholder='100' autocomplete required></td>
							<td><input type='text' name='Amount[]' id='Amount_0' value='{{ 'Rp '. number_format( $pemesananlists[0]->Amount, 0,',', '.' ) }}' class='form-control input-sm Amount' placeholder='Rp 100.000' autocomplete required></td>
							@if(env('APP_TYPE')=='Jual')
								<td><input type='text' name='Type[]' id='Type_0' value='{{$pemesananlists[0]->Type}}' class='form-control input-sm Type' readonly></td>
							@endif
						</tr>
						@for($key=1;$key<count($pemesananlists);$key++)
							<tr class='tr_input'>
								<td><a href='javascript:void(0);' class='remCF glyphicon glyphicon-remove'></a></td>
								<td><input type='text' name='Barang[]' id='{{"Barang_".$key}}' value='{{$pemesananlists[$key]->Barang}}' class='form-control input-sm Barang' placeholder='Main Frame' autocomplete required></td>
								<td><input type='text' name='ICode[]' id='{{"ICode_".$key}}' value='{{$pemesananlists[$key]->ICode}}' class='form-control input-sm ICode' readonly></td>
								<td><input type='number' name='Quantity[]' id='{{"Quantity_".$key}}' value='{{$pemesananlists[$key]->Quantity}}' class='form-control input-sm Quantity' placeholder='100' autocomplete required></td>
								<td><input type='text' name='Amount[]' id='{{"Amount_".$key}}' value='{{ 'Rp '. number_format( $pemesananlists[$key]->Amount, 0,',', '.' ) }}' class='form-control input-sm Amount' placeholder='Rp 100.000' autocomplete required></td>
								@if(env('APP_TYPE')=='Jual')
									<td><input type='text' name='Type[]' id='{{"Type_".$key}}' value='{{$pemesananlists[$key]->Type}}' class='form-control input-sm Type' readonly></td>
								@endif
							</tr>
						@endfor
					</tbody>
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
	  //startDate: new Date(),
  }); 
});

$(document).ready(function(){
	//Every time add dynamically table button is clicked
	$("#addmore").click(function(){
		// Get last id 
		var lastid = $('.tr_input input[type=text]:nth-child(1)').last().attr('id');
		var split_id = lastid.split('_');
		// New index
		var index = Number(split_id[1]) + 1;

		// Create row with input elements
		if("{{env('APP_TYPE')}}"=='Sewa'){
			var html = "<tr class='tr_input'><td><a href='javascript:void(0);' class='remCF glyphicon glyphicon-remove'></a></td><td><input type='text' type='text' name='Barang[]' id='Barang_"+index+"' class='form-control input-sm Barang' placeholder='Main Frame' autocomplete required></td><td><input type='text' name='ICode[]' id='ICode_"+index+"' class='form-control input-sm ICode' readonly></td><td><input type='number' name='Quantity[]' id='Quantity_"+index+"' class='form-control input-sm Quantity' placeholder='100' autocomplete required></td><td><input type='text' name='Amount[]' id='Amount_"+index+"' class='form-control input-sm Amount' placeholder='Rp 100.000' autocomplete required></td></tr>";
		}else{
			var html = "<tr class='tr_input'><td><a href='javascript:void(0);' class='remCF glyphicon glyphicon-remove'></a></td><td><input type='text' type='text' name='Barang[]' id='Barang_"+index+"' class='form-control input-sm Barang' placeholder='Main Frame' autocomplete required></td><td><input type='text' name='ICode[]' id='ICode_"+index+"' class='form-control input-sm ICode' readonly></td><td><input type='number' name='Quantity[]' id='Quantity_"+index+"' class='form-control input-sm Quantity' placeholder='100' autocomplete required></td><td><input type='text' name='Amount[]' id='Amount_"+index+"' class='form-control input-sm Amount' placeholder='Rp 100.000' autocomplete required></td><td><input type='text' name='Type[]' id='Type_"+index+"' class='form-control input-sm Type' readonly></td></tr>";
		}

		// Append data
		$('tbody').append(html);
	
		//Mask Price
		$(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
	
	//autocomplete on Barang
	$(document).on('keydown', '.Barang', function() {
		var id = this.id;
		var splitid = id.split('_');
		var index = splitid[1];

		// Initialize jQuery UI autocomplete
		$( '#'+id ).autocomplete({
			source: function( request, response ){
				$.post("/dropdown/inventorylist/" + request.term, { '_token':'{{ csrf_token() }}' }, function(data){})
				.done(function(data){
					response($.map(data, function (value, key) {
						return {
							label: value.label,
							key: value.key
						};
					}));
				})
				.fail(function(){})
			},
			select: function(event, ui) {
				event.preventDefault();
				$(this).val(ui.item.label); // display the selected text
				$(this).attr("uid", ui.item.key);
			}
		});
	});
	
	//On mouse
	$(document).on('click autocompletechange', '.Barang, .Type, .ICode, .Quantity, .Amount', function(){
		var this2 = this;
		$.post("/barang/beli", { "_token": "{{ csrf_token() }}", id: $(this).closest('tr').find(".Barang").attr("uid"), namabarang: $(this).closest('tr').find(".Barang").val() }, function(data){})
		.done(function(data){
			result = $.parseJSON(data);
			var price = result.BeliPrice
			$(this2).closest('tr').find(".Amount").val('Rp '+price.toLocaleString().replace(/\,/g,'.'));
			$(this2).closest('tr').find(".Type").val(result.Type);
			$(this2).closest('tr').find(".ICode").val(result.Code);
		})
		.fail(function(data){
			if( data.status === 500 ) {
				console.log("Barang tak ditemukan");
			}
		});
	});
	
	//On keyboard
	$(document).on('keyup', '.Barang, .Type, .ICode, .Quantity, .Amount', function(e){
		var this2 = this;
		if(e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40){
			$.post("/barang/beli", { "_token": "{{ csrf_token() }}", id: $(this).closest('tr').find(".Barang").attr("uid"), namabarang: $(this).closest('tr').find(".Barang").val() }, function(data){})
			.done(function(data){
				result = $.parseJSON(data);
				var price = result.BeliPrice
				$(this2).closest('tr').find(".Amount").val('Rp '+price.toLocaleString().replace(/\,/g,'.'));
				$(this2).closest('tr').find(".Type").val(result.Type);
				$(this2).closest('tr').find(".ICode").val(result.Code);
			})
			.fail(function(data){
				if( data.status === 500 ) {
					console.log("Barang tak ditemukan");
				}
			});
		}
	});
			
	//Not below zero
	$(document).on('keyup', '.Quantity', function(){
		if(parseInt($(this).closest('tr').find(".Quantity").val()) < 1)
			$(this).closest('tr').find(".Quantity").val(1);
	});
	
	//Capital
	$(document).on('keyup', '.Barang', function(){
		this.value = this.value.toUpperCase();
		console.log($(this).closest('tr').find(".Warehouse").val());
	});
	
	//Mask Price
	$(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	
	//Decrease row
	$("#customFields").on('click','.remCF',function(){
		$(this).parent().parent().remove();
	});
});
	
//autocomplete on SCode
$("#SCode").autocomplete({
	autoFocus: true,
	source: function( request, response ) {
		$.post("/dropdown/supplierlist/" + request.term, { '_token':'{{ csrf_token() }}' }, function(data){})
		.done(function(data){
			response($.map(data, function (value, key) {
				return {
						label: value.label
				};
			}));
		})
		.fail(function(){})
	}
});
	
function capital() {
	var x = document.getElementById("PesanCode");
	x.value = x.value.toUpperCase();
	var x = document.getElementById("SCode");
	x.value = x.value.toUpperCase();
}
</script>
@stop