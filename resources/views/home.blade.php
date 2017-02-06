@extends('layouts.xana.layout')
@section('title')
	All Project
@stop

@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">General Reports</h3>

        </div><!-- /.box-header -->
      <div class="box-body">
              <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>150</h3>

              <p>Jatuh Tempo</p>
            </div>
            <div class="icon">
              <i class="fa fa-shopping-cart"></i>
            </div>
            <a href="invoice" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>53<sup style="font-size: 20px"></sup></h3>

              <p>Stock</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="inventory/viewinventory" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>44</h3>

              <p>Surat Jalan</p>
            </div>
            <div class="icon">
              <i class="fa fa-bus"></i>
            </div>
            <a href="sjkirim" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>

              <p>Extend Sewa</p>
            </div>
            <div class="icon">
              <i class="fa fa-share-square-o"></i>
            </div>
            <a href="transaksi" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <!-- ./col -->
      </div>
                <!-- /.row -->
      </div>
            <!-- ./box-body -->
        <div class="box-footer">
        
        </div>
            <!-- /.box-footer -->
    </div><!-- /.box -->
  </div><!-- /.col -->
</div> <!-- Row -->
@stop

@section('script')

@stop