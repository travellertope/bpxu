<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo trans('payment-receipt') ?></title>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/admin_default.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-invoice.css">
    </head>

    <body>

        <div class="main-box">
        
            <div class="invoice-box print_area <?php if(isset($page_title) && $page_title != 'Export'){echo "br1 shadow";} ?>">

                <table cellpadding="0" cellspacing="0">
                    <tr class="top">
                        <td colspan="3">
                            <table>
                                <tr>

                                    <?php if (!empty(settings()->logo)): ?>
                                        <td class="title">
                                            <img src="<?php echo base_url(settings()->logo) ?>" class="w-40">
                                        </td>
                                    <?php endif ?>
                                    
                                    <td>
                                        <?php echo trans('invoice') ?> - <?php echo html_escape(sprintf('%02d', $user->id)) ?><br>
                                        <?php echo trans('order-no') ?>: <?php echo html_escape($user->puid) ?> <br> 
                                        <?php echo trans('date') ?>: <?php echo my_date_show($user->created_at) ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <?php $price = $user->amount;  ?>
                    
                    <tr class="information">
                        <td colspan="3">
                            <table>
                                <tr>
                                    <td>
                                        <?php echo get_by_id($user->user_id, 'users')->name ?><br>
                                        <?php echo get_by_id($user->user_id, 'users')->email ?>
                                    </td>
                                    
                                    <td>
                                        <strong><?php echo get_by_id($user->customer_id, 'users')->name ?></strong><br>
                                          <p class="mb-0"><?php echo get_by_id($user->customer_id, 'users')->email ?></p>
                                        <?php if (!empty(get_by_id($user->customer_id, 'users')->phone)): ?>
                                          <p class="mb-1"><?php echo get_by_id($user->customer_id, 'users')->phone ?></p>
                                        <?php endif ?>
                                        
                                        <?php if ($user->status == 'pending'): ?>
                                          <span class="float-right badge badge-danger-soft"> <?php echo trans('payment') ?> - <?php echo trans('pending') ?></span>
                                        <?php elseif ($user->status == 'verified'): ?>
                                          <span class="float-right badge badge-success-soft"> <?php echo trans('payment') ?> - <?php echo trans('paid') ?></span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                

                    <tr class="heading">
                        <td>
                            <?php echo trans('session') ?>
                        </td>
                        
                        <td>
                            <?php echo trans('price') ?>
                        </td>

                        <td class="text-right">
                            <?php echo trans('total') ?>
                        </td>
                    </tr>
                    
                    <?php $session = get_by_id($user->session_id, 'sessions'); ?>
                    <?php 
                      $booking = get_by_id($user->booking_id, 'session_booking');

                      $price = $session->price;
                      

                      $check_coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
                      
                          if (!empty($check_coupon)):
                              $discount = $check_coupon->discount;
                              $amount = $price - ($price * ($discount / 100));
                              $discount_amount = abs($amount - $price);
                          else:
                              $discount = 0;
                              $discount_amount = 0;
                              $amount = $price;
                          endif;
                    ?>


                    <tr class="item">
                      <td>
                        <?php echo html_escape($session->name) ?> <br> <span class="fs-12"><?php echo html_escape($session->duration).' '.'Minutes'; ?></span>
                        
                      </td>

                      <td>

                        

                        <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?><?php echo number_format($session->price, settings()->num_format) ?> <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                            
                      </td>

                      <td class="text-right"><?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?><?php echo number_format($price, settings()->num_format) ?> <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?></td>
                    </tr>
                    
                    
                    <?php if ($discount > 0): ?>
                    <tr class="item">
                        <td></td>
                        <td class="text-right"><strong><?php echo html_escape($discount) ?>% <?php echo trans('off') ?></strong></td>
                        <td class="text-right"><span><strong><?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?><?php echo number_format($discount_amount, settings()->num_format) ?> <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?></strong></span></td>
                    </tr>
                    <?php endif ?>

                    <tr class="item">
                        <td></td>
                        <td class="text-right"><strong><?php echo trans('sub-total') ?></strong></td>
                        <td class="text-right"><span><strong><?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?><?php echo html_escape(number_format($amount,settings()->num_format)) ?> <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?></strong></span></td>
                    </tr>

                    <tr class="total">
                        <td></td>
                        <td class="text-right"><strong><?php echo trans('total') ?></strong></td>
                        <td class="text-right"><span><strong><?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?><?php echo html_escape(number_format($amount,settings()->num_format)) ?> <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?></strong></span></td>
                    </tr>

                    <tr>
                      <td></td>
                    </tr>
                    <tr>
                      <td></td>
                    </tr>
                    <tr>
                      <td></td>
                    </tr>
                    <tr>
                      <td></td>
                    </tr>

                </table>

                <div class="pwf">
                  <?php echo trans('powered-by') ?> - <?php echo html_escape(settings()->site_name) ?>
                </div>
            </div>
        </div>



    </body>
</html>
