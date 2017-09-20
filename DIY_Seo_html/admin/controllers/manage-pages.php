<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Manage Pages";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!isset($_POST['editPage']))
    {
        $page_title = escapeTrim($con, $_POST['page_title']);
        $page_url = escapeTrim($con, $_POST['page_url']);
        $meta_des = escapeTrim($con, $_POST['meta_des']);
        $header_show = escapeTrim($con, $_POST['header_show']);
        $page_name = escapeTrim($con, $_POST['page_name']);
        $posted_date = escapeTrim($con, $_POST['posted_date']);
        $meta_tags = escapeTrim($con, $_POST['meta_tags']);
        $footer_show = escapeTrim($con, $_POST['footer_show']);
        $page_content = escapeTrim($con, $_POST['page_content']);

        $query = "INSERT INTO pages (page_title,page_url,meta_des,header_show,page_name,posted_date,meta_tags,footer_show,page_content) VALUES ('$page_title','$page_url','$meta_des','$header_show','$page_name','$posted_date','$meta_tags','$footer_show','$page_content')";

        if (!mysqli_query($con, $query))
        {
            $msg = '<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> Something Went Wrong!
    </div>';
        } else
        {
            $msg = '<div class="alert alert-success alert-dismissable">
    <i class="fa fa-check"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> New page added successfully!
    </div>
    ';

            $page_title = "";
            $page_url = "";
            $meta_des = "";
            $header_show = "";
            $page_name = "";
            $posted_date = "";
            $meta_tags = "";
            $footer_show = "";
            $page_content = "";

        }
    }else{
        $page_title = escapeTrim($con, $_POST['page_title']);
        $page_url = escapeTrim($con, $_POST['page_url']);
        $meta_des = escapeTrim($con, $_POST['meta_des']);
        $header_show = escapeTrim($con, $_POST['header_show']);
        $page_name = escapeTrim($con, $_POST['page_name']);
        $posted_date = escapeTrim($con, $_POST['posted_date']);
        $meta_tags = escapeTrim($con, $_POST['meta_tags']);
        $footer_show = escapeTrim($con, $_POST['footer_show']);
        $page_content = escapeTrim($con, $_POST['page_content']);
        $editID = escapeTrim($con, $_POST['editID']);
        
        $query = "UPDATE pages SET page_title='$page_title', page_url='$page_url', meta_des='$meta_des', header_show='$header_show', page_name='$page_name', posted_date='$posted_date', meta_tags='$meta_tags', footer_show='$footer_show', page_content='$page_content' WHERE id='$editID'";
    
        if (!mysqli_query($con, $query))
        {
            $msg = '<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> Something Went Wrong!
    </div>';
        } else
        {
            $msg = '<div class="alert alert-success alert-dismissable">
    <i class="fa fa-check"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> Page data updated successfully!
    </div>
    ';

            $page_title = "";
            $page_url = "";
            $meta_des = "";
            $header_show = "";
            $page_name = "";
            $posted_date = "";
            $meta_tags = "";
            $footer_show = "";
            $page_content = "";

        }
    }
}

if (isset($_GET{'delete'}))
{
    $delete = raino_trim($_GET['delete']);
    $query = "DELETE FROM pages WHERE id=$delete";
    $result = mysqli_query($con, $query);

    if (mysqli_errno($con))
    {
        $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> ' . mysqli_error($con) . '
                                    </div>';
    } else
    {
        $msg = '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Page deleted from database successfully.
                                    </div>';
    }
}

if (isset($_GET{'edit'}))
{
    $addPage = true;
    $page_id = raino_trim($_GET['edit']);
    $sql = "SELECT * FROM pages where id='$page_id'";
    $result = mysqli_query($con, $sql);


    while ($row = mysqli_fetch_array($result))
    {
        $editID = $row['id'];
        $page_title = $row['page_title'];
        $page_url = $row['page_url'];
        $meta_des = $row['meta_des'];
        $page_name = $row['page_name'];
        $posted_date = $row['posted_date'];
        $meta_tags = $row['meta_tags'];
        $header_show = filter_var($row['header_show'], FILTER_VALIDATE_BOOLEAN);
        $footer_show = filter_var($row['footer_show'], FILTER_VALIDATE_BOOLEAN);
        $page_content = $row['page_content'];
    }
}

if ($addPage)
{
    $page1 = "";
    $page2 = "active";
} else
{
    $page1 = "active";
    $page2 = "";
}

?>