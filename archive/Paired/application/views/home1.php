
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<section class="border-bottom border-light pt-12 pb-12 bsection">
    <div class="container">
        <div class="row">
            <div class="col-md-12 home-image-main">

                <div class="hero-mentors-imgs">
                    <div data-aos="zoom-in" data-aos-delay="50" class="home-image-sm home-image-1" style="background-image:url(<?php if(isset($random_mentor['3']['image'])){echo  base_url($random_mentor['3']['image']);} ?>)"></div>

                    <div data-aos="zoom-in" data-aos-delay="150" class="home-image-md home-image-2" style="background-image:url(<?php if(isset($random_mentor['1']['image'])){echo  base_url($random_mentor['1']['image']);} ?>)"></div>

                    <div data-aos="zoom-in" data-aos-delay="250" class="home-image-lg home-image-3" style="background-image:url(<?php if(isset($random_mentor['0']['image'])){echo  base_url($random_mentor['0']['image']);} ?>)"></div>

                    <div data-aos="zoom-in" data-aos-delay="350" class="home-image-sm home-image-4" style="background-image:url(<?php if(isset($random_mentor['4']['image'])){echo  base_url($random_mentor['4']['image']);} ?>)"></div>

                    <div data-aos="zoom-in" data-aos-delay="450" class="home-image-sm home-image-6" style="background-image:url(<?php if(isset($random_mentor['2']['image'])){echo  base_url($random_mentor['2']['image']);} ?>)"></div>
                    
<!--
                    <i class="bi bi-bell home-icon-1 text-light1 fs-25"></i>
                    <i class="bi bi-person-bounding-box home-icon-2 text-light2 fs-30"></i>
                    <i class="bi bi-box-seam home-icon-4 text-light3 fs-30"></i> -->
                    
                </div>

                <div class="tab-card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist" data-aos="fade-up">
                        <li class="nav-item ml-0">
                            <a class="nav-link active" id="one-tab" data-toggle="tab" href="#one" role="tab" aria-controls="One" aria-selected="true"><?php echo trans('mentee') ?></a>
                        </li>
                        <li class="nav-item ml-0">
                            <a class="nav-link" id="two-tab" data-toggle="tab" href="#two" role="tab" aria-controls="Two" aria-selected="false"><?php echo trans('mentor') ?></a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="one" role="tabpanel" aria-labelledby="one-tab">
                        <div class="col-md-12 pt-8 pl-0">
                            
                            <h1 data-aos="fade-up" data-aos-delay="250" class="display-4 w-lg-60 mb-2 font-weight-bold custom-font">
                                <?php echo html_escape(settings()->site_title) ?>
                            </h1>
                           
                            <p data-aos="fade-up" data-aos-delay="350" class="text-muted w-lg-50 fs-20 mt-2 mb-5"><?php echo html_escape(settings()->description) ?></p>
                        </div> 

                        <div class="col-lg-10 col-md-10 mb-8 pl-0" data-aos="zoom-in">
                            <div class="home-search style-two position-relative pull-left">
                                <form action="<?php echo base_url('home/mentors') ?>" class="" method="get">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <div class="input-box border-right">
                                                 <div class="form-group has-search">
                                                    <span class="bi bi-search text-primary form-control-feedback"></span>
                                                    <input type="text" name="mentor_search_name" class="form-control isearch" value="<?php if(isset($_POST['mentor_search_name'])){echo html_escape($_POST['mentor_search_name']);} ?>" placeholder="Type a search term">
                                                  </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="border-right">
                                                <div class="input-box">
                                                    <select class="nice_select wide" name="search_category">
                                                        <option value="">Industry</option>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?php echo html_escape($category->id) ?>" <?php if(isset($_POST['search_category']) && $_POST['search_category'] == $category->id){echo 'selected';} ?>><?php echo html_escape($category->name) ?></option>
                                                        <?php endforeach ?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-box">
                                                <select class="nice_select wide" name="mentor_search_country">
                                                    <option value=""><?php echo trans('country') ?></option>
                                                    <?php foreach ($countries as $country): ?>
                                                        <option value="<?php echo html_escape($country->id) ?>" <?php if(isset($_POST['mentor_search_country']) && $_POST['mentor_search_country'] == $country->id){echo 'selected';} ?>><?php echo html_escape($country->name) ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">
                                        
                                        <div class="col-md-2 sm-mb-10 sm-mt-10">
                                            <button type="submit" class="text-uppercase btn btn-primary btn-block-xs-only btn-md fs-14 m-auto"><?php echo trans('search') ?></button>
                                        </div>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade" id="two" role="tabpanel" aria-labelledby="two-tab">
                         <div class="col-md-12 pt-8 pl-0 text-left">
                            
                            <h1 class="display-4 w-lg-60 mb-2 font-weight-bold custom-font">
                                <?php echo html_escape(settings()->site_title_mentor) ?>
                            </h1>
                           
                            <p class="text-muted w-lg-50 fs-20 mt-2 mb-5"><?php echo trans('build-confidence-as-a-leader') ?></p>
                         
                            <div class="lift-sm mb-2 mt-3">
                                <a href="<?php echo base_url('register?register_type=mentor') ?>" class="btn btn-lg btn-primary mt-4 fs-14">Become a Mentor <i class="pl-1 pt-1 bi bi-arrow-right"></i></a>
                            </div>
                        </div>     
                    </div>
                </div>
            </div>
        </div>

       


        <div class="row align-items-center justify-content-center d-none">
            <div class="col-md-12 col-lg-6 order-md-1 pr-lg-5 pr-xl-0 mb-8 mb-lg-0">
              
                <h1 data-aos="fade-left" data-aos-delay="250" class="display-5 w-lg-80 font-weight-bold custom-fonts1">
                    <?php echo html_escape(settings()->site_title) ?>
                </h1>
               
                <p data-aos="fade-left" data-aos-delay="250" class="text-muted fs-18 mt-3 mb-5 <?php if(text_dir() == 'rtl'){echo "pl-15";}else{echo "pr-15";} ?>"><?php echo html_escape(settings()->description) ?></p>
                
                <?php if (settings()->trial_days != 0): ?>
                    <div class="lift-sm mb-2" data-aos="fade-left" data-aos-delay="100">
                        <a href="<?php echo base_url('register?trial=start') ?>" class="btn btn-lg btn-primary mt-3 fs-14"><?php echo trans('get-started') ?> <i class="pl-1 pt-1 bi bi-arrow-right"></i></a>
                    </div>
                    <p class="text-muted mt-2 fs-12" data-aos="zoom-in"><?php echo trans('start-free-trial.-no-credit-card-required') ?></p>
                <?php endif ?>
            </div>

            <div class="col-md-12 col-lg-6 order-md-2 pl-3">
                <div class="banner-img" data-aos="zoom-in">
                    <img src="<?php echo base_url(settings()->hero_img) ?>" class="text-right w-lg-90" alt="Hero Image">
                </div>
            </div>
        </div>
    </div>
</section>
    
<?php if (settings()->enable_workflow == 1): ?>
    <section class="zindex-low">
        <div class="container z0">
            <div class="w-md-80 w-lg-50 text-center mx-auto mb-8 mb-lg-10" data-aos="fade-up">
                <span class="badge badge-secondary-soft badge-square mb-3"><?php echo trans('workflow') ?></span>
                <h1 class="text-dark font-weight-bold mx-auto mb-1"><?php echo trans('workflow-title') ?></h2>
            </div>

            <div class="row">
                <?php $w=1; foreach ($workflows as $workflow): ?>
                    <div class="col-md-4 mb-7 mb-md-0" data-aos="zoom-in-up" data-aos-delay="150">
                        <div class="text-center m-2 py-6 px-4 <?php if($w==2){echo "shadow-workflow";} ?>">
                            <div class="mb-5 workflow-img"><img class="display-5" src="<?php echo base_url($workflow->image) ?>" alt="Image"></div>

                            <h5 class="mb-2 mx-auto text-dark"><?php echo html_escape($workflow->title) ?></h5>
                            <p class="text-muted"><?php echo html_escape($workflow->details) ?></p>
                        </div>
                    </div>
                <?php $w++; endforeach ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($features)): ?>
    <section class="bg-white">
        <div class="container">
            <div class="text-center mx-auto mb-8">
                <div class="text-center mx-md-auto mb-5 mb-md-7 mb-lg-9">
                    <div class="badge badge-square badge-secondary-soft mb-3">
                        <span><?php echo trans('features') ?></span>
                    </div>
                    <h1 class="w-70 mx-auto"><?php echo trans('learn-that-new-skill-launch-that-project') ?></h1>
                </div>
            </div>

            <div class="row justify-content-center">
                <?php if(empty($features)): ?>
                    <?php $this->load->view('include/not_found_msg'); ?>
                <?php else: ?>
                    <?php $f=1; foreach ($features as $feature): ?>
                        <div class="col-12 col-md-4 col-lg-4 mb-0 p-0" data-aos="zoom-in-up" data-aos-delay="<?php echo html_escape($f * 100) ?>">
                            <a href="javascript:;">
                                <div class="template-box feature brd-0 lift-sms text-left asminh shadow-sm rounded-1">
                                    <div class="circle-icon ftur bg-white mb-1">
                                        <img src="<?php echo base_url($feature->image) ?>" class="screen-one w-80 p-2" alt="Feature Image">
                                    </div>
                                    <p class="template-box-text ftur text-dark fs-16 mb-2"><?php echo html_escape($feature->name); ?></p>
                                    <p class="template-box-titles fw-500 fs-14 text-muted"><?php echo html_escape($feature->details) ?></p>
                                </div>
                            </a>
                        </div>
                    <?php $f++; endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </section>
<?php endif; ?>



<?php if(!empty($mentors)): ?>
<section>
    <div class="container">
        
        <div class="row">
            <div class="col-12 text-left mx-md-auto mb-1">
                <div class="badge badge-square badge-secondary-soft mb-3 hide">
                    <span><?php echo trans('our-teams') ?></span>
                </div>
                <h3 class="pull-left"><?php echo trans('discover-the-worlds-top-mentors') ?></h3>
            </div>
        </div>

        <div class="row p-3">
            <?php if(empty($mentors)): ?>
                <?php $this->load->view('include/not_found_msg'); ?>
            <?php else: ?>
                <div class="carousel-4 owl-carousel owl-theme h-100 w-100 navTopRight h-100 w-100">
                    <?php foreach ($mentors as $mentor): ?>
                        <?php include APPPATH.'views/include/mentor_item.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

    
<?php if (settings()->enable_blog == 1 && !empty($posts)): ?>
    <section class="bg-lights pt-6">
        <div class="container">
            <div class="w-md-80 w-lg-50 text-center mx-auto mb-8 mb-lg-10" data-aos="fade-up">
                <span class="badge badge-primary-soft badge-square mb-3"><?php echo trans('blogs') ?></span>
                <h1 class="text-dark font-weight-bold mx-auto mb-1"><?php echo trans('learn-more-empower-yourself') ?></h2>
            </div>

            <div class="row">
                <?php $b=1; foreach ($posts as $post): ?>
                    <?php include'include/blog_post_item.php'; ?>
                <?php $b++; endforeach ?>
            </div>
        </div>
    </section>
<?php endif ?>


<?php if (!empty($testimonials)): ?>
    <section class="bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center mb-5">
                    <p class="badge-secondary-soft badge badge-square"><?php echo trans('testimonia') ?></p>
                    <h1 class="text-dark font-weight-normal w-lg-40 mx-auto mb-5"><?php echo trans('testimonial-title') ?> <b class="text-primary"><?php echo settings()->site_name ?></b></h1>
                </div>
                <div class="col-md-12">
                    <div class="testimonial testimonial-carousel owl-carousel owl-theme navTopRight">
                        <?php if(!empty($testimonials)): ?>
                            <?php foreach ($testimonials as $testimonial): ?>
                                <div class="col-6s item mb-5">
                                    <div class="card shadow-none border-1 h-100 bg-lights mr-2 round-1">
                                        <div class="card-body testimonial-box">
                                            <div class="text-center mb-3">
                                                <div class="text-center pt-3">
                                                    <div class="avatar-sm mx-auto" style="background-image: url(<?php echo base_url($testimonial->image) ?>);"></div>
                                                    
                                                    <div class="mt-3">
                                                        <h5 class="mb-0 text-dark"><?php echo html_escape($testimonial->name) ?></h5>
                                                        <p class="text-muted">
                                                            <?php echo html_escape($testimonial->designation) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <?php if (!empty($testimonial->feedback)): ?>
                                                    <div class="pl-4 pr-4 pt-0">
                                                        <p class="text-muted font-weight-normal"><?php echo html_escape($testimonial->feedback) ?></p>
                                                    </div>
                                                <?php endif ?>

                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

   
<?php endif ?>
<!-- Testimonials -->

<?php if (!empty($brands)): ?>
    <section class="bg-grays py-6 border-top">
        <div class="container">
            <div class="brand-carousel-5 owl-carousel owl-theme">
                <?php foreach ($brands as $brand): ?>
                   <div class="item">
                        <a href="<?php echo prep_url($brand->link) ?>">
                            <div class="px-0 px-sm-2 hover-opacity brand_img" style="background-image:url(<?php echo base_url($brand->logo) ?>)"></div>
                        </a>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?php endif ?>