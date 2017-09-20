<form class="form-horizontal" role="form" id="siteSettingsForm">
	<input type="hidden" name="siteID" id="siteID" value="<?php echo e($data[0]['id']); ?>">
	<div id="siteSettingsWrapper" class="siteSettingsWrapper">
		<div class="optionPane">
			<h6>Site details</h6>
			<!-- <?php echo e(print_r($data)); ?> -->
			<div class="form-group">
				<label for="name" class="col-sm-3 control-label">Site name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="siteSettings_siteName" name="siteSettings_siteName" placeholder="Site name" value="<?php echo e($data[0]['site_name']); ?>">
				</div>
			</div>
			  <?php if( Auth::user()->type == 'admin'): ?>
			<div class="form-group">
				<label for="siteSettings_category" class="col-sm-3 control-label">Site Category</label>
				<div class="col-sm-9">
					<select name="siteSettings_category" class="form-control" id="siteSettings_category">
						<?php foreach($cat as $c): ?>
							<option value="<?php echo e($c['id']); ?>" <?php if($data[0]['site_category']==$c['id']): ?> <?php echo e('selected=selected'); ?> <?php endif; ?> ><?php echo e($c['name']); ?></option>
						<?php endforeach; ?>
					</select>	
				</div>
			</div>
			<?php endif; ?>
			<div class="form-group">
				<label for="name" class="col-sm-3 control-label">Global CSS</label>
				<div class="col-sm-9">
					<textarea class="form-control" id="siteSettings_siteCSS" name="siteSettings_siteCSS" placeholder="Global CSS" rows="6"><?php echo e($data[0]['global_css']); ?></textarea>
				</div>
			</div>
		</div><!-- /.optionPane -->

		<div class="optionPane" id="siteSettingsPublishing">
			<h6>Publishing details</h6>
			<div class="form-group">
				<label for="server" class="col-sm-3 control-label">Public URL</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="siteSettings_remoteUrl" name="siteSettings_remoteUrl" placeholder="Public URL, ie http://mysite.com" value="<?php echo e($data[0]['remote_url']); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="server" class="col-sm-3 control-label">FTP Server</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="siteSettings_ftpServer" name="siteSettings_ftpServer" placeholder="FTP Server" value="<?php echo e($data[0]['ftp_server']); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="user" class="col-sm-3 control-label">FTP User</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="siteSettings_ftpUser" name="siteSettings_ftpUser" placeholder="FTP User" value="<?php echo e($data[0]['ftp_user']); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="password" class="col-sm-3 control-label">FTP Password</label>
				<div class="col-sm-9">
					<input type="password" class="form-control" id="siteSettings_ftpPassword" name="siteSettings_ftpPassword" placeholder="FTP Password" value="<?php echo e($data[0]['ftp_password']); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="password" class="col-sm-3 control-label">FTP Port</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="siteSettings_ftpPort" name="siteSettings_ftpPort" placeholder="21" value="<?php if($data[0]['ftp_port'] != 0): ?><?php echo e($data[0]['ftp_port']); ?><?php else: ?><?php echo e(21); ?><?php endif; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="path" class="col-sm-3 control-label">FTP Path</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="siteSettings_ftpPath" name="siteSettings_ftpPath" placeholder="FTP Path" value="<?php if($data[0]['ftp_path'] != ''): ?><?php echo e($data[0]['ftp_path']); ?><?php else: ?><?php echo e('/'); ?><?php endif; ?>">
				</div>
				<div class="col-sm-3">
					<button type="button" class="btn btn-info btn-embossed btn-block " id="siteSettingsBrowseFTPButton"><span class="fui-search"></span> Browse Server</button>
				</div>
			</div>
			<div class="form-group ftpBrowse" id="ftpBrowse">
				<div class="col-sm-6 col-sm-offset-3">

					<div class="ftpList" id="ftpList">

						<div class="loaderFtp">
							<img src="<?php echo e(URL::to('src/images/loading.gif')); ?>" alt="Loading...">
							connecting to ftp ...
						</div>

						<div id="ftpAlerts"></div>

						<div id="ftpListItems"></div>

					</div><!-- /.ftpList -->

				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="button" class="btn btn-inverse btn-embossed btn-wide" id="siteSettingsTestFTP"><span class="fui-power"></span> Test FTP Connection</button>
					<span class="FTP_Connecting" style="display: none;">Testing FTP Connection ...</span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9" id="ftpTestAlerts">

				</div>
			</div>
		</div><!-- ./optionPane -->
	</div><!-- /.siteSettingsWrapper -->
</form>