
    <!-- Footer -->
    <?php if (isset($menu) && $menu == TRUE): ?>
        <?php if (settings()->front_layout == 1): ?>
            <footer class="pt-8 border-top border-light">
                <div class="container">
                    <div class="row pb-5">
                        <div class="col-sm-5 col-lg-5 mb-5 mb-lg-0">
                            <img src="<?php echo base_url(settings()->logo) ?>" class="w-30 mb-4" alt="logo">
                            <p class=""><?php echo html_escape(settings()->footer_about) ?></p>
                            <ul class="list-unstyled social-icon3 mb-0">
                                <?php if (!empty($settings->facebook)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->facebook) ?>"><i class="lni lni-facebook-original"></i></a></li>
                                <?php endif ?>

                                <?php if (!empty($settings->twitter)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->twitter) ?>"><i class="lni lni-twitter"></i></a></li>
                                <?php endif ?>

                                <?php if (!empty($settings->linkedin)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->linkedin) ?>"><i class="lni lni-linkedin-original"></i></a></li>
                                <?php endif ?>

                                <?php if (!empty($settings->instagram)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->instagram) ?>"><i class="lni lni-instagram-original"></i></a></li>
                                <?php endif ?>
                            </ul>
                        </div>

                        <div class="col-sm-1 col-lg-1 mb-5 mb-sm-0"></div>

                        <div class="col-sm-3 col-lg-3 mb-5 mb-lg-0">
                            <h3 class="h6"><?php echo trans('services') ?></h3>
                            <ul class="footer-list-style-two">
                                <li><a href="<?php echo base_url('mentors') ?>"><?php echo trans('mentors') ?></a></li>
                                
                                <?php if (settings()->enable_blog == 1): ?>
                                    <li><a href="<?php echo base_url('blogs') ?>"><?php echo trans('blogs') ?></a></li>
                                <?php endif; ?>

                                <?php if (settings()->enable_faq == 1): ?>
                                <li><a href="<?php echo base_url('faqs') ?>"><?php echo trans('faqs') ?></a></li>
                                <?php endif; ?>

                                <li><a href="<?php echo base_url('contact') ?>"><?php echo trans('contact') ?></a></li>
                            </ul>
                        </div>

                        <div class="col-sm-3 col-lg-3 mb-5 mb-sm-0">
                            <?php if (!empty(get_pages(0, 'admin', 1))): ?>
                            <h3 class="h6"><?php echo trans('pages') ?></h3>
                            <ul class="footer-list-style-two">
                                <?php foreach (get_pages(0, 'admin', 1) as $page): ?>
                                    <li><a href="<?php echo base_url('page/'.$page->slug) ?>"><?php echo html_escape($page->title) ?></a></li>
                                <?php endforeach ?>
                            </ul>
                            <?php endif ?>
                        </div>

                    </div>
                </div>

                <div class="text-center border-top border-light">
                    <div class="container">
                        <div class="row py-4">
                            <div class="col-md-12">
                                <p class="mb-0"><?php echo html_escape(settings()->copyright) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        <?php endif; ?>
    
    
        <?php if (settings()->front_layout == 2): ?>
            <footer class="footer-07">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12 text-center">
                            <h2 class="footer-heading"><a href="#" class="logo"><?php echo settings()->site_name ?></a></h2>
                            <p class="menu">
                                
                                <a href="<?php echo base_url('mentors') ?>"><?php echo trans('mentors') ?></a>
                                
                                <?php if (settings()->enable_blog == 1): ?>
                                    <a href="<?php echo base_url('blogs') ?>"><?php echo trans('blogs') ?></a>
                                <?php endif; ?>

                                <?php if (settings()->enable_faq == 1): ?>
                                <a href="<?php echo base_url('faqs') ?>"><?php echo trans('faqs') ?></a>
                                <?php endif; ?>

                                <a href="<?php echo base_url('contact') ?>"><?php echo trans('contact') ?></a>
                                
                            </p>
                            <ul class="list-unstyled social-icon33 mb-0">
                                <?php if (!empty($settings->facebook)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->facebook) ?>"><i class="lni lni-facebook-original"></i></a></li>
                                <?php endif ?>

                                <?php if (!empty($settings->twitter)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->twitter) ?>"><i class="lni lni-twitter"></i></a></li>
                                <?php endif ?>

                                <?php if (!empty($settings->linkedin)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->linkedin) ?>"><i class="lni lni-linkedin-original"></i></a></li>
                                <?php endif ?>

                                <?php if (!empty($settings->instagram)) : ?>
                                    <li><a target="_blank" href="<?= prep_url($settings->instagram) ?>"><i class="lni lni-instagram-original"></i></a></li>
                                <?php endif ?>
                            </ul>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-12 text-center">
                            <p class="copyright fs-13">
                                <?php echo html_escape(settings()->copyright) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        <?php endif; ?>
    <?php endif; ?>
    

    <!-- End Footer -->

    </div>

    <?php if (settings()->enable_pwa == 1): ?>
        <a class="btn btn-primary bg-primary-soft" id="installPwa" href="#" style="display: none"><i class="bi bi-arrow-down-circle-fill fs-15"></i> Install PWA</a>
    <?php endif; ?>

    <?php include'js_msg_list.php'; ?>

    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <?php $success = $this->session->flashdata('msg'); ?>
    <?php $error = $this->session->flashdata('error'); ?>

    <input type="hidden" id="success" value="<?php if(isset($success)){echo html_escape($success);} ?>">
    <input type="hidden" id="error" value="<?php if(isset($error)){echo html_escape($error);} ?>">  
    <input type="hidden" id="cp" value="<?php echo strlen(settings()->purchase_code);?>">
    <a href="javascript:void(0)" class="scroll-to-top"><i class="fa fa-angle-up"></i></a>
    <input type="hidden" class="accept_cookies" value="<?php echo trans('accept_cookies') ?>">
    <input type="hidden" class="accept" value="<?php echo trans('accept') ?>">
    <input type="hidden" id="country_code" value="<?php echo strtolower(settings()->code); ?>">
    <input type="hidden" id="lan_type" value="<?php echo text_dir(); ?>">

    <!-- Global JS -->
    <script src="<?php echo base_url() ?>assets/front/libs/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>assets/front/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?php echo base_url() ?>assets/front/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- owl carousel js -->
    <script src="<?php echo base_url() ?>assets/front/libs/owl-carousel/dist/js/owl.carousel.min.js"></script>
    <script src="<?php echo base_url() ?>assets/admin/js/sweet-alert.js"></script>
    <!-- nice select js -->
    <script src="<?php echo base_url()?>assets/admin/js/nice-select.min.js"></script>
    <script src="<?php echo base_url()?>assets/admin/plugins/select2/js/select2.full.min.js"></script>
    <!-- tagsinput js -->
    <script src="<?php echo base_url() ?>assets/admin/js/bootstrap-tagsinput.js"></script>
    <!-- animation js -->
    <?php if(settings()->enable_animation == 1): ?>
        <script src="<?php echo base_url() ?>assets/front/js/aos.js"></script>
    <?php endif; ?>
    <!-- moment js -->
    <script src="<?php echo base_url() ?>assets/front/js/moment.min.js"></script>
    <!-- Custom JS -->
    <script type="text/javascript" src="<?php echo base_url() ?>assets/front/js/custom.js?var=<?= settings()->version ?>&time=<?=time();?>"></script>
    <!-- stripe js -->
    <?php $this->load->view('admin/include/stripe-js.php');?>

    <?php if(isset($page_title) && $page_title == 'Booking' || $page_title == 'Profile'): ?>
        <script src="<?php echo base_url() ?>assets/admin/js/jquery-ui.min.js"></script>
        <?php //$this->load->view('include/datepicker-js.php');?>
    <?php endif; ?>





    <?php if (settings()->enable_pwa == 1): ?>
        <?php include 'pwa_footer_js.php'; ?>
    <?php endif; ?>

</body>


</html>