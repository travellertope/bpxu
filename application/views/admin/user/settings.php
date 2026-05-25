<div class="content-wrapper">
    <?php $this->load->view('admin/include/breadcrumb'); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <?php $this->load->view('admin/user/include/settings_menu.php'); ?>

                <div class="col-lg-9 pl-3">
                    <div class="card">
                        <div class="box-header with-border">
                          <h3 class="box-title"><?php echo trans('profile') ?></h3>
                        </div>

                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/settings/update_profile') ?>" role="form" class="form-horizontal pl-20">
                            <div class="card-body">
                                <?php if(user()->visible_profile == 1): ?>
                                    <a href="<?php echo base_url('admin/settings/update_visibility/2') ?>" class="btn btn-danger mb-3">Hide Profile</a>
                                <?php else: ?>
                                    <a href="<?php echo base_url('admin/settings/update_visibility/1') ?>" class="btn btn-primary mb-3">Show Profile</a>
                                <?php endif; ?>

                                <div class="row ">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="mih-100">
                                                <?php if (!empty($user->image)):?>
                                                    <img class="m-auto" width="100px" src="<?php echo base_url($user->image); ?>">
                                                <?php else: ?>
                                                   <p class="m-auto text-muted"><?php echo trans('profile-photo') ?></p>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="photo" id="customFile">
                                                    <label class="custom-file-label" for="customFile"><?php echo trans('upload-profile-photo') ?></label>
                                                    <p class="text-muted mt-1 fs-12 small"><i class="fas fa-info-circle"></i> <?php echo trans('for-better-view-use') ?> 300 x 150px</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="mih-100">
                                                <?php if (!empty($user->cover)):?>
                                                    <img class="m-auto" width="100px" height="80px" src="<?php echo base_url($user->cover); ?>">
                                                <?php else: ?>
                                                    <img class="m-auto" width="100px" src="<?php echo base_url('assets/front/img/vericla-cover.jpg'); ?>">
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group">
                                                <div class="custom-file">
                                                <input type="file" name="photo1" class="custom-file-input" id="customFiles">
                                                <label class="custom-file-label" for="customFiles"><?php echo trans('cover-photo') ?></label>
                                                </div>
                                                <p class="text-muted mt-1 fs-12 small"><i class="fas fa-info-circle"></i> <?php echo trans('for-better-view-use') ?> 1600 x 1000px</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo trans('name') ?></label>
                                            <input type="text" name="name" value="<?php echo html_escape($user->name); ?> " class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo trans('email') ?></label>
                                            <input type="text" name="email" value="<?php echo html_escape($user->email); ?>" class="form-control" >
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo trans('phone') ?></label>
                                            <input type="text" name="phone" value="<?php echo html_escape($user->phone); ?>" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo trans('gender') ?></label>
                                            <select class="form-control" name="gender">
                                                <option value=""><?php echo trans('select-your-gender') ?></option>
                                                <option value="Male" <?php if(isset($user->gender) && $user->gender=='Male'){echo 'selected';} ?>>Male</option>
                                                <option value="Female" <?php if(isset($user->gender) && $user->gender=='Female'){echo 'selected';} ?>>Female</option>
                                                <option value="Other" <?php if(isset($user->gender) && $user->gender=='Other'){echo 'selected';} ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo trans('country') ?></label>
                                            <select class="form-control select2" name="country">
                                                <option value=""><?php echo trans('select-your-ountry') ?></option>

                                                <?php foreach ($countries as $country): ?>
                                                    <option value="<?php echo html_escape($country->id) ?>" <?php if(isset($user->country) && $user->country==$country->id){echo 'selected';} ?> ><?php echo html_escape($country->name) ?></option>
                                                <?php endforeach ?>                 
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-1">City/Town of Residence <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="<?php if(isset($user->residence)){echo html_escape($user->residence);} ?>" name="residence" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 hide">
                                        <div class="form-group">
                                            <label><?php echo trans('time-zone') ?></label>
                                            <select class="form-control select2" name="time_zone">
                                                <option value=""><?php echo trans('select-your-time-zone') ?></option>
                                                <?php foreach ($time_zones as $time): ?>
                                                  <option value="<?php echo html_escape($time->id) ?>" <?php if($time->id == settings()->time_zone){echo "selected";} ?> ><?php echo html_escape($time->name) ?></option>
                                                <?php endforeach ?>                 
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><?php echo trans('language') ?></label>
                                            <input type="text" data-role="tagsinput" name="language" value="<?php if(!empty($user->language)){echo html_escape($user->language);} ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-12">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('about') ?></label>
                                            <p class="small mt-0 mb-2">Share a bit about yourself to help mentees get to know you better! Include your current role, professional background, and any unique skills or interests that could guide and inspire others. Feel free to keep it short and focused.</p>
                                            <textarea class="form-control" name="about" rows="4" ><?php if(isset($user->about_me)){echo html_escape($user->about_me);} ?></textarea>
                                        </div>
                                    
                                    
                                <!-- This field is not depreciated <div class="form-group">
                                    <label>Bio/About You</label>
                                    <p class="small mt-0 mb-2">Share a bit about yourself to help mentees get to know you better! Include your current role, professional background, and any unique skills or interests that could guide and inspire others. Feel free to keep it short and focused.</p>
                                    <textarea class="form-control" name="description" rows="6"><?php echo html_escape($user->description); ?></textarea>
                                </div> -->
                                
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="mb-0">What Are Your Mentorship Requirements? </label>
                                            <p class="small mt-0 mb-2">What do you expect a suitable mentee to be or have done or acquired before they can be considered for mentorship?</p>
                                            <textarea class="form-control" rows="6" name="mentorship_requirements" ><?php if(isset($user->mentorship_requirements)) {echo html_escape($user->mentorship_requirements);} ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="form-group">
                                  <label><?php echo trans('keywords') ?></label>
                                    <input type="text" data-role="tagsinput" name="keywords" value="<?php echo html_escape($user->keywords); ?>" class="form-control" >
                                </div> -->
<br/><br/>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo trans('respond-in') ?></label>
                                            <div class="input-group "> 
                                                <input type="text" width="30%" class="form-control" name="respond_time" value="<?php echo html_escape($user->respond_time); ?>" 
                                                    placeholder="insert responding day or hour">

                                                <select class="form-control" name="respond_in">
                                                    <option value=""><?php echo trans('select') ?></option>
                                                    <option value="hour" <?php if(isset($user->respond_in) && $user->respond_in == 'hour'){echo 'selected';} ?>><?php echo trans('hours') ?></option>
                                                    <option value="day" <?php if(isset($user->respond_in) && $user->respond_in == 'day'){echo 'selected';} ?>><?php echo trans('days') ?></option>
                                                </select> 
                                            </div>
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
