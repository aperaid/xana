@extends('layouts.xana.layout')
@section('title')
	View User
@stop

@section('css')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('content')
<div class="row">
  <div class="col-lg-5">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">View Users</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table id="usertab" class="table table-hover">
          <thead>
            <tr>
              <th>UserID</th>
              <th>Reserved As</th>
              <th>Name</th>
              <th>Issued On</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr id={{$user->id}}>
                <td>{{$user->email}}</td>
                <td>{{$user->access}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->created_at}}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <div class="col-lg-7">
    <div class="box box-info">
      <div class="box-body box-profile">
        <h3 class="text-center email" id="email">-</h3>
        <p class="text-muted text-center name" id="name">-</p>
        <input type="hidden" id="userid" class="userid">
        <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <p class="fa fa-user-secret"></p><b> Reserved As</b> <a class="text-green pull-right access" id="access">-</a>
            </li>
            <li class="list-group-item">
              <p class="fa fa-clock-o"></p><b> Date/time</b><a class="text-green pull-right created_at">-</a>
            </li>
            <li class="list-group-item">
              <button type="button" class="btn btn-danger" id="delete" disabled>Delete</button>
							<button type="button" class="btn btn-info" id="changepassword" disabled>Change Password</button>
              <button type="button" class="btn btn-success pull-right" id="edit" disabled>Edit User</button>
            </li>
        </ul>
      </div>
      <!-- /.box-body -->
    <div class="overlay disabled">
    </div>
    <div class="overlay loading" hidden>
      <i class="fa fa-refresh fa-spin"></i>
    </div>
    <!-- /.box-loading --> 
    </div>
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->

<div class="modal fade" id="editmodal">
  <div class="modal-dialog">
    <div class="box">
      <div class="box-header with-border">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Edit User</h4>
      </div>
      <!-- form start -->
      <form id="editform" name="editform" class="form-horizontal">
        <input type="hidden" id="olduserid" name="olduserid">
        <div class="box-body">
          <div id="message">
          </div>
          <div class="form-group">
            <label class="col-lg-4 control-label">Name</label>
            <div class="col-lg-5">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-user"></i>
                </div>
                  <input type="text" class="form-control" id="editname" name="editname" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-4 control-label">Username</label>
            <div class="col-lg-5">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user-plus"></i>
                </div>
                  <input type="text" class="form-control" id="editemail" name="editemail" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-4 control-label">Access</label>
            <div class="col-lg-5">
              <select id="editaccess" class="form-control" name="editaccess" style="width: 100%;">
								<option value="" disabled>Choose Access</option>
                <option value="Administrator">Administrator</option>
								<option value="PPNAdmin">PPN Admin</option>
								<option value="NonPPNAdmin">Non PPN Admin</option>
								<!--<option value="Purchasing">Purchasing</option>
								<option value="SuperPurchasing">SuperPurchasing</option>
								<option value="StorageManager">StorageManager</option>
								<option value="SuperStorageManager">SuperStorageManager</option>-->
              </select>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary pull-right">Save changes</button>
        </div>
      </form>
      <div class="overlay loading" hidden>
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <!-- /.box-loading -->
    </div>
    <!-- /.box -->
  </div>
</div>

<div class="modal fade" id="passwordmodal">
  <div class="modal-dialog">
    <div class="box">
      <div class="box-header with-border">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Change Password</h4>
      </div>
      <!-- form start -->
      <form id="passwordform" name="passwordform" class="form-horizontal">
				<input type="hidden" id="olduserid" name="olduserid">
        <div class="box-body">
          <div id="message2">
          </div>
          <div class="form-group">
            <label class="col-lg-4 control-label">New Password</label>
            <div class="col-lg-5">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-lock"></i>
                </div>
                  <input type="password" class="form-control" id="newpassword" name="newpassword" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-4 control-label">Confirm Password</label>
            <div class="col-lg-5">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-lock"></i>
                </div>
                  <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" autocomplete="off">
              </div>
            </div>
          </div>
					<div class="form-group">
            <label class="col-lg-4 control-label">Old Password</label>
            <div class="col-lg-5">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-lock"></i>
                </div>
                  <input type="password" class="form-control" id="oldpassword" name="oldpassword" autocomplete="off">
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary pull-right">Change Password</button>
        </div>
      </form>
      <div class="overlay loading" hidden>
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <!-- /.box-loading -->
    </div>
    <!-- /.box -->
  </div>
</div>

<div class="modal fade" id="deletemodal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- form start -->
      <form id="deleteform" name="deleteform" class="form-horizontal">
        <input type="hidden" id="olduserid" name="olduserid">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete User</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal" data-target="#deletemodal"><h4> Are you sure you want to remove this User? (Delete Permanently)</h4></label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('script')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

<style type="text/css">
  .highlight { background-color: lightblue; }
</style>

<script>
$( document ).ready(function() {
  //Highlight the user in the flashed session
  @if(Session::has('id'))
    $("#{{Session::get('id')}}").addClass("highlight");
    highlight({{Session::get('id')}});
  @endif
});

$('#usertab tbody').on('click', 'tr', function () {
var selected = $(this).hasClass("highlight");
$("#usertab tr").removeClass("highlight");
if(!selected)
  $(this).addClass("highlight");
  highlight((this.id));
});

function highlight(id) {
  $(".loading").show();
  $(".disabled").hide();
  //What happens when the controller title is clicked
  $.getJSON("/user/" + id,
    function(data, status){
      $("#buttonsave").prop("disabled", true);
			$("#changepassword").prop("disabled", true);
      $("#edit").prop("disabled", true);
      $("#delete").prop("disabled", true);
      if(status == 'success'){
        $(".userid").val(data.id);
        $(".name").text(data.name);
        $(".email").text(data.email);
        $(".access").text(data.access);
        $(".created_at").text(data.created_at);
        $("#buttonsave").prop("disabled", false);
				if("{{Auth::user()->email}}"==data.email){
					$("#changepassword").prop("disabled", false);
				}
				if("{{Auth::user()->access=='Administrator'}}"){
					$("#edit").prop("disabled", false);
				}
				if("{{Auth::user()->access=='Administrator'}}"){
					$("#delete").prop("disabled", false);
				}
      }
  })
  .done(function() {
    $(".loading").hide();
  })
  .fail(function() {
    alert("Failed to contact server");
  });
}
//When edit is clicked
$("#edit").click(function(){
  //Toggle the modal
  $('#editmodal').modal('toggle');
  $("#olduserid").val($("#userid").val());
  $("#editname").val($("#name").text());
  $("#editemail").val($("#email").text());
  $("#editaccess").val($("#access").text());
});
//When edit form is submitted
$("#editform").submit(function(event){
  $(".loading").show();
  $.post( "user/edit",{ "_token": "{{ csrf_token() }}", id: $("#olduserid").val(), name: $("#editname").val(), email: $("#editemail").val(), access: $("#editaccess").val()}, function( data ) {})
  .done(function(data){
    location.reload();
    $('#editmodal').modal('toggle');
    $(".loading").hide();
  })
  .fail(function(data) {
    if( data.status === 422 ) {
      var errors = data.responseJSON; //get the errors response data.

      var errorsHtml = '<div class="alert alert-danger alert-dismissible">';
      errorsHtml += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
      errorsHtml += '<h4><i class="icon fa fa-ban"></i> Error!</h4>';

      $.each( errors, function( key, value ) {
          errorsHtml += '<li>' + value[0] + '</li>';
      });

      errorsHtml += '</div>';

      $("#message").html(errorsHtml);
      $(".loading").hide();
    }
  });
  event.preventDefault();
});
//When change password is clicked
$("#changepassword").click(function(){
  //Toggle the modal
  $('#passwordmodal').modal('toggle');
  $("#olduserid").val($("#userid").val());
});
//When change password form is submitted
$("#passwordform").submit(function(event){
  $(".loading").show();
  $.post( "user/password",{ "_token": "{{ csrf_token() }}", id: $("#olduserid").val(), password: $("#newpassword").val(), cpassword: $("#confirmpassword").val(), opassword: $("#oldpassword").val()}, function( data ) {})
  .done(function(data){
    location.reload();
    $('#passwordmodal').modal('toggle');
    $(".loading").hide();
  })
  .fail(function(data) {
    if( data.status === 422 ) {
      var errors = data.responseJSON; //get the errors response data.

      var errorsHtml = '<div class="alert alert-danger alert-dismissible">';
      errorsHtml += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
      errorsHtml += '<h4><i class="icon fa fa-ban"></i> Error!</h4>';

      $.each( errors, function( key, value ) {
          errorsHtml += '<li>' + value[0] + '</li>';
      });

      errorsHtml += '</div>';

      $("#message2").html(errorsHtml);
      $(".loading").hide();
    }
  });
  event.preventDefault();
});
//When delete button is clicked
$("#delete").click(function(){
  //Toggle the modal
  $('#deletemodal').modal('toggle');
  $("#olduserid").val($("#userid").val());
});
//When delete form is submitted
$("#deleteform").submit(function(event){
  $(".loading").show();
  $.post("user/delete", { "_token": "{{ csrf_token() }}", id: $("#olduserid").val() }, function(data){})
  .done(function(data){
    location.reload();
    $('#deletemodal').modal('toggle');
    $(".loading").hide();
  })
  .fail(function(data){
    if( data.status === 422 ) {
        var errors = data.responseJSON; //get the errors response data.

        var errorsHtml = '<div class="alert alert-danger alert-dismissible">';
        errorsHtml += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        errorsHtml += '<h4><i class="icon fa fa-ban"></i> Error!</h4>';

        $.each( errors, function( key, value ) {
            errorsHtml += '<li>' + value[0] + '</li>';
        });

        errorsHtml += '</div>';

        $("#globalmessage").html(errorsHtml);
    }
    $(".loading").hide();
  });
});
$('#usertab').DataTable({

});

</script>
@endsection
