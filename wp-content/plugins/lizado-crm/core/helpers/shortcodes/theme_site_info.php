<?php
	add_shortcode("theme_site_title", "theme_site_title");
	add_shortcode("theme_site_domain", "theme_site_domain");
	add_shortcode("theme_site_info", "theme_site_info");
	function theme_site_domain(){
		$site_info = get_field('site_info','option');
		return $site_info['site_domain'];
	}
	function theme_site_title(){
		$site_info = get_field('site_info','option');
		return $site_info['site_title'];
	}
	function theme_site_info(){
		$site_info = get_field('site_info','option');

		ob_start();
		?>
		<p><?= $site_info['site_phone'];?><br />
		<?= $site_info['site_open_hours'];?><br />
		<?= $site_info['contact_email_address'];?><br />
		<?= $site_info['site_address'];?></p>
		<?php
		return ob_get_clean();
	}
?>