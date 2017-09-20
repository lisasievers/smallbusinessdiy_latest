<?php
/**
 * QRcdr - php QR Code generator
 * lib/functions.php
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

/**
* Get language
*
* @param string $default       default lang
* @param bool   $browserDetect detect browser language
*
* @return $lang
*/
function getLang($default, $browserDetect = false)
{
    if ($browserDetect) {
        $browserlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (file_exists("lang/".$browserlang.".php")) {
            $lang = $browserlang;
            $_SESSION['lang'] = $lang;   
        }
    }
    if (isset($_GET['lang'])) {
        $lang = $_GET['lang'];
        $_SESSION['lang'] = $lang;
    }
    if (isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
    } else {
        $lang = $default;
    }
    return $lang;
}

/**
* Get translated string
*
* @param string $string key to search
*
* @return translated string
*/
function getString($string)
{
    global $_translations;
    $result = '>'.$string.'<';

    if (isset($_translations[$string])) {
        $stringa = $_translations[$string];
        if (strlen($stringa) > 0) {
            $result = $_translations[$string];
        }
    } 
    return $result;
}


/**
* Set error
*
* @param string $error error message
*
* @return global error
*/
function setError($error)
{
    global $_ERROR;
    $_ERROR = $error;
}

/**
* Delete old files
*
* @param string $dir the dir to scan
* @param int    $age files lifetime
*
* @return a clean directory
*/
function deleteOldFiles($dir = 'temp/', $age = 3600)
{
    if (file_exists($dir) && $handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) { 
        
            if (file_exists($dir.$file)) {

                if (preg_match("/^.*\.(png|svg|gif|jpeg|jpg|eps)$/i", $file)) {
  
                    $filelastmodified = filemtime($dir.$file);
                    $now = time();
                    $life = $now - $filelastmodified;
                    if ($life > $age) {
                        unlink($dir.$file);
                    }
                }
            }
        }
        closedir($handle); 
    }
}

/**
* Language menu
*
* @param string $type  menu output availabe: 'menu' | 'list'
* @param string $class optional class to add
*
* @return the language menu
*/
function langMenu($type = 'menu', $class = 'langmenu')
{
    $waterdir = 'lang/';
    $langfiles = glob($waterdir.'*.php');

    if ($type == 'menu') {
        $mymenu = '<div class="btn-group '.$class.'" style="float:right;"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> '.getString('language').'</button><ul class="dropdown-menu" role="menu">';

        foreach ($langfiles as $value) {
            $val = basename($value, '.php');
            $link = '?lang='.$val;
            $mymenu .= '<li><a href="'.$link.'">'.$val.'</a></li>';
        }
        $mymenu .='</ul></div>';
    } else {
        $mymenu = '<ul class="'.$class.'">';
        foreach ($langfiles as $value) {
            $val = basename($value, '.php');
            $link = '?lang='.$val; 
            $mymenu .= '<li><a href="'.$link.'">'.$val.'</a></li>';
        }
        $mymenu .='</ul>';
    }

    return $mymenu;
}
/**
* Make thumbnails
*
* @param string  $original     the original file
* @param string  $thumbname    the final file
* @param boolean $destroy      destroy original image
* @param int     $thumb_width  thumbnail width
* @param int     $thumb_height thumbnail height
*
* @return the image thumbnailed
*/
function makeThumb(
    $original = false, 
    $thumbname = 'thumb.png', 
    $destroy = false, 
    $thumb_width = 80, 
    $thumb_height = 80
) {

    if ($original == false) {
        setError(getString('error_getting_original_image'));
        return false;
    }
    if (!file_exists($original) && !file_exists($thumbname)) {
        setError($original." ".getString('does_not_exists'));
        unset($_SESSION['logo']);
        return false;
    }

    if (file_exists($thumbname)) {
        if (file_exists($original)) {
            unlink($original);
        }
        return $thumbname;
    }

    list($width, $height) = getimagesize($original);
    $image = imagecreatefromstring(file_get_contents($original));
    $width = imagesx($image);
    $height = imagesy($image);

    $original_aspect = $width / $height;
    $thumb_aspect = $thumb_width / $thumb_height;

    if ($original_aspect >= $thumb_aspect) {
        // If image is wider than thumbnail (in aspect ratio sense)
        $new_height = $thumb_height;
        $new_width = $width / ($height / $thumb_height);
    } else {
        // If the thumbnail is wider than the image
        $new_width = $thumb_width;
        $new_height = $height / ($width / $thumb_width);
    }
    $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

    imagealphablending($thumb, false);
    imagesavealpha($thumb, true);

    $color = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
    imagefill($thumb, 0, 0, $color);

    // Resize and crop
    imagecopyresampled(
        $thumb,
        $image,
        0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
        0 - ($new_height - $thumb_height) / 2, // Center the image vertically
        0, 0,
        $new_width, $new_height,
        $width, $height
    );

    imagepng($thumb, $thumbname, 0);
    imagedestroy($thumb);

    if ($destroy == true) {
        unlink($original);
    }
    setError(getString('thumb_created'));
    return $thumbname;
}

/**
* Add watermark
*
* @param string $back  qrcode
* @param string $front watermark
*
* @return QRcode + watermark
*/
function mergeImages($back = false, $front = false) 
{

    if ($back == false || !file_exists($back)) {
        setError(getString('error_getting_qrcode_image').": ".$back);
        return false;
    }

    if ($front == false || !file_exists($front)) {
        setError(getString('error_getting_watermark').": ".$front);
        return false;
    }
    $frame = imagecreatefromstring(file_get_contents($back));
    $image = imagecreatefromstring(file_get_contents($front));

    $frame_width = imagesx($frame);
    $frame_height = imagesy($frame);

    $thumb_width = $frame_width/4;
    $thumb_height = $frame_height/4;

    $width = imagesx($image);
    $height = imagesy($image);

    $dest_x = ($frame_width/2) - ($thumb_width/2);
    $dest_y = ($frame_height/2) - ($thumb_height/2);

    $fframe = imagecreatetruecolor($frame_width, $frame_height);

    imagecopyresampled($fframe, $frame, 0, 0, 0, 0, $frame_width, $frame_height, $frame_width, $frame_height); 

    imagecopyresampled(
        $fframe, $image,
        $dest_x, $dest_y, 0, 0,
        $thumb_width, $thumb_height,
        $width, $height
        // $width, $height
    );

    imagepng($fframe, $back);
    imagedestroy($fframe);
    //setError("image merged");
    return $back;
}