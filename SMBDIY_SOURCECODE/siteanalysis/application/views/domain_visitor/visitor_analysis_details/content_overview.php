<div role="tabpanel" class="tab-pane" id="content_overview">	
	<div id="content_overview_success_msg" class="text-center" ></div>	
	<!-- <div id="content_overview_name"></div> -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3><div class="well text-center">Domain Name - <span class="domain_name"></span></div></h3>
		</div>

		<div class="col-xs-10 col-md-12 col-lg-10" style="margin-right: -23px;">
			<!-- Date range -->
			<div class="form-group">
				<label><?php echo $this->lang->line("date range");?>:</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right reservation" id="content_overview_date" />
				</div><!-- /.input group -->
			</div><!-- /.form group -->
		</div>
		<div class="col-xs-12 col-lg-1" style="margin-top:25px;"><button class="btn btn-info search_button"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("search");?></button></div>
		
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"><i class="fa fa-star"></i> Top Web Pages From <span id="content_overview_from_date"></span> To <span id="content_overview_to_date"></span></h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div id="content_overview_data">
						
					</div>
				</div>
			</div> <!-- end box -->			
		</div>

	</div>
</div>