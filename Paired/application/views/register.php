<div class="bg-light d-flex align-items-center position-relative min-vh-100 py-6">
    <?php if (isset($page_title) && $page_title == 'Register'): ?>
    <!-- Register form -->
    <div class="container <?php if(settings()->site_info == 3){echo "d-hide";} ?>">
        <div class="row cards">
            <div class="col-md-8 my-4 py-5 m-auto shadow-light bg-white rounded" data-aos="fade-up"
                data-aos-duration="300">

                <?php if (settings()->enable_registration == 0): ?>
                    <div class="mb-6 text-center">
                        <img class="mb-4" width="30%" src="<?php echo base_url('assets/front/img/not-found.png') ?>">
                        <h3 class="text-muted"><?php echo trans('registration-system-is-disabled-') ?> </h3>
                        <a class="btn btn-secondary btn-sm mt-2" href="<?php echo base_url('contact') ?>"> <?php echo trans('contact') ?>
                            </a>
                        <a class="btn btn-primary btn-sm mt-2" href="<?php echo base_url() ?>"><i
                                class="icon-home"></i> <?php echo trans('home') ?> </a>
                    </div>
                <?php else: ?>

                <div class="text-center">
                    <h3 class="mb-0 custom-font"><?php echo trans('sign-up') ?></h3>
                    <p class="mb-0"><?php echo trans('create-an-account') ?></p>
                </div>

                <div class="mt-5 px-7 row">
                    <div class="col-md-6 px-0">
                       <a href="<?php echo base_url('register?register_type=mentor') ?>"> <span class="btn <?php if(isset($_GET['register_type']) && $_GET['register_type'] == 'mentor'){echo 'btn-secondary';} ?> py-2 mentor_register_btnc mb-2 btn-block fs-15"><i class="bi bi-person-workspace"></i> <?php echo trans('mentor') ?></span></a>
                    </div>

                    <div class="col-md-6 px-0 pl-md-2">
                        <a href="<?php echo base_url('register?register_type=mentee') ?>"><span class="btn <?php if(isset($_GET['register_type']) && $_GET['register_type'] == 'mentee'){echo 'btn-secondary';} ?> py-2 border-1 mentee_register_btnc mb-2 btn-block fs-15"><i class="bi bi-person-badge"></i> <?php echo trans('mentee') ?></span></a>
                    </div>
                </div>

                <div class="mb-4 mt-4 pl-5">
                    <div class="success text-success"></div>
                    <div class="success_extend text-success"></div>
                    <div class="error text-danger"></div>
                    <div class="warning text-warning"></div>
                </div>

                <form id="register_form" class="authorization__form authorization__form--shadow leave_con px-5" method="post"
                    action="<?php echo base_url('auth/register_user'); ?>">


                    <?php if(isset($_GET['register_type']) && $_GET['register_type'] == 'mentor'): ?>

                        <div class="row">
                            
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label class="mb-1"><?php echo trans('name') ?> <span class="text-danger">*</span></label>
                                    <input type="text" required class="form-control" name="name">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1"><?php echo trans('email') ?> <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1"><?php echo trans('password') ?> <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Gender<span class="text-danger">*</span></label>
                                    <select name="gender" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label><?php echo trans('country') ?> <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="country" required>
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?php echo html_escape($country->id) ?>"><?php echo html_escape($country->name) ?></option>
                                        <?php endforeach ?>                 
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">City/Town of Residence <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="residence" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">How Did You Hear About Us? <span class="text-danger">*</span></label>
                                    <select name="where" class="select wide w-100 form-control" required >
                                        <option value=""> <?php echo trans('select') ?></option>
                                        
                                        <option value="Website/Google Search"> Website/Google Search</option>
                                        <option value="BPU Staff">BPU Staff</option>
                                        <option value="BAME Law Society">BAME Law Society</option>
                                        <option value="University/College Careers Service">University/College Careers Service</option>
                                        <option value="Employer or Workplace Network">Employer or Workplace Network</option>
                                        <option value="Social Media (LinkedIn, Instagram, X/Twitter, Facebook)">Social Media (LinkedIn, Instagram, X/Twitter, Facebook)</option>
                                        <option value="Friend or Colleague">Friend or Colleague</option>
                                        <option value="Event or Conference">Event or Conference</option>
                                        <option value="BPU Newsletter or Email">BPU Newsletter or Email</option>
                                        <option value="Online Search (Google, etc.)">Online Search (Google, etc.)</option>
                                        <option value="Partnership Organisation">Partnership Organisation</option>
                                        <option value="Other (please specify)">Other (please specify)</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="col-12 mentor_category">
                                <div class="form-group">
                                    <label class="mb-1">Expertise Industry <span class="text-danger">*</span></label>
                                    <select name="category" class="register_category select2 wide w-100 form-control" required >
                                        <option value=""> <?php echo trans('select') ?></option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo html_escape($category->id)?>"> <?php echo html_escape($category->name)?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div> 
                            
                        
                            <!-- <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Expertise Industry<span class="text-danger">*</span></label>
                                    <select name="expertise_industry" class="wide w-100 form-control" required >
                                        <option value="Advertising/Public Relations">Advertising/Public Relations</option>
                                       <option value="Aerospace/Aviation">Aerospace/Aviation</option>
                                       <option value="Arts">Arts</option>
                                       <option value="Entertainment">Entertainment</option>
                                       <option value="Publishing">Publishing</option>
                                       <option value="Automotive">Automotive</option>
                                       <option value="Banking/Mortgage">Banking/Mortgage</option>
                                       <option value="Business Development">Business Development</option>
                                       <option value="Business Opportunity">Business Opportunity</option>
                                       <option value="Clerical/Administrative">Clerical/Administrative</option>
                                       <option value="Construction/Facilities">Construction/Facilities</option>
                                       <option value="Consumer Goods">Consumer Goods</option>
                                       <option value="Customer Service">Customer Service</option>
                                       <option value="Education/Training">Education/Training</option>
                                       <option value="Energy">Energy</option>
                                       <option value="Engineering">Engineering</option>
                                       <option value="Financial Services">Financial Services</option>
                                       <option value="Government/Military">Government/Military</option>
                                       <option value="Healthcare">Healthcare</option>
                                       <option value="Hospitality/Travel">Hospitality/Travel</option>
                                       <option value="Human Resources">Human Resources</option>
                                       <option value="Installation/Maintenance">Installation/Maintenance</option>
                                       <option value="Insurance">Insurance</option>
                                       <option value="Internet">Internet</option>
                                       <option value="Job Search Aids">Job Search Aids</option>
                                       <option value="Law Enforcement/Security">Law Enforcement/Security</option>
                                       <option value="Legal">Legal</option>
                                       <option value="Management/Executive">Management/Executive</option>
                                       <option value="Manufacturing/Operations">Manufacturing/Operations</option>
                                       <option value="Marketing">Marketing</option>
                                       <option value="Non-Profit">Non-Profit</option>
                                       <option value="Pharmaceutical/Biotech">Pharmaceutical/Biotech</option>
                                       <option value="Professional Services">Professional Services</option>
                                       <option value="Quality Control">Quality Control</option>
                                       <option value="Real Estate">Real Estate</option>
                                       <option value="Restaurant/Food Service">Restaurant/Food Service</option>
                                       <option value="Retail">Retail</option>
                                       <option value="Sales">Sales</option>
                                       <option value="Sciences">Sciences</option>
                                       <option value="Skilled Labour">Skilled Labour</option>
                                       <option value="Technology">Technology</option>
                                       <option value="Telecommunications">Telecommunications</option>
                                       <option value="Transportation/Logistics">Transportation/Logistics</option>
                                       <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div> -->


                            <div class="col-12 mentor_skill">
                                <div class="form-group">
                                    <label class="mb-1"><?php echo trans('skills') ?> <span class="text-danger">*</span></label>
                                    <p class="small mt-0 mb-2">Enter a skill then tap Enter key to enter another one. Add as many as possible.</p>
                                    <input type="txt" class="form-control" name="skills" placeholder="" data-role="tagsinput">
                                </div>
                            </div>
                            
                            <div class="col-6 hide">
                                <div class="form-group">
                                    <label><?php echo trans('time-zone') ?> <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="time_zone" required>
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php foreach ($time_zones as $time): ?>
                                          <option value="<?php echo html_escape($time->id) ?>" <?php if($time->id == settings()->time_zone){echo "selected";} ?>><?php echo html_escape($time->name) ?></option>
                                        <?php endforeach ?>                 
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Select Your BP Network<span class="text-danger">*</span></label>
                                    <select name="bp_network" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Black Professionals United Kingdom">Black Professionals United Kingdom</option>
                                        <option value="Black Professionals Europe">Black Professionals Europe</option>
                                        <option value="Black Professionals Ireland">Black Professionals Ireland</option>
                                        <option value="Black Professionals Australia">Black Professionals Australia</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Employment Status<span class="text-danger">*</span></label>
                                    <select name="employment_status" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Employed Full-Time">Employed Full-Time</option>
                                        <option value="Employed Part-Time">Employed Part-Time</option>
                                        <option value="Self-employed">Self-employed</option>
                                        <option value="Not employed but looking for work">Not employed but looking for work</option>
                                        <option value="Not employed and not looking for work">Not employed and not looking for work</option>
                                        <option value="Retired">Retired</option>
                                        <option value="Student">Student</option>
                                        <option value="Prefer Not to Answer">Prefer Not to Answer</option>
                                    </select>
                                </div>
                            </div>

                            <!-- <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">If You Choose "Other Industry" Above, Please Input Here</label>
                                    <input type="text" class="form-control" name="other_industry">
                                </div>
                            </div> -->

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Current Role <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="current_role" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Company/Organisation Name </label>
                                    <input type="text" class="form-control" name="company">
                                </div>
                            </div>
                            
                            <!-- About me for MENTORS -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-0">About <span class="text-danger"> *</span></label>
                                     <p class="small mt-0 mb-2">Share a bit about yourself to help mentees get to know you better! Include your current role, professional background, and any unique skills or interests that could guide and inspire others. Feel free to keep it short and focused.</p>
                                    <textarea class="form-control" rows="6" name="about_me" required="required"></textarea>
                                </div>
                            </div>

                            <!-- This field is now depreciated
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-0">About <span class="text-danger"> *</span></label>
                                    <p class="small mt-0 mb-2">Share a bit about yourself to help mentees get to know you better! Include your current role, professional background, and any unique skills or interests that could guide and inspire others. Feel free to keep it short and focused.</p>
                                    <textarea class="form-control" rows="6" name="description" required="required"><?php if(isset($session->details)) {echo html_escape($session->details);} ?></textarea>
                                </div>
                            </div> -->

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-0">What Are Your Mentorship Requirements? <span class="text-danger"> *</span></label>
                                    <p class="small mt-0 mb-2">What do you expect a suitable mentee to be or have done or acquired before they can be considered for mentorship?</p>
                                    <textarea class="form-control" rows="6" name="mentorship_requirements" required="required"></textarea>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">How Often Are You Available to Mentor<span class="text-danger">*</span></label>
                                    <select name="mentorship_availability" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Once a month">Once a month</option>
                                        <option value="Twice a month">Twice a month</option>
                                        <option value="Once in 2 Months">Once in 2 Months</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">How Many Mentees Can You Mentor At Any One Time?<span class="text-danger">*</span></label>
                                    <select name="mentees_at_once" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    <?php else: ?>
                        <div class="row">
                        
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label class="mb-1"><?php echo trans('name') ?> <span class="text-danger">*</span></label>
                                    <input type="text" required class="form-control" name="name">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1"><?php echo trans('email') ?> <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1"><?php echo trans('password') ?> <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Location (Country) <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="country" required>
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?php echo html_escape($country->id) ?>"><?php echo html_escape($country->name) ?></option>
                                        <?php endforeach ?>                 
                                    </select>
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Location (City) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="residence" required>
                                </div>
                            </div>
                            
                            

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">How Did You Hear About Us? <span class="text-danger">*</span></label>
                                    <select name="where" class="select wide w-100 form-control" required >
                                        <option value=""> <?php echo trans('select') ?></option>
                                        
                                        <option value="Website/Google Search"> Website/Google Search</option>
                                        <option value="BPU Staff">BPU Staff</option>
                                        <option value="BAME Law Society">BAME Law Society</option>
                                        <option value="University/College Careers Service">University/College Careers Service</option>
                                        <option value="Employer or Workplace Network">Employer or Workplace Network</option>
                                        <option value="Social Media (LinkedIn, Instagram, X/Twitter, Facebook)">Social Media (LinkedIn, Instagram, X/Twitter, Facebook)</option>
                                        <option value="Friend or Colleague">Friend or Colleague</option>
                                        <option value="Event or Conference">Event or Conference</option>
                                        <option value="BPU Newsletter or Email">BPU Newsletter or Email</option>
                                        <option value="Online Search (Google, etc.)">Online Search (Google, etc.)</option>
                                        <option value="Partnership Organisation">Partnership Organisation</option>
                                        <option value="Other (please specify)">Other (please specify)</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Gender<span class="text-danger">*</span></label>
                                    <select name="gender" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Preferred Mentorship Availability<span class="text-danger">*</span></label>
                                    <select name="mentorship_availability" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Once a month">Once a month</option>
                                        <option value="Twice a month">Twice a month</option>
                                        <option value="Once in 2 Months">Once in 2 Months</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Field of Expertise<span class="text-danger">*</span></label>
                                    <select name="expertise_industry" class="wide w-100 form-control" required >
                                        <option value="" selected="selected" class="gf_placeholder">Select Option</option>
                                       <option value="Advertising/Public Relations">Advertising/Public Relations</option>
                                       <option value="Agriculture">Agriculture</option>
                                       <option value="Arts/Creative/Entertainment">Arts/Creative/Entertainment</option>
                                       <option value="Automotive">Automotive</option>
                                       <option value="Banking &amp; Financial Services">Banking &amp; Financial Services</option>
                                       <option value="Construction">Construction</option>
                                       <option value="Consumer Goods">Consumer Goods</option>
                                       <option value="Education">Education</option>
                                       <option value="Energy">Energy</option>
                                       <option value="Engineering">Engineering</option>
                                       <option value="Entertainment">Entertainment</option>
                                       <option value="Fashion">Fashion</option>
                                       <option value="Food &amp; Beverage">Food &amp; Beverage</option>
                                       <option value="Healthcare">Healthcare</option>
                                       <option value="Hospitality">Hospitality</option>
                                       <option value="Information Technology">Information Technology</option>
                                       <option value="Legal">Legal</option>
                                       <option value="Manufacturing">Manufacturing</option>
                                       <option value="Media">Media</option>
                                       <option value="Non-Profit">Non-Profit</option>
                                       <option value="Pharmaceuticals">Pharmaceuticals</option>
                                       <option value="Professionals Services">Professionals Services</option>
                                       <option value="Public Sector">Public Sector</option>
                                       <option value="Real Estate">Real Estate</option>
                                       <option value="Retail">Retail</option>
                                       <option value="Sciences">Sciences</option>
                                       <option value="Telecommunications">Telecommunications</option>
                                       <option value="Transportation">Transportation</option>
                                       <option value="Travel &amp; Tourism">Travel &amp; Tourism</option>
                                       <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Employment Status<span class="text-danger">*</span></label>
                                    <select name="employment_status" class="wide w-100 form-control" required >
                                        <option value="" selected="selected" class="gf_placeholder">Select Option</option>
                                       <option value="Employed Full-Time">Employed Full-Time</option>
                                       <option value="Employed Part-Time">Employed Part-Time</option>
                                       <option value="Self-employed">Self-employed</option>
                                       <option value="Not employed but looking for work">Not employed but looking for work</option>
                                       <option value="Not employed and not looking for work">Not employed and not looking for work</option>
                                       <option value="Homemaker">Homemaker</option>
                                       <option value="Retired">Retired</option>
                                       <option value="Student">Student</option>
                                       <option value="Prefer Not to Answer">Prefer Not to Answer</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">LinkedIn Profile URL<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="linkedin_profile" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Number of Years of Experience<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="experience_year" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-0">About <span class="text-danger"> *</span></label>
                                    <textarea class="form-control" rows="6" name="about_me" required="required"></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-0">Career Goals <span class="text-danger"> *</span></label>
                                    <textarea class="form-control" rows="6" name="career_goals" required="required"></textarea>
                                </div>
                            </div>

                            <!-- <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">How often will you be available for mentoring?<span class="text-danger">*</span></label>
                                    <select name="available" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Once a month">Once a month</option>
                                        <option value="Twice a month">Twice a month</option>
                                        <option value="Once in 2 Months">Once in 2 Months</option>
                                    </select>
                                </div>
                            </div> -->

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-1">Select Your BP Network<span class="text-danger">*</span></label>
                                    <select name="membershhip_locale" class="wide w-100 form-control" required >
                                        <option value="" selected="selected">Select an option</option>
                                        <option value="Black Professionals United Kingdom">Black Professionals United Kingdom</option>
                                        <option value="Black Professionals Europe">Black Professionals Europe</option>
                                        <option value="Black Professionals Ireland">Black Professionals Ireland</option>
                                        <option value="Black Professionals Ireland">Black Professionals Australia</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-6 hide">
                                <div class="form-group">
                                    <label><?php echo trans('time-zone') ?> <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="time_zone" required>
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php foreach ($time_zones as $time): ?>
                                          <option value="<?php echo html_escape($time->id) ?>" <?php if($time->id == settings()->time_zone){echo "selected";} ?>><?php echo html_escape($time->name) ?></option>
                                        <?php endforeach ?>                 
                                    </select>
                                </div>
                            </div>

                        </div>
                    <?php endif; ?>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <?php if (settings()->enable_captcha == 1 && settings()->captcha_site_key != ''): ?>
                            <div class="g-recaptcha pull-left"
                                data-sitekey="<?php echo html_escape(settings()->captcha_site_key); ?>"></div>
                            <?php endif ?>
                        </div>
                    </div>


                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="agree" class="custom-control-input agree_btn"
                                    id="terms-condition" required>
                                <label class="custom-control-label" for="terms-condition">
                                    <?php echo trans('i-have-read-and-understood-the') ?> 
                                    <a class="text-primary" href="<?php echo base_url('page/terms-and-condition') ?>"><?php echo trans('terms-and-conditions') ?></a>
                                    <?php echo trans('and') ?> 

                                    <a class="text-primary" href="<?php echo base_url('page/privacy-policy') ?>"> <?php echo trans('privacy-policy') ?> </a><?php echo trans('of-this-site') ?>.
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12 center">
                            <?php if(isset($_GET['register_type']) && $_GET['register_type'] == 'mentor'): ?>
                                <input type="hidden" name="register_type" value="1" class="register_type">
                            <?php else: ?>
                                <input type="hidden" name="register_type" value="2" class="register_type">
                            <?php endif; ?>

                            <input type="hidden" name="plan"
                                value="<?php if(isset($_GET['plan'])){echo html_escape($_GET['plan']);}else{echo "basic";} ?>">
                            <input type="hidden" name="billing"
                                value="<?php if(isset($_GET['billing'])){echo html_escape($_GET['billing']);}else{echo "monthly";} ?>">
                            <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>"
                                value="<?php echo html_escape($this->security->get_csrf_hash());?>">
                            <button type="submit" class="btn btn-primary btn-block mt-4 mb-0 register_button">
                                <?php echo trans('register') ?>
                            </button>
                        </div>
                    </div>


                    <div class="text-center text-small mt-4">
                        <span><?php echo trans('already-have-an-account') ?> <a href="<?php echo base_url('login') ?>"><?php echo trans('sign-in') ?></a></span>
                    </div>

                </form>
                
                <?php endif ?>

            </div>
        </div>
    </div>
    <!-- End register form -->
    <?php endif; ?>


    <?php if (isset($page_title) && $page_title == 'Email Verification'): ?>
    <!-- email verify -->
    <div class="container">
        <div class="row justify-content-center justify-content-lg-start">
            <div class="col-md-8 col-lg-7 col-xl-5 offset-lg-2 offset-xl-3 my-5" data-aos="fade-down" data-aos-duration="400">
                    <?php $verify_type = $_GET['type']; ?>
         
                    <div class="mb-3 text-center">
                        <img class="mb-4" width="30%" src="<?php echo base_url('assets/front/img/message.png') ?>">
                        <p><?php echo trans('we-have-send-a-verification-code-in-your') ?> <?php if($verify_type == 'sms'){echo trans('phone');}else{echo trans('email');} ?>.</p>
                    </div>

                    <form id="verify_from" method="post" action="<?php echo base_url('auth/verify_account'); ?>">

                        <div class="row justify-content-center">
                            <div class="col-6 mb-2">
                                <div class="form-group">
                                    <input type="text" class="form-control text-center" name="code" placeholder="<?php echo trans('enter-code-here') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-6">
                                <input type="hidden" name="type" value="<?php echo html_escape($verify_type) ?>">
                                <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">
                                <button type="submit" class="btn btn-success btn-block mb-0 verify_btn"><i class="fas fa-check-circle"></i> <?php echo trans('verify-code') ?></button>
                            </div>
                        </div>


                        <div class="loader mb-2 mt-4 text-primary text-center hide"><?php echo trans('sending') ?> <i class="fa fa-spinner fa-spin"></i></div>

                        <div class="text-center text-small mt-2">
                            <?php if ($verify_type == 'sms'): ?>
                                <span><?php echo trans('dont-received-any-code') ?><a class="resend_mail" href="<?php echo base_url('auth/resend_sms') ?>"><?php echo trans('resend') ?></a></span>
                            <?php else: ?>
                                <span><?php echo trans('dont-received-any-code') ?><a class="resend_mail" href="<?php echo base_url('auth/resend') ?>"><?php echo trans('resend') ?></a></span>
                            <?php endif ?>

                            <p><a class="btn btn-light-secondary btn-sm mt-2" href="<?php echo base_url() ?>"><i
                                class="fas fa-long-arrow-alt-left"></i> <?php echo trans('back') ?> </a></p>
                        </div>

                    </form>

            </div>
        </div>
    </div>
    <!-- End email verify -->
    <?php endif ?>
</div>