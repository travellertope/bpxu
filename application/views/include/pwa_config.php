<meta name="mobile-wep-app-capable" content="yes">
<meta name="apple-mobile-wep-app-capable" content="yes">

<?php 
	$thumb = !empty(settings()->pwa_logo)? settings()->pwa_logo :"assets/pwa/logo-bk.png";
	$pwa_data = [
		'img_144' => resize_img($thumb,144,144),
		'img_192' => resize_img($thumb,192,192),
		'img_512' => resize_img($thumb,512,512),
		'theme_color' => !empty(settings()->site_color)?settings()->site_color:'6777ef',
		'background_color' => !empty(settings()->site_color)?settings()->site_color:'6777ef',
		'title' => settings()->site_name,
		'url' => base_url(),
		'name' => settings()->site_name,
	];
	$pwa = serialize($pwa_data);
?>
<meta name="apple-mobile-web-app-status-bar-style" content="#<?= $pwa_data['theme_color'] ;?>">
<link rel="apple-touch-icon" href="<?=$pwa_data['img_144'] ;?>" type="image/png" sizes="144x144">
<link rel="apple-touch-icon" href="<?=$pwa_data['img_192'] ;?>" type="image/png" sizes="192x192">
<link rel="apple-touch-icon" href="<?=$pwa_data['img_512'] ;?>" type="image/png" sizes="512x512">

<link rel="icon" href="<?= $pwa_data['img_512'] ;?>" type="image/png" sizes="512x512">
<link rel="icon" href="<?= $pwa_data['img_144'] ;?>" type="image/png" sizes="144x144">

<link rel="manifest" href="<?= base_url(); ?>assets/pwa/manifest.php?pwa_data=<?= urlencode($pwa);?>&time=<?= time() ;?>" type="text/html">