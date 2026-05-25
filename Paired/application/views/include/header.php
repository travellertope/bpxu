<!DOCTYPE html>
<html lang="en" dir="<?php echo text_dir(); ?>">

<head>

    <!-- Title  -->
    <?php $settings = get_settings(); ?>
    <title><?php echo html_escape($settings->site_name) ?> &bull; <?php if(isset($page_title)){echo trans(strtolower($page_title)).' &bull; ';} ?>  <?php echo html_escape($settings->site_title) ?></title>
    <!-- Metas -->
    <meta charset="utf-8">
    <?php if (isset($page) && $page == 'Mentor'): ?>
    <meta name="author" content="<?php if(!empty($mentor)){echo html_escape($mentor->name);} ?>">
    <meta name="description" content="<?php if(!empty($mentor)){echo html_escape($mentor->description);} ?>">
    <meta name="keywords" content="<?php if(!empty($mentor)){echo html_escape($mentor->keywords);} ?>">
    <?php else: ?>
    <meta name="author" content="<?php echo html_escape($settings->site_name) ?>">
    <meta name="description" content="<?php echo html_escape($settings->description) ?>">
    <meta name="keywords" content="<?php echo html_escape($settings->keywords) ?>">
    <?php endif ?>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#286efb" />
    <meta name="msapplication-navbutton-color" content="#286efb" />
    <meta name="apple-mobile-web-app-status-bar-style" content="#286efb" />

    <!-- Favicons-->
    <link rel="icon" href="<?php echo base_url($settings->favicon) ?>">
    <link rel="apple-touch-icon" href="<?php echo base_url($settings->favicon) ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url($settings->favicon) ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url($settings->favicon) ?>">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url() ?>assets/img/favicon.ico">
   
    <!-- CSS Libs  -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/libs/font-awesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/fonts/bootstrap/bootstrap-icons.css">

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/sweet-alert.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/line-icons/lineicons.css">

    <!-- nice-select -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/nice-select.css">
    <!-- select2 -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/select2/css/select2.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/select2/css/select2.min.css">
    <!-- tagsinput -->
    <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-tagsinput.css" rel="stylesheet" />
    <!-- Template CSS -->
    <link href="<?php echo base_url() ?>assets/front/css/template.css" rel="stylesheet">
    
    <?php if (settings()->front_layout == 2): ?>
        <link href="<?php echo base_url() ?>assets/front/css/template2.css" rel="stylesheet">
    <?php endif ?>

    <?php if(settings()->enable_animation == 1): ?>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/css/aos.css">
    <?php endif; ?>  
    <!-- sweet alert -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/sweet-alert.css">

    

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/libs/owl-carousel/dist/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/libs/owl-carousel/dist/css/owl.theme.default.min.css">

    <!-- overwrite css -->
    <?php $font = get_by_id(settings()->site_font,'fonts')->name; ?>
    <link href="https://fonts.googleapis.com/css?family=<?php echo str_replace(' ', '+', $font); ?>:400,500,600,700" rel="stylesheet">
    
    <?php if(isset($page_title) && $page_title == 'Home'): ?>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/css/style-search.css">
    <?php endif; ?>

    <?php if (text_dir() == 'rtl'): ?>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-rtl.css">
        <link rel="stylesheet" href="<?php echo base_url()?>assets/admin/css/bootstrap-rtl.min.css" crossorigin="anonymous">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-ltr.css">
    <?php endif ?>

    <?php if (site_mode() == 'dark'): ?>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/css/dark.css">
    <?php endif ?>

    <?php $rgb = hex2rgb(settings()->site_color) ?>
    <link href="<?php echo base_url() ?>assets/front/css/style-over.php?color=<?php echo settings()->site_color; ?>&font=<?php echo str_replace(' ', '+', $font).'&rgb='.$rgb ?>" rel="stylesheet">

    <!-- csrf token -->
    <script type="text/javascript">
       var csrf_token = '<?= $this->security->get_csrf_hash(); ?>';
       var token_name = '<?= $this->security->get_csrf_token_name();?>';
    </script>
    
    <?php if (settings()->enable_captcha == 1 && settings()->captcha_site_key != ''): ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php endif; ?>

    <style type="text/css">
        <?php echo json_decode(settings()->custom_css) ?>
    </style>

    <?php if (settings()->enable_pwa == 1): ?>
        <?php include 'pwa_config.php'; ?>
    <?php endif ?>

    <?php if (!empty($settings->google_analytics)): ?>
        <?php echo base64_decode($settings->google_analytics) ?>
    <?php endif ?>

</head>

<body class="<?php if(site_mode() == 'dark'){echo "dark-mode";} ?>">
    <!-- main wrapper -->
    <div class="main-wrapper">

        <!-- header -->
        <?php if (isset($menu) && $menu == TRUE): ?>
            <header id="navbar">
                <div class="container">
                    <nav class="navbar navbar-expand-lg navbar-light bg-whites py-3">
                        <a class="navbar-brand" href="<?php echo base_url() ?>">
                            <img width="160px" src="<?php echo base_url(settings()->logo) ?>" alt="logo">
                        </a>

                        <button type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
                            <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
                        </button>

                        <!-- Menu -->
                        <div id="navbarContent" class="collapse navbar-collapse mt-2 pb-1">

                            <ul class="navbar-nav align-items-lg-center ml-auto">


                                <li class="nav-item xs-mb-10 <?php if (settings()->enable_frontend == 0){echo "d-none";} ?>"><a href="<?php echo base_url() ?>" class="nav-link  <?php if(isset($page_title) && $page_title == "Home"){echo "active";} ?>"><?php echo trans('home') ?></a></li>
                          
                                <li class="nav-item xs-mb-10"><a href="<?php echo base_url('mentors') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Mentors"){echo "active";} ?>"><?php echo trans('mentors') ?></a></li>

                                <?php if (settings()->enable_blog == 1): ?>
                                <li class="nav-item xs-mb-10 <?php if (settings()->enable_frontend == 0){echo "d-none";} ?>"><a href="<?php echo base_url('blogs') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Blogs"){echo "active";} ?>"><?php echo trans('blogs') ?></a></li>
                                <?php endif ?>


                                <?php if (settings()->enable_faq == 1): ?>
                                <li class="nav-item xs-mb-10 <?php if (settings()->enable_frontend == 0){echo "d-none";} ?>"><a href="<?php echo base_url('faqs') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Faqs"){echo "active";} ?>"><?php echo trans('faqs') ?></a></li>
                                <?php endif ?>

                                <li class="nav-item xs-mb-10 <?php if (settings()->enable_frontend == 0){echo "d-none";} ?>"><a href="<?php echo base_url('contact') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Contact"){echo "active";} ?>"><?php echo trans('contact') ?></a></li>

                                <?php if (settings()->enable_multilingual == 1): ?>
                                    <li class="nav-item dropdown <?php if (settings()->enable_frontend == 0){echo "d-none";} ?>">
                                        <a href="javascript:void(0);" data-toggle="dropdown" class="nav-link dropdown-toggle"><?php echo lang_short_form(); ?></a>

                                        <ul class="dropdown-menu shadow-lg mt-1">
                                            <?php foreach (get_language() as $lang): ?>
                                                <li><a class="dropdown-item" href="<?php echo base_url('home/switch_lang/'.$lang->slug) ?>"><?php echo html_escape($lang->name) ?></a></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </li>
                                <?php endif ?>

                            </ul>

                            <ul class="navbar-nav align-items-lg-center ml-lg-auto mt-0 mb-xs-3">
                                <li class="nav-item mr-0">
                                    <?php if (site_mode() == 'dark'): ?>
                                        <a class="btn btn-mode text-light ml-auto" href="<?php echo base_url('auth/switch_mode/light') ?>"><i class="bi bi-brightness-high-fill"></i></a>
                                    <?php endif ?>

                                    <?php if (site_mode() == 'light'): ?>
                                        <a class="btn btn-mode text-dark ml-auto" href="<?php echo base_url('auth/switch_mode/dark') ?>"><i class="bi bi-moon-stars-fill"></i></a>
                                    <?php endif ?>
                                    

                                    <?php if (is_admin()): ?>
                                        <a class="mr-2 btn btn-sm btn-outline-secondary ml-auto" href="<?php echo base_url('auth/logout') ?>"><i class="lni lni-exit"></i> <?php echo trans('logout') ?> </a>

                                        <a class="mr-2 btn btn-sm btn-<?php if(settings()->front_layout == 1){echo "primary";}else{echo "black";} ?> ml-auto" href="<?php echo base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> <?php echo trans('dashboard') ?></a>
                                    <?php elseif(is_mentee()): ?>

                                        <a class="mr-2 btn btn-sm btn-outline-secondary ml-auto" href="<?php echo base_url('auth/logout') ?>"><i class="lni lni-exit"></i> <?php echo trans('logout') ?> </a>

                                        <?php if(settings()->enable_email_verify == 1 && user()->email_verified == 0): ?>

                                            <a class="mr-2 btn btn-sm btn-warning ml-auto" href="<?php echo base_url('auth/verify?type=mail') ?>"><i class="bi bi-arrow-right mr-1"></i>Verify Email</a>
                                        <?php else: ?>
                                            <a class="mr-2 btn btn-sm btn-<?php if(settings()->front_layout == 1){echo "primary";}else{echo "black";} ?> ml-auto" href="<?php echo base_url('admin/dashboard/mentee') ?>"><i class="bi bi-speedometer2"></i> <?php echo trans('dashboard') ?></a>
                                        <?php endif; ?>
                                    <?php elseif(is_user()): ?>

                                        <a class="mr-2 btn btn-sm btn-outline-secondary ml-auto" href="<?php echo base_url('auth/logout') ?>"><i class="lni lni-exit"></i> <?php echo trans('logout') ?> </a>

                                         <?php if(settings()->enable_email_verify == 1 && user()->email_verified == 0): ?>

                                            <a class="mr-2 btn btn-sm btn-warning ml-auto" href="<?php echo base_url('auth/verify?type=mail') ?>"><i class="bi bi-arrow-right mr-1"></i>Verify Email</a>
                                        <?php else: ?>
                                            <a class="mr-2 btn btn-sm btn-<?php if(settings()->front_layout == 1){echo "primary";}else{echo "black";} ?> ml-auto" href="<?php echo base_url('admin/dashboard/user') ?>"><i class="bi bi-speedometer2"></i> <?php echo trans('dashboard') ?></a>
                                        <?php endif; ?>
                                         
                                    <?php else: ?>
                                        <a class="mr-2 btn btn-sm btn-outline-secondary ml-auto" href="<?php echo base_url('login') ?>"><?php echo trans('sign-in') ?></a>
                                        <a class="mr-2 btn btn-sm btn-<?php if(settings()->front_layout == 1){echo "primary";}else{echo "black";} ?> ml-auto" href="<?php echo base_url('register?register_type=mentor') ?>"><?php echo trans('get-started') ?></a>
                                    <?php endif ?>
                                </li>
                            </ul>

                        </div>
                        <!-- End Menu -->

                    </nav>
                </div>
            </header>
        <?php endif ?>

        <?php if (isset($page) && $page == 'Company'): ?>
            <header class="borderb-1 <?php if (isset($page_title) && $page_title == 'Company Home'){echo 'position-absolute'; $text = 'light'; $bg_color = 'darks';}else{$text = 'dark'; $bg_color = 'white';} ?> left-0 top-0 w-100">
                <div class="container">

                    <nav class="navbar navbar-expand-lg navbar-lights bg-whites py-111">

                        <!-- Brand -->
                        <a class="navbar-brand mr-lg-5" href="<?php echo base_url($slug) ?>">
                            <?php if (!empty($company->logo)):?>
                                <img width="120px" src="<?php echo base_url($company->logo) ?>" alt="logo">
                            <?php else: ?>
                                <span class="text-<?php echo html_escape($text) ?> company-name"><?php echo html_escape($company->name) ?></span>
                            <?php endif; ?>
                        </a>
                        <!-- End Brand -->

                        <!-- Toggler -->
                        <button type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
                            <span class="navbar-toggler-icon"><i class="bi bi-list"></i></span>
                        </button>
                        <!-- End Toggler -->

                        <!-- Collaps -->
                        <div id="navbarContent" class="collapse navbar-collapse company bg-<?php echo html_escape($bg_color) ?>">

                            <!-- Navigation -->
                            <ul class="navbar-nav align-items-lg-center m-auto">

                                <li class="nav-item py-xs-3">
                                    <a href="<?php echo base_url($slug) ?>"  class="nav-link <?php if(isset($page_title) && $page_title == "Company Home"){echo "active";} ?>  text-<?php echo html_escape($text) ?>">Home</a>
                                </li>

                                <li class="nav-item py-xs-3">
                                    <a href="<?php echo base_url('abouts/'.$slug) ?>"  class="nav-link <?php if(isset($page_title) && $page_title == "About us"){echo "active";} ?>  text-<?php echo html_escape($text) ?>">About us</a>
                                </li>


                                <?php if (check_user_feature_access($company->user_id, 'services') == TRUE && $company->enable_service == TRUE): ?>
                                    <li class="nav-item py-xs-3">
                                        <a href="<?php echo base_url('services/'.$slug) ?>"  class="nav-link <?php if(isset($page_title) && $page_title == "Services"){echo "active";} ?> text-<?php echo html_escape($text) ?>">Services</a>
                                    </li>
                                <?php endif; ?>


                                <?php if (check_user_feature_access($company->user_id, 'portfolios') == TRUE && $company->enable_portfolio == TRUE): ?>
                                    <li class="nav-item py-xs-3">
                                        <a href="<?php echo base_url('portfolios/'.$slug) ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Portfolios"){echo "active";} ?> text-<?php echo html_escape($text) ?>">Portfolios</a>
                                    </li>
                                <?php endif; ?>


                                <?php if (check_user_feature_access($company->user_id, 'products') == TRUE && $company->enable_product == TRUE): ?>
                                    <li class="nav-item py-xs-3">
                                        <a href="<?php echo base_url('products/'.$slug) ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Products"){echo "active";} ?> text-<?php echo html_escape($text) ?>">Products</a>

                                        <div class="dropdown-menu mega-dropdown-menu p-2 p-lg-0 shadow d-none">
                                            <div class="row m-0 p-0 p-lg-5">
                                                <?php foreach (get_product_categories($company->uid) as $p_category): ?>
                                                    <div class="col-md-4 mb-4">
                                                        <h6 class="mb-1 mb-lg-2 pl-2"><a class="text-dark" href="<?php echo base_url('products/'. $slug) ?>?category=<?php echo html_escape($p_category->category_id) ?>"><?php echo html_escape($p_category->name) ?></a></h6>
                                                        <ul class="mega list-unstyled">
                                                            <?php foreach ($p_category->products as $product): ?>
                                                                <li class="pl-2"><a href="<?php echo base_url('product/'). $product->slug . '/' . $slug ?>" class="mega-dropdown-item"><?php echo html_escape($product->title) ?></a></li>
                                                            <?php endforeach ?>
                                                        </ul>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>


                                <?php if (check_user_feature_access($company->user_id, 'events') == TRUE && $company->enable_event == TRUE): ?>
                                    <li class="nav-item py-xs-3">
                                        <a href="<?php echo base_url('events/'.$slug) ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Events"){echo "active";} ?> text-<?php echo html_escape($text) ?>">Events</a>

                                        <div class="dropdown-menu mega-dropdown-menu p-2 p-lg-0 shadow d">
                                            
                                            <div class="row m-0 p-0 p-lg-5">
                                                <?php foreach (get_event_categories($company->uid) as $e_category): ?>
                                                    <div class="col-md-4 mb-2 mb-lg-3">
                                                        <h6 class="mb-1 mb-lg-2 pl-2"><a class="text-dark" href="<?php echo base_url('events/'. $slug) ?>?category=<?php echo html_escape($e_category->category_id) ?>"><?php echo html_escape($e_category->name) ?></a></h6>
                                                        <ul class="mega list-unstyled">
                                                            <?php foreach ($e_category->events as $event): ?>
                                                                <li class="pl-2"><a href="<?php echo base_url('event/'). $event->slug . '/' . $slug ?>" class="mega-dropdown-item"><?php echo html_escape($event->title) ?></a></li>
                                                            <?php endforeach ?>
                                                        </ul>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                               
                                        </div>
                                    </li>
                                <?php endif; ?>


                                <li class="nav-item py-xs-3 dropdown">
                                    <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle text-<?php echo html_escape($text) ?>">Pages</a>

                                    <ul class="dropdown-menu shadow">

                                        <?php if (check_user_feature_access($company->user_id, 'gallery') == TRUE && $company->enable_gallery == TRUE): ?>
                                            <li class="nav-item pr-0">
                                                <a href="<?php echo base_url('gallery/'.$slug) ?>"  class="dropdown-item">Gallery</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (check_user_feature_access($company->user_id, 'team') == TRUE && $company->enable_team == TRUE): ?>
                                            <li class="nav-item pr-0">
                                                <a href="<?php echo base_url('teams/'.$slug) ?>"  class="dropdown-item">Teams</a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (check_user_feature_access($company->user_id, 'blogs') == TRUE && $company->enable_blog == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('blogs/'.$slug) ?>"  class="dropdown-item">Blogs</a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (check_user_feature_access($company->user_id, 'portfolios') == TRUE && $company->enable_portfolio == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('portfolios/'.$slug) ?>"  class="dropdown-item">Portfolios</a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (check_user_feature_access($company->user_id, 'career') == TRUE && $company->enable_career == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('careers/'.$slug) ?>"  class="dropdown-item">Careers</a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (check_user_feature_access($company->user_id, 'services') == TRUE && $company->enable_service == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('services/'.$slug) ?>"  class="dropdown-item">Services</a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (check_user_feature_access($company->user_id, 'events') == TRUE && $company->enable_event == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('events/'.$slug) ?>"  class="dropdown-item">Events</a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (check_user_feature_access($company->user_id, 'faqs') == TRUE && $company->enable_faq == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('faqs/'.$slug) ?>"  class="dropdown-item">Faq</a>
                                            </li>
                                        <?php endif; ?>


                                        <li class="nav-item">
                                            <a href="<?php echo base_url('abouts/'.$slug) ?>"  class="dropdown-item">About us</a>
                                        </li>

                                        
                                        <li class="nav-item">
                                            <a href="<?php echo base_url('contacts/'.$slug) ?>"  class="dropdown-item">Contact</a>
                                        </li>


                                        <?php if (check_user_feature_access($company->user_id, 'quotes') == TRUE && $company->enable_quote == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('quotes/'.$slug) ?>"  class="dropdown-item">Quote</a>
                                            </li>
                                        <?php endif; ?>



                                        <?php if (check_user_feature_access($company->user_id, 'products') == TRUE && $company->enable_product == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('products/'.$slug) ?>"  class="dropdown-item">Products</a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (check_user_feature_access($company->user_id, 'products') == TRUE && $company->enable_product == TRUE): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo base_url('cart/'.$slug) ?>"  class="dropdown-item">Cart</a>
                                            </li>
                                        <?php endif; ?>

                                    </ul>


                                </li>

                                <li class="nav-item py-xs-3">
                                    <a href="<?php echo base_url('contacts/'.$slug) ?>"  class="nav-link <?php if(isset($page_title) && $page_title == "Contact"){echo "active";} ?>  text-<?php echo html_escape($text) ?>">Contact</a>
                                </li>

                                

                                <?php if (check_user_feature_access($company->user_id, 'products') == TRUE && $company->enable_product == TRUE): ?>
                                    <li class="nav-item dropdown">
                                        <a href="#" data-toggle="dropdown" class="nav-link  dropdown-toggles cart_link text-<?php echo html_escape($text) ?>">
                                            <i class="bi bi-cart icons fs-15">
                                                <span class="cart-num <?php if($this->cart->total_items() == 0){echo 'd-none';}else{echo 'd-show';};?>">
                                                    <?php $this->load->view('include/header_cart_count'); ?>
                                                </span>
                                            </i> 
                                            
                                        </a>
                                        
                                        <div id="cart" class="load_cart_data">              
                                          <?php $this->load->view('include/header_cart');?>
                                        </div>
                                    </li>
                                <?php endif; ?>

                            </ul>


                            <ul class="navbar-nav align-items-lg-center ml-lg-auto mt-0">
                                <li class="nav-item mr-0">
                                    <?php if (is_admin()): ?>
                                        <a class="btn btn-sm btn-secondary ml-auto" href="<?php echo base_url('auth/logout') ?>"><i class="lni lni-exit"></i> <?php echo trans('logout') ?> </a>

                                        <a class="btn btn-sm btn-primary ml-auto" href="<?php echo base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> <?php echo trans('dashboard') ?></a>
                                    <?php elseif(is_customer()): ?>

                                        <a class="btn btn-sm btn-secondary ml-auto" href="<?php echo base_url('auth/logout') ?>"><i class="lni lni-exit"></i> <?php echo trans('logout') ?> </a>

                                         <a class="btn btn-sm btn-primary ml-auto" href="<?php echo base_url('customer/appointments') ?>"><i class="bi bi-speedometer2"></i> <?php echo trans('dashboard') ?></a>
                                    <?php elseif(is_staff()): ?>

                                        <a class="btn btn-sm btn-secondary ml-auto" href="<?php echo base_url('auth/logout') ?>"><i class="lni lni-exit"></i> <?php echo trans('logout') ?> </a>

                                         <a class="btn btn-sm btn-primary ml-auto" href="<?php echo base_url('staff/appointments') ?>"><i class="bi bi-speedometer2"></i> <?php echo trans('dashboard') ?></a>
                                    <?php elseif(is_user()): ?>

                                        <a class="btn btn-sm btn-secondary ml-auto" href="<?php echo base_url('auth/logout') ?>"><i class="lni lni-exit"></i> <?php echo trans('logout') ?> </a>

                                         <a class="btn btn-sm btn-primary ml-auto" href="<?php echo base_url('admin/dashboard/user') ?>"><i class="bi bi-speedometer2"></i> <?php echo trans('dashboard') ?></a>
                                         
                                    <?php else: ?>
                                        <a class="btn btn-sm btn-light ml-auto" href="<?php echo base_url('login') ?>"><?php echo trans('sign-in') ?></a>
                                        <a class="btn btn-sm btn-primary ml-auto" href="<?php echo base_url('register?register_type=mentor') ?>"><?php echo trans('get-started') ?></a>
                                    <?php endif ?>
                                </li>
                            </ul>

                            <!-- End Navigation -->

                        </div>
                        <!-- End Collaps -->

                    </nav>

                    
                </div>
            </header>
        <?php endif ?>

