<style>
#chosensite{width:350px;height: 42px;padding: 10px;}
.act-group{margin-top: 10px;}
.sitedoc-section{border:2px solid green;}
.sitespy-section{border:2px solid orange;}
.sitereview-section{border:2px solid blue;}

.panel-heading{text-align: center;border-bottom-width: 1px;border-bottom-color: green;}
</style>
  <?php //dd($data); ?>
    <?php if( isset($sites) && count( $sites ) > 0 ): ?>
<div class="row">

  <div class="sitelist col-md-6">
  <form role="form" id="ReportSite" action="<?php echo e(route('user.reportshome')); ?>" method="post">
     <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
    <h2>Your Sites</h2>
<select id="chosensite" data-style="btn-info" name="sitename">
 
<?php foreach( $sites as $site ): ?>
<option value="<?php echo e($site['id']); ?>"><?php echo e($site['site_name']); ?></option>
 <?php endforeach; ?>

  </select>
  <div class="act-group">
<button type="submit" class="btn btn-info">Go to Reports</button>
</div>
</form>
  </div>
</div>
<?php endif; ?>
<?php if( isset($site_info) && count( $site_info ) > 0 ): ?>
  <div class="row sitedoc-section">
<?php //print_r($site_info); ?> 
                <div class="panel-heading">
                    <h3 class="panel-title">
                        DIY Doctor report details
                    </h3>
                    <a href="<?php echo e($tools['sitedoctor']); ?>/index.php/health_check/report/<?php echo e($site_info[0]['id']); ?>/<?php echo e($page_title); ?>"> Learn More</a>
                </div>
<div class="row">
      <div class="col-xs-12">       
        <!-- page title start-->
        <?php 
        $recommendation_word="Knowledge Base";
        $value=$site_info[0]["title"];
       // $check=$this->site_check->title_check($value); 
        $item="Page Title";
        //$long_recommendation=$this->config->item('page_title_recommendation');
        if(strlen($value)==0) //error
        {
          $class="red";
          $status="remove";
          $short_recommendation="Your site do not have any title.";
        }
        
        else //ok
        {
          $class="green";
          $status="check";
          $short_recommendation="Your page title does not exceed 60 characters. It's fine.";
        }
        ?>
        <div class="box box-<?php echo $class;?>">
          <div class="box-header with-border">
            <h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
            <div class="box-tools pull-right">
              <i class="fa fa-minus minus"></i>
            </div>
          </div>
          <div class="box-body chart-responsive minus"> 
            <i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item; ?> :</b> <?php echo $value; ?>       
            <br/><br/><br/>
            <?php echo $short_recommendation; ?>
            <br/><br/>
            <a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
            <div class="recommendation well"><?php //echo $long_recommendation; ?></div>

          </div>
        </div> 
        <!--  page title end-->
      </div>
    </div>


<?php 
    $pass="Unknown";
    $score="Unknown";
    if(isset($mobile_ready_data["ruleGroups"]["USABILITY"]["pass"]))
    $pass=$mobile_ready_data["ruleGroups"]["USABILITY"]["pass"];
    if(isset($mobile_ready_data["ruleGroups"]["USABILITY"]["score"]))
    $score=$mobile_ready_data["ruleGroups"]["USABILITY"]["score"];

    if($pass=="1") //ok
    {
      $class="green";
      $status="check";
    }
    else //error
    {
      $class="red";
      $status="remove";
    }
    $item="Mobile Friendly Check";  
    
    ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-<?php echo $class;?>">
          <div class="box-header with-border">
            <h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
            <div class="box-tools pull-right">
              <i class="fa fa-minus minus"></i>
            </div>
          </div>
          <div class="box-body">  
            <div class="row">
              <div class="col-xs-12 col-md-9">
                <?php               
                 
                if($pass=="1") 
                echo "<br><h3 style='margin-top:0px;'><span class='label label-success'>Mobile Friendly : Yes <br></span></h3> Score : ".$score;
                else if($pass=="Unknown")  
                echo "<br><h3 style='margin-top:0px;'><span class='label label-danger'>Mobile Friendly : Unknown <br></span></h3> Score : ".$score;
                else echo "<br><h3 style='margin-top:0px;'><span class='label label-danger'>Mobile Friendly : No <br></span></h3> Score : ".$score;

                ?>
                <br><br>
                <div class=" table-responsive">
                  <table class="table table-hover table-striped">
                    <tr>
                      <th>Localized Rule Name</th>
                      <th>Rule Impact</th>
                    </tr>
                    <?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
                    {?>   
                      <tr>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"];
                          ?>
                        </td>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"];
                          ?>
                        </td>
                      </tr>
                    <?php 
                    } ?>

                    <?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
                    {?>   
                      <tr>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"];
                          ?>
                        </td>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"];
                          ?>
                        </td>
                      </tr>
                    <?php 
                    } ?>

                    <?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
                    {?>   
                      <tr>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"];
                          ?>
                        </td>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"];
                          ?>
                        </td>
                      </tr>
                    <?php 
                    } ?>

                    <?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
                    {?>   
                      <tr>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"];
                          ?>
                        </td>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"];
                          ?>
                        </td>
                      </tr>
                    <?php 
                    } ?>

                    <?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
                    {?>   
                      <tr>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"];
                          ?>
                        </td>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"];
                          ?>
                        </td>
                      </tr>
                    <?php 
                    } ?>

                    <?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
                    {?>   
                      <tr>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"];
                          ?>
                        </td>
                        <td>
                          <?php 
                          if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"]))
                          echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"];
                          ?>
                        </td>
                      </tr>
                    <?php 
                    } ?>

                    <?php if(!isset($mobile_ready_data["formattedResults"]["ruleResults"])) 
                    {?>   
                      <tr>
                        <td colspan="2" class="text-center">No data to show.</td>
                      </tr>
                    <?php 
                    } ?>

                  </table>
                </div>

                <div class="well">
                  <b>CMS:</b> <?php if(isset($mobile_ready_data["pageStats"]["cms"])) echo $mobile_ready_data["pageStats"]["cms"];?>                
                  <br><b>Locale:</b> <?php if(isset($mobile_ready_data["formattedResults"]["locale"])) echo $mobile_ready_data["formattedResults"]["locale"];?>               
                  <br><b>Roboted Resources:</b> <?php if(isset($mobile_ready_data["pageStats"]["numberRobotedResources"])) echo $mobile_ready_data["pageStats"]["numberRobotedResources"];?>                
                  <br><b>Transient Fetch Failure Resources:</b> <?php if(isset($mobile_ready_data["pageStats"]["numberTransientFetchFailureResources"])) echo $mobile_ready_data["pageStats"]["numberTransientFetchFailureResources"];?>                
                  <br>
                </div>

              </div>
              <div class="col-xs-12 col-md-3" style="margin-bottom:30px !important;padding-left:12px;min-height:530px;background: url('<?php echo e(asset("src/images/mobile.png")); ?>') no-repeat !important;">
                <?php 
                $mobile_ready_data=json_decode($site_info[0]["mobile_ready_data"],true);
                            
                if(isset($mobile_ready_data["screenshot"]["data"]))
                {
                  $src=str_replace("_", "/", $mobile_ready_data["screenshot"]["data"]);
                  $src=str_replace("-", "+", $src);
                  echo '<img src="data:image/jpeg;base64,'.$src.'" style="max-width:225px !important;margin-top:52px;">';
                }
                ?>
              </div>
            </div>        
          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div>    
   
    </div>


</div>

<?php endif; ?>

<!-- Sitespy reports -->

<?php if( isset($domain_info) && count( $domain_info ) > 0 ): ?>
  <div class="row sitespy-section">
<?php //print_r($site_info); ?> 
                <div class="panel-heading">
                    <h3 class="panel-title">
                        DIY SPY report details
                    </h3>
                    <a href="<?php echo e($tools['sitespy_website']); ?>/domain/domain_details_view/<?php echo e(2); ?>"> Learn More</a>
                </div>


        <div class="row">
          <div class="col-xs-12">           
            <div class="box bg-blue box-solid">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-street-view"></i> &nbsp&nbsp WhoIs Information</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div><!-- /.box-tools -->
              </div><!-- /.box-header -->
              <div class="box-body table-responsive" style="background: white; color: black; padding-bottom: 28px;">
                <table class="table table-hover table-condensed">
                  <tr>
                    <td>Registered</td>
                    <td>
                      <?php 
                        if($domain_info[0]['whois_is_registered'] == 'yes')
                          echo '<span class="label label-success">Yes</span>'; 
                        else
                          echo '<span class="label label-danger">No</span>';
                      ?> 
                    </td>
                  </tr>
                  <tr>
                    <td>Domain Age</td>
                    <td>
                      <?php 
                        if($domain_info[0]['whois_created_at'] != '0000-00-00'){                  
                          $end = date("Y-m-d");
                          $start = date("Y-m-d",strtotime($domain_info[0]['whois_created_at']));
                          //echo calculate_date_differece($end,$start); 
                        }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Tech Email</td>
                    <td><?php echo $domain_info[0]['whois_tech_email']; ?></td>
                  </tr>
                  <tr>
                    <td>Name Servers</td>
                    <td><?php echo $domain_info[0]['whois_name_servers']; ?></td>
                  </tr>
                  <tr>
                    <td>Created At</td>
                    <td><?php if($domain_info[0]['whois_created_at'] != '0000-00-00') echo date("d-M-Y",strtotime($domain_info[0]['whois_created_at'])); ?></td>
                  </tr>
                  <tr>
                    <td>Changed At</td>
                    <td><?php if($domain_info[0]['whois_changed_at'] != '0000-00-00') echo date("d-M-Y",strtotime($domain_info[0]['whois_changed_at'])); ?></td>
                  </tr>
                  <tr>
                    <td>Expire At</td>
                    <td><?php if($domain_info[0]['whois_expire_at'] != '0000-00-00') echo date("d-M-Y",strtotime($domain_info[0]['whois_expire_at'])); ?></td>
                  </tr>
                  <tr>
                    <td>Sponsor</td>
                    <td><?php echo $domain_info[0]['whois_sponsor']; ?></td>
                  </tr>
                  <tr>
                    <td>Registrant Name</td>
                    <td><?php echo $domain_info[0]['whois_registrant_name']; ?></td>
                  </tr>
                  <tr>
                    <td>Admin Name</td>
                    <td><?php echo $domain_info[0]['whois_admin_name']; ?></td>
                  </tr>
                  <tr>
                    <td>Registrant Email</td>
                    <td><?php echo $domain_info[0]['whois_registrant_email']; ?></td>
                  </tr>
                  <tr>
                    <td>Admin Email</td>
                    <td><?php echo $domain_info[0]['whois_admin_email']; ?></td>
                  </tr>
                  <tr>
                    <td>Registrant Country</td>
                    <td><img style="height: 15px; width: 20px; margin-top: -3px;" alt=" " src="<?php //echo base_url().'assets/images/flags/'.$domain_info[0]['whois_registrant_country'].'.png' ?>" >&nbsp;<?php echo $domain_info[0]['whois_registrant_country']; ?></td>
                  </tr>
                  <tr>
                    <td>Admin Country</td>
                    <td><img style="height: 15px; width: 20px; margin-top: -3px;" alt=" " src="<?php //echo base_url().'assets/images/flags/'.$domain_info[0]['whois_admin_country'].'.png' ?>" >&nbsp;<?php echo $domain_info[0]['whois_admin_country']; ?></td>
                  </tr>
                  <tr>
                    <td>Registrant Phone</td>
                    <td><?php echo $domain_info[0]['whois_registrant_phone']; ?></td>
                  </tr>
                  <tr>
                    <td>Admin Phone</td>
                    <td><?php echo $domain_info[0]['whois_admin_phone']; ?></td>
                  </tr>           
                </table>          
              </div><!-- /.box-body -->
            </div><!-- /.box -->
          </div>        
        </div>


</div>

<?php endif; ?>

<!-- End sitespy reports; Begin Website Review -->
<?php if( isset($site_info) && count( $site_info ) > 0 ): ?>
  <div class="row sitereview-section">
<?php //print_r($site_info); ?> 
                <div class="panel-heading">
                    <h3 class="panel-title">
                        DIY Site Review report details
                    </h3>
                    <a href="<?php echo e($tools['website-review']); ?>/en/www/<?php echo e($page_title); ?>"> Learn More</a>
                </div>



</div>

<?php endif; ?>

<!-- End sitespy reports -->
