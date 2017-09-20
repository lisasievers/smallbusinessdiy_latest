<style type="text/css">
	.tabs-below > .nav-tabs,
	.tabs-right > .nav-tabs,
	.tabs-left > .nav-tabs {
	  border-top: 0;
	}

	.tab-content > .tab-pane,
	.pill-content > .pill-pane {
	  display: none;
	}

	.tab-content > .active,
	.pill-content > .active {
	  display: block;
	}

	.tabs-below > .nav-tabs {
	  border-top: 1px solid #fff;
	}

	.tabs-below > .nav-tabs > li {
	  margin-bottom: -1px;
	  margin-top: 0;
	}

	.tabs-below > .nav-tabs > li > a {
	  -webkit-border-radius: 4px 4px 0 0;
	     -moz-border-radius: 4px 4px 0 0;
	          border-radius: 4px 4px 0 0;
	}

	.tabs-below > .nav-tabs > li > a:hover,
	.tabs-below > .nav-tabs > li > a:focus {
	  border-bottom-color: transparent;
	  border-top-color: #ddd;
	  border-top:2px solid orange;
	}

	.tabs-below > .nav-tabs > .active > a,
	.tabs-below > .nav-tabs > .active > a:hover,
	.tabs-below > .nav-tabs > .active > a:focus {
	  border-color: #ddd #ddd transparent #ddd;
	  border-top:2px solid orange;
	}
</style>
<section class="content">
	<div class="row">
		<div class="col-xs-12">			
			<div class="tabs-below">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#todays_report" aria-controls="todays_report" role="tab" data-toggle="tab">Today's Report</a></li>
					<li role="presentation"><a href="#this_week_report" aria-controls="this_week_report" role="tab" data-toggle="tab">This Week's Report</a></li>
					<li role="presentation"><a href="#this_month_report" aria-controls="this_month_report" role="tab" data-toggle="tab">This Month's Report</a></li>
					<li role="presentation"><a href="#all_time_report" aria-controls="all_time_report" role="tab" data-toggle="tab">All Time's Report</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">					
					<?php $this->load->view('admin/url_shortener/todays_report'); ?>
					<?php $this->load->view('admin/url_shortener/this_week_report'); ?>
					<?php $this->load->view('admin/url_shortener/this_month_report'); ?>
					<?php $this->load->view('admin/url_shortener/all_time_report'); ?>				
				</div>
			</div>
		</div>
	</div>
</section>
