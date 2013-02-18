<?php
	require_once('logo_cache.php');

	$dir = "carrier_logos/";
	$not_found_image = $dir . 'not_found.gif';
	$cached_image = "logos_cached.jpg";
	$cached_logos = "logos_cached.xml";
	
	$cache = new LogoCache($dir, $cached_logos, $not_found_image, $cached_image);
	$cache->sendImage();
?>
