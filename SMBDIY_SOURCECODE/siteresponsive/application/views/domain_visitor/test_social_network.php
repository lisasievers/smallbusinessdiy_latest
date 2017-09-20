<div role="tabpanel" class="tab-pane fade" id="social_network">
	<div id="social_network_success_msg" class="text-center" ></div>		
	<!-- <div id="social_network_name"></div> -->
	
	<div class="row">
		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<!-- Tabs within a box -->
				<ul class="nav nav-tabs pull-right">
					<li class="active"><a href="#revenue-chart" data-toggle="tab">Area</a></li>
					<!-- <li><a href="#sales-chart" data-toggle="tab">Donut</a></li> -->
					<li class="pull-left header"><i class="fa fa-inbox"></i> Sales</li>
				</ul>
				<div class="tab-content no-padding">
					<!-- Morris chart - Sales -->
					<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
					<!-- <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;"></div> -->
				</div>
			</div><!-- /.nav-tabs-custom -->
		</div>		
	</div>

	<div class="row" style="padding:15px;">
		<div class="col-lg-4">
			<!-- small box -->
			<div class="small-box bg-aqua">
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
		<div class="col-lg-4">
			<!-- small box -->
			<div class="small-box bg-green">
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
		<div class="col-lg-4">
			<!-- small box -->
			<div class="small-box bg-red">
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
</div>


<script type="text/javascript">
	var area = new Morris.Area({
	element: 'revenue-chart',
	resize: true,
	data: [
	  {y: '2011 Q1', item1: 2666, item2: 2666, item3: 2234},
	  {y: '2011 Q2', item1: 2778, item2: 2294, item3: 2634},
	  {y: '2011 Q3', item1: 4912, item2: 1969, item3: 2534},
	  {y: '2011 Q4', item1: 3767, item2: 3597, item3: 2334},
	  {y: '2012 Q1', item1: 6810, item2: 1914, item3: 2534},
	  {y: '2012 Q2', item1: 5670, item2: 4293, item3: 2634},
	  {y: '2012 Q3', item1: 4820, item2: 3795, item3: 2534},
	  {y: '2012 Q4', item1: 15073, item2: 5967, item3: 2234},
	  {y: '2013 Q1', item1: 10687, item2: 4460, item3: 2434},
	  {y: '2013 Q2', item1: 8432, item2: 5713, item3: 2334}
	],
	xkey: 'y',
	ykeys: ['item1', 'item2', 'item3'],
	labels: ['Item 1', 'Item 2', 'Item 3'],
	lineColors: ['#a0d0e0', '#3c8dbc', '#adabca'],
	hideHover: 'auto'
	});
</script>