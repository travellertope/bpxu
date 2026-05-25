<div class="content-wrapper">
    <?php $this->load->view('admin/include/breadcrumb'); ?>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="cards">
                    <div class="card-bodys">
                        <div class="row">
                            <div class="col-md-4">
                                <h5><i class="far fa-calendar-alt"></i> <?php echo trans('holidays') ?></h5>
                                <div class="mt-2" id="holiday_picker"></div>
                            </div>
                            <div class="col-md-6 pl-md-5"><br>
                                <div class="hol-list mt-3 py-4 px-4 card-body">
                                    <h6 class="mb-3 text-muted"><?php echo trans('disabled-days') ?></h6>
                                    <?php  $holidays_list = json_decode($user->holidays, true); ?>
                                    <?php if (!empty($holidays_list)): ?>
                                        <?php foreach ($holidays_list as $list): ?>
                                            <span class="btn bg-danger text-white border-danger disabled mr-3 mb-3 fs-13"><i class="fas fa-calendar-alt"></i> <?php echo my_date_show($list) ?></span>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
