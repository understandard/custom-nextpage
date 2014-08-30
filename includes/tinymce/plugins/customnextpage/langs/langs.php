<?php
function customnextpage_translation() {
	$custom_next_page = new CustomNextPageInit();
	$strings    = array(
		'buttonTitle' => esc_js( __( 'Custom Nextpage Shortcode', $custom_next_page->domain ) ),
		'popupTitle'  => esc_js( __( 'Custom Nextpage Shortcode', $custom_next_page->domain ) ),
	);
	$locale     = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.customnextpage", ' . json_encode( $strings ) . ");\n";
	return $translated;
}

$strings = customnextpage_translation();