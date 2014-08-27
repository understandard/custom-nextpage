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
			global $wp_version;
			add_action( 'admin_print_scripts-post.php', array( &$this, 'admin_print_scripts' ), 999 );
			add_filter( 'tiny_mce_version', array( &$this, 'tiny_mce_version' ) );
			add_filter( 'mce_external_plugins', array( &$this, 'mce_external_plugins' ) );
			add_filter( 'mce_buttons_3', array( &$this, 'mce_buttons_3' ) );
			if ( version_compare( $wp_version, '3.9', '<' ) )
				add_action( 'admin_footer', array( &$this, 'editor_dialog' ) );
		}
	}
	// Admin
	function admin_print_scripts() {
		global $wp_version;
		if ( version_compare( $wp_version, '3.9', '<' ) )
			wp_enqueue_style( 'admin-customnextpage', $this->plugin_dir_url . '/css/admin-customnextpage.css', array(), CustomNextPage::VERSION );
	}

	function mce_buttons_3($buttons) {
		array_push( $buttons, 'customnextpage');
		return $buttons;
	}
	function mce_external_plugins($plugin_array) {
		global $wp_version;
		if ( version_compare( $wp_version, '3.9', '>=' ) ) {
			//$plugins_array['table'] = get_template_directory_uri() . '/tinymce4/plugins/table/plugin.min.js';
			$plugin_array['customnextpage']  =  $this->plugin_dir_url . '/includes/tinymce/plugins/customnextpage/plugin.js';
		} else {
			$plugin_array['customnextpage']  =  $this->plugin_dir_url . '/includes/tinymce/plugins/customnextpage/editor_plugin.js';
		}
		return $plugin_array;
	}
	function tiny_mce_version($version) {
		var_dump($version);
		return ++$version;
	}
	function editor_dialog() { ?>
		<div style="display:none;">
			<form id="customnextpage-dialog">
				<div id="customnextpage-selector">
					<div id="customnextpage-options">
						<div>
							<label><span><?php _e( 'Title' ); ?></span><input id="customnextpage-title-field" type="text" name="title" /></label>
						</div>
					</div>
				</div>
				<div class="submitbox">
					<div id="customnextpage-update">
						<input type="submit" value="<?php esc_attr_e( 'Insert' ); ?>" class="button-primary" id="customnextpage-submit" name="customnextpage-submit">
					</div>
					<div id="customnextpage-cancel">
						<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
					</div>
				</div>
			</form>
		</div>
	<?php }
}
