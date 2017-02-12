@extends('layouts.xana.layout')
@section('title')
	All Transaksi
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#sewa_tab" data-toggle="tab">Sewa</a></li>
        <li><a href="#jual_tab" data-toggle="tab">Jual</a></li>
        <li><a href="#claim_tab" data-toggle="tab">Claim</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="sewa_tab">
          <table id="datatabless" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>Reference</th>
                <th>Invoice</th>
                <th>Periode</th>
                <th>E</th>
                <th>Project</th>
                <th>Extend</th>
								<th>Cancel</th>
              </tr>
            </thead>
            <tbody>
              @foreach($transaksiss as $transaksis)
              <tr>
                <td hidden>{{$transaksis->invoiceid}}</td>
                <td>{{$transaksis->Reference}}</td>
                <td>{{$transaksis->Invoice}}</td>
                <td>{{$transaksis->Periode}}</td>
                <td>{{$transaksis->E}}</td>
                <td>{{$transaksis->Project}}</td>
                <td>
                  @if ($transaksis->periodeid == $transaksis->maxid && $transaksis->SumQKirim == $transaksis->SumQTertanda)
										<button type="button" class="btn btn-block btn-info extend">Extend</button>
                  @else
                    <button type="button" class="btn btn-block btn-default extend" disabled>Extend</button>
                  @endif
                </td>
								<td>
									@if ($transaksis->Periode == $transaksis->maxperiode && $transaksis->Periode != 1)
										<button class='btn btn-block btn-danger delete'>Cancel</button>
									@else
										<button class='btn btn-block btn-default' disabled>Cancel</button>
									@endif
								</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="jual_tab">
          <table id="datatablesj" class="table table-hover table-bordered">
            <thead>
              <tr>
                <th hidden>id</th>
                <th>Reference</th>
                <th>Invoice</th>
                <th>Project</th>
              </tr>
            </thead>
            <tbody>
              @foreach($transaksijs as $transaksij)
              <tr>
                <td hidden>{{$transaksij->id}}</td>
                <td>{{$transaksij->Reference}}</td>
                <td>{{$transaksij->Invoice}}</td>
                <td>{{$transaksij->Project}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="tab-pane" id="claim_tab">

          <table id="datatablesc" class="table table-hover table-bordered">
            <thead>
              <th hidden>id</th>
              <th>Reference</th>
              <th>Invoice</th>
              <th>Periode</th>
              <th>Tanggal Claim</th>
              <th>Project</th>
              <th>Cancel</th>
            </thead>
            <tbody>
              @foreach($transaksics as $transaksic)
              <tr>
                <td hidden>{{$transaksic->invoiceid}}</td>
                <td>{{$transaksic->Reference}}</td>
                <td>{{$transaksic->Invoice}}</td>
                <td>{{$transaksic->Periode}}</td>
                <td>{{$transaksic->Tgl}}</td>
                <td>{{$transaksic->Project}}</td>
                <td><button class='btn btn-block btn-danger claimdelete'>Cancel</button></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- nav-tabs-custom -->
  </div>
  <!-- col -->
</div>
<!-- row -->

<div class="modal fade" id="extendmodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="extendform" name="extendform" class="form-horizontal">
        <div class="modal-header">
					<input type="hidden" id="extendid">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Extend</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal"><h4> Extend Invoice for 1 Month? Setelah extend, tambah PO dan pengiriman tidak akan dapat dilakukan di periode sebelumnya</h4></label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success pull-right">Extend</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deletemodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="deleteform" name="deleteform" class="form-horizontal">
        <div class="modal-header">
					<input type="hidden" id="deleteid">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Cancel Extend</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal"><h4> Are you sure you want to cancel Extend Invoice? (Delete Permanently)</h4></label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger pull-right">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteclaimmodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="deleteclaimform" name="deleteclaimform" class="form-horizontal">
        <div class="modal-header">
					<input type="hidden" id="deleteclaimid">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Claim</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal"><h4> Are you sure you want to delete Transaksi Claim? (Delete Permanently)</h4></label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger pull-right">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('script')
<script>
$(document).ready(function () {
	var table = $("#datatabless").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs":[
			{
				"targets" : 0,
				"visible" : true,
				"searchable": false
			},
		]
	});
	
	/*$('#datatabless tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		window.open("invoice/showsewa/" + data[0], "_self");
	});*/
});

$(document).ready(function () {
	var table = $("#datatablesj").DataTable({
		"processing": true,
		"order": [0, "desc"],
		"columnDefs": [{
			"targets": 0,
			"visible": true,
			"searchable": false
		}
	],
	});
	
	/*$('#datatablesj tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		window.open("invoice/showjual/" + data[0], "_self");
	});*/
});

$(document).ready(function () {
	var table = $("#datatablesc").DataTable({
		"processing" : true,
		"order": [0, "desc"],
		"columnDefs": [{
			"targets": 0,
			"visible": true,
			"searchable": false
		}
	],
	});
	
	/*$('#datatablesc tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		window.open("invoice/showclaim/" + data[0], "_self");
	});*/
});

//When extend button is clicked
$(".extend").click(function(){
	$('#extendmodal').modal('toggle');
	$("#extendid").val($(this).parent().siblings(":first").text());
});

//When extend form is submitted
$("#extendform").submit(function(event){
  $(".loading").show();
  $.post("transaksi/extend", { "_token": "{{ csrf_token() }}", id: $("#extendid").val() }, function(data){})
  .done(function(data){
    location.reload();
    $('#extendmodal').modal('toggle');
  })
  .fail(function(data){
    console.log('fail');
  });
});

//When delete button is clicked
$(".delete").click(function(){
	$('#deletemodal').modal('toggle');
	$("#deleteid").val($(this).parent().siblings(":first").text());
});

//When delete form is submitted
$("#deleteform").submit(function(event){
  $(".loading").show();
  $.post("transaksi/extenddelete", { "_token": "{{ csrf_token() }}", id: $("#deleteid").val() }, function(data){})
  .done(function(data){
    location.reload();
    $('#deletemodal').modal('toggle');
  })
  .fail(function(data){
    console.log('fail');
  });
});

//When delete claim button is clicked
$(".claimdelete").click(function(){
	$('#deleteclaimmodal').modal('toggle');
	$("#deleteclaimid").val($(this).parent().siblings(":first").text());
});

//When delete claim form is submitted
$("#deleteclaimform").submit(function(event){
  $(".loading").show();
  $.post("transaksi/claimdelete", { "_token": "{{ csrf_token() }}", id: $("#deleteclaimid").val() }, function(data){})
  .done(function(data){
    location.reload();
    $('#deleteclaimmodal').modal('toggle');
  })
  .fail(function(data){
    console.log('fail');
  });
});

</script>
@stop