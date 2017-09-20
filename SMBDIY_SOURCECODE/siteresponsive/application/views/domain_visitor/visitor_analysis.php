<style type="text/css">
	.tabs-left > .nav-tabs > li,
	.tabs-right > .nav-tabs > li {
	  float: none;
	}

	.tabs-left > .nav-tabs > li > a,
	.tabs-right > .nav-tabs > li > a {
	  min-width: 74px;
	  margin-right: 0;
	  margin-bottom: 3px;
	}

	.tabs-left > .nav-tabs {
	  float: left;
	  margin-right: 19px;
	  border-right: 1px solid #ddd;
	}

	.tabs-left > .nav-tabs > li > a {
	  margin-right: -1px;
	  -webkit-border-radius: 4px 0 0 4px;
	     -moz-border-radius: 4px 0 0 4px;
	          border-radius: 4px 0 0 4px;
	}

	.tabs-left > .nav-tabs > li > a:hover,
	.tabs-left > .nav-tabs > li > a:focus {
	  border-color: #eeeeee #dddddd #eeeeee #eeeeee;
	  border-left:2px solid orange;

	}

	.tabs-left > .nav-tabs .active > a,
	.tabs-left > .nav-tabs .active > a:hover,
	.tabs-left > .nav-tabs .active > a:focus {
	  border-color: #ddd transparent #ddd #ddd;
	  *border-right-color: #ffffff;
	  border-left:2px solid orange;
	}

	.tabs-right > .nav-tabs {
	  float: right;
	  margin-left: 19px;
	  border-left: 1px solid #ddd;
	}

	.tabs-right > .nav-tabs > li > a {
	  margin-left: -1px;
	  -webkit-border-radius: 0 4px 4px 0;
	     -moz-border-radius: 0 4px 4px 0;
	          border-radius: 0 4px 4px 0;
	}

	.tabs-right > .nav-tabs > li > a:hover,
	.tabs-right > .nav-tabs > li > a:focus {
	  border-color: #eeeeee #eeeeee #eeeeee #dddddd;
	  border-right:2px solid orange;
	}

	.tabs-right > .nav-tabs .active > a,
	.tabs-right > .nav-tabs .active > a:hover,
	.tabs-right > .nav-tabs .active > a:focus {
	  border-color: #ddd #ddd #ddd transparent;
	  *border-left-color: #ffffff;
	  border-right:2px solid orange;
	}
</style>
<div role="tabpanel" class="tab-pane fade" id="visitor_analysis">
	<div id="visitor_analysis_success_msg" class="text-center" ></div>	
	<div id="visitor_analysis_name"></div>

	<div class="tabs-right">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">overview</a></li>
			<li role="presentation"><a href="#traffic_source" aria-controls="traffic_source" role="tab" data-toggle="tab">traffic source</a></li>
			<li role="presentation"><a href="#visitor_type" aria-controls="visitor_type" role="tab" data-toggle="tab">visitor type</a></li>
			<li role="presentation"><a href="#content_overview" aria-controls="content_overview" role="tab" data-toggle="tab">content overview</a></li>
			<li role="presentation"><a href="#country_wise_report" aria-controls="country_wise_report" role="tab" data-toggle="tab">country wise report</a></li>
			<li role="presentation"><a href="#browser_report" aria-controls="browser_report" role="tab" data-toggle="tab">browser report</a></li>
			<li role="presentation"><a href="#os_report" aria-controls="os_report" role="tab" data-toggle="tab">os report</a></li>
			<li role="presentation"><a href="#device_report" aria-controls="device_report" role="tab" data-toggle="tab">device report</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">			
			<?php $this->load->view('domain/visitor_analysis_details/overview'); ?>
			<?php $this->load->view('domain/visitor_analysis_details/traffic_source'); ?>
			<?php $this->load->view('domain/visitor_analysis_details/visitor_type'); ?>
			<?php $this->load->view('domain/visitor_analysis_details/content_overview'); ?>
			<?php $this->load->view('domain/visitor_analysis_details/country_wise_report'); ?>
			<?php $this->load->view('domain/visitor_analysis_details/browser_report'); ?>
			<?php $this->load->view('domain/visitor_analysis_details/os_report'); ?>
			<?php $this->load->view('domain/visitor_analysis_details/device_report'); ?>
		</div>

	</div>
</div>