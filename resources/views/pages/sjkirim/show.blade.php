@extends('layouts.xana.layout')
@section('title')
	View Surat Jalan Kirim
@stop

@section('content')
{!! Form::open([
  'method' => 'delete',
  'route' => ['sjkirim.destroy', $sjkirim->id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <h2 class="page-header">
      <i class="fa fa-globe"></i> SJ Kirim | {{ $sjkirim -> SJKir }}
      <small class="pull-right">Date: {{ $sjkirim -> Tgl }}</small>
    </h2>
  </div>
</div>

<!-- info row -->
<div class="row invoice-info">
  <div class="col-sm-4 invoice-col">
    Company
    <address>
      <strong><?php echo $row_ViewIsiSJKirim['Company']; ?></strong><br>
      <?php echo $row_ViewIsiSJKirim['Alamat']; ?><br>
      <?php echo $row_ViewIsiSJKirim['Kota']; ?>,  <?php echo $row_ViewIsiSJKirim['Zip']; ?><br>
      Phone: <?php echo $row_ViewIsiSJKirim['CompPhone']; ?><br>
      Email: <?php echo $row_ViewIsiSJKirim['CompEmail']; ?>
    </address>
  </div>
  <div class="col-sm-4 invoice-col">
    Project
    <address>
      <strong><?php echo $row_ViewIsiSJKirim['Project']; ?></strong><br>
      <?php echo $row_ViewIsiSJKirim['Alamat']; ?><br>
      <?php echo $row_ViewIsiSJKirim['Kota']; ?>,  <?php echo $row_ViewIsiSJKirim['Zip']; ?><br>
    </address>
  </div>
  <div class="col-sm-4 invoice-col">
    Contact Person
    <address>
      <strong><?php echo $row_ViewIsiSJKirim['Customer']; ?></strong><br>
      Phone: <?php echo $row_ViewIsiSJKirim['CustPhone']; ?><br>
      Email: <?php echo $row_ViewIsiSJKirim['CustEmail']; ?>
    </address>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 table-responsive">
    <table id="tb_viewsjkirim_example1" class="table table-striped">
      <thead>
        <tr>
          <th>J/S</th>
          <th>Barang</th>
          <th>Warehouse</th>
          <th>Q Kirim</th>
          <th>Q Tertanda</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $Periode = $row_ViewIsiSJKirim['Periode']; 
        $Reference = $row_ViewIsiSJKirim['Reference'];
        ?>
        <?php do { ?>
        <tr>
          <td><input name="tx_viewsjkirim_JS" type="text" class="form-control" id="tx_viewsjkirim_JS" value="<?php echo $row_ViewIsiSJKirim['JS']; ?>" readonly></td>
          <td><input name="tx_viewsjkirim_Barang" type="text" class="form-control" id="tx_viewsjkirim_Barang" value="<?php echo $row_ViewIsiSJKirim['Barang']; ?>" readonly></td>
          <td><input name="tx_viewsjkirim_Warehouse" type="text" class="form-control" id="tx_viewsjkirim_Warehouse" value="<?php echo $row_ViewIsiSJKirim['Warehouse']; ?>" readonly></td>
          <td><input name="tx_viewsjkirim_QKirim" type="text" class="form-control" id="tx_viewsjkirim_QKirim" value="<?php echo $row_ViewIsiSJKirim['QKirim']; ?>" readonly></td>
          <td><input name="tx_viewsjkirim_QTertanda" type="text" class="form-control" id="tx_viewsjkirim_QTertanda" value="<?php echo $row_ViewIsiSJKirim['QTertanda']; ?>" readonly></td>
        </tr>
        <?php } while ($row_ViewIsiSJKirim = mysql_fetch_assoc($ViewIsiSJKirim)); ?>
      </tbody>
    </table>
  </div>

  <?php
  //Edit button disabled function
  $query = mysql_query($query_ViewIsiSJKirim) or die(mysql_error());
  $angka = array();
  while($row = mysql_fetch_assoc($query)){
  $angka[] = $row['QTertanda'];
  }
  $jumlah = array_sum($angka) ;
  ?>
  
  <div class="box-footer">
    <a href="SJKirim.php"><button type="button" class="btn btn-default">Back</button></a>
    <a href="#"><button type="button" class="btn btn-default">Print</button></a>

    <div class="btn-group pull-right">
      <a href="EditSJKirim.php?SJKir=<?php echo $_GET['SJKir']; ?>"><button type="button" <?php if ($jumlah > '0'){ ?> class="btn btn-default" disabled <?php   } else { ?> class="btn btn-primary" <?php } ?>>Edit Pengiriman</button></a>
      <a href="EditSJKirimQuantity.php?SJKir=<?php echo $_GET['SJKir']; ?>&Periode=<?php echo $Periode; ?>&Reference=<?php echo $Reference; ?>"><button type="button" <?php if ($row_qttdbutton['result'] > 0) { ?> class="btn btn-default" disabled <?php } else { ?> class="btn btn-success" <?php } ?>>Q Tertanda</button></a>
    </div>
  </div>
  <!-- box footer -->
</div>
<!-- row -->
{!! Form::close() !!}
@stop