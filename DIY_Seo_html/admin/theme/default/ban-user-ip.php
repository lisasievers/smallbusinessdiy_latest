<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?php echo $p_title; ?>  
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/admin/?route=ban-user-ip"><i class="fa fa-bar-chart"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
        

                <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add User IP Ban</h3>
                                </div><!-- /.box-header -->
                                <!-- form start -->

                
                                <form action="/admin/?route=ban-user-ip" method="POST">
                                    <div class="box-body">
                                                     <?php
                if(isset($msg)){
                    echo $msg.'<br>';
                }?>
                                        <div class="form-group">
                                            <label for="ban_ip">User IP:</label>
                                            <input type="ip" class="form-control" id="ban_ip" name="ban_ip" placeholder="Enter user ip to ban">
                                                                        <p><br /> Note: Banned IP's can't able to browse the site!</p>
                                        </div><button type="submit" class="btn btn-primary">Add</button>
                                     </div><!-- /.box-body -->
                                </form>
                            </div>
                            
         <div class="box box-danger">
                                <div class="box-header with-border">
                                    <!-- tools box -->

                                    <h3 class="box-title">
                                        Recently banned IP's
                                    </h3>
                                </div>

                                <div class="box-body">
                                    <table class="table table-striped">
                                                 <tbody><tr>
                                           <th>Added Date</th>
                                            <th>IP</th>
                                            <th>Delete</th>
                                        </tr>
                                    
<?php     

if (isset($_GET{'delete'})) {
$delete = raino_trim($_GET['delete']);
$query = "DELETE FROM ban_user WHERE id=$delete";
$result = mysqli_query($con,$query);

    if (mysqli_errno($con)) {   
    echo '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> '.mysqli_error($con).'
                                    </div>';
    }
    else
    {
        echo '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> IP deleted from database successfully.
                                    </div>';
    }
}    
$rec_limit = 10;   
$query = "SELECT count(id) FROM ban_user";
$retval = mysqli_query($con,$query);
 
$row = mysqli_fetch_array($retval);
$rec_count = Trim($row[0]);



if (isset($_GET{'page'})) { //get the current page
$page = $_GET{'page'} + 1;
$offset = $rec_limit * $page;
} else {
// show first set of results
$page = 0;
$offset = 0;
}
$left_rec = $rec_count - ($page * $rec_limit);
//we set the specific query to display in the table
$sql = "SELECT * FROM ban_user ORDER BY `id` DESC LIMIT $offset, $rec_limit";
$result = mysqli_query($con, $sql);
$no =1;
//we loop through each records
while($row = mysqli_fetch_array($result)) {
//populate and display results data in each row
echo '<tr>';
echo '<td>' . $row['last_date'] . '</td>';
echo '<td>' . $row['ip'] . '</td>';
$myid = $row['id'];
echo '<td>' . "<a class='btn btn-danger btn-sm' href='/admin/?route=ban-user-ip&delete=".$myid."'> Delete </a>" . '</td>';
$no++;
}
echo '</tr>';
echo '</tbody>';
echo '</table>';
//we display here the pagination
echo '<ul class="pager">';
if ($left_rec < $rec_limit) {
$last = $page - 2;
if ($last < 0)
{

}
else
{
    echo @"<li><a href=\"/admin/?route=ban-user-ip&page=$last\">Previous</a></li>";
}
} else if ($page == 0) {
echo @"<li><a href=\"/admin/?route=ban-user-ip&page=$page\">Next</a></li>";
} else if ($page > 0) {
$last = $page - 2;
echo @"<li><a href=\"/admin/?route=ban-user-ip&page=$last\">Previous</a></li> ";
echo @"<li><a href=\"/admin/?route=ban-user-ip&page=$page\">Next</a></li>";
}
echo '</ul>';
?>
    
                                </div><!-- /.box-body -->

                                <div class="box-footer">

                                </div>
                            </div>
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
