@extends('layouts.xana.layout')
@section('title')
	Create Penawaran
@stop

@section('content')
	{!! Form::open([
  'route' => 'penawaran.store'
  ]) !!}

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('penawaran.index')}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
        </div>
        <!-- box-body -->
      </div>
      <!-- box -->
    </div>
    <!-- col -->
  </div>
  <!-- row -->
  
<!-- CONTENT ROW -->
<div class="row">
  <div class="col-md-3">
    <div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Penawaran Detail</h3>
			</div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Penawaran', 'Penawaran Code') !!}
          {!! Form::text('Penawaran', null, array('id' => 'Penawaran', 'class' => 'form-control', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'placeholder' => 'Input Penawaran', 'required')) !!}
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
          {!! Form::label('PCode', 'Project Code') !!}
          {!! Form::text('PCode', null, array('class' => 'form-control', 'id' => 'PCode', 'placeholder' => 'ABC01', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
          <p class="help-block">Enter the beginning of the Project Code, then pick from the dropdown</p>
        </div>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
  <div class="col-md-9">
    <!-- general form elements -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Input Penawaran Item</h3>
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
      <!-- box-body -->
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
		var y = {{ $maxid }};
    //Every time Add CF button is clicked:
		$("#addCF").click(function(){
			if(x < max_fields){ //max input box allowed
				x++; //text box count increment
				y++;
        $("#customFields").append('<tr><td align="center"><a href="javascript:void(0);" class="remCF glyphicon glyphicon-remove"></a></td><td hidden><input type="text" name="penawaranid[]" id="penawaranid" value="'+ y +'"></td><td hidden><input type="text" name="Purchase[]" value="'+ y +'"></td><td>{!! Form::text('Barang[]', null, ['class' => 'form-control Barang', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::text('ICode[]', null, ['class' => 'form-control ICode', 'readonly']) !!}</td><td>{!! Form::select('Type[]', ['NEW' => 'NEW', 'SECOND' => 'SECOND'], null, ['class' => 'form-control Type']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], null, ['class' => 'form-control JS']) !!}</td><td>{!! Form::number('Quantity[]', null, ['class' => 'form-control Quantity', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::text('Amount[]', null, ['class' => 'form-control Amount', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
      
        $(".Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
      
        var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletebarang.php");?>;
        $( ".Barang" ).autocomplete({
          source: availableTags,
          autoFocus: true
        });
        
        $('.Barang').keyup(function(){
          this.value = this.value.toUpperCase();
          console.log($(this).closest('tr').find(".Warehouse").val());
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
      if($(this).closest('tr').find("#penawaranid").val() < y)
				y-2;
			else
				y--;
		});	
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
<script>
  function capital() {
    var x = document.getElementById("Penawaran");
    x.value = x.value.toUpperCase();
    var x = document.getElementById("PCode");
    x.value = x.value.toUpperCase();
  }
</script>
@stop