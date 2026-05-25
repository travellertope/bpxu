<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  var razorpay_options = {
    key: "<?php echo html_escape($key_id); ?>",
    amount: "<?php echo html_escape($total); ?>",
    name: "<?php echo html_escape($name); ?>",
    description: "Package: <?php echo html_escape($productinfo) ?>",
    netbanking: true,
    currency: "<?php echo settings()->currency_code; ?>",
    prefill: {
      name:"<?php echo html_escape($card_holder_name); ?>",
      email: "<?php echo html_escape($email); ?>",
      contact: "<?php echo html_escape($phone); ?>"
    },
    notes: {
      soolegal_order_id: "<?php echo html_escape($merchant_order_id); ?>",
    },
    handler: function (transaction) {
        document.getElementById('razorpay_payment_id').value = transaction.razorpay_payment_id;
        document.getElementById('razorpay-form').submit();
    },
    "modal": {
        "ondismiss": function(){
            location.reload()
        }
    }
  };
  var razorpay_submit_btn, razorpay_instance;

  function razorpaySubmit(el){
    if(typeof Razorpay == 'undefined'){
      setTimeout(razorpaySubmit, 200);
      if(!razorpay_submit_btn && el){
        razorpay_submit_btn = el;
        el.disabled = true;
        el.value = 'Please wait...';  
      }
    } else {
      if(!razorpay_instance){
        razorpay_instance = new Razorpay(razorpay_options);
        if(razorpay_submit_btn){
          razorpay_submit_btn.disabled = false;
          razorpay_submit_btn.value = "Pay Now";
        }
      }
      razorpay_instance.open();
    }
  }  
</script>