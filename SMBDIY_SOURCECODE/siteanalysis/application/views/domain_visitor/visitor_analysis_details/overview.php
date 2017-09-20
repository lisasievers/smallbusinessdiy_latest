<div role="tabpanel" class="tab-pane active" id="overview">
	<div id="overview_success_msg" class="text-center" ></div>	
	<!-- <div id="overview_name"></div> -->
	<!-- <div id="visitor_analysis_name"></div> -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3><div class="well text-center">Domain Name - <span class="domain_name"></span></div></h3>
		</div>

		<div class="col-xs-10 col-md-10 col-lg-10" style="margin-right: -23px;">
			<!-- Date range -->
			<div class="form-group">
				<label><?php echo $this->lang->line("date range");?>:</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right reservation" id="overview_date" />
				</div><!-- /.input group -->
			</div><!-- /.form group -->
			<!-- end of date range -->
		</div>
		<div class="col-xs-12 col-lg-1" style="margin-top:25px;"><button class="btn btn-info search_button"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("search");?></button></div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <!-- LINE CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title" style="color: blue; word-spacing: 4px;">Day Wise New Visitor Report From <span id="overview_from_date"></span> to <span id="overview_to_date"></span></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="line-chart" style="height: 300px;"></div>
            </div><!-- /.box-body -->
          </div><!-- /.box -->
        </div><!-- /.col lg-10 -->
		
		<div style="margin-top: 30px;" class="col-xs-10"></div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        	<div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
				<span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text" style="font-size: 20px;">Total Unique Visitor</span>
					<span class="info-box-number" id="total_unique_visitor"></span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        	<div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
				<span class="info-box-icon bg-green"><i class="fa fa-file-text-o"></i></span>
				<div class="info-box-content">
					<span class="info-box-text" style="font-size: 20px;">Total Page View</span>
					<span class="info-box-number" id="total_page_view"></span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
        </div>

        <div style="margin-top: 20px;" class="col-xs-12"></div>

        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        	<div class="info-box bg-blue">
				<span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number" id="average_stay_time"></span>
					<div class="progress">
						<div class="progress-bar" style="width: 70%"></div>
					</div>
					<span class="progress-description">
						<b style="font-size: 18px;">Average Stay Time</b>
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
        </div>

        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        	<div class="info-box bg-green">
				<span class="info-box-icon"><i class="fa fa-eye"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number" id="average_visit"></span>
					<div class="progress">
						<div class="progress-bar" style="width: 70%"></div>
					</div>
					<span class="progress-description">
						<b style="font-size: 18px;">Average Visit</b>
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        	<div class="info-box bg-red">
				<span class="info-box-icon"><i class="fa fa-sign-out"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number" id="bounce_rate"></span>
					<div class="progress">
						<div class="progress-bar" style="width: 70%"></div>
					</div>
					<span class="progress-description">
						<b style="font-size: 18px;">Bounce Rate</b>
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
        </div>
	
	</div>

</div>