<div role="tabpanel" class="tab-pane" id="device_report">
	<div id="device_report_success_msg" class="text-center" ></div>	
	<!-- <div id="device_report_name"></div> -->
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
					<input type="text" class="form-control pull-right reservation" id="device_report_date" />
				</div><!-- /.input group -->
			</div><!-- /.form group -->
		</div>
		<div class="col-xs-12 col-lg-1" style="margin-top:25px;"><button class="btn btn-info search_button"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("search");?></button></div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 3px;" class="box-title"><i class="fa fa-tv"></i> Report From <span id="device_table_from_date"></span> To <span id="device_table_to_date"></span></h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div id="device_report_name" class="table-responsive">
						
					</div>
				</div>
			</div> <!-- end box -->			
		</div>

	</div>
</div>


<!-- Start modal for new search. -->
<div id="modal_for_device_report" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="new_search_details_title" class="modal-title"><i class="fa fa-binoculars"></i> Details Information About <span id="id_for_device_name"></span></h4>
			</div>

			<div class="modal-body">
				<div class="row"><div class="text-center" id="modal_waiting_device_name"></div></div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			          <!-- LINE CHART -->
			          <div class="box box-info">
			            <div class="box-header with-border">
			              <h3 class="box-title" style="color: blue; word-spacing: 4px;">Day Wise Sessions Report From <span id="device_name_from_date"></span> to <span id="device_name_to_date"></span></h3>
			              <div class="box-tools pull-right">
			                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
			              </div>
			            </div>
			            <div class="box-body table-responsive">
			              <div class="chart" id="device_name_line_chart" style="height: 300px; width: 850px;"></div>
			            </div><!-- /.box-body -->
			          </div><!-- /.box -->
			        </div><!-- /.col lg-10 -->
				</div>
			</div>


			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End modal for new search. -->