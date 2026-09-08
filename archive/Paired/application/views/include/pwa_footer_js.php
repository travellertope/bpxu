<!-- PWA -->
<script src="<?= base_url(); ?>assets/pwa/sw.js"></script>
<script>
    function onLoad() {
        if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('/sw.js')
            .then(function (reg) {
              console.log("Service worker has been registered for scope "+ reg.scope);
            }).catch(function (err) {
              console.log("No", err)
            });
        }
    }
    //window.addEventListener('load', onLoad);
    
    if (window.matchMedia('(display-mode: standalone)').matches) {
      $('#installPwa').addClass('d-none');
    } else {
      if($(window).width() < 768){
          $('#installPwa').addClass('d-block');
      }
    }


    let deferredPrompt;

    // Function to show install prompt when the button is clicked
    document.getElementById('installPwa').addEventListener('click', () => {
      if (deferredPrompt) {
        // Show the installation prompt
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the install prompt');
          } else {
            console.log('User dismissed the install prompt');
          }
          // Reset the deferredPrompt variable
          deferredPrompt = null;
        });
      }
    });

    // Event listener to capture the install prompt event
    window.addEventListener('beforeinstallprompt', (event) => {
      // Prevent Chrome 76 and later from showing the mini-infobar
      event.preventDefault();
      // Stash the event so it can be triggered later.
      deferredPrompt = event;
    });
    

</script>
<!-- PWA END-->