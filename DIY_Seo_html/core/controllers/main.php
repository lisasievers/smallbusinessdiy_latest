<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
 * @copyright © 2015 ProThemes.Biz
 *
 */

$tools = array();

$result = mysqli_query($con,"SELECT * FROM seo_tools ORDER BY CAST(tool_no AS UNSIGNED) ASC");

while ($row = mysqli_fetch_array($result))
{
    $showTool = filter_var($row['tool_show'], FILTER_VALIDATE_BOOLEAN);
    if($showTool) {
    $tools[] = array($row['tool_name'],$row['tool_url'],$row['icon_name'],$row['tool_show'],$row['tool_no']);
    }
}

?>