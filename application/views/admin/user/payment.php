<?php include"include/breadcrumb.php"; ?>
<?php if(get_user_info() == TRUE){$uval = 'd-show';}else{$uval = 'd-hide';} ?>
<?php
    $paypal_url = (settings()->paypal_mode == 'sandbox')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr';
    $paypal_id = html_escape(settings()->paypal_email);

    $session = get_by_id($booking->session_id, 'sessions');
    $booking_number = $booking->booking_number;
    $customer_name = get_by_id($booking->mentee_id,'users')->name;
    $customer_email = get_by_id($booking->mentee_id,'users')->email;
    $customer_phone = get_by_id($booking->mentee_id,'users')->phone;

    $check_discount = $this->admin_model->check_discount_by_session($booking->session_id, 'discounts');
    $check_coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
 
    if(!empty($check_discount) && !empty($check_coupon) ){
        $discount_amount = ($check_coupon->discount * $booking->price) / 100 ;
        $totalCost = $booking->price - $discount_amount;
    }else{
        $discount_amount = '0';
        $totalCost = $booking->price ;
    }
?>

<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 m-auto">


                    <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                        <?php if (settings()->paypal_payment == 1): ?>
                            <li class="nav-item mr-2 mt-1">
                                <a class="btn btn-outline-primary border-1 nav-link active" id="pills-paypal-tab" data-toggle="pill" href="#pills-paypal" role="tab"
                                    aria-controls="pills-paypal" aria-selected="true"> <?php echo trans('paypal') ?></a>
                            </li>
                        <?php endif ?>

                        <?php if (settings()->stripe_payment == 1): ?>
                            <li class="nav-item mr-2 mt-1">
                                <a class="btn btn-outline-primary border-1 nav-link" id="pills-stripe-tab" data-toggle="pill" href="#pills-stripe" role="tab"
                                    aria-controls="pills-stripe" aria-selected="false"><?php echo trans('stripe') ?></a>
                            </li>
                        <?php endif ?>

                        <?php if (settings()->razorpay_payment == 1): ?>
                            <li class="nav-item mr-2 mt-1">
                                <a class="btn btn-outline-primary border-1 nav-link" id="pills-razorpay-tab" data-toggle="pill" href="#pills-razorpay" role="tab"
                                    aria-controls="pills-razorpay" aria-selected="false"><?php echo trans('razorpay') ?></a>
                            </li>
                        <?php endif ?>

                        <?php if (settings()->paystack_payment == 1): ?>
                            <li class="nav-item mr-2 mt-1">
                                <a class="btn btn-outline-primary border-1 nav-link" id="pills-paystack-tab" data-toggle="pill" href="#pills-paystack" role="tab"
                                    aria-controls="pills-paystack" aria-selected="false"><?php echo trans('paystack') ?></a>
                            </li>
                        <?php endif ?>

                        <?php if (settings()->flutterwave_payment == 1): ?>
                            <li class="nav-item mr-2 mt-1">
                                <a class="btn btn-outline-primary border-1  nav-link" id="pills-flutterwave-tab" data-toggle="pill" href="#pills-flutterwave" role="tab"
                                    aria-controls="pills-flutterwave" aria-selected="false"><?php echo trans('flutterwave') ?></a>
                            </li>
                        <?php endif ?>

                        <?php if (settings()->mercado_payment == 1): ?>
                            <li class="nav-item mr-2 mt-1">
                                <a class="btn btn-outline-primary border-1  nav-link" id="pills-mercado-tab" data-toggle="pill" href="#pills-mercado" role="tab"
                                    aria-controls="pills-mercado" aria-selected="false">MercadoPago</a>
                            </li>
                        <?php endif ?>

                        <?php if (settings()->enable_offline_payment == 1): ?>
                        <li class="nav-item mr-2 mt-1">
                            <a class="btn btn-outline-primary border-1  nav-link" id="pills-offline-tab" data-toggle="pill" href="#pills-offline" role="tab"
                                aria-controls="pills-offline" aria-selected="false">Offline</a>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <div class="col-12 mt-5 text-center">
                        <p class="m-auto mb-0">
                            <?php echo trans('total').' '.trans('price') ?>: 
                            <?php if(settings()->curr_locate == 0){echo get_currency_by_country(settings()->country)->currency_symbol;} ?><?php echo number_format($totalCost, settings()->num_format) ?> <?php if(settings()->curr_locate == 1){echo get_currency_by_country(settings()->country)->currency_symbol;} ?> 

                            <?php if (!empty($check_discount)): ?>
                                <span>(<?php echo trans('discount') ?>: <?php echo html_escape($check_discount->discount) ?>%)</span>
                            <?php endif ?>
                        </p>
                        <?php if ($discount_amount != 0): ?>
                            <p class="m-auto mb-0">
                                <?php echo trans('discount') ?>:
                                <?php if(settings()->curr_locate == 0){echo get_currency_by_country(settings()->country)->currency_symbol;} ?><?php echo number_format($discount_amount, settings()->num_format) ?> <?php if(settings()->curr_locate == 1){echo get_currency_by_country(settings()->country)->currency_symbol;} ?>
                            </p>
                        <?php endif ?>
                    </div>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-paypal" role="tabpanel" aria-labelledby="pills-paypal-tab">
                            <?php if (settings()->paypal_payment == 1): ?>
                                <div class="payment_area m-auto container <?php if (settings()->paypal_payment == 1){echo "d-show";}else{echo "d-hide";} ?>" id="paypal">
                                    <div class="row">
                                        <div class="box col-md-12 m-auto text-center">

                                            <div class="box-body text-center py-4 px-3">

                                                <!-- PRICE ITEM -->
                                                <form action="<?php echo html_escape($paypal_url); ?>" method="post" name="frmPayPal1">
                                                    <input type="hidden" name="business" value="<?php echo html_escape($paypal_id); ?>"
                                                        readonly>
                                                    <input type="hidden" name="cmd" value="_xclick">
                                                    <input type="hidden" name="item_name" value="<?php echo html_escape($session->name) ?>">
                                                    <input type="hidden" name="item_number" value="<?php echo html_escape($booking_number) ?>">
                                                    <input type="hidden" name="amount" class="paypal_price" value="<?php echo html_escape($totalCost) ?>">
                                                    <input type="hidden" name="no_shipping" value="1">
                                                    <input type="hidden" name="currency_code"
                                                        value="<?php echo html_escape(get_currency_by_country(settings()->country)->currency_code);?>">
                                                    <input type="hidden" name="cancel_return"
                                                        value="<?php echo base_url('customer/payment_msg/error/'.html_escape($booking->id)) ?>">
                                                    <input type="hidden" name="return"
                                                        value="<?php echo base_url('admin/payment/payment_success/'.html_escape($booking->id).'/paypal') ?>">

                                                    <div class="mt-30 mt-8">
                                                        <button class="btn btn-success paypal-btn btn-blocks px-5" href="#"><i class="fas fa-check-circle"></i> <?php echo trans('pay-now') ?></button>
                                                    </div>
                                                </form>
                                                <!-- PRICE ITEM -->

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="tab-pane fade" id="pills-stripe" role="tabpanel" aria-labelledby="pills-stripe-tab">
                            <?php if (settings()->stripe_payment == 1): ?>
                                <div class="payment_area m-auto container" id="stripe">
                                    <div class="row justify-content-center">
                                        <div class="box col-md-12 m-auto text-center">

                                            <div class="credit-card-box py-4 px-3">
                                                <div class="d-flex justify-content-between info-title mb-4">
                                                    <div class="pt-1"><h5 class="mb-0"><?php echo trans('card-details') ?></h5> </div>
                                                    <div>
                                                        <img width="200px" src="<?php echo base_url('assets/images/accept-cards.jpg') ?>">
                                                    </div>
                                                </div>
                                               
                                                <div class="box-body p-0">
                                                    <form role="form" action="<?php echo base_url('admin/payment/stripe_booking_payment') ?>"
                                                        method="post" class="require-validation" data-cc-on-file="false"
                                                        data-stripe-publishable-key="<?php echo html_escape(settings()->publish_key); ?>"
                                                        id="payment-form">

                                                        <div class='row'>
                                                            <div class='col-xs-12 col-md-6 form-group required text-left'>
                                                            </div>
                                                            <div class='col-xs-12 col-md-6 form-group required text-left'>
                                                            </div>
                                                        </div>

                                                        <div class='row'>
                                                            <div class='col-xs-12 col-md-12 form-group required text-left'>
                                                                <label><?php echo trans('cardholders-name')?></label>
                                                                <input class='textfield textfield--grey' type='text' value=""
                                                                    placeholder="" size='12'>
                                                            </div>

                                                            <div class='col-xs-12 col-md-12 form-group required text-left'>
                                                                <label><?php echo trans('card-number')?></label>
                                                                <input autocomplete='off' class='textfield textfield--grey card-number'
                                                                    type='text' placeholder="" value="" size='12'>
                                                            </div>
                                                        </div>


                                                        <div class='form-row row'>
                                                            <div class='col-xs-12 col-md-4 form-group expiration required text-left'>
                                                                <label><?php echo ucfirst(trans('month')) ?></label>
                                                                <input class='textfield textfield--grey card-expiry-month'
                                                                    placeholder='MM' size='2' type='text' value="">
                                                            </div>
                                                            <div class='col-xs-12 col-md-4 form-group expiration required text-left'>
                                                                <label><?php echo ucfirst(trans('year')) ?></label>
                                                                <input class='textfield textfield--grey card-expiry-year'
                                                                    placeholder='YYYY' size='4' type='text' value="">
                                                            </div>
                                                            <div class='col-xs-12 col-md-4 form-group cvc required text-left'>
                                                                <label>CVC</label>
                                                                <input autocomplete='off' class='textfield textfield--grey card-cvc'
                                                                    placeholder='' size='4' type='text' value="">
                                                            </div>
                                                        </div>

                                                        <div class="text-center text-success">
                                                            <div class="payment_loader hide"><i class="fa fa-spinner fa-spin"></i> <?php echo trans('loading') ?>....
                                                            </div><br>
                                                        </div>

                                                        <!-- csrf token -->
                                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>"
                                                            value="<?php echo $this->security->get_csrf_hash();?>">

                                                        <input type="hidden" name="booking_id" value="<?php echo html_escape($booking->id); ?>">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="badge badge-pill badge-warning-soft mb-4"><i class="icon-lock"></i>
                                                                    <?php echo trans('secure-and-encrypted') ?></div>
                                                            </div>
                                                            <div class="spacer py-4"></div>
                                                            <div class="col-md-12 mb-30">
                                                                <button class="btn btn-primary paypal-btn btn-block" type="submit"><i class="fas fa-check-circle"></i> <?php echo trans('pay-now') ?></button>
                                                            </div>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="tab-pane fade" id="pills-razorpay" role="tabpanel" aria-labelledby="pills-razorpay-tab">
                            <?php if (settings()->razorpay_payment == 1): ?>
                                <div class="payment_area m-auto container <?php if (settings()->razorpay_payment == 1){echo "d-show";}else{echo "d-hide";} ?>" id="razorpay">
                                    <div class="row">
                                        <div class="box col-md-12 m-auto text-center">
                                            <div class="box-body text-center py-4 px-3">

                                               <?php
                                                    $productinfo = $session->name;
                                                    $txnid = time();
                                                    $price = $totalCost;
                                                    $surl = '';
                                                    $furl = '';
                                                    $key_id = settings()->razorpay_key_id;
                                                    $currency_code =  get_currency_by_country(settings()->country)->currency_code;           
                                                    $total = ($price * 100);
                                                    $amount = $price;
                                                    $merchant_order_id = $booking_number;
                                                    $card_holder_name = $customer_name;
                                                    $email = $customer_email;
                                                    $phone = $customer_phone;
                                                    $name = settings()->site_name;
                                                    $return_url = base_url().'razorpay/user_payment';
                                                ?>

                                                <form name="razorpay-form" id="razorpay-form" action="<?php echo html_escape($return_url); ?>" method="POST">
                                                  <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
                                                  <input type="hidden" name="merchant_order_id" id="merchant_order_id" value="<?php echo html_escape($merchant_order_id); ?>"/>
                                                  <input type="hidden" name="merchant_trans_id" id="merchant_trans_id" value="<?php echo html_escape($txnid); ?>"/>
                                                  <input type="hidden" name="merchant_product_info_id" id="merchant_product_info_id" value="<?php echo html_escape($productinfo); ?>"/>
                                                  <input type="hidden" name="merchant_surl_id" id="merchant_surl_id" value="<?php echo html_escape($surl); ?>"/>
                                                  <input type="hidden" name="merchant_furl_id" id="merchant_furl_id" value="<?php echo html_escape($furl); ?>"/>
                                                  <input type="hidden" name="card_holder_name_id" id="card_holder_name_id" value="<?php echo html_escape($card_holder_name); ?>"/>
                                                  <input type="hidden" name="merchant_total" id="merchant_total" value="<?php echo html_escape($total); ?>"/>
                                                  <input type="hidden" name="merchant_amount" id="merchant_amount" value="<?php echo html_escape($amount); ?>"/>

                                                   <input type="hidden" name="booking_id" value="<?php echo html_escape($booking->id); ?>">
                                                   <input type="hidden" name="currency_code" value="<?php echo get_currency_by_country(settings()->country)->currency_code; ?>">
                                                  <!-- csrf token -->
                                                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                                </form>

                               
                                                <div class="mt-0">
                                                    <button id="submit-pay" type="submit" onclick="razorpaySubmit(this);" class="btn btn-success btn-blocks px-5"> <i class="fas fa-check-circle"></i> <?php echo trans('pay-now') ?></button>
                                                </div>
                                              
                                                <?php include APPPATH.'views/admin/include/razorpay-user-js.php'; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>

                         <div class="tab-pane fade" id="pills-paystack" role="tabpanel" aria-labelledby="pills-paystack-tab">
                            <?php if (settings()->paystack_payment == 1): ?>
                                <div class="payment_area m-auto container <?php if (settings()->paystack_payment == 1){echo "d-show";}else{echo "d-hide";} ?>" id="paystack">
                                    <div class="row">
                                        <div class="box col-md-12 m-auto text-center">
                                            <div class="box-body text-center py-4 px-3">

                                                <?php 
                                                    $paystack_type = 'admin';
                                                    $email = $customer_email;
                                                    $price = $totalCost;
                                                 ?>
                                                <div class="mt-0">
                                                    <script src="https://js.paystack.co/v1/inline.js"></script>
                                                    <button type="button" onclick="payWithPaystack()" class="btn btn-success btn-blocks px-5"><i class="fas fa-check-circle"></i> <?php echo trans('pay-now') ?> </button>
                                                </div>
                                              
                                                <?php include APPPATH.'views/admin/include/paystack-js.php'; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>


                        <div class="tab-pane fade" id="pills-flutterwave" role="tabpanel" aria-labelledby="pills-flutterwave-tab">
                            <?php if (settings()->flutterwave_payment == 1): ?>
                                <div class="payment_area m-auto container <?php if (settings()->flutterwave_payment == 1){echo "d-show";}else{echo "d-hide";} ?>" id="flutterwave">
                                    <div class="row">
                                        <div class="box col-md-12 m-auto text-center">
                                            <div class="box-body text-center py-4 px-3">

                                                <?php 
                                                    $flutterwave_type = 'admin';
                                                    $currency_code =  get_currency_by_country(settings()->country)->currency_code; 
                                                    $email = $customer_email;
                                                    $phone = $customer_phone;
                                                    $price = $totalCost;
                                                 ?>
                                                <div class="mt-0">
                                                    <script type="text/javascript" src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
                                                    <button type="button" id="flutterwave_payment" class="btn btn-success btn-blocks px-5"><i class="fas fa-check-circle"></i> <?php echo trans('pay-now') ?> </button>
                                                </div>
                                              
                                                <?php include APPPATH.'views/admin/include/flutterwave-js.php'; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>


                        <div class="tab-pane fade" id="pills-mercado" role="tabpanel" aria-labelledby="pills-mercado-tab">
                            <?php if (settings()->mercado_payment == 1): ?>
                                <div class="payment_area m-auto container <?php if (settings()->mercado_payment == 1){echo "d-show";}else{echo "d-hide";} ?>" id="mercado">
                                    <div class="row">
                                        <div class="box col-md-12 m-auto text-center">
                                            <div class="box-body text-center py-4 px-3">
                                                <div class="mt-0">
                                                    <a href="<?= prep_url($init); ?>" class="btn btn-success btn-blocks px-5"><i class="fas fa-check-circle"></i> <?php echo trans('pay-now') ?> </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>


                        <div class="tab-pane fade" id="pills-offline" role="tabpanel" aria-labelledby="pills-offline-tab">
                            <?php if (settings()->enable_offline_payment == 1): ?>
                                <div class="payment_area m-auto container <?php if (settings()->enable_offline_payment == 1){echo "d-show";}else{echo "d-hide";} ?>" id="mercado">
                                    <div class="row">
                                        <div class="box p-4 col-md-12 text-center">
                                            <div class="box-body text-center">
                                                <div class="mt-2">
                                                    <div>
                                                        <p class="text-center"><?php echo trans('offline-payment-instructions') ?></p>
                                                        <div class="bg-light p-4 rounded"><p><?php echo settings()->offline_details ?></p></div>
                                                    </div>

                                                    <form enctype="multipart/form-data" action="<?php echo base_url('admin/payment/offline_payment_customer/'.md5($booking->id)) ?>" method="post" class="form-horizontal mt-3">
                                                        
                                                        <div class="form-group text-left pt-3 pb-3">    
                                                            <label for="exampleFormControlFile1"><?php echo trans('upload-payment-proof') ?></label>
                                                            <input type="file" name="file" class="form-control-file" id="exampleFormControlFile1" required>
                                                        </div>
                                            
                                                        <!-- csrf token -->
                                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                                        <button class="btn btn-primary btn-block" type="submit"><?php echo trans('submit') ?></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>