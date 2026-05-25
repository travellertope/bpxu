<!DOCTYPE html>
<html>
<head><title><?php echo trans('404-not-found') ?></title>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
  	<link href="<?php echo base_url() ?>assets/front/css/template.css" rel="stylesheet">
  	<link href="https://fonts.googleapis.com/css?family=DM+Sans:300,400,600,700,800,900&amp;display=swap" rel="stylesheet">
</head>

<body>

	<div>     
		<div class="text-center">
			<img src="<?php echo base_url() ?>/assets/front/img/404.jpg">
			<h4 class="font-weight-normal"><?php echo trans('error-404') ?></h4><br>
			<a href="<?php echo base_url() ?>" class="btn btn-primary"> <i class="flaticon-left-arrow" aria-hidden="true"></i> <?php echo trans('back-to-home') ?> </a>
		</div>
	</div>

	<!-- jQuery -->
	<script src="<?php echo base_url() ?>assets/front/libs/jquery/dist/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="<?php echo base_url() ?>assets/front/libs/popper.js/dist/umd/popper.min.js"></script>
	<script src="<?php echo base_url() ?>assets/front/libs/bootstrap/dist/js/bootstrap.min.js"></script>

</body>
</html>