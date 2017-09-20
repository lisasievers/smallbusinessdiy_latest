<link href="<?php echo base_url();?>assets/site/css/facebook.css" rel="stylesheet">
<style type="text/css" media="screen">
	body
	{
		background:#E9EBEE;
	    font-family:'Open Sans';
	}
</style>
<br><br><br><br><br><br><br><br>
<?php 
$name=isset($info['name']) ? $info['name']:'';                
$page_id=isset($info['page_id']) ? $info['page_id']:'';          
$phone=isset($info['phone']) ? $info['phone']:'';          
$emails=isset($info['emails']) ? $info['emails']:'';    
$emails=str_replace(array("[","]",'"'),"",$emails);     
$website=isset($info['website']) ? $info['website']:'';          
$link=isset($info['link']) ? $info['link']:'';                
$mission=isset($info['mission']) ? $info['mission']:'';          
$about=isset($info['about']) ? $info['about']:'';          
$founded=isset($info['founded']) ? $info['founded']:'';          
$category=isset($info['category']) ? $info['category']:'';            
$username=isset($info['username']) ? $info['username']:'';            
$searched_at=isset($info['searched_at']) ? $info['searched_at']:'';          
$fan_count=isset($info['fan_count']) ? $info['fan_count']:'';          
$talking_about_count=isset($info['talking_about_count']) ? $info['talking_about_count']:'';          
$likes=json_decode($info['likes'],true);                
$location=json_decode($info['location'],true);
$posts=json_decode($info['posts'],true);                
$picture=json_decode($info['picture'],true);                
$cover=json_decode($info['cover'],true);                
$start_info=json_decode($info['start_info'],true);    		

if(isset($cover["source"])) 		$cover_source=$cover["source"]; 		else $cover_source=base_url("assets/site/images/cover.jpg");		
if(isset($picture["data"]["url"]))  $pic_source=$picture["data"]["url"]; 	else $pic_source=base_url("assets/site/images/profile.jpg");		

if(isset($location["latitude"]))  unset($location["latitude"]);
if(isset($location["longitude"])) unset($location["longitude"]);
if(is_array($location))
{
	array_walk($location, function(&$value, $key) {
	$key=ucfirst($key);
    $value = "{$key} : {$value}";
	});
	$location =  "<br/>".implode('<br>', $location);
}
else $location="";


if(isset($start_info["type"])) $start_type=$start_info["type"]; else $start_type="";
if(isset($start_info["date"]) && is_array($start_info["date"]))
{
	array_walk($start_info["date"], function(&$value, $key) {
	$key=ucfirst($key);
    $value = "{$key} : {$value}";
	});
	$start_date =  "<br/>".implode('<br>', $start_info["date"]);
} 
else $start_date="";
$start_info="<br/><br/>Type: ".$start_type." <br/> Stared at : ".$start_date;
?>
<div class="container facebook-body">
	<div class="row no-padding" >
		<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1 img-container">
			<a href="<?php echo $link; ?>" target="_BLANK">
				<img class="img-cover img-responsive" src="<?php echo $cover_source; ?>" >
				<img class="img-profile img-responsive" src="<?php echo $pic_source; ?>" >
			</a>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-1 col-lg-4 col-lg-offset-1 col-box-container col-left">
			<div class="col-xs-12 col-box">
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Name</b> : <?php echo $name; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Username</b> : <?php echo $username; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Website</b> : <a target="_BLANK" href="<?php echo $website; ?>"><?php echo $website; ?></a></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Email</b> : <?php echo $emails; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Phone</b> : <?php echo $phone; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Category</b> : <?php echo $category; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Location</b> : <br/><?php echo $location; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Founded</b> : <?php echo $founded; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Start Info</b> : <?php echo $start_info; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>Mission</b> : <br/><br/><?php echo $mission; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span><b>About</b> : <br/><br/><?php echo $about; ?></span> <br/>
				</div>			    		
				<div class="layer">
					<i class="fa fa-star"></i> <span><?php echo $talking_about_count; ?> people were talking about</span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> <span> <b>Likes: </b> <?php echo $fan_count; ?></span> <br/>
				</div>
				<div class="layer">
					<i class="fa fa-star"></i> 
					<span>
						<b>Liked Pages</b> : <br/><br/>
						<ul>
							<?php 
							if(isset($likes["data"]))
							{
								foreach($likes["data"] as $like)
								{
									echo "<li><a terget='_BLANK' href='".$like['link']."'>".$like['name']."</a></li>";
								}
							}
							?>						
						</ul>
					</span> 
					<br/>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-box-container col-right">		
			<?php 
			if(isset($posts["data"])) 
			{ 
				foreach($posts["data"]as $post)
				{ ?>
					<div class="col-xs-12 col-box">
						<img src='<?php echo $pic_source;?>' class="img-pic-thumbail">
						<span class="publish-time"> published at  <?php echo $post["created_time"];?></span> 
						<a target="_BLANK" href="<?php echo $post["permalink_url"];?>">
							<?php if(isset($post["picture"])) 
							{ ?>
								<img src="<?php echo $post["picture"];?>" class="post-thumbnail">
							<?php
							} ?>		    		
							<p class="post-description">
								<?php if(isset($post["message"])) echo $post["message"];?>
							</p>	
						</a>

						<?php if(isset($post["shares"]["count"])) 
						{ ?>			    		
							<i class="fa fa-share pull-right share-count"> <?php echo $post["shares"]["count"];?> shares</i>
						<?php
						} ?>

					</div> 	<?php
				}
			}
			?>    	
				

		</div>

	</div>
</div>





