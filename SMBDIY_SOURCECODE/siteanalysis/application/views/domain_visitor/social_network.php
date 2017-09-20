<div role="tabpanel" class="tab-pane fade" id="social_network">
	<div id="social_network_success_msg" class="text-center" ></div>		
	<!-- <div id="social_network_name"></div> -->
	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center">Domain Name - <span class="domain_name"></span></div></h3>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">
			<div class="text-center"><h2 style="font-weight:900;">Social Network Comparison</h2></div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-8 col-xs-12">
						<div class="chart-responsive">
							<canvas id="social_network_pieChart" height="220"></canvas>
						</div><!-- ./chart-responsive -->
					</div><!-- /.col -->
					<div class="col-md-4 col-xs-12" style="padding-top:35px;">
						<ul class="chart-legend clearfix" id="color_codes">
						</ul>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.box-body -->
		</div>		
	</div>
	
	<div class="row" style="margin-top: 25px;"></div>

	<div class="row well" style="">
		<div class="col-xs-12 text-center"><h2 style="color: #0073B7;">Facebook Information</h2></div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<!-- small box -->
			<div class="small-box bg-blue">
				<div class="inner">
					<h3 id="fb_total_like"></h3>
					<p>Likes</p>
				</div>
				<div class="icon">
					<i class="fa fa-facebook"></i>
					<i class="fa fa-thumbs-o-up"></i>
				</div>
				<a href="#" class="small-box-footer" style="font-size:16px;">
					<b>Facebook</b>
				</a>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<!-- small box -->
			<div class="small-box bg-blue">
				<div class="inner">
					<h3 id="fb_total_comment"></h3>
					<p>Comments</p>
				</div>
				<div class="icon">
					<i class="fa fa-facebook"></i>
					<i class="fa fa-comments"></i>
				</div>
				<a href="#" class="small-box-footer" style="font-size:16px;">
					<b>Facebook</b>
				</a>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<!-- small box -->
			<div class="small-box bg-blue">
				<div class="inner">
					<h3 id="fb_total_share"></h3>
					<p>Shares</p>
				</div>
				<div class="icon">
					<i class="fa fa-facebook"></i>
					<i class="fa fa-share-alt"></i>
				</div>
				<a href="#" class="small-box-footer" style="font-size:16px;">
					<b>Facebook</b>
				</a>
			</div>
		</div>
	</div>

	<div class="row well" style="">
		<div class="col-xs-12 text-center"><h2 style="color: #82683B;">Stumbleupon Information</h2></div>

		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #DD4B39;border-bottom:2px solid #DD4B39;">
				<span class="info-box-icon bg-red"><i class="fa fa-stumbleupon"></i></span>
				<div class="info-box-content">
					<span class="info-box-text" style="font-size: 18px;">Total Like</span>
					<span class="info-box-number" id="stumbleupon_total_like"></span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>

		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-stumbleupon"></i></span>
				<div class="info-box-content">
					<span class="info-box-text" style="font-size: 17px;">Total Comment</span>
					<span class="info-box-number" id="stumbleupon_total_comment"></span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>

		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
				<span class="info-box-icon bg-green"><i class="fa fa-stumbleupon"></i></span>
				<div class="info-box-content">
					<span class="info-box-text" style="font-size: 18px;">Total View</span>
					<span class="info-box-number" id="stumbleupon_total_view"></span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>

		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;">
				<span class="info-box-icon bg-blue"><i class="fa fa-stumbleupon"></i></span>
				<div class="info-box-content">
					<span class="info-box-text" style="font-size: 18px;">Total List</span>
					<span class="info-box-number" id="stumbleupon_total_list"></span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
	</div>


	<div class="row well">
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title text-center"><i class="fa fa-google-plus" style="color: orange;"></i> Google Plus Info</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<h1 style="color: orange;" id="google_plus_share"></h1>
					<b>Google Plus Share</b>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title text-center"><i class="fa fa-pinterest-p" style="color: orange;"></i> Pinterest Info</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<h1 style="color: orange;" id="pinterest_pin"></h1>
					<b>Pinterest Pin</b>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title text-center"><i class="fa fa-btc" style="color: orange;"></i> Buffer Info</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<h1 style="color: orange;" id="buffer_share"></h1>
					<b>Buffer Share</b>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title text-center"><i class="fa fa-xing" style="color: orange;"></i> Xing Info</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<h1 style="color: orange;" id="xing_share"></h1>
					<b>Xing Share</b>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title text-center"><i class="fa fa-linkedin" style="color: orange;"></i> Linkedin Info</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<h1 style="color: orange;" id="linkedin_share"></h1>
					<b>Linkedin Share</b>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>

	<div class="row well" style="">
		<div class="col-xs-12 text-center"><h2 style="color: #00A65A;">Reddit Information</h2></div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-blue">
				<span class="info-box-icon"><i class="fa fa-reddit"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number" id="reddit_score"></span>
					<div class="progress">
						<div class="progress-bar" style="width: 70%"></div>
					</div>
					<span class="progress-description">
						<b style="font-size: 18px;">Reddit Score</b>
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-green">
				<span class="info-box-icon"><i class="fa fa-reddit"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number" id="reddit_ups"></span>
					<div class="progress">
						<div class="progress-bar" style="width: 70%"></div>
					</div>
					<span class="progress-description">
						<b style="font-size: 18px;">Reddit Ups</b>
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-red">
				<span class="info-box-icon"><i class="fa fa-reddit"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number" id="reddit_downs"></span>
					<div class="progress">
						<div class="progress-bar" style="width: 70%"></div>
					</div>
					<span class="progress-description">
						<b style="font-size: 18px;">Reddit Downs</b>
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
	</div>

</div>