{!! Form::open([
  'route' => ['inventory.postremoveinventory', $id]
]) !!}
{!! Form::close() !!}

<script src="{{ asset('/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<script>
  $(document).ready(function() {
    window.document.forms[0].submit();
  });
</script>