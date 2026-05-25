<div class="content-wrapper">
    <?php $this->load->view('admin/include/breadcrumb'); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <?php $this->load->view('admin/user/include/settings_menu.php'); ?>

                <div class="col-lg-9 pl-3">
                    <div class="card">
                        <div class="box-header with-border">
                          <h3 class="box-title"><?php echo trans('mentorship-profile') ?></h3>
                        </div>

                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/settings/update_mentorship_profile') ?>" role="form" class="form-horizontal pl-20">
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('intro-video-url') ?></label>
                                            <input class="form-control" type="text" name="intro_video" value="<?php echo html_escape($user->intro_video) ?>">
                                        </div>
                                    </div>

                            <div class="col-12 mentor_category">
                                <div class="form-group">
                                    <label class="mb-1">Expertise Industry <span class="text-danger">*</span></label>
                                    <select name="category" class="register_category select2 wide w-100 form-control" required >
                                        <option value=""> <?php echo trans('select') ?></option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo html_escape($category->id)?>" <?php if(isset($user->category) && $user->category == $category->id){echo 'selected';} ?> > <?php echo html_escape($category->name)?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                                   <!-- <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-1">Expertise Industry<span class="text-danger">*</span></label>
                                            <select name="expertise_industry" class="wide w-100 form-control" required >
                                                <option>Select expertise</option>
                                                <option value="Advertising/Public Relations" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Advertising/Public Relations'){echo "selected";} ?>>Advertising/Public Relations</option>

                                               <option value="Aerospace/Aviation" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Aerospace/Aviation'){echo "selected";} ?>>Aerospace/Aviation</option>

                                               <option value="Arts" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Arts'){echo "selected";} ?>>Arts</option>

                                               <option value="Entertainment" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Entertainment'){echo "selected";} ?>>Entertainment</option>

                                               <option value="Publishing" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Publishing'){echo "selected";} ?>>Publishing</option>

                                               <option value="Automotive" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Automotive'){echo "selected";} ?>>Automotive</option>

                                               <option value="Banking/Mortgage" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Banking/Mortgage'){echo "selected";} ?>>Banking/Mortgage</option>

                                               <option value="Business Development" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Business Development'){echo "selected";} ?>>Business Development</option>

                                               <option value="Business Opportunity" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Business Opportunity'){echo "selected";} ?>>Business Opportunity</option>

                                               <option value="Clerical/Administrative" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Clerical/Administrative'){echo "selected";} ?>>Clerical/Administrative</option>

                                               <option value="Construction/Facilities" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Construction/Facilities'){echo "selected";} ?>>Construction/Facilities</option>

                                               <option value="Consumer Goods" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Consumer Goods'){echo "selected";} ?>>Consumer Goods</option>

                                               <option value="Customer Service" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Customer Service'){echo "selected";} ?>>Customer Service</option>

                                               <option value="Education/Training" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Education/Training'){echo "selected";} ?>>Education/Training</option>

                                               <option value="Energy" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Energy'){echo "selected";} ?>>Energy</option>

                                               <option value="Engineering" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Engineering'){echo "selected";} ?>>Engineering</option>

                                               <option value="Financial Services" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Financial Services'){echo "selected";} ?>>Financial Services</option>

                                               <option value="Government/Military" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Government/Military'){echo "selected";} ?>>Government/Military</option>

                                               <option value="Healthcare" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Healthcare'){echo "selected";} ?>>Healthcare</option>

                                               <option value="Hospitality/Travel" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Hospitality/Travel'){echo "selected";} ?>>Hospitality/Travel</option>

                                               <option value="Human Resources" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Human Resources'){echo "selected";} ?>>Human Resources</option>
                                               <option value="Installation/Maintenance" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Installation/Maintenance'){echo "selected";} ?>>Installation/Maintenance</option>
                                               <option value="Insurance" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Insurance'){echo "selected";} ?>>Insurance</option>
                                               <option value="Internet" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Internet'){echo "selected";} ?>>Internet</option>
                                               <option value="Job Search Aids" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Job Search Aids'){echo "selected";} ?>>Job Search Aids</option>
                                               <option value="Law Enforcement/Security" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Law Enforcement/Security'){echo "selected";} ?>>Law Enforcement/Security</option>
                                               <option value="Legal" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Legal'){echo "selected";} ?>>Legal</option>
                                               <option value="Management/Executive" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Management/Executive'){echo "selected";} ?>>Management/Executive</option>
                                               <option value="Manufacturing/Operations" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Manufacturing/Operations'){echo "selected";} ?>>Manufacturing/Operations</option>

                                               <option value="Marketing" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Marketing'){echo "selected";} ?>>Marketing</option>

                                               <option value="Non-Profit" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Non-Profit'){echo "selected";} ?>>Non-Profit</option>

                                               <option value="Pharmaceutical/Biotech" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Pharmaceutical/Biotech'){echo "selected";} ?>>Pharmaceutical/Biotech</option>

                                               <option value="Professional Services" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Professional Services'){echo "selected";} ?>>Professional Services</option>

                                               <option value="Quality Control" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Quality Control'){echo "selected";} ?>>Quality Control</option>

                                               <option value="Real Estate" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Real Estate'){echo "selected";} ?>>Real Estate</option>

                                               <option value="Restaurant/Food Service" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Restaurant/Food Service'){echo "selected";} ?>>Restaurant/Food Service</option>

                                               <option value="Retail" <?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Retail'){echo "selected";} ?>>Retail</option>

                                               <option value="Sales"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Sales'){echo "selected";} ?>>Sales</option>

                                               <option value="Sciences"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Sciences'){echo "selected";} ?>>Sciences</option>

                                               <option value="Skilled Labour"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Skilled Labour'){echo "selected";} ?>>Skilled Labour</option>

                                               <option value="Technology"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Technology'){echo "selected";} ?>>Technology</option>

                                               <option value="Telecommunications"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Telecommunications'){echo "selected";} ?>>Telecommunications</option>

                                               <option value="Transportation/Logistics"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Transportation/Logistics'){echo "selected";} ?>>Transportation/Logistics</option>

                                               <option value="Other"<?php if(isset($user->expertise_industry) && $user->expertise_industry == 'Other'){echo "selected";} ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    -->

                                    <div class="col-12 ">
                                        <div class="form-group">
                                            <label class="mb-1"><?php echo trans('skill') ?> <span class="text-danger">*</span></label>
                                            <input type="txt" class="form-control" name="skills" value="<?php if(!empty($user)){echo html_escape($user->skills);} ?>" placeholder="" data-role="tagsinput">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-1">Select Your BP Network<span class="text-danger">*</span></label>
                                            <select name="bp_network" class="wide w-100 form-control" required >
                                                <option value="" selected="selected">Select an option</option>
                                                <option value="Black Professionals United Kingdom" <?php if(isset($user->bp_network) && $user->bp_network == 'Black Professionals United Kingdom'){echo "selected";} ?>>Black Professionals United Kingdom</option>

                                                <option value="Black Professionals Europe"<?php if(isset($user->bp_network) && $user->bp_network == 'Black Professionals Europe'){echo "selected";} ?>>Black Professionals Europe</option>

                                                <option value="Black Professionals Ireland"<?php if(isset($user->bp_network) && $user->bp_network == 'Black Professionals Ireland'){echo "selected";} ?>>Black Professionals Ireland</option>

                                                <option value="Black Professionals Australia"<?php if(isset($user->bp_network) && $user->bp_network == 'Black Professionals Australia'){echo "selected";} ?>>Black Professionals Australia</option>
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
                                            <label class="mb-1">How Many Mentees Can You Mentor At Any One Time?</label>
                                            <select name="mentees_at_once" class="wide w-100 form-control" >
                                                <option value="" selected="selected">Select an option</option>

                                                <option value="1" <?php if(isset($user->mentees_at_once) && $user->mentees_at_once == '1'){echo "selected";} ?>>1</option>

                                                <option value="2"<?php if(isset($user->mentees_at_once) && $user->mentees_at_once == '2'){echo "selected";} ?>>2</option>

                                                <option value="3"<?php if(isset($user->mentees_at_once) && $user->mentees_at_once == '3'){echo "selected";} ?>>3</option>

                                                <option value="4"<?php if(isset($user->mentees_at_once) && $user->mentees_at_once == '4'){echo "selected";} ?>>4</option>

                                                <option value="5"<?php if(isset($user->mentees_at_once) && $user->mentees_at_once == '5'){echo "selected";} ?>>5</option>

                                                <option value="6"<?php if(isset($user->mentees_at_once) && $user->mentees_at_once == '6'){echo "selected";} ?>>6</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('level-of-experience') ?></label>
                                            <select class="form-control" name="level" >
                                                <option value=""><?php echo trans('select-your-experience-level') ?></option>
                                                <?php foreach (get_levels() as $level): ?>
                                                    <option value="<?php echo html_escape($level); ?>" <?php if(isset($user->level) && $user->level == $level){echo 'selected';} ?>>
                                                        <?php echo html_escape($level); ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('experience') ?></label>
                                            <select class="form-control" name="experience_year" >
                                                <option value=""><?php echo trans('select-your-experience') ?></option>

                                                <?php for ($i=1 ; $i <31; $i++ ): ?>
                                                    <option value="<?php echo html_escape($i); ?>" <?php if(isset($user->experience_year) && $user->experience_year == $i){echo 'selected';} ?>><?php echo html_escape($i); ?> Year</option>
                                                    
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('company') ?></label>
                                            <input class="form-control" type="text" name="company" value="<?php echo html_escape($user->company) ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('designation') ?></label>
                                            <input class="form-control" type="text" name="designation" value="<?php echo html_escape($user->designation) ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('linkedin-profile') ?></label>
                                            <input class="form-control" type="text" name="linkedin_profile" value="<?php echo html_escape($user->linkedin_profile) ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('facebook-profile') ?></label>
                                            <input class="form-control" type="text" name="facebook_profile" value="<?php echo html_escape($user->facebook_profile) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('instagram-profile') ?></label>
                                            <input class="form-control" type="text" name="instagram_profile" value="<?php echo html_escape($user->instagram_profile) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label>X (Twitter) Profile</label>
                                            <input class="form-control" type="text" name="x_profile" value="<?php echo html_escape($user->x_profile) ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label><?php echo trans('portfolio-website') ?></label>
                                            <input class="form-control" type="text" name="portfolio" value="<?php echo html_escape($user->portfolio) ?>">
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
