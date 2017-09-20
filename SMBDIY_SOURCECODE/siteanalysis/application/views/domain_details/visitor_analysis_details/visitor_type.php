<div role="tabpanel" class="tab-pane" id="visitor_type">
	<div id="visitor_type_success_msg" class="text-center" ></div>	
	<!-- <div id="visitor_type_name"></div> -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
			<h3><div class="well text-center">Domain Name - <span class="domain_name"></span></div></h3>
		</div>

		<div class="col-xs-10 col-md-10 col-lg-8" style="margin-right: -23px;">
			<!-- Date range -->
			<div class="form-group">
				<label>Date range:</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right reservation" id="visitor_type_date" />
				</div><!-- /.input group -->
			</div><!-- /.form group -->
		</div>
		<div class="col-xs-1 col-lg-1" style="margin-top:25px;"><button class="btn btn-info search_button"><i class="fa fa-binoculars"></i> search</button></div>

		<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;">Day Wise New vs Returning User Report From <span id="visitor_type_from_date"></span> to <span id="visitor_type_to_date"></span></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="chart">
						<div class="chart" id="visitor_type_bar-chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-lg-10" style="margin-top: 35px;"></div>

		<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-2 col-lg-6 col-lg-offset-2">
			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;">Total New vs Returning User</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<div class="chart-responsive">
									<canvas id="visitor_type_pieChart" height="220"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:35px;">
								<ul class="chart-legend clearfix" id="visitor_type_color_codes">
									<li><i class="fa fa-circle-o" style="color: #C8EBE5;"></i> New User</li>
			                        <li><i class="fa fa-circle-o" style="color: #F5A196;"></i> Returning User</li>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

	</div>
</div>