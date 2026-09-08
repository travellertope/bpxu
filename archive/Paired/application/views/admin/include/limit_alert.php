<?php if (isset($limit) && $limit == TRUE): ?>
  <div class="empty-data text-center card-bodys bg-danger-soft">
    <h2 class="text-danger"><i class="bi bi-x-circle-fill"></i></h2>
    <p class="text-danger fs-14 mt-4"><?php echo trans('you-have-reached-the-maximum-limit') ?> <?php echo html_escape($limit_total) ?> ! <?php echo trans('please-upgrade-your-plan') ?></p>

    <a href="<?php echo base_url('admin/subscription') ?>" class="btn btn-outline-danger btn-sm mt-2"><i class="bi bi-rocket"></i> <?php echo trans('upgrade-plan') ?></a>
  </div>
<?php endif ?>