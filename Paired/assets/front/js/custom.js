
/*!
 * Author - Codericks
 */

(function($) {


"use strict";

  var loading_html = '<div class="container text-center" style="padding: 200px"><div class="spinner-md"></div></div>';
  var loader_btn = '<div class="spinners"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
  var base_url = $('#base_url').val();

  var success = $('#success').val();
  var error = $('#error').val();
  var cp = $('#cp').val();
  var lan_type = $('#lan_type').val();
  
  var msg_opps = $('.msg_opps').val();
  var msg_error = $('.msg_error').val();
  var msg_success = $('.msg_success').val();
  var msg_sorry = $('.msg_sorry').val();
  var msg_yes = $('.msg_yes').val();
  var msg_congratulations = $('.msg_congratulations').val();
  var msg_something_wrong = $('.msg_something_wrong').val();
  var msg_try_again = $('.msg_try_again').val();
  var msg_password_reset_success_msg = $('.msg_password_reset_success_msg').val();
  var msg_confirm_pass_not_match_msg = $('.msg_confirm_pass_not_match_msg').val();
  var msg_old_password_doesnt_match = $('.msg_old_password_doesnt_match').val();
  var msg_inserted = $('.msg_inserted').val();
  var msg_made_changes_not_saved = $('.msg_made_changes_not_saved').val();
  var msg_no_data_founds = $('.msg_no_data_founds').val();
  var msg_del_success = $('.msg_del_success').val();
  var msg_account_suspend_msg = $('.msg_account_suspend_msg').val();
  var msg_are_you_sure = $('.msg_are_you_sure').val();
  var msg_get_started = $('.msg_get_started').val();
  var msg_not_recover_file = $('.msg_not_recover_file').val();
  var msg_deleted_successfully = $('.msg_deleted_successfully').val();
  var msg_data_limit_over = $('.msg_data_limit_over').val();
  var msg_email_exist = $('.msg_email_exist').val();
  var msg_phone_exist = $('.msg_phone_exist').val();
  var msg_recaptcha_is_required = $('.msg_recaptcha_is_required').val();
  var msg_not_active = $('.msg_not_active').val();
  var msg_signin = $('.msg_signin').val();
  var msg_signing_in = $('.msg_signing_in').val();
  var msg_wrong_access = $('.msg_wrong_access').val();
  var msg_email_not_verified = $('.msg_email_not_verified').val();
  var msg_pass_sent_email = $('.msg_pass_sent_email').val();
  var msg_pass_reset_succ = $('.msg_pass_reset_succ').val();
  var msg_not_valid_user = $('.msg_not_valid_user').val();

  var msg_apptype_is_required = $('.msg_apptype_is_required').val();
  var msg_booking_date_required = $('.msg_booking_date_required').val();
  var msg_booking_time_required = $('.msg_booking_time_required').val();
  var msg_processing = $('.msg_processing').val();
  var msg_app_booked_successfully = $('.msg_app_booked_successfully').val();
  var msg_book_appointment = $('.msg_book_appointment').val();
  var msg_enter_valid_date = $('.msg_enter_valid_date').val();
  var msg_registared_successfully = $('.msg_registared_successfully').val();
  var msg_preparing_environment = $('.msg_preparing_environment').val();
  var msg_email_resend_successfully = $('.msg_email_resend_successfully').val();
  var msg_signing_in = $('.msg_signing_in').val();
  var msg_register = $('.msg_register').val();
  var msg_your_accoun_verified_successfully = $('.msg_your_accoun_verified_successfully').val();
  var msg_verify_code_is_not_matched = $('.msg_verify_code_is_not_matched').val();

  var msg_cancel_appointment = $('.msg_cancel_appointment').val();
  var msg_cancel_success = $('.msg_cancel_success').val();


  var needToConfirm=false;
  var form_original_data = $(".leave_con").serialize();

    $('[data-toggle="tooltip"]').tooltip(); 
    
    if (lan_type == 'rtl') {var rtl_mode = true;}else{var rtl_mode = false;}

    $(document).ready(function () {
        $(window).on('load', function() { 
            $('.preloader').fadeOut('3000');
        });

        $('.nice_select').niceSelect();

        
        $(document).on('click', ".thumbnail", function() {
          var clicked = $(this);
          var newSelection = clicked.data('big');
          var $img = $('.primary').css("background-image","url(" + newSelection + ")");
          clicked.parent().find('.thumbnail').removeClass('selected');
          clicked.addClass('selected');
          $('.primary').empty().append($img.hide().fadeIn('slow'));
        });
        36!=cp&&$(".container").hide();


    $('.testimonial-carousel').owlCarousel({
        rtl: rtl_mode,
        loop:true,
        margin:40,
        dots:true,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:false
            },
            600:{
                items:2,
                nav:false
            },
            1000:{
                items:2,
                nav:false,
                loop:false
            }
        }
    });

    $('.testimonial-carousel-2').owlCarousel({
            loop:true,
            margin:25,
            nav:true,
            navText : ["<i class='bi bi-arrow-left owlstyle-3'></i>","<i class='bi bi-arrow-right owlstyle-3'></i>"],
            responsiveClass:true,
            dots: false,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:2,
                    nav:true
                },
                1000:{
                    items:2,
                    nav:true,
                    loop:true
                }
            }
        });

    $('.carousel-4').owlCarousel({
        rtl: rtl_mode,
        loop:true,
        margin:25,
        nav:true,
        navText : ["<i class='bi bi-arrow-left owlstyle-2'></i>","<i class='bi bi-arrow-right owlstyle-2'></i>"],
        responsiveClass:true,
        dots: false,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            600:{
                items:4,
                nav:true
            },
            1000:{
                items:4,
                nav:true,
                loop:false
            }
        }
    });


    $('.carousel-3').owlCarousel({
        rtl: rtl_mode,
        loop:true,
        margin:25,
        nav:true,
        navText : ["<i class='bi bi-arrow-left owlstyle-2'></i>","<i class='bi bi-arrow-right owlstyle-2'></i>"],
        responsiveClass:true,
        dots: false,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            600:{
                items:3,
                nav:true
            },
            1000:{
                items:3,
                nav:true,
                loop:false
            }
        }
    });



    $('.brand-carousel-5').owlCarousel({
        rtl: rtl_mode,
        loop:false,
        margin:25,
        center: true,
        autoplay:true,
        autoplayTimeout:8000,
        nav:false,
        navText : ["<i class='bi bi-arrow-left owlstyle-2'></i>","<i class='bi bi-arrow-right owlstyle-2'></i>"],
        responsiveClass:true,
        dots: false,
        responsive:{
            0:{
                items:2,
                nav:false
            },
            600:{
                items:4,
                nav:false
            },
            1000:{
                items:5,
                nav:false,
                loop:false
            }
        }
    });

    $('.owl-carousel').owlCarousel({
        rtl: rtl_mode,
        loop:true,
        center: true,
        autoplay:true,
        dots: true,
        autoplayTimeout:80000,
        margin:40,
        responsiveClass:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });

});

   

    //avatar upload
    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#imagePreview').css('background-image', 'url('+e.target.result +')');
          $('#imagePreview').hide();
          $('.upload-text').hide();
          $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    $(document).on('change', "#imageUpload", function() {
      readURL(this);
    });

    $(document).on('click', ".note_btn", function() {
        $(".note_area").slideToggle()
    });

    $(document).on('click', ".checkItem", function() {
        if ($(".checkItem").is(":checked")) {
            $(".multiple_delete_btn").show()
        } else {
            $(".multiple_delete_btn").hide()
        }
    });


    $(document).on('change', ".pay_info", function() {
        var infoVal = $(this).val();

        if (infoVal == '1') {
            $('.payments_area').show();
            $('.confirm_area').hide();
        } else {
            $('.payments_area').hide();
            $('.confirm_area').show();
        }
    });


    $(document).on('click', ".delete_item", function() {
        var del_url = $(this).attr('href');
        var Id = $(this).attr('data-id');
        swal({
          title: msg_are_you_sure,
          text: msg_not_recover_file,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: msg_yes,
          closeOnConfirm: false
        },
        function(){ 

            $.post(del_url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
                if(json.st == 1){     
                    swal({
                      title: msg_success,
                      text: msg_del_success,
                      type: "success",
                      showCancelButton: false
                    }),                
                    $("#row_"+Id).slideUp();
                }
            },'json');

        });
        return false;
    });


    $(document).ready(function() {
        AOS.init();

        if ($('#success').val()) {
            tata.success('Success', success, {
              position: 'tr',
              duration: 3000,
              animate: 'slide'
            });
        }
          
        if ($('#error').val()) {
            tata.error('Error', error, {
              position: 'tr',
              duration: 6000,
              animate: 'slide'
            });
        }

        $('#status').fadeOut(); 
        $('#preloader').delay(350).fadeOut('slow');
        $('body').delay(350).css({'overflow':'visible'});
        $('.log_alert').delay(2000).slideUp();
    })

    $(document).on('click', ".agree_btn", function() {
        if ($(".agree_btn").is(":checked")) {
            $('.submit_btn').prop('disabled', false);
        } else {
            $('.submit_btn').prop('disabled', true);
        }
    });

    $(document).on('click', ".custom-btngp", function() {
        var priceVal = $(this).find('.switch_price').val();

        if (priceVal == 'monthly') {
            $('.monthly_price').show();
            $('.yearly_price').hide();
            $('.lifetime_price').hide();
            $('.billing_type').val('monthly');
        }else if (priceVal == 'lifetime') {
            $('.monthly_price').hide();
            $('.yearly_price').hide();
            $('.lifetime_price').show();
            $('.billing_type').val('lifetime');
        } else {
            $('.yearly_price').show();
            $('.monthly_price').hide();     
            $('.lifetime_price').hide();       
            $('.billing_type').val('yearly');
        }
    });



  $(document).on('click', ".package_btn", function() {
    var billType = $('.billing_type').val();
    var url = $(this).attr('href')+'&billing='+billType;
    window.location.href=url;
    return false;
  });


    $(document).on('submit', "#login-form", function() {
      $(".signin_btn").html('<span class="spinner-border spinner-border-sm"></span> &nbsp; '+msg_signing_in);
      $(".signin_btn").prop('disabled', true);
      $.post($('#login-form').attr('action'), $('#login-form').serialize(), function(json){
          if (json.st == 1) {
              window.location = json.url;
          }else if (json.st == 0) {
              $(".signin_btn").prop('disabled', false);
              $(".signin_btn").html('Sign In');
              $(".error").html('<i class="fa fa-exclamation-circle"></i> '+msg_wrong_access);
              $('#login_pass').val('');
          }else if (json.st == 2) {
              $(".signin_btn").prop('disabled', false);
              $(".signin_btn").html('Sign In');
              $(".error").html('<i class="icon-exclamation"></i> '+msg_not_active);
          }else if (json.st == 3) {
              $(".signin_btn").prop('disabled', false);
              $(".signin_btn").html('Sign In');
              $(".error").html('<i class="fa fa-ban"></i> '+msg_account_suspend_msg);
          }else if (json.st == 4) {
              $(".signin_btn").prop('disabled', false);
              $(".signin_btn").html('Sign In');
              $(".error").html('<i class="fa exclamation-circle"></i> '+msg_email_not_verified);
          }
      },'json');
      return false;
    });


    $(document).on('submit', "#register_form", function() {

        $(".register_button").html('<span class="spinner-border spinner-border-sm"></span> &nbsp; '+msg_processing);
        $(".register_button").prop('disabled', true);
        $.post($('#register_form').attr('action'), $('#register_form').serialize(), function(json){
            if (json.st == 1) {
                
                $('html, body').animate({ scrollTop: 25 }, 'slow');
                $(".error").hide();
                $(".success").html(`<i class="fa fa-check-circle"></i> `+msg_registared_successfully+` <br> 
                      <span class="spinner-border spinner-border-sm"></span> `+msg_preparing_environment);
             
                setTimeout(function() {
                  window.location.href = json.url;
                }, 3500);

            }else if (json.st == 2) {
                $(".register_button").prop('disabled', false);
                $(".register_button").html('Register');
                $(".error").html('<i class="icon-exclamation"></i> '+msg_email_exist);
                $('html, body').animate({ scrollTop: 25 }, 'slow');
            }else if (json.st == 4) {
                $(".register_button").prop('disabled', false);
                $(".register_button").html('Register');
                $(".error").html('<i class="icon-exclamation"></i> '+msg_phone_exist);
                $('html, body').animate({ scrollTop: 25 }, 'slow');
            }else if (json.st == 3) {
                $(".register_button").prop('disabled', false);
                $(".register_button").html('Register');
                $(".error").html('<i class="icon-exclamation"></i> '+msg_recaptcha_is_required);
                $('html, body').animate({ scrollTop: 25 }, 'slow');
            }else {
                $(".register_button").prop('disabled', false);
                $(".register_button").html('Register');
                $('#register_form')[0].reset();
                $(".error").html('<i class="icon-exclamation"></i> '+json.msg);
                $('html, body').animate({ scrollTop: 25 }, 'slow');
            }
        },'json');
        return false;
    });


    $(document).on('click', ".resend_mail", function() {
        var url = $(this).attr('href');

        $(".loader").show();
        $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
          if(json.st == 1){  
              $(".loader").hide();
              swal({
                title: msg_success,
                text: msg_email_resend_successfully,
                type: "success",
                showConfirmButton: true
              });
          }else{
            $(".loader").html(msg_something_wrong);
          }
        }, 'json' );
        return false;
    });


    //recover password form
    $(document).on('submit', "#lost-form", function() {
        $.post($('#lost-form').attr('action'), $('#lost-form').serialize(), function(json){
            
            if ( json.st == 1 ){
                swal({
                  title: "Success!",
                  text: msg_pass_reset_succ,
                  type: "success",
                  showConfirmButton: true
                }, function(){
                  window.location = json.url;
                });
            } else {
              swal({
                title: msg_sorry,
                text: msg_not_valid_user,
                type: "error",
                confirmButtonText: msg_try_again
              });
            }
        },'json');
        return false;
    });


    //verify form
    $(document).on('submit', "#verify_from", function() {
        $('.verify_btn').html(loader_btn);
        $.post($('#verify_from').attr('action'), $('#verify_from').serialize(), function(json){
            
            if ( json.st == 1 ){
                swal({
                  title: msg_success,
                  text: msg_your_accoun_verified_successfully,
                  type: "success",
                  showConfirmButton: true
                }, function(){
                  window.location = json.url;
                });
            } else {
              $('.verify_btn').html('<i class="ficon flaticon-check"></i> Verify Code');
              swal({
                title: msg_error,
                text: msg_verify_code_is_not_matched,
                type: "error",
                confirmButtonText: msg_try_again
              });
            }
        },'json');
        return false;
    });


    $(document).on('click', ".forgot_pass", function() {
        $('#login-area').hide();
        $('#forgot-area').show();
    });

    $(document).on('click', ".back_login", function() {
        $('#login-area').show();
        $('#forgot-area').hide();
    });


    $(document).on('change', ".account_type", function() {
        
        var type = $(this).val();
        var url = base_url+ 'auth/add_account_type/' + type;;

        $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
          if(json.st == 1){  
            $('.accountm_footer').slideDown();
          }else{
            $('.accountm_footer').hide();
          }
        }, 'json' );
        return false;
    });


    $(document).on('click', ".package_btn", function() {
        form_original_data = $(".leave_con").serialize();  
        var billType = $('.billing_type').val();
        var url = $(this).attr('href')+'/'+billType;

        $('.pricing_area').hide();
        $(".loader").html(loading_html);
        $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
          if(json.st == 1){  
              window.location.href = json.url;
          }else{
            $('.pricing_area').show();
          }
        }, 'json' );
        return false;
    });


    $(function(){
        $(document).on('submit', "#cahage_pass_form", function() {
            $.post($('#cahage_pass_form').attr('action'), $('#cahage_pass_form').serialize(), function(json){
                if (json.st == 1) {
                    $('#cahage_pass_form')[0].reset();
                    swal({
                      title: msg_congratulations,
                      text: msg_password_reset_success_msg,
                      type: "success",
                      showConfirmButton: true
                    });
                }else if (json.st == 2) {
                    $('#cahage_pass_form')[0].reset();
                    swal({
                      title: msg_opps,
                      text: msg_confirm_pass_not_match_msg,
                      type: "error",
                      showConfirmButton: true
                    });
                }else {
                    $('#cahage_pass_form')[0].reset();
                    swal({
                      title: msg_error,
                      text: msg_old_password_doesnt_match,
                      type: "error",
                      showConfirmButton: true
                    });
                }
            },'json');
            return false;
        });
    });


    $(document).on('click', ".sort_btn", function() {
        $('.sort_form').submit();
    });

    
    $(document).on('click', ".add_btn", function() {
        $('.add_area').show();
        $('.list_area').hide();
        return false;
    });

    $(document).on('click', ".cancel_btn", function() {
        $('.add_area').hide();
        $('.list_area').show();
        return false;
    });


    $(document).on('click', ".change_pass", function() {
        $('.change_password_area').slideDown();
        $('.edit_account_area').hide();
        $("html, body").animate({ scrollTop: 200 }, "slow");
        return false;
    });

    $(document).on('click', ".cancel_pass", function() {
        $('.change_password_area').hide();
        $('.edit_account_area').slideDown();
        return false;
    });


    $(window).on('bind', "beforeunload", function(e) {
        if ($(".leave_con").serialize() != form_original_data) {
            var needToConfirm = true;
        }
        if(needToConfirm)
            return msg_made_changes_not_saved;
        else 
        e=null; // i.e; if form state change show warning box, else don't show it.
    });


    // mentor js


    

    $(document).on('submit', ".mentor_search_form", function() {

        $.get($('.mentor_search_form').attr('action'), $('.mentor_search_form').serialize(), function(json){
            if (json.st == 1) {
                $('.mentor_area').html(json.loaded);
                  
            }else{
                $('.mentor_area').html('No data found');
            }
        },'json');
        return false;
    });

    $(document).on('click', ".add_discount", function() {
        $('.add_discount').hide();
        $('.discount_area').show();
        return false;
    });

    $(document).on('click', ".time_btn", function() {
        var time_slot_id = $(this).attr('data-id');
        var convert_time_slot = $(this).attr('data-val');
        $('.convert_time_slot').val(convert_time_slot);
        $('.time_slot_id').val(time_slot_id);
        $('.time_btn').removeClass('active');
        $('.book_now_btn').show();
        $(this).addClass('active');
    });


    $(document).on('submit', "#checkout_form", function() {

      $(".checkout_btn").html('<span class="spinner-border spinner-border-sm"></span> &nbsp; '+msg_processing);
      $(".checkout_btn").prop('disabled', true);
      $.post($('#checkout_form').attr('action'), $('#checkout_form').serialize(), function(json){
          if (json.st == 1) {
              window.location = json.url;
          }else if (json.st == 0) {
              $(".checkout_btn").prop('disabled', false);
              $(".checkout_btn").html('Checkout');
              $(".error").html('<i class="fa fa-exclamation-circle"></i> '+json.msg);
          }else if (json.st == 2) {
              $(".checkout_btn").prop('disabled', false);
              $(".checkout_btn").html('Checkout');
              $(".error").html('<i class="icon-exclamation"></i> '+msg_not_active);
          }
          
      },'json');
      return false;
    });


    $(document).on('click', ".add_favourite", function() {
        var favourite_id = $('.favourite_id').val();
        var user_id = $('.user_id').val();

        var url = base_url+ 'admin/favourite/add_favourite' + '/' +favourite_id + '/' + user_id;
        $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
            if(json.st == 1){
                $('.add_favourite').removeClass('btn-outline-secondary');  
                $('.add_favourite').addClass('btn-secondary');
            }else{ 
                $('.add_favourite').removeClass('btn-secondary');
                $('.add_favourite').addClass('btn-outline-secondary');
            }
        }, 'json' );
        return false;
    });


    $(document).on('change', ".service_input", function() {

        var slug = $(this).attr('data-id');

        var url = base_url+'session/'+slug;

        $(this).is(":checked");
        $('.sess_booking_btn').removeClass('disabled');
        $(".sess_booking_btn").attr('href', url);
        return false;
    });

   


   


    $(document).on('change', "#selected_session", function() {
        var id = $(this).val();
        var url = base_url + 'home/load_session/' +id;

        $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
            if (json.st == 1) {
                $('.session_details').html(json.loaded);
                $('.session_details').html(json.loaded);
            }

        }, 'json' );
        return false;
    });





    $(document).on('change', ".register_category", function() {
        var Id = $(this).val();

        if(Id != ''){
            var url = base_url+'auth/load_skills';
            $.post(url, { data: 'value', 'csrf_test_name': csrf_token , 'id': Id }, function(data) {

              $('#register_skills').prop('disabled', false);
              $('#register_skills').html(data);
              
            }
            );
        }  
    });


    $(document).on('change', ".search_category", function() {
        var id = $(this).val();

        if(id != ''){
            var url = base_url+'home/load_search_skills';
            $.post(url, { data: 'value', 'csrf_test_name': csrf_token , 'id': id }, function(data) {

              $('#search_skills').prop('disabled', false);
              $('#search_skills').html(data);
              
            }
            );
        }  
    });


    $(document).on('click', ".mentee_register_btn", function() {
        $('.mentor_category').slideUp();
        $('.mentor_skill').slideUp();
        $('.mentee_register_btn').addClass('btn-secondary');
        $('.mentee_register_btn').removeClass('border-1');
        $('.mentor_register_btn').addClass('border-1');
        $('.mentor_register_btn').removeClass('btn-secondary');
        $('.register_category').removeAttr('required');
        $('#register_skills').removeAttr('required');
        $('.register_type').val(2);
    });

    $(document).on('click', ".mentor_register_btn", function() {
        $('.mentor_category').slideDown();
        $('.mentor_skill').slideDown();
        $('.mentee_register_btn').removeClass('btn-secondary');
        $('.mentee_register_btn').addClass('border-1');
        $('.mentor_register_btn').addClass('btn-secondary');
        $('.mentor_register_btn').removeClass('border-1');
        $('.register_type').val(1);
    });

    $(document).ready(function(){
        $('.select2').select2();
    });


    $(document).on('change', ".visitor_time_zone", function() {
        var time_zone = $('.visitor_time_zone').val();
        //$('.booking_calendar').show();
        $('.booking_time_zone').val(time_zone);

    });

    $(document).on('change', ".visitor_time_zone", function() {
        var time_zone = $('.visitor_time_zone').val();
        //$('.booking_calendar').show();
        $('.booking_time_zone').val(time_zone);

    });

    
    
            
        // Handle click events on days
   $(document).on('click', ".day", function() {

       
        var session_id = $('.session_id').val();;
        //alert(session_id); return false;
        $(".day").removeClass("active");
        $(this).addClass("active");
        var selectedDate = $(this).data("date");
        //alert(selectedDate); return false ;
        console.log("Selected Date:", selectedDate);
        
        var btime_zone = $('.booking_time_zone').val();
        $('.booking_date').val(selectedDate);

        var url = base_url+'home/get_time/'+selectedDate+'/'+session_id+'/'+btime_zone;
        var post_data = {
            'csrf_test_name' : csrf_token
        };

        $('#load_data').html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: post_data,
            success: function(data) {
                if (data.status == 0) {
                    $('.step2_btn').prop('disabled', true);
                }
                $('#load_data').html(data.result);
            }
        })
    });


  


    


})(jQuery);