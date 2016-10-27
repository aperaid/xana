<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

<ul class="sidebar-menu">
	<!-- Menu Title -->
	<li class="header">MENU</li>
	<!-- Penjualan -->
	<li @if ($top_menu_sel=="menu_home") class="active" @endif ><a href="{{route('customer.index')}}"><i class="fa fa-home"></i> <span>Home</span></a></li>
	<li class="treeview @if ($top_menu_sel=="menu_penawaran" || $top_menu_sel=="menu_customer" || $top_menu_sel=="menu_project" || $top_menu_sel=="menu_referensi" || $top_menu_sel=="menu_transaksi" ||  $top_menu_sel=="menu_sjkirim" || $top_menu_sel=="menu_sjkembali" || $top_menu_sel=="menu_invoice") active @endif ">
		<a href="#">
			<i class="fa fa-cart-arrow-down"></i>
			<span>Penjualan</span>
			<i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
      <li @if ($top_menu_sel=="menu_customer") class="active" @endif ><a href="{{route('customer.index')}}"><i class="fa fa-users"></i> <span>Customer</span></a></li>
			<li @if ($top_menu_sel=="menu_project") class="active" @endif ><a href="{{route('project.index')}}"><i class="fa fa-building-o"></i> <span>Project</span></a></li>
			<li @if ($top_menu_sel=="menu_penawaran") class="active" @endif><a href="{{route('customer.index')}}"><i class="fa fa-envelope-o"></i> <span>Penawaran</span></a></li>
			<li @if ($top_menu_sel=="menu_referensi") class="active" @endif ><a href="{{route('reference.index')}}"><i class="fa fa-file-text-o"></i> <span>Referensi</span></a></li>
			<li @if ($top_menu_sel=="menu_transaksi") class="active" @endif ><a href="{{route('transaksi.index')}}"><i class="fa fa-money"></i> <span>Transaksi</span></a></li>
			<li @if ($top_menu_sel=="menu_sjkirim") class="active" @endif ><a href="{{route('sjkirim.index')}}"><i class="fa fa-automobile"></i> <span>SJ Kirim</span></a></li>
			<li @if ($top_menu_sel=="menu_sjkembali") class="active" @endif ><a href="{{route('customer.index')}}"><i class="fa fa-automobile"></i> <span>SJ Kembali</span></a></li>
			<li @if ($top_menu_sel=="menu_invoice") class="active" @endif ><a href="{{route('invoice.index')}}"><i class="fa fa-list-alt"></i> <span>Invoice</span></a></li>
		</ul>
	</li>
	<li class="treeview <?php if (0) { ?> active <?php } ?>">
		<a href="#">
			<i class="fa fa-cart-plus"></i>
			<span>Pembelian</span>
			<i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-envelope-o"></i> <span>Permintaan</span></a></li>
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-file-text-o"></i> <span>PO</span></a></li>
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-automobile"></i> <span>Penerimaan</span></a></li>
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-history"></i> <span>Retur</span></a></li>
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-list-alt"></i> <span>Invoice</span></a></li>
		</ul>
	</li>
	<li class="treeview @if ($top_menu_sel=="menu_inventory" ||  $top_menu_sel=="menu_adjustment" || $top_menu_sel=="menu_transfer" || $top_menu_sel=="menu_registerinventory" || $top_menu_sel=="menu_registerwarehouse") active @endif ">
		<a href="#">
			<i class="fa fa-archive"></i>
			<span>Inventori</span>
			<i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
			<li @if ($top_menu_sel=="menu_inventory") class="active" @endif ><a href="{{route('viewinventory.index')}}"><i class="fa fa-folder-open-o"></i> <span>Lihat Stok</span></a></li>
			<li @if ($top_menu_sel=="menu_adjustment") class="active" @endif ><a href="{{route('adjustinventory.index')}}"><i class="fa fa-database"></i> <span>Penyesuaian Stok</span></a></li>
			<li @if ($top_menu_sel=="menu_transfer") class="active" @endif ><a href="{{route('customer.index')}}"><i class="fa fa-exchange"></i> <span>Transfer Antar Gudang</span></a></li>
			<li @if ($top_menu_sel=="menu_registerinventory") class="active" @endif ><a href="{{route('registerinventory.create')}}"><i class="fa fa-cubes"></i> <span>Daftar Barang</span></a></li>
			<li @if ($top_menu_sel=="menu_registerwarehouse") class="active" @endif ><a href="{{route('customer.index')}}"><i class="fa fa-industry"></i> <span>Daftar Gudang</span></a></li>
		</ul>
	</li>
	<li class="treeview <?php if (0) { ?> active <?php } ?>">
		<a href="#">
			<i class="fa fa-gears "></i>
			<span>Manufaktur</span>
			<i class="fa fa-angle-left pull-right"></i>
		</a>
	</li>
	<li class="treeview <?php if (0) { ?> active <?php } ?>">
		<a href="#">
			<i class="fa fa-money"></i>
			<span>Kas</span>
			<i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-plus"></i> <span>Penerimaan</span></a></li>
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-minus"></i> <span>Pengeluaran</span></a></li>
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-exchange"></i> <span>Transfer Antar Akun</span></a></li>
			<li <?php if (0){ ?> class="active" <?php } ?>><a href="{{route('customer.index')}}"><i class="fa fa-user-plus"></i> <span>Daftar Akun</span></a></li>
		</ul>
	</li>
	<li class="treeview <?php if (0) { ?> active <?php } ?>">
		<a href="#">
			<i class="fa  fa-book"></i>
			<span>Buku Besar</span>
			<i class="fa fa-angle-left pull-right"></i>
		</a>
	</li>
	
</ul>

  </section>
  <!-- /.sidebar -->
</aside>