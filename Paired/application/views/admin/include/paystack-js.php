
<script>
  <?php if (isset($paystack_type) && $paystack_type == 'user'): ?>
    
    <?php $public_key = $user->paystack_public_key; ?>

    function payWithPaystack(){
      var handler = PaystackPop.setup({
        key: '<?php echo html_escape($public_key); ?>',
        email: '<?php echo html_escape($email); ?>',
        amount: '<?php echo html_escape($price * 100); ?>',
        currency: '<?php echo get_currency_by_country($company->country)->currency_code; ?>',
        ref: ''+Math.floor((Math.random() * 1000000000) + 1), 
        callback: function(response){
            window.location.href = `<?php echo base_url().'paystack/verify_customer_payment/' ?>${response.reference}/<?php echo html_escape($type_id).'/'.html_escape($type); ?>/<?php echo html_escape($amount); ?>`;                    
        },
        onClose: function(){
            alert('window closed');
        }
      });
      handler.openIframe();
    }
  <?php else: ?>
    function payWithPaystack(){
      var handler = PaystackPop.setup({
        key: '<?php echo html_escape(settings()->paystack_public_key); ?>',
        email: '<?php echo html_escape($email); ?>',
        amount: '<?php echo html_escape($price * 100); ?>',
        currency: '<?php echo html_escape(settings()->currency_code); ?>',
        ref: ''+Math.floor((Math.random() * 1000000000) + 1), 
        callback: function(response){
            window.location.href = `<?php echo base_url().'paystack/verify_customer_payment/' ?>${response.reference}/<?php echo html_escape($booking->id); ?>/<?php echo html_escape($price); ?>`;                    
        },
        onClose: function(){
            alert('window closed');
        }
      });
      handler.openIframe();
    }
  <?php endif ?>
</script>