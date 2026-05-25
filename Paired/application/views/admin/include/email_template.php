<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/summernote/summernote-bs4.min.css">



<div class="row mb-4">
  <div class="col-md-12">
    <div class="form-group">
      <label class="font-weight-bold"><?php echo trans('subject') ?></label>
      <input type="text" class="form-control" name="subject" value="<?php if(isset($template->subject)){echo html_escape($template->subject);}  ?>">
    </div>
  </div>

  <!-- <div class="col-md-12">
    <div class="form-group">
      <label class="font-weight-bold">Variables</label>
      <p class="bg-light mb-0 varib"><?php if(isset($template->variables)){echo html_escape($template->variables);}  ?></p>
    </div>
  </div> -->

  <div class="col-md-12">
    <div class="form-group">
      <label class="font-weight-bold"><?php echo trans('variables') ?></label>
      <textarea class="form-control"><?php if(isset($template->variables)){echo html_escape($template->variables);}  ?></textarea>
      <!-- <input type="text" class="form-control" name="" value=""> -->
    </div>
  </div>
  
  <div class="col-md-12">
    <div class="form-group">
        <label class="font-weight-bold"><?php echo trans('body') ?></label>
        <textarea id="ckEditor" class="form-control summernote" name="body" rows="6"><?php if(isset($template->body)){echo html_escape($template->body);}  ?></textarea>
    </div>

    <div class="alert bg-danger-soft brd-4" role="alert">
        <i class="fa fa-info-circle"></i> <?php echo trans('variable-write-instruction') ?>   
    </div>
    
  </div>

</div>


<script src="<?php echo base_url() ?>assets/admin/plugins/summernote/summernote-bs4.min.js"></script>


<script type="text/javascript">
  $('.summernote').summernote({
        toolbar: [
          // [groupName, [list of button]]
          ['style', ['bold', 'italic', 'underline', 'clear']],
          ['font', ['strikethrough', 'superscript', 'subscript']],
          ['fontsize', ['fontsize']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['height', ['height']],
          ['insert', ['link']],
          ['view', ['codeview']],
        ]
  });
</script>