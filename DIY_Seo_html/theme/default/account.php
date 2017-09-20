<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>
<style>
label{
    display: block;
}

</style>
   
    <div class="container main-container">
        <div class="row">
            <div class="col-md-8 main-index">
                <div class="xd_top_box">
                    <?php echo $ads_720x90; ?>
                </div>
                
                <br />
                
                <?php 

if (isset($success))
{
echo '<div class="alert alert-success">
<strong>Alert!</strong> '.$success.'
</div>'; 

if (isset($_GET['login']))
{
echo '<br/> <div class="alert alert-info">
<strong>Alert!</strong> '.$lang['248'].'
</div>'; 
header("Location: ../");
echo '<meta http-equiv="refresh" content="1;url=../">';
}
if (isset($_GET['register']))
{
echo '<br/> <div class="alert alert-info">
<strong>Alert!</strong> '.$lang['298'].'
</div>'; 
}
}
elseif (isset($error))
{
    echo '<div class="alert alert-error">
<strong>Alert!</strong> '.$error.'
</div>'; 
}
if (isset($_GET['login']))
{
?>
            <form method="POST" action="/?route=account&login" class="loginme-form">
			<div class="modal-body">

                <?php if($enable_oauth){ ?>
				<div class="form-group connect-with">
					<div class="info"><?php echo $lang['267']; ?></div>
                    <br />
					<a href="/?route=facebook&login" class="connect facebook" title="<?php echo $lang['268']; ?>">Facebook</a>
		        	<a href="/?route=google&login" class="connect google" title="<?php echo $lang['269']; ?>">Google</a>  			        

                </div>
                <?php } ?>
				<div class="info"><?php echo $lang['270']; ?></div>
                <br />
				<div class="form-group">
					<label><?php echo $lang['271']; ?> <br />
						<input type="text" name="username" class="form-input"  style=" width: 96%;"/>
					</label>
				</div>	
				<div class="form-group">
					<label><?php echo $lang['272']; ?> <br />
						<input type="password" name="password" class="form-input"  style=" width: 96%;" />
					</label>
				</div>
			</div>
			<div class="modal-footer"> <br />
				<button type="submit" class="btn btn-primary  pull-left"><?php echo $lang['263']; ?></button>
				<div class="pull-right align-right">
				    <a style="color: #3C81DE;" href="/?route=account&forget"><?php echo $lang['273']; ?></a><br />
					<a style="color: #3C81DE;" href="/?route=account&resend"><?php echo $lang['274']; ?></a>
				</div>
			</div>
			 <input type="hidden" name="signin" value="<?php echo md5($date.$ip); ?>" />
			</form> 
            
<?php } elseif (isset($_GET['register']))  {?>
			<form action="/?route=account&register" method="POST" class="loginme-form">
			<div class="modal-body">

                <?php if($enable_oauth){ ?>
				<div class="form-group connect-with">
					<div class="info"><?php echo $lang['267']; ?></div>
                    <br />
					<a href="/?route=facebook&login" class="connect facebook" title="<?php echo $lang['268']; ?>">Facebook</a>
		        	<a href="/?route=google&login" class="connect google" title="<?php echo $lang['269']; ?>">Google</a>  		        
			    </div>
                <?php } ?>
				<div class="info"><?php echo $lang['277']; ?></div>
                <br />
				<div class="form-group">
					<label><?php echo $lang['271']; ?> <br />
						<input type="text" name="username" class="form-input" />
					</label>
				</div>	
								<div class="form-group">
					<label><?php echo $lang['275']; ?> <br />
						<input type="text" name="email" class="form-input" />
					</label>
				</div>
								<div class="form-group">
					<label><?php echo $lang['276']; ?> <br />
						<input type="text" name="full" class="form-input" />
					</label>
				</div>
								<div class="form-group">
					<label><?php echo $lang['272']; ?> <br />
						<input type="password" name="password" class="form-input" />
					</label>
				</div>
				</div>
			<div class="modal-footer"> <br />
				<button type="submit" class="btn btn-primary"><?php echo $lang['264']; ?></button>	
			</div>
			 <input type="hidden" name="signup" value="<?php echo md5($date.$ip); ?>" />
			</form>
            
<?php } elseif (isset($_GET['forget']))  {?>

<form action="/?route=account&forget" method="POST" class="loginme-form">
		
        	<div class="modal-body">

				<div class="info"><?php echo $lang['301']; ?></div><br />
	
								<div class="form-group">
					<label><?php echo $lang['192']; ?> <br />
						<input type="text" name="email" class="form-input" />
					</label>
				</div>

				</div>
			<div class="modal-footer">
            <br />
				<button type="submit" class="btn btn-primary"><?php echo $lang['8']; ?></button>	
			</div>
			 <input type="hidden" name="forget" value="<?php echo md5($date.$ip); ?>" />
			</form>
            
<?php } elseif (isset($_GET['resend']))  {?>   
   <form action="/?route=account&resend" method="POST" class="loginme-form">
		
        	<div class="modal-body">

				<div class="info"><?php echo $lang['301']; ?></div><br />
	
								<div class="form-group">
					<label><?php echo $lang['192']; ?> <br />
						<input type="text" name="email" class="form-input" />
					</label>
				</div>

				</div>
			<div class="modal-footer">
            <br />
				<button type="submit" class="btn btn-primary"><?php echo $lang['8']; ?></button>	
			</div>
			 <input type="hidden" name="resend" value="<?php echo md5($date.$ip); ?>" />
			</form>   
<?php } else  {?> <br /><h4>
<?php echo $lang['302']; ?></h4>
<a  style="color: #3C81DE;" href="/?route=account&login" ><?php echo $lang['299']; ?></a><br />
<a  style="color: #3C81DE;" href="/?route=account&register" ><?php echo $lang['300']; ?></a> <br />     
<a  style="color: #3C81DE;" href="/?route=account&forget" ><?php echo $lang['273']; ?></a><br />
<a  style="color: #3C81DE;" href="/?route=account&resend" ><?php echo $lang['274']; ?></a><br />
<br /><br />
<?php  } ?>
                
                
                
                <div class="xd_top_box">
                    <?php echo $ads_720x90; ?>
                </div>

                <br />
            </div>
            <?php 
            // Sidebar 
            require_once(THEME_DIR. "sidebar.php"); 
            ?>
        </div>
    </div>
    <br />