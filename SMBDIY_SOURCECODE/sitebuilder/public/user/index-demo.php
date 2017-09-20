<?php
/**
 * QRcdr - php QR Code generator
 * index.php
 *
 * PHP version 5.3+
 *
 * @category  PHP
 * @package   QRcdr
 * @author    Nicola Franchini <info@veno.it>
 * @copyright 2015 Nicola Franchini
 * @license   item sold on codecanyon https://codecanyon.net/item/qrcdr-responsive-qr-code-generator/9226839
 * @version   Release: 1.7
 * @link      http://qrcdr.veno.it/
 */
error_reporting(E_ALL ^ E_NOTICE);
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require "config.php";

session_name($_CONFIG['session_name']);
session_start();

if (isset($_GET['reset'])) {
    unset($_SESSION['logo']);
}

global $_ERROR;

if (isset($_SESSION['error'])) {
    $_ERROR = $_SESSION['error'];
    unset($_SESSION['error']);
}

require "lib/functions.php";

$browserDetect = array_key_exists('detect_browser_lang', $_CONFIG) ? $_CONFIG['detect_browser_lang'] : false;
$defaultlang = array_key_exists('lang', $_CONFIG) ? $_CONFIG['lang'] : 'en';

$lang = getLang($defaultlang, $browserDetect);

if (file_exists("lang/".$lang.".php")) {
    include "lang/".$lang.".php";
}

require "head.php";
require "lib/countrycodes.php";

?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title><?php echo getString('title'); ?></title>
        <link rel="shortcut icon" href="images/favicon.ico">
        <meta name="description" content="<?php echo getString('description'); ?>">
        <meta name="keywords" content="<?php echo getString('tags'); ?>">

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!--[if lt IE 9]>
            <script src="js/ie8.js"></script>
        <![endif]-->
        <link href="css/font-awesome.min.css" rel="stylesheet">
        <link href="css/qrcdr.css" rel="stylesheet">

        <!-- template file -->
        <link href="style.css" rel="stylesheet">
        <link href="style-demo.css" rel="stylesheet">

        <script src="js/jquery-1.11.1.min.js"></script>
    </head>
    <body>
        <div class="container">
        <!-- language menu -->
        <?php echo langMenu('list', 'langmenu'); ?>
        <!-- end language menu -->
        </div>

        <!-- Main jumbotron for a primary marketing message or call to action -->
        <div class="jumbotron">
          <div class="container">
            <h1>Hello, world!</h1>
            <p>This is a template for a simple marketing or informational website. It includes a large callout called a jumbotron and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
            <p><a class="btn btn-success btn-lg" href="#" role="button">Learn more &raquo;</a></p>
          </div>
        </div>

        <!-- QRcdr -->
        <div class="container">

            <div class="row">
                <div class="col-sm-8 col-md-9 col-lg-8">
                    <div id="alert_placeholder">
                        <?php
                        if (strlen($_ERROR) > 0) { ?>
                            <div class="alert alert-warning alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert">
                                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                </button><?php echo $_ERROR; ?>
                            </div>
                        <?php
                        } ?>
                    </div>

                    <div class="row">
                    <?php 
                    if ($_CONFIG['uploader'] == true) { ?>
                        <div class="col-sm-12">
                            <p class="small"><?php echo getString('upload_or_select_watermark'); ?></p>
                        </div>

                        <div class="col-sm-2">
                            <form method="post" enctype="multipart/form-data" id="sottometti">
                                <div class="form-group">
                                    <span class="file-input btn btn-default btn-block btn-file">
                                    <i class="fa fa-upload"></i>
                                    <input type="file" name="file" id="file" />
                                    </span>
                                </div>
                            </form>
                        </div>
                    <?php 
                    } ?>
                    <?php 
                        /**
                        * Watermarks
                        */ ?>
                        <form action="_process.php" method="post" class="form" role="form" id="create">
                            <input type="hidden" name="section" id="getsec" value="<?php echo $getsection; ?>">
                    
                        <?php 
                        //
                        // Default watermarks
                        //
                        $waterdir = "images/watermarks/";
                        $watermarks = glob($waterdir.'*.{gif,jpg,png}', GLOB_BRACE);
                        $count = count($watermarks);
                        if ($_CONFIG['uploader'] == true || $count > 0) { ?>
                            <div class="form-group col-sm-10">
                                <div class="logoselecta">
                                    <div class="btn-group" data-toggle="buttons">

                                        <label class="btn btn-default <?php if ($optionlogo == "none" && $uploaded == false) echo "active"; ?>" 
                                            data-toggle="tooltip" data-placement="bottom">
                                            <input type="radio" name="optionlogo" value="none"
                                            <?php if ($optionlogo == "none" && $uploaded == false) echo "checked"; ?>>
                                            <img src="images/x.png">
                                        </label>
    <?php 
    foreach ($watermarks as $key => $water) {
        echo '<label class="btn btn-default';
        if ($optionlogo == $water) echo ' active ';
        echo '" data-toggle="tooltip" data-placement="bottom">
        <input type="radio" name="optionlogo" value="'.$water.'"';
        if ($optionlogo == $water) echo ' checked';
        echo ' id ="optionlogo'.$key.'"><img src="'.$water.'"></label>';
    }

    if ($logo && $upthumb) { ?>
        <label class="btn btn-default <?php if ($optionlogo == $upthumb || $uploaded == true) echo "active"; ?>">
            <input type="radio" name="optionlogo" id="optionsRadios6" value="<?php echo $upthumb; ?>"
            <?php if ($optionlogo == $upthumb || $uploaded == true) echo "checked"; ?>>
            <img src="<?php echo $upthumb; ?>">
        </label>
        <?php
    } 
                                    ?>
                                    </div>
                                </div>
                            </div>
                            <?php 
                        } ?>

                            <?php 
                            /**
                            * MAIN QR CODE CONFIG
                            */ ?>
                            <div class="form-group col-sm-12">
                                <div class="row">
                                    <div class="col-xs-6 col-md-3">
                                        <label><?php echo getString('background'); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon getcol"><i class="fa fa-qrcode"></i></span>
                                            <input type="text" class="form-control colorpickerback" 
                                            value="<?php echo $stringbackcolor; ?>" name="backcolor">
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-md-3">
                                        <label><?php echo getString('foreground'); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon getcol"><i class="fa fa-qrcode"></i></span>
                                            <input type="text" class="form-control colorpickerfront" 
                                            value="<?php echo $stringfrontcolor; ?>" name="frontcolor">
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-md-3">
                                        <label><?php echo getString('size'); ?></label>
                                        <select name="size" class="form-control">
                                    <?php        
                                    for ($i=4; $i<=16; $i+=2) {
                                        $value = $i;
                                        echo '<option value="'.$i.'"'.(($matrixPointSize==$i)?' selected':'').'>'.$value.'</option>';
                                    }; ?>
                                        </select>
                                    </div>

                                    <div class="col-xs-6 col-md-3">
                                        <label><?php echo getString('error_correction_level'); ?></label>
                                        <select name="level" class="form-control">
                                            <option value="L" <?php if ($errorCorrectionLevel=="L") echo "selected"; ?>>
                                                <?php echo getString('precision_l'); ?>
                                            </option>
                                            <option value="M" <?php if ($errorCorrectionLevel=="M") echo "selected"; ?>>
                                                <?php echo getString('precision_m'); ?>
                                            </option>
                                            <option value="Q" <?php if ($errorCorrectionLevel=="Q") echo "selected"; ?>>
                                                <?php echo getString('precision_q'); ?>
                                            </option>
                                            <option value="H" <?php if ($errorCorrectionLevel=="H") echo "selected"; ?>>
                                                <?php echo getString('precision_h'); ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            /**
                            * QR CODE DATA
                            */ ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <ul class="nav nav-tabs" role="tablist">
                                    <?php 
                                    if ($_CONFIG['link'] == true) { ?>
                                        <li class="<?php if ($getsection == "#link") echo "active"; ?>">
                                            <a href="#link" role="tab" data-toggle="tab"><i class="fa fa-link"></i> <span class="hidden-xs hidden-sm"><?php echo getString('link'); ?></span></a>
                                        </li>
                                    <?php 
                                    }  
                                    if ($_CONFIG['location'] == true) { ?>
                                        <li class="<?php if ($getsection == "#location") echo "active"; ?>">
                                            <a href="#location" role="tab" data-toggle="tab"><i class="fa fa-map-marker"></i> <span class="hidden-xs hidden-sm"><?php echo getString('location'); ?></span></a>
                                        </li>
                                    <?php 
                                    }  
                                    if ($_CONFIG['email'] == true) { ?>
                                        <li class="<?php if ($getsection == "#email") echo "active"; ?>">
                                            <a href="#email" role="tab" data-toggle="tab"><i class="fa fa-envelope-o"></i> <span class="hidden-xs hidden-sm"><?php echo getString('email'); ?></span></a>
                                        </li>
                                    <?php 
                                    }  
                                    if ($_CONFIG['text'] == true) { ?>
                                        <li class="<?php if ($getsection == "#text") echo "active"; ?>">
                                            <a href="#text" role="tab" data-toggle="tab"><i class="fa fa-align-left"></i> <span class="hidden-xs hidden-sm"><?php echo getString('text'); ?></span></a>
                                        </li>
                                    <?php 
                                    }
                                    if ($_CONFIG['tel'] == true) { ?>
                                        <li class="<?php if ($getsection == "#tel") echo "active"; ?>">
                                            <a href="#tel" role="tab" data-toggle="tab"><i class="fa fa-phone"></i> <span class="hidden-xs hidden-sm"><?php echo getString('phone'); ?></span></a>
                                        </li>
                                    <?php 
                                    }
                                    if ($_CONFIG['sms'] == true) { ?>
                                        <li class="<?php if ($getsection == "#sms") echo "active"; ?>">
                                            <a href="#sms" role="tab" data-toggle="tab"><i class="fa fa-mobile"></i> <span class="hidden-xs hidden-sm"><?php echo getString('sms'); ?></span></a>
                                        </li>
                                    <?php 
                                    }  
                                    if ($_CONFIG['wifi'] == true) { ?>
                                        <li class="<?php if ($getsection == "#wifi") echo "active"; ?>">
                                            <a href="#wifi" role="tab" data-toggle="tab"><i class="fa fa-wifi"></i> <span class="hidden-xs hidden-sm"><?php echo getString('wifi'); ?></span></a>
                                        </li>
                                    <?php 
                                    }  
                                    if ($_CONFIG['vcard'] == true) { ?>
                                        <li class="<?php if ($getsection == "#vcard") echo "active"; ?>">
                                            <a href="#vcard" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> <span class="hidden-xs hidden-sm"><?php echo getString('vcard'); ?></span></a>
                                        </li>
                                    <?php 
                                    }  
                                    if ($_CONFIG['paypal'] == true) { ?>
                                        <li class="<?php if ($getsection == "#paypal") echo "active"; ?>">
                                            <a href="#paypal" role="tab" data-toggle="tab"><i class="fa fa-paypal"></i> <span class="hidden-xs hidden-sm"><?php echo getString('paypal'); ?></span></a>
                                        </li>
                                    <?php 
                                    } ?>
                                    </ul>
                                    <div class="tab-content">
                                    <?php 
                                    //
                                    // LINK
                                    //
                                    if ($_CONFIG['link'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#link") echo "active"; ?>" id="link">
                                            <div class="form-group">
                                                <label><?php echo getString('link'); ?></label>
                                                <input type="url" name="link" class="form-control" value="<?php if ($getsection === "#link" && $output_data) echo $output_data; ?>" placeholder="http://" />
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    //
                                    // LOCATION
                                    //
                                    if ($_CONFIG['location'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#location") echo "active"; ?>" id="location">
                                        <?php
                                        if ($_CONFIG['google_api_key'] == 'YOUR-API-KEY' || strlen($_CONFIG['google_api_key']) < 10) { ?>
                                        <p class="lead">Please set an API KEY inside <strong>config.php</strong><br>
                                            <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key">
                                                &gt; How to get an api key for Gmaps
                                            </a>
                                        </p>
                                    <?php 
                                        } else { ?>
                                    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_CONFIG['google_api_key']; ?>&libraries=places"></script>
                                            <div style="min-height:350px">
                                                <div id="latlong">
                                                    <input id="pac-input" class="controls" type="text" placeholder="<?php echo getString('search'); ?>">
                                                    <input type="text" id="latbox" placeholder="<?php echo getString('latitude'); ?>" class="controls" name="lat" readonly>
                                                    <input type="text" id="lngbox" placeholder="<?php echo getString('longitude'); ?>" class="controls" name="lng" readonly>
                                                </div>
                                                <div id="map-canvas"></div>
                                            </div>
                                    <?php 
                                        } ?>
                                        </div>
                                    <?php
                                    }
                                    //
                                    // E-MAIL
                                    //
                                    if ($_CONFIG['email'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#email") echo "active"; ?>" id="email">
                                            <div class="row form-group">
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('send_to'); ?></label>
                                                    <input type="email" name="mailto" placeholder="E-Mail" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('subject'); ?></label>
                                                    <input type="text" name="subject" class="form-control">
                                                </div>
                                                <div class="col-xs-12">
                                                     <label><?php echo getString('text'); ?></label>
                                                    <textarea name="body" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    //
                                    // TEXT
                                    //
                                    if ($_CONFIG['text'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#text") echo "active"; ?>" id="text">
                                            <div class="form-group">
                                                <label><?php echo getString('text'); ?></label>
                                                <textarea rows="3"  name="data" class="form-control"><?php if ($getsection === "#text" && $output_data) echo $output_data; ?></textarea>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    //
                                    // TEL
                                    //
                                    if ($_CONFIG['tel'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#tel") echo "active"; ?>" id="tel">
                                            <div class="row">

                                                <div class="col-sm-4">
                                                   <div class="form-group">
                                                        <label><?php echo getString('country_code'); ?></label>
                                                        <?php
                                                        $output = "<select class=\"form-control\" name=\"countrycodetel\">";
                                                        foreach ($countries as $i=>$row) {
                                                            $output .= "<option value=\"".$row['code']."\" label=\"".$row['name']."\">".$row['name']."</option>\n";
                                                        }
                                                        $output .= '</select>';
                                                        echo $output;
                                                        ?> 
                                                    </div>
                                                </div>


                                                <div class="col-sm-8">
                                                    <div class="form-group">
                                                        <label><?php echo getString('phone_number'); ?></label>
                                                        <input type="text" type="number" name="tel" placeholder="" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    //
                                    // SMS
                                    //
                                    if ($_CONFIG['sms'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#sms") echo "active"; ?>" id="sms">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                   <div class="form-group">
                                                        <label><?php echo getString('country_code'); ?></label>
                                                        <?php
                                                        $output = "<select class=\"form-control\" name=\"countrycodesms\">";
                                                        foreach ($countries as $i=>$row) {
                                                            $output .= "<option value=\"".$row['code']."\" label=\"".$row['name']."\">".$row['name']."</option>\n";
                                                        }
                                                        $output .= '</select>';
                                                        echo $output;
                                                        ?> 
                                                    </div>
                                                </div>
                                                
                                                <div class="col-sm-8">
                                                    <div class="form-group">
                                                        <label><?php echo getString('phone_number'); ?></label>
                                                        <input type="text" name="sms" placeholder="" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">

                                                    <div class="form-group">
                                                        <label><?php echo getString('text'); ?></label>
                                                        <textarea rows="3"  name="bodysms" class="form-control"></textarea>
                                                    </div>
 
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    //
                                    // WI FI
                                    //
                                    if ($_CONFIG['wifi'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#wifi") echo "active"; ?>" id="wifi">
                                            <div class="row form-group">
                                                <div class="col-xs-4">
                                                    <label><?php echo getString('network_name'); ?></label>
                                                    <input type="email" name="ssid" placeholder="SSID" class="form-control">
                                                </div>
                                                <div class="col-xs-4">
                                                    <label><?php echo getString('network_type'); ?></label>
                                                    <select class="form-control" name="networktype">
                                                      <option value="WEP">WEP</option>
                                                      <option value="WPA">WPA/WPA2</option>
                                                      <option vlasue=""><?php echo getString('no_encryption'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-xs-4">
                                                    <label><?php echo getString('password'); ?></label>
                                                    <input type="text" name="wifipass" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    //
                                    // V CARD
                                    //
                                    if ($_CONFIG['vcard'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#vcard") echo "active"; ?>" id="vcard">
                                            <div class="row form-group">
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('first_name'); ?></label>
                                                    <input type="text" name="vname" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                     <label><?php echo getString('last_name'); ?></label>
                                                    <input type="text" name="vlast" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('phone_number'); ?></label>
                                                    <input type="text" name="vphone" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                     <label><?php echo getString('mobile'); ?></label>
                                                    <input type="text" name="vmobile" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('email'); ?></label>
                                                    <input type="email" name="vemail" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('website'); ?></label>
                                                    <input type="text" name="vurl" class="form-control" placeholder="http://">
                                                </div>
                                                <div class="col-xs-12">
                                                    <label><?php echo getString('company'); ?></label>
                                                    <input type="text" name="vcompany" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('jobtitle'); ?></label>
                                                    <input type="text" name="vtitle" class="form-control">
                                                </div>
                                                <div class="col-xs-6">
                                                    <label><?php echo getString('fax'); ?></label>
                                                    <input type="text" name="vfax" class="form-control">
                                                </div>
                                                <div class="col-xs-12">
                                                     <label><?php echo getString('address'); ?></label>
                                                    <textarea name="vaddress" class="form-control"></textarea>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label><?php echo getString('city'); ?></label>
                                                    <input type="text" name="vcity" class="form-control">
                                                </div>
                                                <div class="col-sm-4 col-xs-6">
                                                    <label><?php echo getString('post_code'); ?></label>
                                                    <input type="text" name="vcap" class="form-control">
                                                </div>
                                                <div class="col-sm-4 col-xs-6">
                                                    <label><?php echo getString('state'); ?></label>
                                                    <input type="text" name="vcountry" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                    } 

                                    //
                                    // PAYPAL
                                    //
                                    if ($_CONFIG['paypal'] == true) { ?>
                                        <div class="tab-pane <?php if ($getsection === "#paypal") echo "active"; ?>" id="paypal">
                                            <div class="row form-group">

                                                <div class="col-sm-6">
                                                    <label><?php echo getString('type'); ?></label>
                                                    <select class="form-control" name="pp_type" id="pp_type">
                                                      <option value="_xclick"><?php echo getString('buy_now'); ?></option>
                                                      <option value="_cart"><?php echo getString('add_to_cart'); ?></option>
                                                      <option value="_donations"><?php echo getString('donations'); ?></option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label><?php echo getString('email'); ?></label>
                                                    <input type="email" name="pp_email" class="form-control">
                                                    <small><?php echo getString('pp_email'); ?></small>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-xs-8">
                                                    <label><?php echo getString('item_name'); ?></label>
                                                    <input type="text" name="pp_name" class="form-control">
                                                </div>

                                                <div class="col-xs-4">
                                                    <label><?php echo getString('item_id'); ?></label>
                                                    <input type="text" name="pp_id" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                               <div class="col-xs-6 col-sm-3 yesdonation">
                                                    <label><?php echo getString('price'); ?></label>
                                                    <input type="text" name="pp_price" class="form-control">
                                                </div>
              
                                                <div class="col-xs-6 col-sm-3 yesdonation">
                                                    
                                                    <label><?php echo getString('currency'); ?></label>
                                                    <select class="form-control" name="pp_currency" id="setcurrency">
                                                      <option value="USD">USD</option>
                                                      <option value="EUR">EUR</option>
                                                      <option value="AUD">AUD</option>
                                                      <option value="CAD">CAD</option>
                                                      <option value="CZK">CZK</option>
                                                      <option value="DKK">DKK</option>
                                                      <option value="HKD">HKD</option>
                                                      <option value="HUF">HUF</option>
                                                      <option value="JPY">JPY</option>
                                                      <option value="NOK">NOK</option>
                                                      <option value="NZD">NZD</option>
                                                      <option value="PLN">PLN</option>
                                                      <option value="GBP">GBP</option>
                                                      <option value="SGD">SGD</option>
                                                      <option value="SEK">SEK</option>
                                                      <option value="CHF">CHF</option>
                                                    </select>
                                                </div>

                                                <div class="col-xs-6 col-sm-3 nodonation">
                                                    <label><?php echo getString('shipping'); ?></label>
                                                    <div class="input-group">
                                                        <input type="text" name="pp_shipping" class="form-control" placeholder="0.00">
                                                        <span class="input-group-addon" id="getcurrency">USD</span>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 col-sm-3 nodonation">
                                                    <label><?php echo getString('tax_rate'); ?></label>
                                                    <div class="input-group">
                                                        <input type="text" name="pp_tax" class="form-control" placeholder="0.00">
                                                        <span class="input-group-addon">%</span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    ?>
                                    </div> <!-- tab content -->
                                </div> <!-- form group -->
                            </div><!-- col sm12-->
                        </form>
                    </div> <!-- row -->
                </div><!-- col sm-8 -->

                <div class="col-sm-4 col-md-3 col-lg-4">
                    <?php 
                    //
                    // FINAL QR CODE placeholder
                    //
                    ?>
                    <div class="placeresult">
                        <div class="form-group text-center wrapresult">
                            <div class="resultholder">
                                <img src="<?php echo $_CONFIG['placeholder']; ?>" />
                            </div>
                        </div>
                        <div class="preloader"><i class="fa fa-cog fa-spin"></i></div>
                        <div class="form-group text-center linksholder"></div>
                        <button class="btn btn-lg btn-block btn-primary" id="submitcreate">
                        <i class="fa fa-magic"></i> <?php echo getString('generate_qrcode'); ?></button>
                    </div>

                </div><!-- col sm4-->
            </div><!-- row -->

            <hr>

            <footer>
                <p>&copy; <?php echo getString('title'); ?></p>
            </footer>

        </div><!-- container -->
        <script src="js/all.js"></script>

        <!-- END QRcdr -->

    </body>
</html>