<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a target="_blank" href="<?php echo base_url() ?>" class="brand-link">
      <img src="<?php echo base_url(settings()->favicon) ?>" alt="AdminLTE Logo" class="brand-image img-circle">
      <span class="brand-text font-weight-bold"><?php echo html_escape(settings()->site_name) ?></span>
      <?php if(get_user_info() == TRUE){$uval = 'd-show';}else{$uval = 'd-hide';} ?>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
     
      <!-- Sidebar Menu -->
      <nav class="mt-4">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      

        <?php if (is_admin()): ?>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/dashboard') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Dashboard"){echo "active";} ?>">
              <i class="nav-icon lni lni-grid-alt"></i> <p><?php echo trans('dashboard') ?></p>
            </a>
          </li>
         
          <li class="nav-item has-treeview <?php if(isset($page) && $page == "Settings"){echo "menu-open";} ?>">
            <a href="#" class="nav-link <?php if(isset($page) && $page == "Settings"){echo "active";} ?>">
              <i class="nav-icon lni lni-cog"></i>
              <p>
                <?php echo trans('settings') ?>
                <i class="right lni lni-chevron-left"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('admin/settings') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "System Settings"){echo "active";} ?>">
                  <i class="lni lni-layout nav-icon"></i>
                  <p><?php echo trans('website-settings') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo base_url('admin/payment/settings') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Payment Settings"){echo "active";} ?>">
                  <i class="lni lni-coin nav-icon"></i>
                  <p><?php echo trans('payment-settings') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Country"){echo "active";} ?>" href="<?php echo base_url('admin/country') ?>">
                  <i class="fa fa-globe nav-icon"></i> <p><?php echo trans('country') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Time Zone"){echo "active";} ?>" href="<?php echo base_url('admin/time_zone') ?>">
                  <i class="far fa-clock nav-icon"></i></i> <p><?php echo trans('time-zone') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Font"){echo "active";} ?>" href="<?php echo base_url('admin/font') ?>">
                  <i class="nav-icon lni lni-text-format"></i> <p><?php echo trans('fonts') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo base_url('admin/settings/license') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "License"){echo "active";} ?>">
                  <i class="lni lni-key nav-icon rt-90"></i>
                  <p><?php echo trans('license') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo base_url('admin/settings/change_password') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Change Password"){echo "active";} ?>">
                  <i class="lni lni-lock-alt nav-icon"></i>
                  <p><?php echo trans('change-password') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Email Template"){echo "active";} ?>" href="<?php echo base_url('admin/email_templates') ?>">
                  <i class="nav-icon bi bi-envelope ml-2 mr-1"></i> <p><?php echo trans('email-templates') ?></p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item has-treeview <?php if(isset($page) && $page == "Payouts"){echo "menu-open";} ?> ">
            <a href="#" class="nav-link <?php if(isset($page) && $page == "Payouts"){echo "active";} ?>">
              <i class="nav-icon lni lni-credit-cards"></i>
              <p>
                <?php echo trans('payouts') ?>
                <i class="right lni lni-chevron-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Add Payout"){echo "active";} ?>" href="<?php echo base_url('admin/payouts/add') ?>"><i class="lni lni-circle-plus nav-icon"></i> <p><?php echo trans('add-payout') ?></p></a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Payout Settings"){echo "active";} ?>" href="<?php echo base_url('admin/payouts/settings') ?>"><i class="lni lni-coin nav-icon"></i> <p><?php echo trans('payout-settings') ?></p></a>
              </li>
              
              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Payout Requests"){echo "active";} ?>" href="<?php echo base_url('admin/payouts/requests') ?>"><i class="lni lni-reload nav-icon"></i>
                  <!-- <span class="badge badge-danger right d-none"><?php echo get_total_payout_request() ?></span> -->
                   <p><?php echo trans('payout-requests') ?></p></a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Payout Completed"){echo "active";} ?>" href="<?php echo base_url('admin/payouts/completed') ?>"><i class="far fa-check-circle nav-icon"></i> <p><?php echo trans('completed') ?></p></a>
              </li>
            </ul>
          </li>

          <li class="nav-item  has-treeview <?php if(isset($page) && $page == "Affiliate"){echo "menu-open";} ?> ">
            <a href="#" class="nav-link <?php if(isset($page) && $page == "Affiliate"){echo "active";} ?>">
              <i class="nav-icon fas fa-share-alt"></i>
              <p>
                <?php echo trans('affiliate') ?>
                <i class="right lni lni-chevron-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Referral_Settings"){echo "active";} ?>" href="<?php echo base_url('admin/referral/settings') ?>">
                  <i class="nav-icon fas fa-cog"></i> <p><?php echo trans('referral-settings') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Payout Request"){echo "active";} ?>" href="<?php echo base_url('admin/referral/payout_request') ?>">
                  <i class="fas fa-credit-card nav-icon"></i> <p><?php echo trans('payout-request') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Completed Payout"){echo "active";} ?>" href="<?php echo base_url('admin/referral/completed_payout') ?>"><i class="far fa-check-circle nav-icon"></i> <p><?php echo trans('completed') ?></p></a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Language"){echo "active";} ?>" href="<?php echo base_url('admin/language') ?>">
              <i class="lni lni-world nav-icon"></i></i></i> <p><?php echo trans('language') ?></p>
            </a>
          </li>

          
          <li class="nav-item d-none">
            <a class="nav-link d-none<?php if(isset($page_title) && $page_title == "Package"){echo "active";} ?>" href="<?php echo base_url('admin/package') ?>">
              <i class="nav-icon lni lni-layers"></i> <p><?php echo trans('plans') ?></p>
            </a>
          </li>

          <li class="nav-item has-treeview <?php if(isset($page) && $page == "Skill"){echo "menu-open";} ?> ">
            <a href="#" class="nav-link <?php if(isset($page) && $page == "Skill"){echo "active";} ?>">
              <i class="nav-icon lni lni-bulb"></i>
              <p>
                <?php echo trans('skills') ?>
                <i class="right lni lni-chevron-left"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Category"){echo "active";} ?>" href="<?php echo base_url('admin/category') ?>">
                  <i class="nav-icon lni lni-folder"></i> <p><?php echo trans('categories') ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Skill"){echo "active";} ?>" href="<?php echo base_url('admin/skill') ?>">
                  <i class="nav-icon lni lni-folder"></i> <p><?php echo trans('sub-categories') ?></p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Users"){echo "active";} ?>" href="<?php echo base_url('admin/mentors') ?>">
              <i class="nav-icon lni lni-users"></i> <p><?php echo trans('mentors') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "KYC"){echo "active";} ?>" href="<?php echo base_url('admin/verification/kyc/?search=pending') ?>">
              <i class="nav-icon lni lni-users"></i> <p><?php echo trans('kyc') ?></p>
            </a>
          </li>

          <li class="nav-item d-nones">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Mentee"){echo "active";} ?>" href="<?php echo base_url('admin/mentees') ?>">
              <i class="nav-icon lni bi bi-people"></i> <p><?php echo trans('mentees') ?></p>
            </a>
          </li>

          <li class="nav-item d-hide">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Coupons"){echo "active";} ?>" href="<?php echo base_url('admin/coupons/plan') ?>">
            <i class="nav-icon lni lni-offer"></i> <p><?php echo trans('coupons') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Transactions"){echo "active";} ?>" href="<?php echo base_url('admin/payment/transactions') ?>">
              <i class="nav-icon lni lni-investment"></i> <p><?php echo trans('transactions') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Blog"){echo "active";} ?>" href="<?php echo base_url('admin/blog') ?>">
              <i class="nav-icon lni lni-image"></i> <p><?php echo trans('blogs') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Brand"){echo "active";} ?>" href="<?php echo base_url('admin/brand') ?>">
              <i class="nav-icon lni lni-app-store"></i> <p><?php echo trans('brand') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Workflow"){echo "active";} ?>" href="<?php echo base_url('admin/workflow') ?>">
              <i class="nav-icon lni lni-arrow-right-circle"></i> <p><?php echo trans('workflow') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Testimonials"){echo "active";} ?>" href="<?php echo base_url('admin/testimonial') ?>">
              <i class="nav-icon far fa-comment-dots"></i> <p><?php echo trans('testimonials') ?> </p> 
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Features"){echo "active";} ?>" href="<?php echo base_url('admin/site_features') ?>">
              <i class="nav-icon lni lni-star"></i> <p><?php echo trans('features') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Pages"){echo "active";} ?>" href="<?php echo base_url('admin/pages') ?>">
              <i class="nav-icon lni lni-layout"></i> <p><?php echo trans('pages') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Faqs"){echo "active";} ?>" href="<?php echo base_url('admin/faq') ?>">
              <i class="nav-icon lni lni-question-circle"></i> <p><?php echo trans('faqs') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Contact"){echo "active";} ?>" href="<?php echo base_url('admin/contact') ?>">
              <i class="nav-icon lni lni-popup"></i> <p><?php echo trans('contacts') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/reports') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Reports"){echo "active";} ?>">
              <i class="far fa-chart-bar nav-icon"></i> <p><?php echo trans('report') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "App Info"){echo "active";} ?>" href="<?php echo base_url('admin/dashboard/app_info') ?>">
              <i class="nav-icon far fa-question-circle"></i> <p><?php echo trans('info') ?></p>
            </a>
          </li>

        <?php endif; ?>


        <!-- user menu start -->
        <?php if (is_user()): ?>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/dashboard/user') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "User Dashboard"){echo "active";} ?>">
              <i class="nav-icon bi bi-house-door"></i> <p><?php echo trans('dashboard') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page) && $page == "Settings"){echo "active";} ?>" href="<?php echo base_url('admin/settings/profile') ?>">
            <i class="nav-icon bi bi-gear"></i> <p><?php echo trans('settings') ?></p>
            </a>
          </li>

          <li class="d-none nav-item has-treeview <?php if(isset($page) && $page == "Settings"){echo "menu-open";} ?>">
            <a href="#" class="nav-link <?php if(isset($page) && $page == "Settings"){echo "active";} ?>">
              <i class="nav-icon bi bi-gear"></i>
              <p>
                <?php echo trans('settings') ?>
                <i class="right lni lni-chevron-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('admin/settings/profile') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Profile Settings"){echo "active";} ?>">
                  <i class="bi bi-person-circle nav-icon"></i>
                  <p><?php echo ucfirst(trans('account')) ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Mentorship Profile Settings"){echo "active";} ?>" href="<?php echo base_url('admin/settings/mentorship') ?>"><i class="bi bi-person-workspace nav-icon"></i> <p></p><?php echo trans('mentorship') ?></a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Schedule"){echo "active";} ?>" href="<?php echo base_url('admin/settings/schedule') ?>"><i class="bi bi-clock nav-icon"></i> <p></p><?php echo trans('schedule') ?></a>
              </li>
            </ul>
          </li>

          <li class="nav-item hide">
            <a href="<?php echo base_url('admin/verification') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Verification"){echo "active";} ?>">
              <i class="bi bi-person-bounding-box nav-icon"></i>
              <p><?php echo trans('kyc') ?></p>
            </a>
          </li>

          <li class="nav-item hide has-treeview <?php if(isset($page) && $page == "Payouts"){echo "menu-open";} ?> ">
            <a href="#" class="nav-link <?php if(isset($page) && $page == "Payouts"){echo "active";} ?>">
              <i class="bi bi-credit-card nav-icon"></i>
              <p>
                <?php echo trans('payouts') ?>
                <i class="lni lni-chevron-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Set Payout Account"){echo "active";} ?>" href="<?php echo base_url('admin/payouts/setup_account') ?>"><i class="bi bi-person-gear nav-icon"></i> <p><?php echo trans('set-payout-account') ?></p></a>
              </li>
              
              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Payouts"){echo "active";} ?>" href="<?php echo base_url('admin/payouts/user ') ?>"><i class="bi bi-credit-card nav-icon"></i> <p><?php echo trans('payouts') ?></p></a>
              </li>
            </ul>
          </li>


          <?php if (affiliate_settings()->is_enable == 1): ?>
            <li class="nav-item has-treeview <?php if(isset($page) && $page == "Affiliate"){echo "menu-open";} ?>">
                <a href="#" class="nav-link <?php if(isset($page) && $page == "Affiliate"){echo "active";} ?>">
                  <i class="nav-icon bi bi-share-fill"></i>
                  <p>
                    <?php echo trans('affiliate') ?>
                    <i class="right lni lni-chevron-left"></i>
                  </p>
                </a>

                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo base_url('admin/referral/user') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Home"){echo "active";} ?>">
                      <i class="nav-icon fas fa-home"></i>
                      <p><?php echo trans('home') ?></p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link <?php if(isset($page_title) && $page_title == "Referral"){echo "active";} ?>" href="<?php echo base_url('admin/referral/my_referrals') ?>"><i class="fas fa-retweet nav-icon"></i></i> <p></p><?php echo trans('referrals') ?></a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link <?php if(isset($page_title) && $page_title == "Payouts"){echo "active";} ?>" href="<?php echo base_url('admin/referral/payouts ') ?>"><i class="fas fa-credit-card nav-icon"></i> <p><?php echo trans('payouts') ?></p></a>
                  </li>
                </ul>
            </li>
          <?php endif; ?>
          

          <li class="nav-item">
            <a href="<?php echo base_url('admin/sessions') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Session" || $page == 'Session'){echo "active";} ?>">
              <i class="bi bi-view-list nav-icon"></i>
              <p><?php echo trans('sessions') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/sessions/booking?search=upcoming') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Session Booking" || $page == "Session Booking"){echo "active";} ?>">
              <i class="bi bi-clock nav-icon"></i>
              <p><?php echo trans('bookings') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/sessions/booking_calendars') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Calendars"){echo "active";} ?>">
              <i class="bi bi-calendar3 nav-icon"></i>
              <p><?php echo trans('calendar') ?></p>
            </a>
          </li>

          <li class="nav-item hide">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Transactions"){echo "active";} ?>" href="<?php echo base_url('admin/payment/transactions') ?>">
              <i class="bi bi-repeat nav-icon"></i></i> <p><?php echo trans('transactions') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/settings/holidays') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Holidays"){echo "active";} ?>">
              <i class="bi bi-calendar-week nav-icon"></i>
              <p><?php echo trans('holidays') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/message') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Message"){echo "active";} ?>">
              <i class="nav-icon bi bi-chat-dots"></i> <p><?php echo trans('message') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/favourite') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Favourite"){echo "active";} ?>">
              <i class="nav-icon bi bi-heart"></i> <p><?php echo trans('favourite') ?></p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?php echo base_url('admin/experience') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Experience" || $page == "Experience"){echo "active";} ?>">
              <i class="bi bi-graph-up nav-icon"></i>
              <p><?php echo trans('experience') ?></p>
            </a>
          </li>
        
          
          <!-- 
          <li class="nav-item">
            <a href="<?php echo base_url('admin/education') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Education" || $page == "Education"){echo "active";} ?>">
              <i class="bi bi-book nav-icon"></i>
              <p>Education</p>
            </a>
          </li>
          -->
          
          
          <li class="nav-item hide">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Coupon" || $page == "Coupon"){echo "active";} ?>" href="<?php echo base_url('admin/coupon') ?>">
            <i class="bi bi-gift nav-icon"></i> <p><?php echo trans('coupon') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if(isset($page_title) && $page_title == "Blog" || $page == "Blog"){echo "active";} ?>" href="<?php echo base_url('admin/blog') ?>">
            <i class="bi bi-gift nav-icon"></i> <p><?php echo trans('blogs') ?></p>
            </a>
          </li>


          <li class="d-none nav-item has-treeview <?php if(isset($page) && $page == "Affiliate"){echo "menu-open";} ?>">
            <a href="#" class="nav-link <?php if(isset($page) && $page == "Affiliate"){echo "active";} ?>">
              <i class="nav-icon bi bi-share"></i>
              <p>
                <?php echo trans('affiliate') ?>
                <i class="right lni lni-chevron-left"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('admin/referral/user') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Home"){echo "active";} ?>">
                  <i class="nav-icon fas fa-home"></i>
                  <p><?php echo ucfirst(trans('account')) ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Referral"){echo "active";} ?>" href="<?php echo base_url('admin/referral/my_referrals') ?>"><i class="fas fa-retweet nav-icon"></i></i> <p></p><?php echo trans('referrals') ?></a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php if(isset($page_title) && $page_title == "Payouts"){echo "active";} ?>" href="<?php echo base_url('admin/referral/payouts ') ?>"><i class="fas fa-credit-card nav-icon"></i> <p><?php echo trans('payouts') ?></p></a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item d-none">
            <a href="<?php echo base_url('admin/mentee') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Mentee"){echo "active";} ?>">
              <i class="nav-icon bi bi-people"></i> <p><?php echo trans('mentee') ?></p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a href="<?php echo base_url('admin/reports') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Reports"){echo "active";} ?>">
              <i class="bi bi-bar-chart-steps nav-icon"></i> <p><?php echo trans('report') ?></p>
            </a>
          </li>
          
        <li class="nav-item">
            <a href="https://kb.pairedbybpu.uk/" class="nav-link <?php if(isset($page_title) && $page_title == "Education" || $page == "Education"){echo "active";} ?>">
              <i class="bi bi-book nav-icon"></i>
              <p>FAQs</p>
            </a>
        </li>

        <?php endif; ?>

        <?php if(is_mentee()): ?>
          <li class="nav-item">
            <a href="<?php echo base_url('admin/dashboard/mentee') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Mentee Dashboard"){echo "active";} ?>">
              <i class="nav-icon bi bi-house-door"></i> <p><?php echo trans('dashboard') ?></p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?php echo base_url('mentors') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Browse Mentors"){echo "active";} ?>">
              <i class="nav-icon bi bi-person"></i> <p>Browse Mentors</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?php echo base_url('admin/settings/mentee_profile') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Mentee Profile"){echo "active";} ?>">
              <i class="nav-icon bi bi-person"></i> <p>Edit <?php echo trans('profile') ?></p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url('admin/sessions/booking?search=upcoming') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Session Booking"){echo "active";} ?>">
              <i class="nav-icon bi bi-calendar-check"></i> <p><?php echo trans('booking') ?></p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?php echo base_url('admin/message') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Message"){echo "active";} ?>">
              <i class="nav-icon bi bi-chat"></i> <p><?php echo trans('message') ?></p>
            </a>
          </li>

<!--
            <li class="nav-item">
            <a href="<?php echo base_url('admin/favourite') ?>" class="nav-link <?php if(isset($page_title) && $page_title == "Favourite"){echo "active";} ?>">
              <i class="nav-icon bi bi-heart"></i> <p><?php echo trans('favourite') ?></p>
            </a>
          </li>
-->          
          
        <?php endif; ?>

        <!-- <?php //endif; ?> -->
        <!-- user menu end -->


        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('auth/logout') ?>">
              <?php if (is_admin()): ?>
                <i class="nav-icon lni lni-exit"></i> <p><?php echo trans('logout') ?>
              <?php else: ?>
                <i class="nav-icon bi bi-box-arrow-left"></i> <p><?php echo trans('logout') ?>
              <?php endif ?>
            </p>
            </a>
        </li>


        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>