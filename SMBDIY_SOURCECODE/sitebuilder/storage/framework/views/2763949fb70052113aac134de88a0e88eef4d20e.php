<nav class="mainnav navbar navbar-inverse navbar-embossed navbar-fixed-top" role="navigation" id="mainNav">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
			<span class="sr-only">Toggle Navigation</span>
		</button>
		<a class="brand-navbar" href="<?php echo e($cdata[1]); ?>">
			<img class="logo-inside" src="<?php echo e(url('images/SMBDIY_Logo2.png')); ?>" alt="SMBDIY">
		</a>
	</div>
	<div class="collapse navbar-collapse" id="navbar-collapse-01">
		<ul class="nav navbar-nav">
			<?php if(isset($data['siteData']) || (isset($page) && $page == 'newPage')): ?>
			<?php if(isset($data['siteData'])): ?>
			<li class="active">
				<a><span class="fui-home"></span> <span id="siteTitle"><?php echo e($data['siteData']['site'][0]->site_name); ?></span></a>
			</li>
			<?php endif; ?>
			<?php if(isset($page) && $page == 'newPage'): ?>
			<li class="active">
				<a><span class="fui-home"></span> <span id="siteTitle">My New Site</span> </a>
			</li>
			<?php endif; ?>
			<!-- <li><a href="<?php echo e(route('dashboard')); ?>" id="backButton"><span class="fui-arrow-left"></span> Back to Sites</a></li> -->
			<!--<li><a href="<?php echo e(URL::previous()); ?>" id="backButton"><span class="fui-arrow-left"></span> Back to Sites</a></li>-->
			<li class="<?php echo e(Request::path() ==  'assets' ? 'active' : ''); ?>"><a href="<?php echo e(route('assets')); ?>"><span class="fui-image"></span> Image Library</a></li>
			<li class=""><a href="<?php echo e(route('userdashboard')); ?>"><span class="fui-arrow-left"></span> Back to User Dashboard</a></li>
			<?php else: ?>
		<!--	<li class="<?php echo e(Request::path() ==  'dashboard' ? 'active' : ''); ?>"><a href="<?php echo e(route('dashboard')); ?>"><span class="fui-windows"></span> Sites</a></li> -->
			<li class="<?php echo e(Request::path() ==  'assets' ? 'active' : ''); ?>"><a href="<?php echo e(route('assets')); ?>"><span class="fui-image"></span> Image Library</a></li>
			<li class=""><a href="<?php echo e(route('userdashboard')); ?>"><span class="fui-arrow-left"></span> Back to User Dashboard</a></li>
			<?php if(Auth::user()->type == 'admin'): ?>
			<li class="<?php echo e(Request::path() ==  'users' ? 'active' : ''); ?>"><a href="<?php echo e(route('users')); ?>"><span class="fui-user"></span> Users</a></li>
			<li class="<?php echo e(Request::path() ==  'settings' ? 'active' : ''); ?>"><a href="<?php echo e(route('settings')); ?>"><span class="fui-gear"></span> Settings</a></li>
			
			<?php endif; ?>
			<?php endif; ?>
		</ul>
		<ul class="nav navbar-nav navbar-right" style="margin-right: 20px;">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Hi, <?php echo e(Auth::user()->first_name); ?> <b class="caret"></b></a>
				<span class="dropdown-arrow"></span>
				<ul class="dropdown-menu">
					<li><a href="#accountModal" data-toggle="modal"><span class="fui-cmd"></span> My Account</a></li>
					<li class="divider"></li>
					<li class=""><a href="<?php echo e(route('userdashboard')); ?>"><span class="fui-arrow-left"></span> Back to User Dashboard</a></li>
					<li class="divider"></li>
					<li><a href="<?php echo e(route('logout')); ?>"><span class="fui-exit"></span> Logout</a></li>
				</ul>
			</li>
		</ul>
	</div><!-- /.navbar-collapse -->
</nav><!-- /navbar -->