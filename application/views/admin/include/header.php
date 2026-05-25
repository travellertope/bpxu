<!DOCTYPE html>
<html lang="en" dir="<?php echo text_dir(); ?>">
<head>
  <?php $settings = get_settings(); ?>
  <?php $user = get_logged_user($this->session->userdata('id')); ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="author" content="Codericks">
  <link rel="icon" href="<?php echo base_url($settings->favicon) ?>">
  
  <title>
    <?php echo html_escape($settings->site_name); ?>  
    <?php if(isset($page_title)){echo ' &bull; '.html_escape($page_title);}else{echo "Dashboard";} ?>
 </title>


  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/line-icons/lineicons.css"> 
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/admin_default.css?var=<?= settings()->version ?>&time=<?=time();?>">
  <!-- Google Font: Inter -->
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,400i,700&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/fonts/bootstrap/bootstrap-icons.css">
  <link href="<?php echo base_url() ?>assets/admin/css/bootstrapicons-iconpicker.css" rel="stylesheet">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- sweet alert -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/sweet-alert.css">
  <!-- tags inputs -->
  <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-tagsinput.css" rel="stylesheet" />
  <!-- css animation -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/css/aos.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/summernote/summernote-bs4.css">

  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/select2/css/select2.min.css">
  <!-- nice-select -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/nice-select.css">

  <!-- timepicker -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/timepicker.min.css">

  <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-datepicker.min.css" rel="stylesheet"/>

  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/bootstrap-colorpicker.min.css">

  <!-- lightbox -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/lightbox/src/css/lightbox.css">

  <!-- fullcalendar -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/fullcalendar-main.min.css">

  <?php if (isset($page_title) && $page_title == 'Holidays'): ?>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/holiday.css">
  <?php endif; ?>
  
  <?php if (text_dir() == 'rtl'): ?>
    <link rel="stylesheet" href="<?php echo base_url()?>assets/admin/css/bootstrap-rtl.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-rtl.css">
  <?php endif ?>

  <?php if (settings()->layout == 1): ?>
    <link href="<?php echo base_url() ?>assets/admin/css/admin_light.css" rel="stylesheet">
  <?php endif ?>

  <?php if (site_mode() == 'dark'): ?>
      <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/dark.css">
  <?php endif ?>

  <script type="text/javascript">
   var csrf_token = '<?= $this->security->get_csrf_hash(); ?>';
   var token_name = '<?= $this->security->get_csrf_token_name();?>'
 </script>

  <?php if (settings()->enable_captcha == 1 && settings()->captcha_site_key != ''): ?>
      <script src='https://www.google.com/recaptcha/api.js'></script>
  <?php endif; ?>
 
  </head>

  <body class="hold-transition sidebar-mini">
  
  <div class="wrapper <?php if(settings()->site_info == 3){echo "d-none";} ?>">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav pl-3">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <?php if(is_user()): ?>
        <li class="nav-item d-sm-inline-block">
            <a target="_blank" href="<?php echo base_url('mentor/'. user()->slug) ?>" class="btn btn-primary btn-sm mt-1 ml-2"><i class="lni lni-eye"></i> <?php echo trans('view-profile') ?></a>
        </li>
        <li class="nav-item d-sm-inline-block" id="copy_profile_btn">
            <button class="btn btn-outline-secondary btn-sm mt-1 ml-2 copy_profile_btn" onclick="CopyMe('<?php echo base_url('mentor/'. user()->slug) ?>')"><i class="fas fa-copy"></i> <?php echo trans('copy-profile') ?></button>
            
        </li>

        <p class="font-weight-normal text-success text-right mt-1" id="successMsg1"></p>

        
      <?php endif; ?>
      <?php if(is_admin()): ?>
        <li class="nav-item d-sm-inline-block">
          <a target="_blank" href="<?php echo base_url() ?>" class="btn btn-primary btn-sm mt-1 ml-2"><i class="lni lni-eye"></i> <?php echo trans('view-site') ?></a>
        </li>

        <li class="nav-item d-sm-inline-blocks d-none">
          <span class="btn btn-secondary-soft border-1 btn-sm mt-1 ml-2"><i class="bi bi-clock"></i> <?php echo get_my_time_by_zone(get_by_id(settings()->time_zone , 'time_zone')->name); ?></span>
        </li>
      <?php else: ?>
        <li class="nav-item d-sm-inline-blocks d-none">
          <span class="btn btn-secondary-soft border-1 btn-sm mt-1 ml-2"><i class="bi bi-clock"></i> <?php echo get_my_time_by_zone(get_by_id(settings()->time_zone , 'time_zone')->name); ?></span>
        </li>
      <?php endif; ?>
    </ul>



    <!-- Right navbar links -->
    <ul class="rtlnav navbar-nav <?php if(text_dir() == 'ltr'){echo "ml-auto";} ?>">
      <!-- Messages Dropdown Menu -->

      <?php if (settings()->enable_multilingual == 1): ?>
        <li class="nav-item d-sm-inline-block">
          <div class="dropdown">
            <a class="btn btn-outline-secondary btn-sm mt-1 ml-3 mr-1 dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php echo lang_short_form(); ?>
            </a>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
              <?php foreach (get_language() as $lang): ?>
                <a selected class="dropdown-item" href="<?php echo base_url('home/switch_lang/'.$lang->slug) ?>"><?php echo html_escape($lang->name) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </li>
      <?php endif; ?>


      <?php if (site_mode() == 'dark'): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('auth/switch_mode/light') ?>">
              <i class="bi bi-brightness-high-fill fs-20"></i>
            </a>
          </li>
      <?php endif ?>

      <?php if (site_mode() == 'light'): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('auth/switch_mode/dark') ?>">
              <i class="bi bi-moon-stars-fill fs-20"></i>
            </a>
          </li>
      <?php endif ?>

      <li class="nav-item  mr-3">
        <a class="nav-link header_notifications" href="#">
          <i class="bi bi-bell fs-20"></i>
          <?php //if (count_unseen_notification() != 0): ?>
            <span class="badge badge-danger navbar-badge unseen-count">
              <?php echo count_unseen_notification() ?></span>
            <?php //endif; ?>
        </a>

        <div class="header_notifications_area">
          <?php $this->load->view('admin/include/header_notifications'); ?>
        </div>
      </li>

      <li class="nav-item dropdown pr-4">
        <a class="nav-link user-log" data-toggle="dropdown" href="#">
          <i class="lnib lni-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right mr-4">
          
          <a href="#" class="dropdown-item">
            <div class="media">
              <?php if (user()->role == 'admin'): ?>
                
              <?php else: ?>
                <div class="avatar-sm mr-2" style="background-image: url(<?php echo base_url(user()->thumb) ?>);"></div>
              <?php endif ?>
              
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  <?php echo character_limiter(user()->name, 18); ?>
                </h3>
                <p class="text-sm text-muted"><?php echo user()->email; ?></p>
              </div>
            </div>
          </a>

          <?php if (user()->role == 'user'): ?>
          <div class="dropdown-divider"></div>
          <a href="<?php echo base_url('admin/settings/profile') ?>" class="dropdown-item fs-13">
            <i class="bi bi-person mr-2"></i> <?php echo trans('manage-profile') ?>
          </a>
          <?php endif ?>

          <div class="dropdown-divider"></div>
          <a href="<?php echo base_url('admin/settings/change_password') ?>" class="dropdown-item fs-13">
            <i class="lni lni-lock-alt mr-2"></i> <?php echo trans('change-password') ?>
          </a>

          <div class="dropdown-divider"></div>
          <a href="<?php echo base_url('auth/logout') ?>" class="dropdown-item fs-13">
            <i class="lni lni-exit mr-2"></i> <?php echo trans('logout') ?>
          </a>
        </div>
      </li>

      
     
    </ul>
  </nav>
  <!-- /.navbar -->


