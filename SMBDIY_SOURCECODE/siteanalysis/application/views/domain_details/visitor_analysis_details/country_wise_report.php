<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div role="tabpanel" class="tab-pane" id="country_wise_report">
	<div id="country_wise_report_success_msg" class="text-center" ></div>	
	<!-- <div id="country_wise_report_name"></div> -->
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
					<input type="text" class="form-control pull-right reservation" id="country_wise_report_date" />
				</div><!-- /.input group -->
			</div><!-- /.form group -->
		</div>
		<div class="col-xs-1 col-lg-1" style="margin-top:25px;"><button class="btn btn-info search_button"><i class="fa fa-binoculars"></i> search</button></div>
		

		<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-2 col-lg-6 col-lg-offset-2">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 3px;" class="box-title"><i class="fa fa-flag"></i> Country Wise New Visitor Report</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive text-center" style="background: gray;">
					<div id="regions_div">
						
					</div>
				</div>
			</div> <!-- end box -->			
		</div>

		<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10" style="margin-top: 25px;"></div>

		<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 3px;" class="box-title"><i class="fa fa-map-marker"></i> Country Wise Report From <span id="country_wise_visitor_from_date"></span> To <span id="country_wise_visitor_to_date"></span></h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive text-center">
					<div class="table-responsive">
						<div id="country_wise_table_data">
							
						</div>
					</div>
				</div>
			</div> <!-- end box -->			
		</div>

	</div>
</div>


<!-- Start modal for new search. -->
<div id="modal_for_country_report" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="new_search_details_title" class="modal-title"><i class="fa fa-binoculars"></i> Details Information About <span id="id_for_country_name"></span></h4>
			</div>

			<div class="modal-body">
				<div class="row"><div class="text-center" id="modal_waiting_country_name"></div></div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			          <!-- LINE CHART -->
			          <div class="box box-info">
			            <div class="box-header with-border">
			              <h3 class="box-title" style="color: blue; word-spacing: 4px;">Day Wise Sessions Report From <span id="country_name_from_date"></span> to <span id="country_name_to_date"></span></h3>
			              <div class="box-tools pull-right">
			                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
			              </div>
			            </div>
			            <div class="box-body chart-responsive">
			              <div class="chart" id="country_name_line_chart" style="height: 300px; width: 100%;"></div>
			            </div><!-- /.box-body -->
			          </div><!-- /.box -->
			        </div><!-- /.col lg-10 -->
				</div>
				<div class="row">
					<div class="col-xs-12" style="padding: 15px;">						
						<div class="table-responsive" id="individual_country_data_table">
							
						</div>
					</div>
				</div>
			</div>


			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End modal for new search. -->