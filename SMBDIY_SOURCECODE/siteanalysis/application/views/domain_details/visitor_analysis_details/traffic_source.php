<div role="tabpanel" class="tab-pane" id="traffic_source">
	<div id="traffic_source_success_msg" class="text-center" ></div>	
	<!-- <div id="traffic_source_name"></div> -->
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
					<input type="text" class="form-control pull-right reservation" id="traffic_source_date" />
				</div><!-- /.input group -->
			</div><!-- /.form group -->
		</div>
		<div class="col-xs-1 col-lg-1" style="margin-top:25px;"><button class="btn btn-info search_button"><i class="fa fa-binoculars"></i> search</button></div>

		<div class="col-lg-5 col-xs-12">
			<!-- DONUT CHART -->
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Top Referrer (%)</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="chart" id="top_referrer_chart" style="height: 300px; position: relative;"></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-lg-5 col-xs-12">
			<!-- BAR CHART -->
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">Total Traffic</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="chart" id="bar-chart" style="height: 300px;"></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		

        <div class="col-lg-10" style="margin-top: 25px;"></div>

		<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
          <!-- LINE CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title" style="color: blue; word-spacing: 5px;">Day Wise Traffic Source Report From <span id="traffic_source_from_date"></span> to <span id="traffic_source_to_date"></span></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
               <!-- Morris chart - Sales -->
               <div class="chart tab-pane active" id="traffic_line-chart" style="position: relative; height: 300px;"></div>
            </div><!-- /.box-body -->
          </div><!-- /.box -->
        </div><!-- /.col lg-10 -->

		<div class="col-lg-10" style="margin-top: 25px;"></div>

		<div class="col-xs-12 col-lg-5">
			<div class="text-center"><h2 style="font-weight:800;">Traffic From Search Engines</h2></div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-8 col-xs-12">
						<div class="chart-responsive">
							<canvas id="search_engine_traffic_pieChart" height="220"></canvas>
						</div><!-- ./chart-responsive -->
					</div><!-- /.col -->
					<div class="col-md-4 col-xs-12" style="padding-top:35px;">
						<ul class="chart-legend clearfix" id="search_engine_traffic_color_codes">
						</ul>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.box-body -->
		</div>

		<div class="col-xs-12 col-lg-5">
			<div class="text-center"><h2 style="font-weight:800;">Traffic From Social Networks</h2></div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-8 col-xs-12">
						<div class="chart-responsive">
							<canvas id="social_network_traffic_pieChart" height="220"></canvas>
						</div><!-- ./chart-responsive -->
					</div><!-- /.col -->
					<div class="col-md-4 col-xs-12" style="padding-top:35px;">
						<ul class="chart-legend clearfix" id="social_network_traffic_color_codes">
						</ul>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.box-body -->
		</div>

	</div>

	
</div>