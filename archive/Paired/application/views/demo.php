<!DOCTYPE html>
<html>
<head>
	<title><?php echo trans('aoxio-demo') ?></title>
     <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
  	<link href="<?php echo base_url() ?>assets/front/css/template.css" rel="stylesheet">
  	<link href="<?php echo base_url() ?>assets/front/css/template.min.css" rel="stylesheet">
  	<link href="https://fonts.googleapis.com/css?family=DM+Sans:300,400,600,700,800,900&amp;display=swap" rel="stylesheet">
</head>

<body>

	<div class="pt-20">     
		<div class="text-center pt-20">
			<img width="150px" src="<?php echo base_url(settings()->logo) ?>" alt="logo">
			<p class="text-muted fs-18 mt-3"><?php echo settings()->site_title ?></p><br>
			<a target="_blank" href="<?php echo base_url() ?>" class="btn btn-primary fs-16 font-weight-bold"><?php echo trans('check-demo') ?></a>
		</div>
	</div>

	<script src="<?php echo base_url() ?>assets/front/js/jquery-2.2.4.min.js"></script>
	<script src="<?php echo base_url() ?>assets/front/js/bootstrap.min.js"></script>

</body>
</html>