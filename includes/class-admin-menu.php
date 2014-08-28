<?php
class CustomNextPageAdmin {
	const OPTION_PAGE  = 'custom-next-page';
	const OPTION_GROUP = 'custom-next-page';

	private $plugin_basename;
	private $plugin_dir_path;
	private $plugin_dir_url;

	public function __construct() {
		$this->plugin_basename       = CustomNextPage::plugin_basename();
		$this->plugin_dir_path       = CustomNextPage::plugin_dir_path();
		$this->plugin_dir_url        = CustomNextPage::plugin_dir_url();
		$this->before_text           = get_option( 'custom-next-page-before-text' );
		$this->after_text            = get_option( 'custom-next-page-after-text' );
		$this->nextpagelink_text     = get_option( 'custom-next-page-nextpagelink', __( 'Next page', CustomNextPage::TEXT_DOMAIN ) );
		$this->previouspagelink_text = get_option( 'custom-next-page-previouspagelink', __( 'Previous page', CustomNextPage::TEXT_DOMAIN ) );
		//$this->enabe_previous  = get_option( 'custom-next-page-enable-previous', 0 );

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'add_general_custom_fields' ) );
		add_filter( 'admin_init', array( &$this, 'add_custom_whitelist_options_fields' ) );
	}
	public function admin_menu() {
		add_menu_page( __( 'Custom Nextpage', CustomNextPage::TEXT_DOMAIN ), __( 'Custom Nextpage', CustomNextPage::TEXT_DOMAIN ), 'create_users', self::OPTION_PAGE, array( &$this, 'add_admin_edit_page' ) );
	}

	public function add_admin_edit_page() {
		$title = __( 'Set Custom Nextpage', CustomNextPage::TEXT_DOMAIN ); ?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php echo esc_html( $title ); ?></h2>
		<form method="post" action="options.php">
		<?php settings_fields( self::OPTION_GROUP ); ?>
		<?php do_settings_sections( self::OPTION_PAGE ); ?>
		<input type="hidden" name="refresh">
		<table class="form-table">
		<?php do_settings_fields( self::OPTION_PAGE, 'default' ); ?>
		</table>
		<?php submit_button(); ?>
		</form>
		</div>
	<?php }

	public function add_general_custom_fields() {
		global $add_settings_field;
		add_settings_field( 'custom-next-page-before-text', __( 'Before Text', CustomNextPage::TEXT_DOMAIN ), array( &$this, 'text_field' ), self::OPTION_PAGE, 'default', array( 'name' => 'custom-next-page-before-text', 'value' => $this->before_text ) );
		add_settings_field( 'custom-next-page-after-text', __( 'After Text', CustomNextPage::TEXT_DOMAIN ), array( &$this, 'text_field' ), self::OPTION_PAGE, 'default', array( 'name' => 'custom-next-page-after-text', 'value' => $this->after_text ) );

		add_settings_field( 'custom-next-page-nextpagelink', __( 'Next Page Link Text', CustomNextPage::TEXT_DOMAIN ), array( &$this, 'text_field' ), self::OPTION_PAGE, 'default', array( 'name' => 'custom-next-page-nextpagelink', 'value' => $this->nextpagelink_text ) );
		add_settings_field( 'custom-next-page-previouspagelink', __( 'Previous Page Link Text', CustomNextPage::TEXT_DOMAIN ), array( &$this, 'text_field' ), self::OPTION_PAGE, 'default', array( 'name' => 'custom-next-page-previouspagelink', 'value' => $this->previouspagelink_text ) );

		//add_settings_field( 'custom-next-page-enable-previous', __( 'Enable the previous button', CustomNextPage::TEXT_DOMAIN ), array( &$this, 'check_box' ), self::OPTION_PAGE, 'default', array( 'name' => 'custom-next-page-enable-previous', 'value' => $this->enabe_previous ) );
	}

	public function check_box( $args ) {
		extract( $args );
		$output = '<label><input type="checkbox" name="' . $args['name'] .'" id="' . $args['name'] .'" value="1"' . checked( 1, $args['value'], false ). ' />' . esc_html__( $args['note'], CustomNextPage::TEXT_DOMAIN ) . '</label>' ."\n";
		echo $output;
	}

	public function text_field( $args ) {
		extract( $args );
		$output = '<label><input type="text" name="' . $args['name'] .'" id="' . $args['name'] .'" value="' . $args['value'] .'" /></label>' ."\n";
		echo $output;
	}

	public function add_custom_whitelist_options_fields() {
		register_setting( self::OPTION_PAGE, 'custom-next-page-before-text' );
		register_setting( self::OPTION_PAGE, 'custom-next-page-after-text' );
		register_setting( self::OPTION_PAGE, 'custom-next-page-nextpagelink' );
		register_setting( self::OPTION_PAGE, 'custom-next-page-previouspagelink' );
		//register_setting( self::OPTION_PAGE, 'custom-next-page-enable-previous' );
	}

}
