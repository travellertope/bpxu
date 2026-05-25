<div class="content-wrapper">
    <?php $this->load->view('admin/include/breadcrumb'); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <?php $this->load->view('admin/user/include/settings_menu.php'); ?>

                <div class="col-lg-9 pl-3">
                    <div class="card">
                        <div class="box-header with-border">
                          <h3 class="box-title"><?php echo trans('online-meeting') ?></h3>
                        </div>

                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/settings/update_online_meeting') ?>" role="form" class="form-horizontal pl-20">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('default-virtual-meeting-option') ?></label>
                                            <select class="form-control skill_category" name="meet_type">
                                                <option  <?php if(user()->meet_type == 'zoom'){echo 'selected';} ?> value="zoom"><?php echo trans('zoom') ?></option>
                                                <option  <?php if(user()->meet_type == 'meet'){echo 'selected';} ?> value="meet"><?php echo trans('google-meet') ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><?php echo trans('google-meet-invitation-url') ?></label>
                                            <textarea class="form-control" name="gmeet_url"><?php echo html_escape(user()->gmeet_url) ?></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card-footer">
                                <input type="hidden" name="id" value="<?php echo html_escape(user()->id); ?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <button type="submit" class="btn btn-primary mt-2"><?php echo trans('save-changes') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
