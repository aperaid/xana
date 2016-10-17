<header class="main-header">
<!-- Logo -->
@include('layouts.xana.logo')
  
	<!-- Header Navbar: style can be found in header.less -->
	<nav class="navbar navbar-static-top" role="navigation">
		<!-- Sidebar toggle button-->
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
			<span class="sr-only">Buka/Tutup</span>
		</a>
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<!-- User Account: style can be found in dropdown.less -->
				<li class="dropdown user user-menu">
					<a href="{{route('customer.index')}}" class="dropdown-toggle" data-toggle="dropdown">
						<span class="hidden-xs">{{ Auth::user()->name }}</span>
					</a>			
					<ul class="dropdown-menu">
						
						<!-- User image -->
						<li class="user-header">
							<img src="{{ asset('/img/'. Auth::user()->id .'.jpg') }}" class="img-circle" alt="User Image">
							<p>
								{{ Auth::user()->name }}
								<small>{{ Auth::user()->access }}</small>
							</p>
						</li>
						
						<!-- Menu Footer-->
						<li class="user-footer">
							<div class="pull-right">
								<a href="{{ url('/logout') }}" 
									class="btn btn-default btn-flat"
									onclick="event.preventDefault();
											 document.getElementById('logout-form').submit();">
									Logout
                                </a>
								
								<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                </form>
								
							</div>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
</header>
<!-- Left side column. contains the logo and sidebar -->
