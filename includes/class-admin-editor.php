<?php
class CustomNextPageEditor extends CustomNextPageInit {

	public function __construct() {
		parent::__construct();

		if ( is_admin() ) {
			global $wp_version;
			add_action( 'admin_print_scripts-post.php', array( &$this, 'admin_print_scripts' ), 999 );
			add_action( 'admin_print_scripts-post-new.php', array( &$this, 'admin_print_scripts' ), 999 );
			add_filter( 'tiny_mce_version', array( &$this, 'tiny_mce_version' ) );
			add_filter( 'mce_external_plugins', array( &$this, 'mce_external_plugins' ) );
			add_filter( 'mce_buttons_3', array( &$this, 'mce_buttons_3' ) );
			add_filter( 'mce_external_languages', array( &$this, 'mce_external_languages') );
			if ( version_compare( $wp_version, '3.9', '<' ) ) {
				add_action( 'admin_footer', array( &$this, 'editor_dialog' ) );
			}
			add_action( 'admin_enqueue_scripts', array( &$this, 'quicktags' ) );
		}
	}

	// Admin
	function admin_print_scripts() {
		wp_enqueue_style( 'admin-customnextpage', CUSTOM_NEXTPAGE_URL . '/css/admin-customnextpage.css', array(), $this->version );
	}

	function mce_external_languages( $locales ) {
		$locales['customnextpage'] = CUSTOM_NEXTPAGE_DIR . '/includes/tinymce/plugins/customnextpage/langs/langs.php';
		return $locales;
	}

	function mce_buttons_3($buttons) {
		array_push( $buttons, 'customnextpage');
		return $buttons;
	}
	function mce_external_plugins($plugin_array) {
		global $wp_version;
		if ( version_compare( $wp_version, '3.9', '>=' ) ) {
			$plugin_array['customnextpage']  =  CUSTOM_NEXTPAGE_URL . '/includes/tinymce/plugins/customnextpage/plugin.js';
		} else {
			$plugin_array['customnextpage']  =  CUSTOM_NEXTPAGE_URL . '/includes/tinymce/plugins/customnextpage/editor_plugin.js';
		}
		return $plugin_array;
	}
	function tiny_mce_version($version) {
		return ++$version;
	}
	function editor_dialog() { ?>
		<div style="display:none;">
			<form id="customnextpage-dialog">
				<div id="customnextpage-selector">
					<div id="customnextpage-options">
						<div>
							<label><span><?php _e( 'Title:', 'custom-nextpage' ); ?></span><input id="customnextpage-title-field" type="text" name="title" /></label>
						</div>
					</div>
				</div>
				<div class="submitbox">
					<div id="customnextpage-update">
						<input type="submit" value="<?php esc_attr_e( 'OK', 'custom-nextpage' ); ?>" class="button-primary" id="customnextpage-submit">
					</div>
					<div id="customnextpage-cancel">
						<input type="button" value="<?php _e( 'Cancel', 'custom-nextpage' ); ?>" class="button tagadd" id="customnextpage-submit">
					</div>
				</div>
			</form>
		</div>
	<?php }
	// add more buttons to the html editor
	function quicktags() {
		if ( wp_script_is( 'quicktags' ) ) {
			wp_enqueue_script( 'custom-nextpage-quicktags', CUSTOM_NEXTPAGE_URL . '/js/quicktags.js', array('quicktags'), $this->version, true );
 ?>
			<script type="text/javascript">
				QTags.addButton( 'custom_nextpage', 'Custom Nextpage', '[nextpage]', '', '', 'Custom Nextpage', 9999 );
			</script>
		<?php }
	}
}
