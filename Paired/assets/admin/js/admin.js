/*!
 * Author - Codericks
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
  typeof define === 'function' && define.amd ? define(['exports'], factory) :
  (global = global || self, factory(global.adminlte = {}));
}(this, (function (exports) { 

  'use strict';

    var loading_html = '<div class="container text-center" style="padding: 200px"><div class="spinner-md"></div></div>';
    var base_url = $('#base_url').val();
    var filter = $('#filter').val();
    var success = $('#success').val();
    var error = $('#error').val();
    var lc = $('#lc').val();

    var msg_opps = $('.msg_opps').val();
    var msg_error = $('.msg_error').val();
    var msg_success = $('.msg_success').val();
    var msg_sorry = $('.msg_sorry').val();
    var msg_yes = $('.msg_yes').val();
    var msg_cancel = $('.msg_cancel').val();
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
    var msg_sms_notify = $('.msg_sms_notify').val();
    var msg_send_success = $('.msg_send_success').val();
    var msg_start_time = $('.msg_start_time').val();
    var msg_end_time = $('.msg_end_time').val();
  


    $(document).ready(function(){

      $('.select2').select2();
      
      /* copy code to clipboard */
      function mhtmlspecialchars_decode (string, quoteStyle) { // eslint-disable-line camelcase
        let optTemp = 0
        let i = 0
        let noquotes = false
        if (typeof quoteStyle === 'undefined') {
          quoteStyle = 2
        }
        string = string.toString()
          .replace(/&lt;/g, '<')
          .replace(/&gt;/g, '>')
        const OPTS = {
          ENT_NOQUOTES: 0,
          ENT_HTML_QUOTE_SINGLE: 1,
          ENT_HTML_QUOTE_DOUBLE: 2,
          ENT_COMPAT: 2,
          ENT_QUOTES: 3,
          ENT_IGNORE: 4
        }
        if (quoteStyle === 0) {
          noquotes = true
        }
        if (typeof quoteStyle !== 'number') {
          // Allow for a single string or an array of string flags
          quoteStyle = [].concat(quoteStyle)
          for (i = 0; i < quoteStyle.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quoteStyle[i]] === 0) {
              noquotes = true
            } else if (OPTS[quoteStyle[i]]) {
              optTemp = optTemp | OPTS[quoteStyle[i]]
            }
          }
          quoteStyle = optTemp
        }
        if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
          // PHP doesn't currently escape if more than one 0, but it should:
          string = string.replace(/&#0*39;/g, "'")
          // This would also be useful here, but not a part of PHP:
          // string = string.replace(/&apos;|&#x0*27;/g, "'");
        }
        if (!noquotes) {
          string = string.replace(/&quot;/g, '"')
        }
        // Put this in last place to avoid escape being double-decoded
        string = string.replace(/&amp;/g, '&')
        return string
      }

      $(document).on('click', '.btn-clipboard', function(){
        const codeId = $(this).attr('data-id');
        if(codeId !== undefined && codeId.length > 0){
          const codeContainer = document.getElementById(codeId);
          if(codeContainer !==undefined){

            tata.success('Copied Successfully', success, {
              position: 'tr',
              duration: 3000,
              animate: 'slide'
            });

            
            /* Select the text field ta */
            // codeContainer.select();
            // codeContainer.setSelectionRange(0, 99999); /* For mobile devices */
            /* Copy the text inside the text field */
            navigator.clipboard.writeText(mhtmlspecialchars_decode(codeContainer.innerHTML));
            $(this).attr('data-title',$(this).attr('title'));
            $(this).attr('title',$(this).attr('data-title2'));
            $(this).removeAttr('data-title2');
            $(this).html('<i class="far fa-clone"></i> '+$(this).attr('title'));
            window.setTimeout(()=>{
              $(this).attr('data-title2',$(this).attr('title'));
              $(this).attr('title',$(this).attr('data-title'));
              $(this).removeAttr('data-title');
              $(this).html('<i class="far fa-clone"></i> '+$(this).attr('title'));
            },1000)

          }
        }
      })

      $(document).on('click', '.btn-clipboard2', function(){
        $('.btn-clipboard').click();
      })
      /* end of copy code to clipboard */


      
      // Initialize the clipboard instance
      var clipboard = new ClipboardJS('.copy_button', {
        target: function() {
          return document.querySelector('.copy_url');
        }
      });
      clipboard.on('success', function(e) {
          $("#successMsg").html('Copied text to clipboard!').delay(3000).slideUp('slow');

        e.clearSelection();
      });


      // mentor js
      $('#is_present').on('change', function () {
        if ($(this).prop('checked')) {
          $('.end_date').hide();
        }else{
          $('.end_date').show();
        }
        return false;
      });

      $('#allow_session').on('change', function () {
        if ($(this).prop('checked')) {
          $('.session_type').hide();
        }else{
          $('.session_type').show();
        }
        return false;
      });

      $('.enable_group_booking').on('change', function () {
        if ($(this).prop('checked')) {
          $('.group_booking_slot').show();
          $('.default_hour_option').prop('disabled',true);
        }else{
          $('.group_booking_slot').hide();
          $('.default_hour_option').prop('disabled',false);
        }
        return false;
      });

      $('.is_reccuring').on('change', function () {

        var value = $('.is_reccuring').val();
        if (value == 2) {
          $('.recurring_session').show();
        }else{
          $('.recurring_session').hide();
        }
        return false;
      });


      $('.is_default').on('change', function () {

        var value = $('.is_default').val();
        if (value == 1) {
          $('.default_hour_area').show();
          $('.custom_hour_area').hide();
        }
        if(value == 2) {
          $('.custom_hour_area').show();
          $('.default_hour_area').hide();
        }
        return false;
      });


      // Previous Step
        $('.next-step').click(function() {

          var country = $('.country').val();
          var doc_type = $('.doc_type').val();

          if(doc_type == 'nid' || doc_type == 'dlicense'){
            $('.image3-require').removeClass('image3-require');
            var image = $('#image').val();
            var image2 = $('#image2').val();
            var image3 = 'image3';
          }

          if(doc_type == 'passport'){
            $('.image-require').removeClass('image-require');
            $('.image2-require').removeClass('image2-require');
            var image3 = $('#image3').val();
            var image = 'image';
            var image2 = 'image2';
          }

          var doc_id_number = $('.doc_id_number').val();
          var image4 = $('#image4').val();

          if(country && doc_type && doc_id_number && image && image2 && image3 && image4){
            $('.step1').hide();
            $('.step2').show();
          }else{
            if(!country){
              $('.country_require').addClass('error-line');
            }

            if(!doc_type){
              $('.doc_type').addClass('error-line');
            }
            if(!doc_id_number){
              $('.doc_id_number').addClass('error-line');
            }
            if(!image){
              $('.image-require').addClass('text-danger');
            }
            if(!image2){
              $('.image2-require').addClass('text-danger');
            }
            if(!image3){
              $('.image3-require').addClass('text-danger');
            }
            if(!image4){
              $('.image4-require').addClass('text-danger');
            }
          }
            
            
        });

         $('.prev-step').click(function() {
            $('.step1').show();
            $('.step2').hide();
            
        });

        

        $('.doc_type').on('change', function() {
          var type = $('.doc_type').val();
          //alert(type); return false;
          if (type == 'nid') {
            $('#load_document_type_number').text("National ID");
            $('#load_document_type_front').text("National ID");
            $('#load_document_type_back').text("National ID");
            $('.document_number').show();
            $('.upload_area_doc_front').show();
            $('.upload_area_doc_back').show();
            $('.upload_area_passport').hide();
            $('.upload_area_selfiee').show();
            $('.file_requirements').show();
            $('#load_document_type_selfie').text("National ID");
          }

          if (type == 'dlicense') {
            $('#load_document_type_number').text("Driving License");
            $('#load_document_type_front').text("Driving License");
            $('#load_document_type_back').text("Driving License");
            $('.document_number').show();
            $('.upload_area_doc_front').show();
            $('.upload_area_doc_back').show();
            $('.upload_area_passport').hide();
            $('.upload_area_selfiee').show();
            $('.file_requirements').show();
            $('#load_document_type_selfie').text("Driving License");
          }

          if (type == 'passport') {
            $('#load_document_type_number').text("Passport");
            $('.document_number').show();
            $('.upload_area_doc_front').hide();
            $('.upload_area_doc_back').hide();
            $('.upload_area_passport').show();
            $('.upload_area_selfiee').show();
            $('.file_requirements').show();
            $('#load_document_type_selfie').text("Passport");
          }

        });



        $('.doc_id_number').click(function(){
          var inputVal = $(this).val();
          if(inputVal.trim() !== '') {
            $(this).removeClass('error-line');
          }
        });

        $('.doc_type').on('change', function() {
          var inputVal = $(this).val();
          if(inputVal.trim() !== '') {
            $(this).removeClass('error-line');
          }
        });


        $('.country').on('change', function() {
          var inputVal = $(this).val();
          if(inputVal.trim() !== '') {
            $('.country_require').removeClass('error-line');
          }
        });

        $('.agree_btn').change(function(){
          if(this.checked) {
            $('.kyc_submit_btn').prop('disabled', false);
          } else {
            $('.kyc_submit_btn').prop('disabled', true);
          }
        });


        $('#image').on('change', function() {
          $('.image-require').removeClass('text-danger');
            
          var input = this;
          var url = $(this).val();
          var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
          if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#imagePreview').attr('src', e.target.result);
              $('#imagePreviewContainer').show();
            }
            reader.readAsDataURL(input.files[0]);
          } else {
            alert("Please select a valid image file.");
            $(this).val('');
            $('#imagePreviewContainer').hide();
          }
        });

        $('#image2').on('change', function() {
          $('.image2-require').removeClass('text-danger');
          var input = this;
          var url = $(this).val();
          var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
          if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#imagePreview2').attr('src', e.target.result);
              $('#imagePreviewContainer2').show();
            }
            reader.readAsDataURL(input.files[0]);
          } else {
            alert("Please select a valid image file.");
            $(this).val('');
            $('#imagePreviewContainer2').hide();
          }
        });

        $('#image3').on('change', function() {
          $('.image3-require').removeClass('text-danger');
          var input = this;
          var url = $(this).val();
          var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
          if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#imagePreview3').attr('src', e.target.result);
              $('#imagePreviewContainer3').show();
            }
            reader.readAsDataURL(input.files[0]);
          } else {
            alert("Please select a valid image file.");
            $(this).val('');
            $('#imagePreviewContainer3').hide();
          }
        });

        $('#image4').on('change', function() {
          $('.image4-require').removeClass('text-danger');
          var input = this;
          var url = $(this).val();
          var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
          if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#imagePreview4').attr('src', e.target.result);
              $('#imagePreviewContainer4').show();
            }
            reader.readAsDataURL(input.files[0]);
          } else {
            alert("Please select a valid image file.");
            $(this).val('');
            $('#imagePreviewContainer4').hide();
          }
        });




      $(document).on('click', ".check_zoom_api", function() {
          var url = base_url+'admin/settings/test_zoom_connection';
          $.post(url,{ data: 'value', 'csrf_test_name': csrf_token },function(json){
              if (json.st == 1) {
                  $('.conn_info').html(json.msg).show();
              }else{
                  $('.conn_error').html(json.msg).show();
              }
          },'json');
          return false;
      });


      $(document).on('submit', ".message_btn", function() {
        var message = $('.message_value').val();
        //alert(message);return false;
        if(message.length === 0){
          $('.message_value').addClass('red-line');
          return false;
        }else{
          $('.message_value').removeClass('red-line');
        }

        $('.btnchat').prop('disabled',true);
        $.post($('.message_btn').attr('action'), $('.message_btn').serialize(), function(json){
            if (json.st == 1) {
              $('.message_btn')[0].reset();
              $('.load_message_content').append(json.append);
              $('.btnchat').prop('disabled',false);
              //window.location.reload();
                
            }
        },'json');
        return false;
      });



      $(document).ready(function () {


      

        $(document).on('change', ".show_method", function() {
          $('.method_details').slideDown();
        });

        $(document).on("focusin",".timepicker", function () {
          var intervals = $('.user_interval').val();
          $('input.timepicker').timepicker({interval: intervals});
        });



        $(document).on('click', ".contact_details", function() {
          var url = $(this).attr('href');
          var id = $(this).attr('data-id');
          var loadm_url = base_url+'admin/message/details/'+id;
          $('.msg_load_url').val(loadm_url);

          $(".msg_with").val(id);
          $(".message_input").show();
          $(".without_message").hide();
          $("#un_"+id).hide();
          $('.contact_details').removeClass('active');
          $(this).addClass('active');

          $(".load_message_content").html('<span class="spinner-border spinner-border-sm"></span>');

          $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
            if(json.st == 1){
              $(".load_message_content").html(json.data_load);
              //autoRefresh();
            }
          }, 'json' );
          return false;
        });
        
        autoRefresh();

        function fetchData(){
          var url = $('.msg_load_url').val()
          var scrollTarget = $('.message-scroll');
          var pos = scrollTarget.scrollTop();
          $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
            if(json.st == 1){
              $(".load_message_content").html(json.data_load);
              $('.message-scroll').scrollTop(pos);
            }
          }, 'json' );
          return false;
        }

        function autoRefresh() {
          setInterval(function(){
            fetchData(); 
          }, 5000);
        }

      });
      


      $(document).on('keyup', ".search_contact", function() {
        var query = $(this).val();
        if (query != '') {
          $.ajax({
            url: base_url+'admin/message/search_contact/'+ query,
            method: 'GET',
            dataType:'json',
            data: { query: query },
            success: function(data) {
                $('.load_contact').html(data.loaded);
            }
          });
        }
      });


      $(document).on('click', function(){
        if( this.class != 'show_noti') {
          $(".show_noti").fadeOut();
        }
      });

      $(document).on('click', '.header_notifications', function(){
        var url = base_url+'admin/notifications/my';
        $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
          if(json.st == 1){
            $(".header_notifications_area").html(json.noti);
            $(".show_noti").show();
            $('.unseen-count').text('0');
          }
        }, 'json' );
          return false;
      });


      $(document).on('change', ".skill_category", function() {
          var Id = $(this).val();
          
            var url = base_url+'admin/settings/load_category_skill';
            $.post(url, { data: 'value', 'csrf_test_name': csrf_token , 'id': Id }, function(json) {
              if(json.st == 1){
                $('#category_skill').prop('disabled', false);
                $('#category_skill').html(json.loaded);
              }
              
            
            }, 'json' );
            return false;
        });


        $(document).on('click', ".apply_coupon", function() {
          
          var booking_id = $('.booking_id').val(); 
          var dcode = $('.coupon_code').val();

          var url = base_url + 'admin/sessions/check_coupon/' + dcode + '/' + booking_id ; 

          $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
              
              if (json.st == 1) {
                $(".coupon_area").show();
                $('.apply_coupon').prop('disabled', true);
                var total_price = json.total_price;
                var tatalCost = Number(total_price) - (total_price * (json.discount/100));
                var discount_amount = total_price - tatalCost;

                 
                 
                $(".discount_amount").html(discount_amount.toFixed(2));
                $(".final_amount").html(tatalCost.toFixed(2));
                $(".apply_msg_error").hide();
                $(".apply_msg_success").show().html(json.msg);
              }else{
                $(".apply_msg_success").hide();
                $(".apply_msg_error").show().html(json.msg);
              }
            
            }, 'json' );
            return false;
        });



        $('.book_session').on('change', function () {
            var session_id = $(this).val();

            var url = base_url + 'admin/sessions/get_session_calendar/' + session_id  ; 

          $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
              
             if (json.st == 1) {
              $(".booking_calender").html(json.loaded);
             }
            
            }, 'json' );
            return false;
        });



        $("body").on('click','.time_btn',function(){
          //$('.step2_btn').prop('disabled', false);
          $('.time_btn').removeClass('active');
          $('.book_now_btn').show();
          $(this).addClass('active');
        });





      $(document).on('click', '.default', function(){
        $('.default').not($(this)).removeClass('active');
        $(this).toggleClass('active').next().find('.sub-table-wrap').slideToggle();
        $(".toggle-row").not($(this).next()).find('.sub-table-wrap').slideUp('fast');
      });


      
      
        $('#summernote').summernote({
          tabsize: 2,
          height: 120,
          toolbar: [
            ['style', ['style']],
            ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'clear', ]],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['height', ['height']],
          ]
        });
      
      

      $('.summernote').summernote({
          tabsize: 2,
          height: 120,
          toolbar: [
            ['style', ['style']],
            ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'clear', ]],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['height', ['height']],
          ]
        });

      $('.colorpicker').colorpicker();


      $('.iconpicker').iconpicker({
          // customize the icon picker with the following options
          title: 'Icon Picker',
          selected: false,
          defaultValue: false,
          placement: "bottom",
          collision: "none",
          animation: true,
          hideOnSelect: true,
          showFooter: true,
          searchInFooter: false,
          mustAccept: false,
          selectedCustomClass: "bg-primary",
          fullClassFormatter: function (e) {
              return e;
          },
          input: "input,.iconpicker-input",
          inputSearch: false,
          container: false,
          component: ".input-group-addon,.iconpicker-component",
          templates: {
              popover: '<div class="iconpicker-popover popover" role="tooltip"><div class="arrow"></div>' + '<div class="popover-title"></div><div class="popover-content"></div></div>',
              footer: '<div class="popover-footer"></div>',
              buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' + ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
              search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
              iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
              iconpickerItem: '<a role="button" href="javascript:;" class="iconpicker-item"><i></i></a>'
          }
      });

      $(document).ready(function () {
        
        $(".datepickers").datepicker({
          dateFormat: 'yy-mm-dd'
        });

        $(".bs-datepicker").datepicker({
          format: 'yyyy-mm-dd'
        });

      });


      !function(window, document, $) {
      "use strict";
        $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
      }(window, document, jQuery);
    
      bsCustomFileInput.init();
      
      $(".datatable").DataTable();

      AOS.init();

      

      if ($('#success').val()) {
        tata.success(msg_success, success, {
          position: 'tr',
          duration: 3000,
          animate: 'slide'
        });
      }
      
      if ($('#error').val()) {
        tata.error(msg_error, error, {
          position: 'tr',
          duration: 6000,
          animate: 'slide'
        });
      }
  
      40!=lc&&$(".wrapper").hide();

    });

   
    // $(document).on('click', ".apply_coupon", function() {
    //     var code = $('.coupon_code').val();
    //     if (code == '') {
    //         $(".apply_msg_error").show().html('Invalid Code');
    //         return false;
    //     }

    //     var plan_id = $('.plan_id').val();
    //     var billing_type = $('.billing_type').val();
    //     var url = base_url+'admin/coupons/apply_coupon/'+code+'/'+plan_id+'/'+billing_type;
    //     var cur_path = window.location;

    //     $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
    //       if(json.st == 1){
    //         $('.apply_coupon').prop('disabled', true);
    //         window.location.href = cur_path+'?coupon='+code;
    //       }else{
    //         $(".apply_msg_success").hide();
    //         $(".apply_msg_error").show().html(json.msg);
    //       }
    //     }, 'json' );
    //     return false;
    // });

    // $(document).on('click', ".apply_coupon_old", function() {
    //     var code = $('.coupon_code').val();
    //     var url = base_url+'admin/coupons/apply_coupon/'+code;
    //     $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
    //       if(json.st == 1){
    //         $('.apply_coupon').prop('disabled', true);
    //         var total_price = json.total_price;
    //         var tatalCost = Number(total_price) - (total_price * (json.discount/100));
    //         var coupon_amount = total_price - tatalCost;

    //         $(".percent").html(' - '+json.discount+'%');
    //         $('.paypal_price').prop("value", tatalCost.toFixed(2)); 
    //         $(".coupon_amount").html(coupon_amount.toFixed(2));
    //         $(".final_amount").html(tatalCost.toFixed(2));

    //         $(".upgrade_area").show();
    //         $(".apply_msg_error").hide();
    //         $(".apply_msg_success").show().html(json.msg);
    //       }else{
    //         $(".apply_msg_success").hide();
    //         $(".apply_msg_error").show().html(json.msg);
    //       }
    //     }, 'json' );
    //     return false;
    // });


    $(document).ready(function(){
        $(".add_time_row").on('click', function() {
            var val = $(this).attr("data-id");
            $('.houritem_'+val).append(`
                <div class="row new_item_row_`+val+`">
                    <div class="col-sm-5 pr-0 mb-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            </div>
                            <input type="text" class="form-control timepicker" name="start_time_`+val+`[]" value="" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-sm-5 mb-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            </div>
                            <input type="text" class="form-control timepicker" name="end_time_`+val+`[]" value="" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-sm-2 mb-3">
                        <a href="javascript:void(0);" class="remove_time_row text-danger"><i class="bi bi-trash3"></i></a>
                    </div>
                </div>
            `);

            return false;
        });

        $(".main_item").on('click','.remove_time_row',function(){
            $(this).parent().parent().remove();
        });

        return false;
    });


    $(document).ready(function(){
        $(".add_verification_row").on('click', function() {
         
            $('.houritem').append(`
                <div class="row new_item_row">
                    <div class="col-sm-5 pr-0 mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="verification_items[]" value="" required autocomplete="off" placeholder="Cerfificate / document name">
                        </div>
                    </div>

                    <div class="col-sm-2 mb-2">
                        <a href="javascript:void(0);" class="remove_time_row text-danger"><i class="bi bi-trash3"></i></a>
                    </div>
                </div>
            `);
        });

        $(".main_item").on('click','.remove_time_row',function(){
            $(this).parent().parent().remove();
        });
    });


    $('.day_option').on('change', function () {
        var val = $(this).val();

        if ($(this).prop('checked')) {
          $('.hideable_'+val).slideDown();
          console.log(val);
        }else{
          $('.hideable_'+val).slideUp();
          console.log(val);
        }
        return false;
    });


    $(document).ready(function(){
        
        $(document).on('click', '.filter-action', function(){
          $(".filter_popup").toggleClass("showFilter");
        });

        $(document).on('click', '.linkd', function(){
          $(".coupon_area").show();
          $(".linkd_hide").hide();
        });

        $(document).on('click', '.fs-close', function(){
          $(".feature-steps").hide();
          return false;
        });

        $(document).on('click', '.apply-button', function(){
            $(".filter_popup").toggleClass("showFilter");
            $(".filter, .filter-remove, .fa-plus, .fa-filter").toggleClass("filter-hidden");
            $(".filter-dropdown-text").text("Add filter");
        });
        
        $(document).on('click', '.filter-remove', function(){
          $(".filter, .filter-remove, .fa-plus, .fa-filter").toggleClass("filter-hidden");
          $(".filter-dropdown-text").text("Filter dataset");
        });

    });

    $(document).on('click', ".package_btn", function() {
      var billType = $('.billing_type').val();
      var url = $(this).attr('href')+'/'+billType;
      window.location.href=url;
      return false;
    });

    $(document).on('click', ".toggle_check_btn", function() {
      var $box = $(this);
      if ($box.is(":checked")) {
        $box.prop("checked", true);
        $('.toggle_area').slideDown();
        $('.add_required').attr('required','required');
      } else {
        $box.prop("checked", false);
           $('.toggle_area').slideUp();
           $('.add_required').removeAttr('required');
      }
    });


    $(document).on('click', ".toggle_btn", function() {
        $('.toggle_area').toggle();
        return false;
    });

  
    $(document).on('click', ".add_btn", function() {
        $('.add_area').attr( "style", "display: block !important;");
        $('.list_area').attr( "style", "display: none !important;");
        return false;
    });

    $(document).on('click', ".cancel_btn", function() {
        $('.add_area').attr( "style", "display: none !important;");
        $('.list_area').attr( "style", "display: block !important;");
        return false;
    });


    $(document).on('click', ".add_btn2", function() {
        $('.add_area2').show();
        $('.list_area2').hide();
        return false;
    });

    $(document).on('click', ".cancel_btn2", function() {
        $('.add_area2').hide();
        $('.list_area2').show();
        return false;
    });


    $(document).on('change', ".sort", function() {
        $('.sort_form').submit();
    });

    $(document).on('click', ".custom-btngp", function() {
        var priceVal = $(this).find('.switch_price').val();
        $(".custom-btngp").removeClass('actives');
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


    $(document).on('click', ".delete_item", function() {

      var del_url = $(this).attr('href');
      var Id = $(this).attr('data-id');

          swal({
            title: msg_are_you_sure,
            text: msg_not_recover_file,
            type: "warning",
            showCancelButton: true,
            cancelButtonText: msg_cancel,
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




    //message insert





    $(document).on('submit', "#document_generate", function() {
        
        if( !$('.prompt').val() ) {
          $('.prompt').addClass('red-line');
          return false;
        }

        $(".generate_btn").html('<span class="spinner-border spinner-border-sm"></span> &nbsp; Generating');
        $(".generate_btn").prop('disabled', true);
        $(".empty_result_area").hide();
        $(".result_loading").show();

        $.post($('#document_generate').attr('action'), $('#document_generate').serialize(), function(json){
            if (json.st == 1) {
                $('#document_generate')[0].reset();
                $(".result_head").show();
                $(".generate_btn").prop('disabled', false);
                $(".generate_btn").html('Generate');
                $(".result_loading").hide();
                $("#load_result").html(json.loaded);

                if ($(".result-body").height() > 650) {
                  $(".result-body").addClass('cus_scroll');
                }
            }else {
                $(".result_loading").hide();
                $(".generate_btn").prop('disabled', false);
                $(".generate_btn").html('Generate');
                $("#load_result").html(json.loaded);
            }
        },'json');
        return false;
    });


    $(document).on('submit', "#image_generate", function() {
        
        if( !$('.prompt').val() ) {
          $('.prompt').addClass('red-line');
          return false;
        }

        $(".generate_btn").html('<span class="spinner-border spinner-border-sm"></span> &nbsp; Generating');
        $(".generate_btn").prop('disabled', true);
        $(".empty_result_area").hide();
        $(".result_loading").show();

        $.post($('#image_generate').attr('action'), $('#image_generate').serialize(), function(json){
            if (json.st == 1) {
                $('#image_generate')[0].reset();
                $(".result_head").show();
                $(".generate_btn").prop('disabled', false);
                $(".generate_btn").html('Generate');
                $(".result_loading").hide();
                $("#load_result").html(json.loaded);
                
                if ($(".result-body").height() > 650) {
                  $(".result-body").addClass('cus_scroll');
                }
            }else {
                $(".result_loading").hide();
                $(".generate_btn").prop('disabled', false);
                $(".generate_btn").html('Generate');
                $("#load_result").html(json.loaded);
            }
        },'json');
        return false;
    });



    $(document).on('click', ".plan_status", function() {
        var pkgId = $(this).attr('data-id');
        var status = $(this).val();

        var url = base_url+'admin/package/status_update/'+status+'/'+pkgId;
        $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
           if(json.st == 1){
                window.location.reload();
            }
        }, 'json' );
        return false;
    });


    $(document).on('click', ".layout", function() {
        $('.layout_form').submit();
    });


   $('.active_status').on('change', function() {
      var id = $(this).attr('data-id');
      var value = $(this).val();
      var url = base_url+'admin/sessions/status_update/'+value+'/'+id;

      $.post(url, { data: 'value', 'csrf_test_name': csrf_token }, function(json) {
          if(json.st == 1){     
              location.reload();
          }
      },'json');
      return false;
    }); 

    $(document).on('click', ".template_email", function() {

      var slug = $(this).attr('data-id');
      var url = base_url+'admin/email_templates/template/'+slug;

      //alert(url); return false;
      $.post(url,{ data: 'value', 'csrf_test_name': csrf_token },function(json){
          if (json.st == 1) {
              $('.email_template_area').html(json.loaded)+'ffggfgf';
              $('.template-slug').val(slug);
              $('.template_email').removeClass('active');
              $('.email-template-'+slug).addClass('active');
          }else{
              $('.conn_error').html(json.msg).show();
          }
      },'json');
      return false;
    });

    
  /**
   * --------------------------------------------
   * AdminLTE ControlSidebar.js
   * License MIT
   * --------------------------------------------
   */
  var ControlSidebar = function ($) {
    /**
     * Constants
     * ====================================================
     */
    var NAME = 'ControlSidebar';
    var DATA_KEY = 'lte.controlsidebar';
    var EVENT_KEY = "." + DATA_KEY;
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var Event = {
      COLLAPSED: "collapsed" + EVENT_KEY,
      EXPANDED: "expanded" + EVENT_KEY
    };
    var Selector = {
      CONTROL_SIDEBAR: '.control-sidebar',
      CONTROL_SIDEBAR_CONTENT: '.control-sidebar-content',
      DATA_TOGGLE: '[data-widget="control-sidebar"]',
      CONTENT: '.content-wrapper',
      HEADER: '.main-header',
      FOOTER: '.main-footer'
    };
    var ClassName = {
      CONTROL_SIDEBAR_ANIMATE: 'control-sidebar-animate',
      CONTROL_SIDEBAR_OPEN: 'control-sidebar-open',
      CONTROL_SIDEBAR_SLIDE: 'control-sidebar-slide-open',
      LAYOUT_FIXED: 'layout-fixed',
      NAVBAR_FIXED: 'layout-navbar-fixed',
      NAVBAR_SM_FIXED: 'layout-sm-navbar-fixed',
      NAVBAR_MD_FIXED: 'layout-md-navbar-fixed',
      NAVBAR_LG_FIXED: 'layout-lg-navbar-fixed',
      NAVBAR_XL_FIXED: 'layout-xl-navbar-fixed',
      FOOTER_FIXED: 'layout-footer-fixed',
      FOOTER_SM_FIXED: 'layout-sm-footer-fixed',
      FOOTER_MD_FIXED: 'layout-md-footer-fixed',
      FOOTER_LG_FIXED: 'layout-lg-footer-fixed',
      FOOTER_XL_FIXED: 'layout-xl-footer-fixed'
    };
    var Default = {
      controlsidebarSlide: true,
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'l'
    };
    /**
     * Class Definition
     * ====================================================
     */

    var ControlSidebar = /*#__PURE__*/function () {
      function ControlSidebar(element, config) {
        this._element = element;
        this._config = config;

        this._init();
      } // Public


      var _proto = ControlSidebar.prototype;

      _proto.collapse = function collapse() {
        // Show the control sidebar
        if (this._config.controlsidebarSlide) {
          $('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
          $('body').removeClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
            $(Selector.CONTROL_SIDEBAR).hide();
            $('html').removeClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
            $(this).dequeue();
          });
        } else {
          $('body').removeClass(ClassName.CONTROL_SIDEBAR_OPEN);
        }

        var collapsedEvent = $.Event(Event.COLLAPSED);
        $(this._element).trigger(collapsedEvent);
      };

      _proto.show = function show() {
        // Collapse the control sidebar
        if (this._config.controlsidebarSlide) {
          $('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
          $(Selector.CONTROL_SIDEBAR).show().delay(10).queue(function () {
            $('body').addClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
              $('html').removeClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
              $(this).dequeue();
            });
            $(this).dequeue();
          });
        } else {
          $('body').addClass(ClassName.CONTROL_SIDEBAR_OPEN);
        }

        var expandedEvent = $.Event(Event.EXPANDED);
        $(this._element).trigger(expandedEvent);
      };

      _proto.toggle = function toggle() {
        var shouldClose = $('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE);

        if (shouldClose) {
          // Close the control sidebar
          this.collapse();
        } else {
          // Open the control sidebar
          this.show();
        }
      } // Private
      ;

      _proto._init = function _init() {
        var _this = this;

        this._fixHeight();

        this._fixScrollHeight();

        $(window).resize(function () {
          _this._fixHeight();

          _this._fixScrollHeight();
        });
        $(window).scroll(function () {
          if ($('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE)) {
            _this._fixScrollHeight();
          }
        });
      };

      _proto._fixScrollHeight = function _fixScrollHeight() {
        var heights = {
          scroll: $(document).height(),
          window: $(window).height(),
          header: $(Selector.HEADER).outerHeight(),
          footer: $(Selector.FOOTER).outerHeight()
        };
        var positions = {
          bottom: Math.abs(heights.window + $(window).scrollTop() - heights.scroll),
          top: $(window).scrollTop()
        };
        var navbarFixed = false;
        var footerFixed = false;

        if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
          if ($('body').hasClass(ClassName.NAVBAR_FIXED) || $('body').hasClass(ClassName.NAVBAR_SM_FIXED) || $('body').hasClass(ClassName.NAVBAR_MD_FIXED) || $('body').hasClass(ClassName.NAVBAR_LG_FIXED) || $('body').hasClass(ClassName.NAVBAR_XL_FIXED)) {
            if ($(Selector.HEADER).css("position") === "fixed") {
              navbarFixed = true;
            }
          }

          if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName.FOOTER_MD_FIXED) || $('body').hasClass(ClassName.FOOTER_LG_FIXED) || $('body').hasClass(ClassName.FOOTER_XL_FIXED)) {
            if ($(Selector.FOOTER).css("position") === "fixed") {
              footerFixed = true;
            }
          }

          if (positions.top === 0 && positions.bottom === 0) {
            $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
            $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
            $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.header + heights.footer));
          } else if (positions.bottom <= heights.footer) {
            if (footerFixed === false) {
              $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer - positions.bottom);
              $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.footer - positions.bottom));
            } else {
              $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
            }
          } else if (positions.top <= heights.header) {
            if (navbarFixed === false) {
              $(Selector.CONTROL_SIDEBAR).css('top', heights.header - positions.top);
              $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.header - positions.top));
            } else {
              $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
            }
          } else {
            if (navbarFixed === false) {
              $(Selector.CONTROL_SIDEBAR).css('top', 0);
              $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window);
            } else {
              $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
            }
          }
        }
      };

      _proto._fixHeight = function _fixHeight() {
        var heights = {
          window: $(window).height(),
          header: $(Selector.HEADER).outerHeight(),
          footer: $(Selector.FOOTER).outerHeight()
        };

        if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
          var sidebarHeight = heights.window - heights.header;

          if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName.FOOTER_MD_FIXED) || $('body').hasClass(ClassName.FOOTER_LG_FIXED) || $('body').hasClass(ClassName.FOOTER_XL_FIXED)) {
            if ($(Selector.FOOTER).css("position") === "fixed") {
              sidebarHeight = heights.window - heights.header - heights.footer;
            }
          }

          $(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', sidebarHeight);

          if (typeof $.fn.overlayScrollbars !== 'undefined') {
            $(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).overlayScrollbars({
              className: this._config.scrollbarTheme,
              sizeAutoCapable: true,
              scrollbars: {
                autoHide: this._config.scrollbarAutoHide,
                clickScrolling: true
              }
            });
          }
        }
      } // Static
      ;

      ControlSidebar._jQueryInterface = function _jQueryInterface(operation) {
        return this.each(function () {
          var data = $(this).data(DATA_KEY);

          var _options = $.extend({}, Default, $(this).data());

          if (!data) {
            data = new ControlSidebar(this, _options);
            $(this).data(DATA_KEY, data);
          }

          if (data[operation] === 'undefined') {
            throw new Error(operation + " is not a function");
          }

          data[operation]();
        });
      };

      return ControlSidebar;
    }();
    /**
     *
     * Data Api implementation
     * ====================================================
     */


    $(document).on('click', Selector.DATA_TOGGLE, function (event) {
      event.preventDefault();

      ControlSidebar._jQueryInterface.call($(this), 'toggle');
    });
    /**
     * jQuery API
     * ====================================================
     */

    $.fn[NAME] = ControlSidebar._jQueryInterface;
    $.fn[NAME].Constructor = ControlSidebar;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return ControlSidebar._jQueryInterface;
    };

    return ControlSidebar;
  }(jQuery);

  /**
   * --------------------------------------------
   * AdminLTE Layout.js
   * License MIT
   * --------------------------------------------
   */
  var Layout = function ($) {
    /**
     * Constants
     * ====================================================
     */
    var NAME = 'Layout';
    var DATA_KEY = 'lte.layout';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var Selector = {
      HEADER: '.main-header',
      MAIN_SIDEBAR: '.main-sidebar',
      SIDEBAR: '.main-sidebar .sidebar',
      CONTENT: '.content-wrapper',
      BRAND: '.brand-link',
      CONTENT_HEADER: '.content-header',
      WRAPPER: '.wrapper',
      CONTROL_SIDEBAR: '.control-sidebar',
      CONTROL_SIDEBAR_CONTENT: '.control-sidebar-content',
      CONTROL_SIDEBAR_BTN: '[data-widget="control-sidebar"]',
      LAYOUT_FIXED: '.layout-fixed',
      FOOTER: '.main-footer',
      PUSHMENU_BTN: '[data-widget="pushmenu"]',
      LOGIN_BOX: '.login-box',
      REGISTER_BOX: '.register-box'
    };
    var ClassName = {
      HOLD: 'hold-transition',
      SIDEBAR: 'main-sidebar',
      CONTENT_FIXED: 'content-fixed',
      SIDEBAR_FOCUSED: 'sidebar-focused',
      LAYOUT_FIXED: 'layout-fixed',
      NAVBAR_FIXED: 'layout-navbar-fixed',
      FOOTER_FIXED: 'layout-footer-fixed',
      LOGIN_PAGE: 'login-page',
      REGISTER_PAGE: 'register-page',
      CONTROL_SIDEBAR_SLIDE_OPEN: 'control-sidebar-slide-open',
      CONTROL_SIDEBAR_OPEN: 'control-sidebar-open'
    };
    var Default = {
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'l',
      panelAutoHeight: true,
      loginRegisterAutoHeight: true
    };
    /**
     * Class Definition
     * ====================================================
     */

    var Layout = /*#__PURE__*/function () {
      function Layout(element, config) {
        this._config = config;
        this._element = element;

        this._init();
      } // Public


      var _proto = Layout.prototype;

      _proto.fixLayoutHeight = function fixLayoutHeight(extra) {
        if (extra === void 0) {
          extra = null;
        }

        var control_sidebar = 0;

        if ($('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || extra == 'control_sidebar') {
          control_sidebar = $(Selector.CONTROL_SIDEBAR_CONTENT).height();
        }

        var heights = {
          window: $(window).height(),
          header: $(Selector.HEADER).length !== 0 ? $(Selector.HEADER).outerHeight() : 0,
          footer: $(Selector.FOOTER).length !== 0 ? $(Selector.FOOTER).outerHeight() : 0,
          sidebar: $(Selector.SIDEBAR).length !== 0 ? $(Selector.SIDEBAR).height() : 0,
          control_sidebar: control_sidebar
        };

        var max = this._max(heights);

        var offset = this._config.panelAutoHeight;

        if (offset === true) {
          offset = 0;
        }

        if (offset !== false) {
          if (max == heights.control_sidebar) {
            $(Selector.CONTENT).css('min-height', max + offset);
          } else if (max == heights.window) {
            $(Selector.CONTENT).css('min-height', max + offset - heights.header - heights.footer);
          } else {
            $(Selector.CONTENT).css('min-height', max + offset - heights.header);
          }

          if (this._isFooterFixed()) {
            $(Selector.CONTENT).css('min-height', parseFloat($(Selector.CONTENT).css('min-height')) + heights.footer);
          }
        }

        if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
          if (offset !== false) {
            $(Selector.CONTENT).css('min-height', max + offset - heights.header - heights.footer);
          }

          if (typeof $.fn.overlayScrollbars !== 'undefined') {
            $(Selector.SIDEBAR).overlayScrollbars({
              className: this._config.scrollbarTheme,
              sizeAutoCapable: true,
              scrollbars: {
                autoHide: this._config.scrollbarAutoHide,
                clickScrolling: true
              }
            });
          }
        }
      };

      _proto.fixLoginRegisterHeight = function fixLoginRegisterHeight() {
        if ($(Selector.LOGIN_BOX + ', ' + Selector.REGISTER_BOX).length === 0) {
          $('body, html').css('height', 'auto');
        } else if ($(Selector.LOGIN_BOX + ', ' + Selector.REGISTER_BOX).length !== 0) {
          var box_height = $(Selector.LOGIN_BOX + ', ' + Selector.REGISTER_BOX).height();

          if ($('body').css('min-height') !== box_height) {
            $('body').css('min-height', box_height);
          }
        }
      } // Private
      ;

      _proto._init = function _init() {
        var _this = this;

        // Activate layout height watcher
        this.fixLayoutHeight();

        if (this._config.loginRegisterAutoHeight === true) {
          this.fixLoginRegisterHeight();
        } else if (Number.isInteger(this._config.loginRegisterAutoHeight)) {
          setInterval(this.fixLoginRegisterHeight, this._config.loginRegisterAutoHeight);
        }

        $(Selector.SIDEBAR).on('collapsed.lte.treeview expanded.lte.treeview', function () {
          _this.fixLayoutHeight();
        });
        $(Selector.PUSHMENU_BTN).on('collapsed.lte.pushmenu shown.lte.pushmenu', function () {
          _this.fixLayoutHeight();
        });
        $(Selector.CONTROL_SIDEBAR_BTN).on('collapsed.lte.controlsidebar', function () {
          _this.fixLayoutHeight();
        }).on('expanded.lte.controlsidebar', function () {
          _this.fixLayoutHeight('control_sidebar');
        });
        $(window).resize(function () {
          _this.fixLayoutHeight();
        });
        setTimeout(function () {
          $('body.hold-transition').removeClass('hold-transition');
        }, 50);
      };

      _proto._max = function _max(numbers) {
        // Calculate the maximum number in a list
        var max = 0;
        Object.keys(numbers).forEach(function (key) {
          if (numbers[key] > max) {
            max = numbers[key];
          }
        });
        return max;
      };

      _proto._isFooterFixed = function _isFooterFixed() {
        return $('.main-footer').css('position') === 'fixed';
      } // Static
      ;

      Layout._jQueryInterface = function _jQueryInterface(config) {
        if (config === void 0) {
          config = '';
        }

        return this.each(function () {
          var data = $(this).data(DATA_KEY);

          var _options = $.extend({}, Default, $(this).data());

          if (!data) {
            data = new Layout($(this), _options);
            $(this).data(DATA_KEY, data);
          }

          if (config === 'init' || config === '') {
            data['_init']();
          } else if (config === 'fixLayoutHeight' || config === 'fixLoginRegisterHeight') {
            data[config]();
          }
        });
      };

      return Layout;
    }();
    /**
     * Data API
     * ====================================================
     */


    $(window).on('load', function () {
      Layout._jQueryInterface.call($('body'));
    });
    $(Selector.SIDEBAR + ' a').on('focusin', function () {
      $(Selector.MAIN_SIDEBAR).addClass(ClassName.SIDEBAR_FOCUSED);
    });
    $(Selector.SIDEBAR + ' a').on('focusout', function () {
      $(Selector.MAIN_SIDEBAR).removeClass(ClassName.SIDEBAR_FOCUSED);
    });
    /**
     * jQuery API
     * ====================================================
     */

    $.fn[NAME] = Layout._jQueryInterface;
    $.fn[NAME].Constructor = Layout;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return Layout._jQueryInterface;
    };

    return Layout;
  }(jQuery);

  /**
   * --------------------------------------------
   * AdminLTE PushMenu.js
   * License MIT
   * --------------------------------------------
   */
  var PushMenu = function ($) {
    /**
     * Constants
     * ====================================================
     */
    var NAME = 'PushMenu';
    var DATA_KEY = 'lte.pushmenu';
    var EVENT_KEY = "." + DATA_KEY;
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var Event = {
      COLLAPSED: "collapsed" + EVENT_KEY,
      SHOWN: "shown" + EVENT_KEY
    };
    var Default = {
      autoCollapseSize: 992,
      enableRemember: false,
      noTransitionAfterReload: true
    };
    var Selector = {
      TOGGLE_BUTTON: '[data-widget="pushmenu"]',
      SIDEBAR_MINI: '.sidebar-mini',
      SIDEBAR_COLLAPSED: '.sidebar-collapse',
      BODY: 'body',
      OVERLAY: '#sidebar-overlay',
      WRAPPER: '.wrapper'
    };
    var ClassName = {
      COLLAPSED: 'sidebar-collapse',
      OPEN: 'sidebar-open',
      CLOSED: 'sidebar-closed'
    };
    /**
     * Class Definition
     * ====================================================
     */

    var PushMenu = /*#__PURE__*/function () {
      function PushMenu(element, options) {
        this._element = element;
        this._options = $.extend({}, Default, options);

        if (!$(Selector.OVERLAY).length) {
          this._addOverlay();
        }

        this._init();
      } // Public


      var _proto = PushMenu.prototype;

      _proto.expand = function expand() {
        if (this._options.autoCollapseSize) {
          if ($(window).width() <= this._options.autoCollapseSize) {
            $(Selector.BODY).addClass(ClassName.OPEN);
          }
        }

        $(Selector.BODY).removeClass(ClassName.COLLAPSED).removeClass(ClassName.CLOSED);

        if (this._options.enableRemember) {
          localStorage.setItem("remember" + EVENT_KEY, ClassName.OPEN);
        }

        var shownEvent = $.Event(Event.SHOWN);
        $(this._element).trigger(shownEvent);
      };

      _proto.collapse = function collapse() {
        if (this._options.autoCollapseSize) {
          if ($(window).width() <= this._options.autoCollapseSize) {
            $(Selector.BODY).removeClass(ClassName.OPEN).addClass(ClassName.CLOSED);
          }
        }

        $(Selector.BODY).addClass(ClassName.COLLAPSED);

        if (this._options.enableRemember) {
          localStorage.setItem("remember" + EVENT_KEY, ClassName.COLLAPSED);
        }

        var collapsedEvent = $.Event(Event.COLLAPSED);
        $(this._element).trigger(collapsedEvent);
      };

      _proto.toggle = function toggle() {
        if (!$(Selector.BODY).hasClass(ClassName.COLLAPSED)) {
          this.collapse();
        } else {
          this.expand();
        }
      };

      _proto.autoCollapse = function autoCollapse(resize) {
        if (resize === void 0) {
          resize = false;
        }

        if (this._options.autoCollapseSize) {
          if ($(window).width() <= this._options.autoCollapseSize) {
            if (!$(Selector.BODY).hasClass(ClassName.OPEN)) {
              this.collapse();
            }
          } else if (resize == true) {
            if ($(Selector.BODY).hasClass(ClassName.OPEN)) {
              $(Selector.BODY).removeClass(ClassName.OPEN);
            } else if ($(Selector.BODY).hasClass(ClassName.CLOSED)) {
              this.expand();
            }
          }
        }
      };

      _proto.remember = function remember() {
        if (this._options.enableRemember) {
          var toggleState = localStorage.getItem("remember" + EVENT_KEY);

          if (toggleState == ClassName.COLLAPSED) {
            if (this._options.noTransitionAfterReload) {
              $("body").addClass('hold-transition').addClass(ClassName.COLLAPSED).delay(50).queue(function () {
                $(this).removeClass('hold-transition');
                $(this).dequeue();
              });
            } else {
              $("body").addClass(ClassName.COLLAPSED);
            }
          } else {
            if (this._options.noTransitionAfterReload) {
              $("body").addClass('hold-transition').removeClass(ClassName.COLLAPSED).delay(50).queue(function () {
                $(this).removeClass('hold-transition');
                $(this).dequeue();
              });
            } else {
              $("body").removeClass(ClassName.COLLAPSED);
            }
          }
        }
      } // Private
      ;

      _proto._init = function _init() {
        var _this = this;

        this.remember();
        this.autoCollapse();
        $(window).resize(function () {
          _this.autoCollapse(true);
        });
      };

      _proto._addOverlay = function _addOverlay() {
        var _this2 = this;

        var overlay = $('<div />', {
          id: 'sidebar-overlay'
        });
        overlay.on('click', function () {
          _this2.collapse();
        });
        $(Selector.WRAPPER).append(overlay);
      } // Static
      ;

      PushMenu._jQueryInterface = function _jQueryInterface(operation) {
        return this.each(function () {
          var data = $(this).data(DATA_KEY);

          var _options = $.extend({}, Default, $(this).data());

          if (!data) {
            data = new PushMenu(this, _options);
            $(this).data(DATA_KEY, data);
          }

          if (typeof operation === 'string' && operation.match(/collapse|expand|toggle/)) {
            data[operation]();
          }
        });
      };

      return PushMenu;
    }();
    /**
     * Data API
     * ====================================================
     */


    $(document).on('click', Selector.TOGGLE_BUTTON, function (event) {
      event.preventDefault();
      var button = event.currentTarget;

      if ($(button).data('widget') !== 'pushmenu') {
        button = $(button).closest(Selector.TOGGLE_BUTTON);
      }

      PushMenu._jQueryInterface.call($(button), 'toggle');
    });
    $(window).on('load', function () {
      PushMenu._jQueryInterface.call($(Selector.TOGGLE_BUTTON));
    });
    /**
     * jQuery API
     * ====================================================
     */

    $.fn[NAME] = PushMenu._jQueryInterface;
    $.fn[NAME].Constructor = PushMenu;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return PushMenu._jQueryInterface;
    };

    return PushMenu;
  }(jQuery);

  /**
   * --------------------------------------------
   * AdminLTE Treeview.js
   * License MIT
   * --------------------------------------------
   */
  var Treeview = function ($) {
    /**
     * Constants
     * ====================================================
     */
    var NAME = 'Treeview';
    var DATA_KEY = 'lte.treeview';
    var EVENT_KEY = "." + DATA_KEY;
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var Event = {
      SELECTED: "selected" + EVENT_KEY,
      EXPANDED: "expanded" + EVENT_KEY,
      COLLAPSED: "collapsed" + EVENT_KEY,
      LOAD_DATA_API: "load" + EVENT_KEY
    };
    var Selector = {
      LI: '.nav-item',
      LINK: '.nav-link',
      TREEVIEW_MENU: '.nav-treeview',
      OPEN: '.menu-open',
      DATA_WIDGET: '[data-widget="treeview"]'
    };
    var ClassName = {
      LI: 'nav-item',
      LINK: 'nav-link',
      TREEVIEW_MENU: 'nav-treeview',
      OPEN: 'menu-open',
      SIDEBAR_COLLAPSED: 'sidebar-collapse'
    };
    var Default = {
      trigger: Selector.DATA_WIDGET + " " + Selector.LINK,
      animationSpeed: 300,
      accordion: true,
      expandSidebar: false,
      sidebarButtonSelector: '[data-widget="pushmenu"]'
    };
    /**
     * Class Definition
     * ====================================================
     */

    var Treeview = /*#__PURE__*/function () {
      function Treeview(element, config) {
        this._config = config;
        this._element = element;
      } // Public


      var _proto = Treeview.prototype;

      _proto.init = function init() {
        this._setupListeners();
      };

      _proto.expand = function expand(treeviewMenu, parentLi) {
        var _this = this;

        var expandedEvent = $.Event(Event.EXPANDED);

        if (this._config.accordion) {
          var openMenuLi = parentLi.siblings(Selector.OPEN).first();
          var openTreeview = openMenuLi.find(Selector.TREEVIEW_MENU).first();
          this.collapse(openTreeview, openMenuLi);
        }

        treeviewMenu.stop().slideDown(this._config.animationSpeed, function () {
          parentLi.addClass(ClassName.OPEN);
          $(_this._element).trigger(expandedEvent);
        });

        if (this._config.expandSidebar) {
          this._expandSidebar();
        }
      };

      _proto.collapse = function collapse(treeviewMenu, parentLi) {
        var _this2 = this;

        var collapsedEvent = $.Event(Event.COLLAPSED);
        treeviewMenu.stop().slideUp(this._config.animationSpeed, function () {
          parentLi.removeClass(ClassName.OPEN);
          $(_this2._element).trigger(collapsedEvent);
          treeviewMenu.find(Selector.OPEN + " > " + Selector.TREEVIEW_MENU).slideUp();
          treeviewMenu.find(Selector.OPEN).removeClass(ClassName.OPEN);
        });
      };

      _proto.toggle = function toggle(event) {
        var $relativeTarget = $(event.currentTarget);
        var $parent = $relativeTarget.parent();
        var treeviewMenu = $parent.find('> ' + Selector.TREEVIEW_MENU);

        if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
          if (!$parent.is(Selector.LI)) {
            treeviewMenu = $parent.parent().find('> ' + Selector.TREEVIEW_MENU);
          }

          if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
            return;
          }
        }

        event.preventDefault();
        var parentLi = $relativeTarget.parents(Selector.LI).first();
        var isOpen = parentLi.hasClass(ClassName.OPEN);

        if (isOpen) {
          this.collapse($(treeviewMenu), parentLi);
        } else {
          this.expand($(treeviewMenu), parentLi);
        }
      } // Private
      ;

      _proto._setupListeners = function _setupListeners() {
        var _this3 = this;

        $(document).on('click', this._config.trigger, function (event) {
          _this3.toggle(event);
        });
      };

      _proto._expandSidebar = function _expandSidebar() {
        if ($('body').hasClass(ClassName.SIDEBAR_COLLAPSED)) {
          $(this._config.sidebarButtonSelector).PushMenu('expand');
        }
      } // Static
      ;

      Treeview._jQueryInterface = function _jQueryInterface(config) {
        return this.each(function () {
          var data = $(this).data(DATA_KEY);

          var _options = $.extend({}, Default, $(this).data());

          if (!data) {
            data = new Treeview($(this), _options);
            $(this).data(DATA_KEY, data);
          }

          if (config === 'init') {
            data[config]();
          }
        });
      };

      return Treeview;
    }();
    /**
     * Data API
     * ====================================================
     */


    $(window).on(Event.LOAD_DATA_API, function () {
      $(Selector.DATA_WIDGET).each(function () {
        Treeview._jQueryInterface.call($(this), 'init');
      });
    });
    /**
     * jQuery API
     * ====================================================
     */

    $.fn[NAME] = Treeview._jQueryInterface;
    $.fn[NAME].Constructor = Treeview;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return Treeview._jQueryInterface;
    };

    return Treeview;
  }(jQuery);


  /**
   * --------------------------------------------
   * AdminLTE Dropdown.js
   * License MIT
   * --------------------------------------------
   */
  var Dropdown = function ($) {
    /**
     * Constants
     * ====================================================
     */
    var NAME = 'Dropdown';
    var DATA_KEY = 'lte.dropdown';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var Selector = {
      NAVBAR: '.navbar',
      DROPDOWN_MENU: '.dropdown-menu',
      DROPDOWN_MENU_ACTIVE: '.dropdown-menu.show',
      DROPDOWN_TOGGLE: '[data-toggle="dropdown"]'
    };
    var ClassName = {
      DROPDOWN_HOVER: 'dropdown-hover',
      DROPDOWN_RIGHT: 'dropdown-menu-right'
    };
    var Default = {};
    /**
     * Class Definition
     * ====================================================
     */

    var Dropdown = /*#__PURE__*/function () {
      function Dropdown(element, config) {
        this._config = config;
        this._element = element;
      } // Public


      var _proto = Dropdown.prototype;

      _proto.toggleSubmenu = function toggleSubmenu() {
        this._element.siblings().show().toggleClass("show");

        if (!this._element.next().hasClass('show')) {
          this._element.parents('.dropdown-menu').first().find('.show').removeClass("show").hide();
        }

        this._element.parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
          $('.dropdown-submenu .show').removeClass("show").hide();
        });
      };

      _proto.fixPosition = function fixPosition() {
        var elm = $(Selector.DROPDOWN_MENU_ACTIVE);

        if (elm.length !== 0) {
          if (elm.hasClass(ClassName.DROPDOWN_RIGHT)) {
            elm.css('left', 'inherit');
            elm.css('right', 0);
          } else {
            elm.css('left', 0);
            elm.css('right', 'inherit');
          }

          var offset = elm.offset();
          var width = elm.width();
          var windowWidth = $(window).width();
          var visiblePart = windowWidth - offset.left;

          if (offset.left < 0) {
            elm.css('left', 'inherit');
            elm.css('right', offset.left - 5);
          } else {
            if (visiblePart < width) {
              elm.css('left', 'inherit');
              elm.css('right', 0);
            }
          }
        }
      } // Static
      ;

      Dropdown._jQueryInterface = function _jQueryInterface(config) {
        return this.each(function () {
          var data = $(this).data(DATA_KEY);

          var _config = $.extend({}, Default, $(this).data());

          if (!data) {
            data = new Dropdown($(this), _config);
            $(this).data(DATA_KEY, data);
          }

          if (config === 'toggleSubmenu' || config == 'fixPosition') {
            data[config]();
          }
        });
      };

      return Dropdown;
    }();
    /**
     * Data API
     * ====================================================
     */


    $(Selector.DROPDOWN_MENU + ' ' + Selector.DROPDOWN_TOGGLE).on("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      Dropdown._jQueryInterface.call($(this), 'toggleSubmenu');
    });
    $(Selector.NAVBAR + ' ' + Selector.DROPDOWN_TOGGLE).on("click", function (event) {
      event.preventDefault();
      setTimeout(function () {
        Dropdown._jQueryInterface.call($(this), 'fixPosition');
      }, 1);
    });
    /**
     * jQuery API
     * ====================================================
     */

    $.fn[NAME] = Dropdown._jQueryInterface;
    $.fn[NAME].Constructor = Dropdown;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return Dropdown._jQueryInterface;
    };

    return Dropdown;
  }(jQuery);

  /**
   * --------------------------------------------
   * AdminLTE Toasts.js
   * License MIT
   * --------------------------------------------
   */
  var Toasts = function ($) {
    /**
     * Constants
     * ====================================================
     */
    var NAME = 'Toasts';
    var DATA_KEY = 'lte.toasts';
    var EVENT_KEY = "." + DATA_KEY;
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var Event = {
      INIT: "init" + EVENT_KEY,
      CREATED: "created" + EVENT_KEY,
      REMOVED: "removed" + EVENT_KEY
    };
    var Selector = {
      BODY: 'toast-body',
      CONTAINER_TOP_RIGHT: '#toastsContainerTopRight',
      CONTAINER_TOP_LEFT: '#toastsContainerTopLeft',
      CONTAINER_BOTTOM_RIGHT: '#toastsContainerBottomRight',
      CONTAINER_BOTTOM_LEFT: '#toastsContainerBottomLeft'
    };
    var ClassName = {
      TOP_RIGHT: 'toasts-top-right',
      TOP_LEFT: 'toasts-top-left',
      BOTTOM_RIGHT: 'toasts-bottom-right',
      BOTTOM_LEFT: 'toasts-bottom-left',
      FADE: 'fade'
    };
    var Position = {
      TOP_RIGHT: 'topRight',
      TOP_LEFT: 'topLeft',
      BOTTOM_RIGHT: 'bottomRight',
      BOTTOM_LEFT: 'bottomLeft'
    };
    var Default = {
      position: Position.TOP_RIGHT,
      fixed: true,
      autohide: false,
      autoremove: true,
      delay: 1000,
      fade: true,
      icon: null,
      image: null,
      imageAlt: null,
      imageHeight: '25px',
      title: null,
      subtitle: null,
      close: true,
      body: null,
      class: null
    };
    /**
     * Class Definition
     * ====================================================
     */

    var Toasts = /*#__PURE__*/function () {
      function Toasts(element, config) {
        this._config = config;

        this._prepareContainer();

        var initEvent = $.Event(Event.INIT);
        $('body').trigger(initEvent);
      } // Public


      var _proto = Toasts.prototype;

      _proto.create = function create() {
        var toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true"/>');
        toast.data('autohide', this._config.autohide);
        toast.data('animation', this._config.fade);

        if (this._config.class) {
          toast.addClass(this._config.class);
        }

        if (this._config.delay && this._config.delay != 500) {
          toast.data('delay', this._config.delay);
        }

        var toast_header = $('<div class="toast-header">');

        if (this._config.image != null) {
          var toast_image = $('<img />').addClass('rounded mr-2').attr('src', this._config.image).attr('alt', this._config.imageAlt);

          if (this._config.imageHeight != null) {
            toast_image.height(this._config.imageHeight).width('auto');
          }

          toast_header.append(toast_image);
        }

        if (this._config.icon != null) {
          toast_header.append($('<i />').addClass('mr-2').addClass(this._config.icon));
        }

        if (this._config.title != null) {
          toast_header.append($('<strong />').addClass('mr-auto').html(this._config.title));
        }

        if (this._config.subtitle != null) {
          toast_header.append($('<small />').html(this._config.subtitle));
        }

        if (this._config.close == true) {
          var toast_close = $('<button data-dismiss="toast" />').attr('type', 'button').addClass('ml-2 mb-1 close').attr('aria-label', 'Close').append('<span aria-hidden="true">&times;</span>');

          if (this._config.title == null) {
            toast_close.toggleClass('ml-2 ml-auto');
          }

          toast_header.append(toast_close);
        }

        toast.append(toast_header);

        if (this._config.body != null) {
          toast.append($('<div class="toast-body" />').html(this._config.body));
        }

        $(this._getContainerId()).prepend(toast);
        var createdEvent = $.Event(Event.CREATED);
        $('body').trigger(createdEvent);
        toast.toast('show');

        if (this._config.autoremove) {
          toast.on('hidden.bs.toast', function () {
            $(this).delay(200).remove();
            var removedEvent = $.Event(Event.REMOVED);
            $('body').trigger(removedEvent);
          });
        }
      } // Static
      ;

      _proto._getContainerId = function _getContainerId() {
        if (this._config.position == Position.TOP_RIGHT) {
          return Selector.CONTAINER_TOP_RIGHT;
        } else if (this._config.position == Position.TOP_LEFT) {
          return Selector.CONTAINER_TOP_LEFT;
        } else if (this._config.position == Position.BOTTOM_RIGHT) {
          return Selector.CONTAINER_BOTTOM_RIGHT;
        } else if (this._config.position == Position.BOTTOM_LEFT) {
          return Selector.CONTAINER_BOTTOM_LEFT;
        }
      };

      _proto._prepareContainer = function _prepareContainer() {
        if ($(this._getContainerId()).length === 0) {
          var container = $('<div />').attr('id', this._getContainerId().replace('#', ''));

          if (this._config.position == Position.TOP_RIGHT) {
            container.addClass(ClassName.TOP_RIGHT);
          } else if (this._config.position == Position.TOP_LEFT) {
            container.addClass(ClassName.TOP_LEFT);
          } else if (this._config.position == Position.BOTTOM_RIGHT) {
            container.addClass(ClassName.BOTTOM_RIGHT);
          } else if (this._config.position == Position.BOTTOM_LEFT) {
            container.addClass(ClassName.BOTTOM_LEFT);
          }

          $('body').append(container);
        }

        if (this._config.fixed) {
          $(this._getContainerId()).addClass('fixed');
        } else {
          $(this._getContainerId()).removeClass('fixed');
        }
      } // Static
      ;

      Toasts._jQueryInterface = function _jQueryInterface(option, config) {
        return this.each(function () {
          var _options = $.extend({}, Default, config);

          var toast = new Toasts($(this), _options);

          if (option === 'create') {
            toast[option]();
          }
        });
      };

      return Toasts;
    }();



    /**
     * jQuery API
     * ====================================================
     */


    $.fn[NAME] = Toasts._jQueryInterface;
    $.fn[NAME].Constructor = Toasts;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return Toasts._jQueryInterface;
    };

    return Toasts;
  }(jQuery);

  exports.ControlSidebar = ControlSidebar;
  exports.Dropdown = Dropdown;
  exports.Layout = Layout;
  exports.PushMenu = PushMenu;
  exports.Toasts = Toasts;
  exports.Treeview = Treeview;

  Object.defineProperty(exports, '__esModule', { value: true });

})));
//# sourceMappingURL=adminlte.js.map
