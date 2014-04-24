<?php
class CustomNextPageEditor {

	private $plugin_basename;
	private $plugin_dir_path;
	private $plugin_dir_url;

	public function __construct() {
		$this->plugin_basename       = CustomNextPage::plugin_basename();
		$this->plugin_dir_path       = CustomNextPage::plugin_dir_path();
		$this->plugin_dir_url        = CustomNextPage::plugin_dir_url();

		if ( is_admin() ) {
			add_filter('tiny_mce_version', array( &$this, 'tiny_mce_version' ) );
			add_filter('mce_external_plugins', array( &$this, 'mce_external_plugins' ) );
			add_filter('mce_buttons_3', array( &$this, 'mce_buttons_3' ) );
		}
	}

	function mce_buttons_3($buttons) {
		array_push( $buttons, 'customnextpage');
		return $buttons;
	}
	function mce_external_plugins($plugin_array) {
		$plugin_array['customnextpage']  =  $this->plugin_dir_url . '/includes/tinymce/editor_plugin.js';
		return $plugin_array;
	}
	function tiny_mce_version($version) {
		return ++$version;
	}
}
