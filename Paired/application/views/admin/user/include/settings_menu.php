<div class="col-lg-3">
	<div class="card mt-42 pr-md-3">
		<div class="card-body">
			<ul class="nav nav-pills flex-column" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link mb-0 <?php if(isset($page_title) && $page_title == "Profile Settings"){echo "active";} ?>" href="<?php echo base_url('admin/settings/profile') ?>"><i class="bi bi-person-circle mr-2"></i><?php echo ucfirst(trans('account')) ?></a>
				</li>
				<li class="nav-item ">
					<a class="nav-link mb-0 <?php if(isset($page_title) && $page_title == "Mentorship Profile Settings"){echo "active";} ?>" href="<?php echo base_url('admin/settings/mentorship') ?>"><i class="bi bi-person-workspace mr-2"></i><?php echo trans('mentorship') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link mb-0 <?php if(isset($page_title) && $page_title == "Schedule"){echo "active";} ?>" href="<?php echo base_url('admin/settings/schedule') ?>"><i class="bi bi-clock mr-2"></i><?php echo trans('schedule') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link mb-0 <?php if(isset($page_title) && $page_title == "Online Meeting"){echo "active";} ?>" href="<?php echo base_url('admin/settings/online_meeting') ?>"><i class="bi bi-person-video mr-2"></i><?php echo trans('online-meeting') ?></a>
				</li>

				<?php if (settings()->zoom_api_user == 1): ?>
				<li class="nav-item">
					<a class="nav-link mb-0 <?php if(isset($page_title) && $page_title == "Zoom Api"){echo "active";} ?>" href="<?php echo base_url('admin/settings/zoom_api') ?>"><i class="bi bi-person-video mr-2"></i> <?php echo trans('zoom-api') ?></a>
				</li>
				<?php endif ?>

				<li class="nav-item">
					<a class="nav-link mb-0 <?php if(isset($page_title) && $page_title == "Change Password"){echo "active";} ?>" href="<?php echo base_url('admin/settings/change_password') ?>"><i class="lnib lni-lock mr-1"></i> <?php echo trans('change-password') ?></a>
				</li>
			</ul>
		</div>
	</div>

	<?php if (isset($page_title) && $page_title == 'Schedule'): ?>
		<form method="post" class="validate-form" action="<?php echo base_url('admin/settings/set_interval')?>" role="form" enctype="multipart/form-data">
	      <div class="card pr-3">
	        <div class="card-body">
	          <div class="form-group">
	            <label><?php echo trans('set-interval') ?></label>
	            <div class="input-group mb-3">
	              <input type="number" class="form-control" name="intervals" value="<?php echo html_escape($user->intervals) ?>">
	              <div class="input-group-append">
	                <span class="input-group-text" id="basic-addon2"><?php echo trans('minutes') ?></span>
	              </div>
	            </div>
	          </div>

	          <!-- csrf token -->
	          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
	        </div>
	      </div>
	      <button type="submit" class="btn btn-primary"><?php echo trans('update') ?></button>

	    </form>
	<?php endif ?>

</div>