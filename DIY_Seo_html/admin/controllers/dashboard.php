<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/
$fullLayout = 1;
$p_title = "Dashboard";

function rndColor()
{
    $bageColor = array(
        'blue',
        'red',
        'green',
        'purple',
        'light-blue',
        'yellow');
    $rndColor = $bageColor[array_rand($bageColor)];
    return $rndColor;
}

function rndFlatColor()
{
    $bageColor = array(
        '#1ccdaa','#2ecc71','#3498db','#9b59b6','#34495e','#16a085','#27ae60','#2980b9','#8e44ad','#2c3e50','purple',
        '#f39c12','#e67e22','#e74c3c','#95a5a6','#7f8c8d','#d35400','#c0392b','#1E8BC3','#1BA39C','#DB0A5B',
        '#96281B');
    $rndColor = $bageColor[array_rand($bageColor)];
    return $rndColor;
}

function GetDirectorySize($path)
{
    $bytestotal = 0;
    //$path = realpath($path);
    if ($path !== false)
    {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,
            FilesystemIterator::SKIP_DOTS)) as $object)
        {
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
}
$query = "SELECT table_schema '$dbName', SUM( data_length + index_length) / 1024 / 1024 'db_size_in_mb' FROM information_schema.TABLES WHERE table_schema='$dbName' GROUP BY table_schema";

$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $database_size = round(Trim($row['db_size_in_mb']), 1);
}

require_once (LIB_DIR . 'geoip.inc');
$gi = geoip_open('../core/library/GeoIP.dat', GEOIP_MEMORY_CACHE);

$query = "SELECT @last_id := MAX(id) FROM admin_history";

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $last_id = $row['@last_id := MAX(id)'];
}
$query = "SELECT @last_id := MAX(id) FROM page_view";

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $page_last_id = $row['@last_id := MAX(id)'];
}

$query = "SELECT * FROM page_view WHERE id=" . Trim($page_last_id);
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $today_page = $row['tpage'];
    $today_visit = $row['tvisit'];
}

if($today_page == "" || $today_page == null){
    $today_page = 0;
}

if($today_visit == "" || $today_visit == null){
    $today_visit = 0;
}

for ($cloop = 0; $cloop <= 8; $cloop++)
{
    $c_my_id = $last_id - $cloop;
    $query = "SELECT * FROM admin_history WHERE id='$c_my_id'";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_array($result))
    {
        $last_date = $row['last_date'];
        $admin_ip = $row['ip'];
        $admin_country = geoip_country_name_by_addr($gi, $admin_ip);
        $admin_country = (!empty($admin_country)) ? $admin_country : "Unknown";
        $admin_browser = $row['browser'];
        $admin_browser = parse_user_agent($admin_browser);
        extract($admin_browser);
        $admin_browser = (!empty($browser)) ? $browser : "Unknown";
    }
    $adminHistory[] = array(
        $last_date,
        $admin_ip,
        $admin_country,
        $admin_browser);
}
$today_users_count = 0;
$c_date = date('jS F Y');
$query = "SELECT * FROM users where date='$c_date'";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $today_users_count = $today_users_count + 1;
}

for ($loop = 0; $loop <= 6; $loop++)
{
    $myid = $page_last_id - $loop;
    $query = "SELECT * FROM page_view WHERE id='$myid'";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_array($result))
    {
        $sdate = $row['date'];
        $sdate = str_replace(date('Y'), '', $sdate);
        $sdate = str_replace('January', 'Jan', $sdate);
        $sdate = str_replace('February', 'Feb', $sdate);
        $sdate = str_replace('March', 'Mar', $sdate);
        $sdate = str_replace('April', 'Apr', $sdate);
        $sdate = str_replace('August', 'Aug', $sdate);
        $sdate = str_replace('September', 'Sep', $sdate);
        $sdate = str_replace('October', 'Oct', $sdate);
        $sdate = str_replace('November', 'Nov', $sdate);
        $sdate = str_replace('December', 'Dec', $sdate);

        $ldate[$loop] = $sdate;
        $tpage[$loop] = $row['tpage'];
        $tvisit[$loop] = $row['tvisit'];
    }
    if ($ldate[1] == null || $ldate[1] == "")
    {
        $ldate[1] = $ldate[0];
        $tpage[1] = $tpage[0];
        $tvisit[1] = $tvisit[0];
    }
    if ($ldate[2] == null || $ldate[2] == "")
    {
        $ldate[2] = $ldate[1];
        $tpage[2] = $tpage[1];
        $tvisit[2] = $tvisit[1];
    }
    if ($ldate[3] == null || $ldate[3] == "")
    {
        $ldate[3] = $ldate[2];
        $tpage[3] = $tpage[2];
        $tvisit[3] = $tvisit[2];
    }
    if ($ldate[4] == null || $ldate[4] == "")
    {
        $ldate[4] = $ldate[3];
        $tpage[4] = $tpage[3];
        $tvisit[4] = $tvisit[3];
    }
    if ($ldate[5] == null || $ldate[5] == "")
    {
        $ldate[5] = $ldate[4];
        $tpage[5] = $tpage[4];
        $tvisit[5] = $tvisit[4];
    }
    if ($ldate[6] == null || $ldate[6] == "")
    {
        $ldate[6] = $ldate[5];
        $tpage[6] = $tpage[5];
        $tvisit[6] = $tvisit[5];
    }
}
$ds = disk_total_space("/");
$df = disk_free_space("/");
$domain = $_SERVER['HTTP_HOST'];
$latestData = getMyData("http://api.prothemes.biz/tools/latest_news.php?domain=$domain&code=$item_purchase_code");
$latestData = explode(":::",$latestData);
$latestVersion = $latestData[0];
$latestNews1 = explode(":::",$latestData[1]);
$latestNews1 = $latestNews1[0];
$latestNews2 = explode(":::",$latestData[2]);
$latestNews2 = $latestNews2[0];

$updater = true;
if($latestVersion == VER_NO){
    $updater = false;
}

$query = "SELECT @last_id := MAX(id) FROM users";

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $last_id = $row['@last_id := MAX(id)'];
}

for ($uloop = 0; $uloop <= 7; $uloop++)
{
    $r_my_id = $last_id - $uloop;
    $query = "SELECT * FROM users WHERE id='$r_my_id'";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_array($result))
    {
        $u_date = $row['date'];
        $userip = $row['ip'];
        $username = $row['username'];
    }
    $user_country = geoip_country_name_by_addr($gi, $userip);
    $userList[] = array(
        $username,
        $u_date,
        $user_country);

}

$query = "SELECT @last_id := MAX(id) FROM recent_history";

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $last_id = $row['@last_id := MAX(id)'];
}

for ($uloop = 0; $uloop <= 7; $uloop++)
{
    $r_my_id = $last_id - $uloop;
    $query = "SELECT * FROM recent_history WHERE id='$r_my_id'";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_array($result))
    {
        $u_tool_name = $row['tool_name'];
        $username = $row['user'];
        $visitor_ip = $row['visitor_ip'];
        $date_usage = $row['date'];
    }
    $user_country = geoip_country_name_by_addr($gi, $visitor_ip);
    $userInputHistory[] = array(
        $u_tool_name,
        $username,
        $user_country,
        $date_usage);

}

$seoCount = 0;
$query = "SELECT * FROM seo_tools";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $tool_show = filter_var(Trim($row['tool_show']), FILTER_VALIDATE_BOOLEAN);
    if ($tool_show)
    {
        $seoCount = $seoCount + 1;
    }
}

geoip_close($gi);

?>