<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="container">
        <div class="row">

          <div class="col-md-8 offset-md-2 text-center mt-3">

            <?php if($kyc->status != 1): ?>
              <h3><i class="bi bi-person-bounding-box"></i><?php echo trans('kyc-verification') ?></h3>
              <p><?php echo trans('kyc-document-requirments') ?></p>
            <?php endif ?>

            <div class="mb-2">
              <?php if(!empty($kyc)): ?>
                <?php if($kyc->status == 0): ?>
                  <div class="alert bg-warning-soft mt-5" role="alert">
                    <p class="mb-0"><i class="bi bi-info-circle"></i> <?php echo trans('kyc-pending-status') ?></p>
                  </div>
                <?php elseif($kyc->status == 1): ?>
                  <div class="alert bg-success-soft mt-5 mb-5 p-5" role="alert">
                    <p class="mb-0"><i class="bi bi-patch-check-fill fa-3x"></i> <p class="font-weight-bold fs-20"><?php echo trans('congratulations') ?></p> <br><?php echo trans('kyc-approve-status') ?></p><br>
                  </div>
                <?php elseif($kyc->status == 2): ?>
                  <?php if ($kyc->is_preview == 0): ?>
                    <div class="alert bg-danger-soft mt-5 mb-2" role="alert">
                      <p class="mb-0"><i class="bi bi-exclamation-circle"></i> <?php echo trans('kyc-reject-status') ?></p>
                    </div>
                  <?php else: ?>
                    <div class="alert bg-warning-soft mt-5" role="alert">
                      <p class="mb-0"><i class="bi bi-info-circle"></i> <?php echo trans('kyc-pending-status') ?></p>
                    </div>
                  <?php endif ?>
                <?php endif; ?>

                <?php if($kyc->status == 2): ?>
                  <div class="mt-3 card-body text-left">
                    <p class="mb-0 fs-14 text-muted"><i class="bi bi-info-circle-fill"></i> <?php echo html_escape($kyc->reject_reason); ?></p>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            </div>

          </div>



          <?php if(empty($kyc) || $kyc->status == 2):?>

            <?php 
              if ($kyc->document_type == 'nid') {
                $text = trans('national-id');
              }elseif($kyc->document_type == 'dlicense'){
                $text = trans('driving-license');
              }elseif($kyc->document_type == 'passport'){
                $text = trans('passport');
              }
            ?>

            <div class="col-md-8 offset-md-2 mt-5">
              <div class="my-4"></div>
              <div class="progress my-4 rounded hide">
                <div class="brd-20 progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
                
              <form method="post" action="<?php echo base_url('admin/verification/submit') ?>" class="validate-form" role="form" enctype="multipart/form-data" novalidate>
                <div  class="step1">
                  <h5 class="mb-3"><?php echo trans('upload-a-proof-of-your-identity') ?></h5>

                  <div class="card-body">
                    <div class="form-group">
                        <label for="fullName"><?php echo trans('issuing-countryregion') ?><span class="text-danger">*</span></label>
                        <div class="country_require">
                          <select class="form-control select2 country" name="country_id" required>
                            <option value=""><?php echo trans('select') ?></option>
                            <?php foreach ($countries as $country): ?>
                              <option <?php if(isset($kyc->country_id) && $kyc->country_id == $country->id){echo 'selected';} ?> value="<?php echo html_escape($country->id) ?>"><?php echo html_escape($country->name) ?></option>
                            <?php endforeach ?>                 
                          </select>
                        </div>
                    </div>
                    

                    <div class="form-group">
                      <label for=""><?php echo trans('document-type') ?><span class="text-danger">*</span></label>
                      <select class="form-control doc_type" name="document_type" required>
                        <option class="" value=""><?php echo trans('select') ?></option>
                        <option class="" <?php if(isset($kyc->document_type) && $kyc->document_type == 'nid'){echo 'selected';} ?> value="nid" <?php if(isset($kyc->document_type) && $kyc->document_type != 'nid'){echo 'disabled';} ?>><?php echo trans('national-id') ?></option>

                        <option class="" <?php if(isset($kyc->document_type) && $kyc->document_type == 'passport'){echo 'selected';} ?> value="passport" <?php if(isset($kyc->document_type) && $kyc->document_type != 'passport'){echo 'disabled';} ?>><?php echo trans('passport') ?></option>

                        <option class="" <?php if(isset($kyc->document_type) && $kyc->document_type == 'dlicense'){echo 'selected';} ?> value="dlicense" <?php if(isset($kyc->document_type) && $kyc->document_type != 'dlicense'){echo 'disabled';} ?>><?php echo trans('driving-license') ?></option>
                      </select>
                    </div>

                    <div class="form-group document_number <?php if(!empty($kyc->doc_id_number)){echo 'show';}else{echo 'hide';} ?>">
                      <label><span id="load_document_type_number"></span><span class="text-muted"> <?php echo trans('number') ?></span> <span class="text-danger">*</span></label>
                      <input type="text" class="form-control doc_id_number" name="doc_id_number" value="<?php if(isset($kyc->doc_id_number)){echo html_escape($kyc->doc_id_number);} ?>" required>
                    </div>


                    <div class="row">
                      <div class="col-md-6 mt-3 upload_area_doc_front <?php if(!empty($kyc->front_side_doc) && $kyc->document_type != 'passport'){echo 'show';}else{echo 'hide';} ?>">
                        <div class="form-group">
                          <div class="form-group">
                            <label class="image-require" for="fullName"><?php echo trans('front-side-of-your') ?> <span id="load_document_type_front"><?php echo html_escape($text) ?></span></label>
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" name="front_side_doc" id="image">
                              <label class="custom-file-label document" for="customFile"><i class="bi bi-upload mr-2"> <?php echo trans('upload-image') ?></i> </label>
                            </div>
                            
                          </div>

                          <div class="mt-3" id="imagePreviewContainer"  style="display: <?php if(empty($kyc)){echo 'none';}else{echo 'block';} ?> ;">
                            <div class="mih-100">
                              
                              <?php if(empty($kyc)): ?>
                                <img id="imagePreview" class="img-fluid preview-img" alt="Uploaded Image">
                              <?php endif; ?>


                              <?php if(!empty($kyc)): ?>
                                <img id="imagePreview" src="<?php echo base_url($kyc->front_side_doc) ?>" class="img-fluid preview-img" alt="Uploaded Image">
                              <?php endif; ?>
                            </div>
                          </div>
                          
                        </div>
                      </div>

                      <div class="col-md-6 mt-3 upload_area_doc_back <?php if(!empty($kyc->back_side_doc) && $kyc->document_type != 'passport'){echo 'show';}else{echo 'hide';} ?>">
                        <div class="form-group">
                          <div class="form-group">
                            <label class="image2-require" for="fullName"><?php echo trans('back-side-of-your') ?> <span id="load_document_type_back"><?php echo html_escape($text) ?></span></label>
                            
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" name="back_side_doc" id="image2">
                              <label class="custom-file-label document" for="customFile"><i class="bi bi-upload mr-2"> <?php echo trans('upload-image') ?></i> </label>
                            </div>
                          </div>
                          
                          <div class="mt-3" id="imagePreviewContainer2" style="display: <?php if(empty($kyc)){echo 'none';}else{echo 'block';} ?> ;">
                            <div class="mih-100">
                              <?php if(empty($kyc)): ?>
                                <img id="imagePreview2" class="img-fluid preview-img" alt="Uploaded Image">
                              <?php endif; ?>


                              <?php if(!empty($kyc)): ?>
                                <img id="imagePreview2" src="<?php echo base_url($kyc->back_side_doc) ?>" class="img-fluid preview-img" alt="Uploaded Image">
                              <?php endif; ?>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row upload_area_passport <?php if(!empty($kyc->doc_id_number)&& $kyc->document_type== 'passport'){echo 'show';}else{echo 'hide';} ?>">
                      <div class="col-md-12 mt-3">
                        <div class="form-group">
                          <div class="form-group">
                              <label class="image3-require" for="fullName"><?php echo trans('passport-photo') ?></label>
                              <div class="custom-file">
                                <input type="file" class="custom-file-input" name="passport" id="image3">
                                <label class="custom-file-label document" for="customFile"><i class="bi bi-upload mr-2"> <?php echo trans('upload-image') ?></i> </label>
                              </div>
                          </div>

                          <div class="mt-3" id="imagePreviewContainer3" style="display: <?php if(empty($kyc)){echo 'none';}else{echo 'block';} ?> ;">
                            <div class="mih-100">
                              <?php if(empty($kyc)): ?>
                                <img id="imagePreview3" class="img-fluid preview-img" alt="Uploaded Image">
                              <?php endif; ?>


                              <?php if(!empty($kyc)): ?>
                                <img id="imagePreview3" src="<?php echo base_url($kyc->back_side_doc) ?>" class="img-fluid preview-img" alt="Uploaded Image">
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="upload_area_selfiee <?php if(!empty($kyc->selfiee_with_doc)){echo 'show';}else{echo 'hide';} ?>">
                      <div class="row">
                        <div class="col-md-6 mt-5 border-rights rounded">
                          <div class="form-group pr-2">
                            <div class="form-group">
                                <label class="mb-1 image4-require" for="fullName"><?php echo trans('selfiee-with') ?> <span id="load_document_type_selfie">your <?php echo html_escape($text) ?></span></label>
                                <p class="mb-2 small text-muted text-italic">* <?php echo trans('make-sure-your-document-and-face-in-this-same-frame') ?></p>
                                <div class="custom-file">
                                  <input type="file" class="custom-file-input" name="selfiee_with_doc" id="image4">
                                  <label class="custom-file-label document" for="customFile"><i class="bi bi-upload mr-2"> <?php echo trans('upload-image') ?></i></label>
                                </div>
                            </div>

                            <div class="mt-4" id="imagePreviewContainer4" style="display: <?php if(empty($kyc)){echo 'none';}else{echo 'block';} ?> ;">
                              <div class="mih-100">
                                <?php if(empty($kyc)): ?>
                                  <img id="imagePreview4" class="img-fluid preview-img" alt="Uploaded Image">
                                <?php endif; ?>

                                <?php if(!empty($kyc)): ?>
                                  <img id="imagePreview4" src="<?php echo base_url($kyc->back_side_doc) ?>" class="img-fluid preview-img" alt="Uploaded Image">
                                <?php endif; ?>

                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mt-3 text-center">
                          <div class="border-dashed-1">
                            <img class="p-4 border-dasheds bg-warning-soft rounded mt-5" width="60%" src="<?php echo base_url('assets/images/selfie.png') ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                    

                    <div class="row mt-5 file_requirements hide">
                      <div class="col-md-12">
                        <div class="p-4 bg-light border-dashed rounded fs-12">
                          <p class="mb-2 text-danger "><i class="bi bi-check-circle"></i> <?php echo trans('file-accept-type') ?></p>
                          <p class="mb-2 text-danger "><i class="bi bi-check-circle"></i> <?php echo trans('face-must-be-clear-visible') ?></p>
                          <p class="mb-0 text-danger "><i class="bi bi-check-circle"></i> <?php echo trans('document-should-be-good-condition-valid-period') ?> </p>
                        </div>
                      </div>
                    </div>
                    
                    <a href="#" class="btn btn-primary btn-lg fs-15 btn-block next-step mt-5"><?php echo trans('continue') ?> <i class="bi bi-arrow-right"></i></a href="#">
                  </div>
                </div>


                <div  class="step2 hide">
                  <h5 class="mb-3"><?php echo trans('personal-information') ?></h5>

                  <div class="card-body">
                    <div class="form-group">
                      <label><?php echo trans('first-name') ?> <span class="text-muted"><?php echo trans('as-on-document') ?></span> <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="first_name" value="<?php if(isset($kyc->first_name)){echo html_escape($kyc->first_name);} ?>" required>
                    </div>

                    <div class="form-group">
                      <label><?php echo trans('last-name') ?></label>
                      <input type="text" class="form-control" name="last_name" value="<?php if(isset($kyc->last_name)){echo html_escape($kyc->last_name);} ?>">
                    </div>

                    <div class="form-group">
                      <label><?php echo trans('date-of-birth') ?> <span class="text-danger">*</span></label>
                      <input type="text" class="form-control bs-datepicker" name="birth_date" value="<?php if(isset($kyc->birth_date)){echo html_escape($kyc->birth_date);} ?>" required>
                    </div>


                    <div class="form-group">
                      <label><?php echo trans('address') ?></label>
                      <textarea class="form-control" name="address" rows="4"></textarea>
                    </div>

                    <div>
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="agree" class="custom-control-input agree_btn"
                            id="terms-condition" required>
                        <label class="custom-control-label" for="terms-condition">
                            <?php echo trans('acknowledge-checkbox-title') ?>
                        </label>
                      </div>
                    </div>


                    <div class="text-center">
                      <input type="hidden" name="id" value="<?php if(!empty($kyc)){echo html_escape($kyc->id);} ?>">
                       <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">
                     

                      <button type="submit" class="btn btn-primary btn-lg fs-15 btn-block mt-5 pull-right kyc_submit_btn" disabled><?php echo trans('submit') ?> <i class="bi bi-check-circle"></i></button><br>

                      <a href="#" class="text-muted prev-step mt-5"><i class="bi bi-arrow-left"></i> <?php echo trans('back-to-previous') ?></a>
                    </div>
                  </div>

                </div>
              </form>
            </div>
          <?php endif; ?>




          <?php if(!empty($kyc) && $kyc->status != 1 && $kyc->status != 2):?>
            <div class="col-md-12">
              <div class="my-4"></div>
                  
                <div class="row">
                  <div class="col-md-4 mb-5">
                      <h5 class="mb-3"><?php echo trans('personal-information') ?></h5>

                      <ul class="list-group card-body p-0">
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 bbm-1">
                          <span class="fs-14 font-weight-bold"><?php echo trans('name') ?></span>
                          <span class="badge badge-secondary badge-pill fs-13"><?php echo html_escape($kyc->first_name) ?> <?php echo html_escape($kyc->last_name) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 bbm-1">
                          <span class="fs-14 font-weight-bold"><?php echo trans('country') ?></span>
                          <span class="badge badge-secondary badge-pill fs-13"><?php echo get_by_id($kyc->country_id,'country')->name ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 bbm-1">
                          <span class="fs-14 font-weight-bold"><?php echo trans('date-of-birth') ?></span>
                          <span class="badge badge-secondary badge-pill fs-13"><?php echo my_date_show($kyc->birth_date) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 bbm-1">
                          <?php 
                            if ($kyc->document_type == 'nid') {
                              $text = trans('national-id');
                            }elseif($kyc->document_type == 'dlicense'){
                              $text = trans('driving-license');
                            }elseif($kyc->document_type == 'passport'){
                              $text = trans('passport');
                            }
                          ?>
                          <span class="fs-14 font-weight-bold"><?php echo html_escape($text) ?> <?php echo trans('number') ?></span>
                          <span class="badge badge-secondary badge-pill fs-13"><?php echo html_escape($kyc->doc_id_number) ?></span>
                        </li>
                      </ul>
                  </div>

                  <div class="col-md-8">
                    
                    <h5 class="mb-3"><?php echo trans('document') ?> (<?php echo html_escape($text) ?>)</h5>
                    <div class="card-body">
                      <div class="row">
                       
                        <?php if($kyc->document_type == 'nid' || $kyc->document_type == 'dlicense'): ?>
                          <div class="col-md-6 text-left">
                            <h6 class="text-left font-weight-normal fs-14"><?php echo trans('front-side-of') ?> <?php echo html_escape($text) ?></h6>
                            <div class="doc_img" style="background-image: url(<?php echo base_url($kyc->front_side_doc) ?>)">
                            </div>
                          </div>

                          <div class="col-md-6 text-left">
                            <h6 class="text-left font-weight-normal fs-14"><?php echo trans('back-side-of') ?> <?php echo html_escape($text) ?></h6>
                            <div class="doc_img" style="background-image: url(<?php echo base_url($kyc->back_side_doc) ?>)">
                            </div>
                          </div>
                        <?php endif; ?>

                        
                        <?php if($kyc->document_type == 'passport'): ?>
                          <div class="col-md-6 text-left mt-2">
                            <h6 class="text-left font-weight-normal fs-14 mt-2"><?php echo trans(' image-of') ?> <?php echo html_escape($text) ?></h6>
                            <div class="doc_img" style="background-image: url(<?php echo base_url($kyc->passport) ?>)">
                            </div>
                          </div>
                        <?php endif; ?>


                        <div class="col-md-6 mt-2">
                          <h6 class="text-left font-weight-normal fs-14 mt-2"><?php echo trans('selfiee-with') ?> <?php echo html_escape($text) ?></h6>
                          <div class="doc_img" style="background-image: url(<?php echo base_url($kyc->selfiee_with_doc) ?>)">
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
              
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
