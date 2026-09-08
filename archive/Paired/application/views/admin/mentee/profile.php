<div class="content-wrapper">
    <?php $this->load->view('admin/include/breadcrumb'); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-9 pl-3">
                    <div class="card">
                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/settings/update_mentee_profile') ?>" role="form" class="form-horizontal pl-20">
                            <div class="card-body">
                                <h4><?php echo trans('account-settings') ?></h4>
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
                                                <option value=""><?php echo trans('select') ?></option>
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
                                                <option value=""><?php echo trans('select') ?></option>
                                                <?php foreach ($countries as $country): ?>
                                                  <option value="<?php echo html_escape($country->id) ?>" <?php if(isset($user->country) && $user->country==$country->id){echo 'selected';} ?> ><?php echo html_escape($country->name) ?></option>
                                                <?php endforeach ?>                 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mt-1">
                                            <label class="mb-1">City/Town of Residence <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="<?php if(isset($user->residence)){echo html_escape($user->residence);} ?>" name="residence" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 hide">
                                        <div class="form-group">
                                            <label> <?php echo trans('time-zone') ?></label>
                                            <select class="form-control select2" name="time_zone">
                                                <option value=""><?php echo trans('select') ?></option>
                                                <?php foreach ($time_zones as $time): ?>
                                                  <option value="<?php echo html_escape($time->id) ?>" <?php if($time->id == settings()->time_zone){echo "selected";} ?>  ><?php echo html_escape($time->name) ?></option>
                                                <?php endforeach ?>                 
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-1">LinkedIn Profile URL</label>
                                            <input type="text" class="form-control" name="linkedin_profile"  value="<?php if(isset($user->linkedin_profile)){echo html_escape($user->linkedin_profile);} ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-1">Number of Years of Experience</label>
                                            <input type="text" class="form-control" value="<?php if(isset($user->experience_year)){echo html_escape($user->experience_year);} ?>" name="experience_year">
                                        </div>
                                    </div>

                                    

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="mb-1">Expertise Industry<span class="text-danger">*</span></label>
                                            <select name="expertise_industry" class="wide w-100 form-control" required >
                                               <option>Select Option</option>

                                               <option value="Advertising/Public Relations" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Advertising/Public Relations'){echo "selected";} ?>>Advertising/Public Relations</option>

                                               <option value="Agriculture" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Agriculture'){echo "selected";} ?>>Agriculture</option>

                                               <option value="Arts/Creative/Entertainment" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Arts/Creative/Entertainment'){echo "selected";} ?>>Arts/Creative/Entertainment</option>

                                               <option value="Automotive" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Automotive'){echo "selected";} ?>>Automotive</option>

                                               <option value="Banking &amp; Financial Services" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Banking &amp; Financial Services'){echo "selected";} ?>>Banking &amp; Financial Services</option>

                                               <option value="Construction" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Construction'){echo "selected";} ?>>Construction</option>

                                               <option value="Consumer Goods" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Construction'){echo "selected";} ?><?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Consumer Goods'){echo "selected";} ?>>Consumer Goods</option>

                                               <option value="Education"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Education'){echo "selected";} ?>>Education</option>

                                               <option value="Energy"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Energy'){echo "selected";} ?>>Energy</option>

                                               <option value="Engineering"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Engineering'){echo "selected";} ?>>Engineering</option>

                                               <option value="Entertainment"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Entertainment'){echo "selected";} ?>>Entertainment</option>

                                               <option value="Fashion" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Fashion'){echo "selected";} ?>>Fashion</option>

                                               <option value="Food &amp; Beverage" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Food &amp; Beverage'){echo "selected";} ?>>Food &amp; Beverage</option>

                                               <option value="Healthcare" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Healthcare'){echo "selected";} ?>>Healthcare</option>

                                               <option value="Hospitality" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Hospitality'){echo "selected";} ?>>Hospitality</option>

                                               <option value="Information Technology" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Information Technology'){echo "selected";} ?>>Information Technology</option>

                                               <option value="Legal" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Legal'){echo "selected";} ?>>Legal</option>

                                               <option value="Manufacturing" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Manufacturing'){echo "selected";} ?>>Manufacturing</option>

                                               <option value="Media" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Media'){echo "selected";} ?>>Media</option>

                                               <option value="Non-Profit" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Non-Profit'){echo "selected";} ?>>Non-Profit</option>

                                               <option value="Pharmaceuticals" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Pharmaceuticals'){echo "selected";} ?>>Pharmaceuticals</option>

                                               <option value="Professionals Services" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Professionals Services'){echo "selected";} ?>>Professionals Services</option>
                                               <option value="Public Sector" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Public Sector'){echo "selected";} ?>>Public Sector</option>

                                               <option value="Real Estate" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Real Estate'){echo "selected";} ?>>Real Estate</option>

                                               <option value="Retail" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Retail'){echo "selected";} ?>>Retail</option>

                                               <option value="Sciences" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Sciences'){echo "selected";} ?>>Sciences</option>

                                               <option value="Telecommunications" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Telecommunications'){echo "selected";} ?>>Telecommunications</option>

                                               <option value="Transportation" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Transportation'){echo "selected";} ?>>Transportation</option>

                                               <option value="Travel &amp; Tourism" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Travel &amp; Tourism'){echo "selected";} ?>>Travel &amp; Tourism</option>

                                               <option value="Other" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Other'){echo "selected";} ?>>Other</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-1">How Often Are You Available to Mentor</label>
                                            <select name="mentorship_availability" class="wide w-100 form-control" >
                                                <option value="" selected="selected">Select an option</option>
                                                <option value="Once a month" <?php if(isset($user->mentorship_availability) && $user->mentorship_availability == 'Once a month'){echo "selected";} ?>>Once a month</option>

                                                <option value="Twice a month" <?php if(isset($user->mentorship_availability) && $user->mentorship_availability == 'Twice a month'){echo "selected";} ?>>Twice a month</option>

                                                <option value="Once in 2 Months" <?php if(isset($user->mentorship_availability) && $user->mentorship_availability == 'Once in 2 Months'){echo "selected";} ?>>Once in 2 Months</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-1">Employment Status<span class="text-danger">*</span></label>
                                            <select name="employment_status" class="wide w-100 form-control" >
                                                <option value="" selected="selected">Select an option</option>
                                                <option value="Employed Full-Time" <?php if(isset($user->employment_status) && $user->employment_status == 'Employed Full-Time'){echo "selected";} ?>>Employed Full-Time</option>

                                                <option value="Employed Part-Time" <?php if(isset($user->employment_status) && $user->employment_status == 'Employed Part-Time'){echo "selected";} ?>>Employed Part-Time</option>

                                                <option value="Self-employed" <?php if(isset($user->employment_status) && $user->employment_status == 'Self-employed'){echo "selected";} ?>>Self-employed</option>

                                                <option value="Not employed but looking for work" <?php if(isset($user->employment_status) && $user->employment_status == 'Not employed but looking for work'){echo "selected";} ?>>Not employed but looking for work</option>

                                                <option value="Not employed and not looking for work" <?php if(isset($user->employment_status) && $user->employment_status == 'Employed Full-Time'){echo "selected";} ?>>Not employed and not looking for work</option>

                                                <option value="Retired" <?php if(isset($user->employment_status) && $user->employment_status == 'Retired'){echo "selected";} ?>>Retired</option>

                                                <option value="Student" <?php if(isset($user->employment_status) && $user->employment_status == 'Student'){echo "selected";} ?>>Student</option>

                                                <option value="Prefer Not to Answer" <?php if(isset($user->employment_status) && $user->employment_status == 'Prefer Not to Answer'){echo "selected";} ?>>Prefer Not to Answer</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-1">Membership Network</label>
                                            <select name="membershhip_locale" class="wide w-100 form-control" required >
                                                <option value="" selected="selected">Select an option</option>
                                                <option value="Black Professionals United Kingdom" <?php if(isset($user->membershhip_locale) && $user->membershhip_locale == 'Black Professionals United Kingdom'){echo "selected";} ?>>Black Professionals United Kingdom</option>

                                                <option value="Black Professionals Europe"<?php if(isset($user->membershhip_locale) && $user->membershhip_locale == 'Black Professionals Europe'){echo "selected";} ?>>Black Professionals Europe</option>

<option value="Black Professionals Australia"<?php if(isset($user->membershhip_locale) && $user->membershhip_locale == 'Black Professionals Australia'){echo "selected";} ?>>Black Professionals Europe</option>

                                                <option value="Black Professionals Ireland"<?php if(isset($user->membershhip_locale) && $user->membershhip_locale == 'Black Professionals Ireland'){echo "selected";} ?>>Black Professionals Ireland</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="mb-0">About </label>
                                            <textarea class="form-control" rows="6" name="about_me" ><?php if(isset($user->about_me)){echo html_escape($user->about_me);} ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="mb-0">Career Goals </label>
                                            <textarea class="form-control" rows="6" name="career_goals" ><?php if(isset($user->career_goals)){echo html_escape($user->career_goals);} ?></textarea>
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
