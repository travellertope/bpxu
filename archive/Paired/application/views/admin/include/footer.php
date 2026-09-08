
  <?php include APPPATH.'views/include/js_msg_list.php'; ?>

  <?php $success = $this->session->flashdata('msg'); ?>
  <?php $error = $this->session->flashdata('error'); ?>
  <input type="hidden" id="filter" value="<?php if (isset($page_title) && $page_title == 'Templates'){echo "1";}else{echo "0";}  ?>">
  <input type="hidden" id="success" value="<?php if(isset($success)){echo html_escape($success);} ?>">
  <input type="hidden" id="error" value="<?php if(isset($error)){echo html_escape($error);} ?>">  
  <input type="hidden" id="lc" value="<?php echo strlen(settings()->ind_code); ?>">
  <input type="hidden" class="user_interval" value="<?php echo user()->intervals ?>">
  <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
  <?php echo html_escape($this->session->unset_userdata('msg')); $this->session->unset_userdata('error'); ?>

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      <?php echo trans('version') ?> <?php echo html_escape(settings()->version) ?>
    </div>
    <!-- Default to the left -->
    <strong><?php echo trans('copyright') ?> &copy; <?php echo date('Y') ?>  <?php echo trans('all-rights-reserved') ?>.

  </footer>
</div>
<!-- wrapper -->


<!-- jQuery -->
<script src="<?php echo base_url() ?>assets/front/libs/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url() ?>assets/front/libs/popper.js/dist/umd/popper.min.js"></script>
<script src="<?php echo base_url() ?>assets/front/libs/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/bootstrap-datepicker.min.js"></script>

<!-- DataTables -->
<script src="<?php echo base_url() ?>assets/admin/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url() ?>assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/validation.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/sweet-alert.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/bootstrap-tagsinput.js"></script>
<!-- bs-custom-file-input -->
<script src="<?php echo base_url() ?>assets/admin/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- animation js -->
<script src="<?php echo base_url() ?>assets/front/js/aos.js"></script>
<!-- Summernote -->
<script src="<?php echo base_url() ?>assets/admin/plugins/summernote/summernote-bs4.js"></script>
<!-- Icon Picker -->
<script src="<?php echo base_url() ?>assets/admin/js/bootstrapicon-iconpicker.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/tata.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/admin.js?var=<?= settings()->version ?>&time=<?=time();?>"></script>
<script src="<?php echo base_url() ?>assets/admin/js/clipboard.min.js"></script>

<!-- select2 js -->
<script src="<?php echo base_url()?>assets/admin/plugins/select2/js/select2.full.min.js"></script>
<!-- nice select js -->
<script src="<?php echo base_url()?>assets/admin/js/nice-select.min.js"></script>
<script src="<?php echo base_url()?>assets/admin/js/tata.js"></script>

<!-- timepicker -->
<script src="<?php echo base_url()?>assets/admin/js/timepicker.min.js"></script>

<script src="<?php echo base_url() ?>assets/admin/js/bootstrap-colorpicker.min.js"></script>

<?php if (isset($page_title) && $page_title != 'Verification'): ?>
  <script src="<?php echo base_url() ?>assets/admin/js/jquery-ui.min.js"></script>
<?php endif ?>

<!-- lightbox js -->
<script src="<?php echo base_url() ?>assets/admin/lightbox/src/js/lightbox.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<!-- calendar js -->
<?php if (isset($page_title) && $page_title == 'Calendars'): ?>
<?php include'calendar-js.php'; ?>
<?php endif ?>

<!-- stripe js -->
<?php include'stripe-js.php'; ?>


<?php if (isset($page_title) && $page_title == 'Holidays'): ?>
  <?php $this->load->view('admin/include/datepicker-js.php'); ?>
<?php endif ?>

<!-- chart js -->
<?php if (isset($page) && $page == 'Dashboard'): ?>
  <?php $this->load->view('admin/include/charts'); ?>
<?php elseif (isset($page) && $page == 'Reports'): ?>
  <?php $this->load->view('admin/include/user-charts'); ?>
<?php endif ?>

<script type="text/javascript">
  function CopyMe(TextToCopy) {
    var TempText = document.createElement("input");
    TempText.value = TextToCopy;
    document.body.appendChild(TempText);
    TempText.select();
    
    document.execCommand("copy");
    document.body.removeChild(TempText);
    $(".copy_profile_btn").html('Copied').delay(3000).slideUp('slow');
    //window.location.reload();
    //alert("Copied the text: " + TempText.value);
  }
</script>



</body>
</html>
