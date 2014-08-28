<?php
function customnextpage_translation() {
	$strings = array(
		'buttonTitle' => esc_js( __( 'Custom Nextpage Shortcode', 'custom-nextpage' ) ),
		'popupTitle'  => esc_js( __( 'Custom Nextpage Shortcode', 'custom-nextpage' ) ),
	);
	$locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.customnextpage", ' . json_encode( $strings ) . ");\n";
	return $translated;
}

$strings = customnextpage_translation();