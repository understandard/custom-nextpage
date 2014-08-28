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

		$this->filter                = get_option( 'custom-next-page-filter' );
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
		$title = __( 'Set Custom Nextpage', CustomNextPage::TEXT_DOMAIN );
		echo '<div class="wrap">' . "\n";
		screen_icon();
		echo '<h2>' . esc_html( $title ) . '</h2>' . "\n";
		echo '<form method="post" action="options.php">' . "\n";
		settings_fields( self::OPTION_GROUP );
		do_settings_sections( self::OPTION_PAGE );
		submit_button();
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}

	public function add_general_custom_fields() {
		global $wp_version;
		add_settings_section(
			'general',
			__( 'General', CustomNextPage::TEXT_DOMAIN ),
			'',
			self::OPTION_PAGE
		);

		if ( version_compare( $wp_version, '3.6', '>=' ) ) {
			add_settings_field(
				'custom-next-page-filter',
				__( 'Automatically replace the wp_link_pages.', CustomNextPage::TEXT_DOMAIN ),
				array( &$this, 'check_field' ),
				self::OPTION_PAGE,
				'general',
				array(
					'name'    => 'custom-next-page-filter',
					'default' => $this->filter,
				)
			);
		}
		add_settings_field(
			'custom-next-page-before-text',
			__( 'Before Text', CustomNextPage::TEXT_DOMAIN ),
			array( &$this, 'text_field' ),
			self::OPTION_PAGE,
			'general',
			array(
				'name'    => 'custom-next-page-before-text',
				'default' => $this->before_text,
			)
		);
		add_settings_field(
			'custom-next-page-after-text',
			__( 'After Text', CustomNextPage::TEXT_DOMAIN ),
			array( &$this, 'text_field' ),
			self::OPTION_PAGE,
			'general',
			array(
				'name'    => 'custom-next-page-after-text',
				'default' => $this->after_text,
			)
		);

		add_settings_field(
			'custom-next-page-nextpagelink',
			__( 'Next Page Link Text', CustomNextPage::TEXT_DOMAIN ),
			array( &$this, 'text_field' ),
			self::OPTION_PAGE,
			'general',
			array(
				'name'    => 'custom-next-page-nextpagelink',
				'default' => $this->nextpagelink_text,
			)
		);

		add_settings_field(
			'custom-next-page-previouspagelink',
			__( 'Previous Page Link Text', CustomNextPage::TEXT_DOMAIN ),
			array( &$this, 'text_field' ),
			self::OPTION_PAGE,
			'general',
			array(
				'name'    => 'custom-next-page-previouspagelink',
				'default' => $this->previouspagelink_text,
			)
		);
	}

	public function text_field( $args ) {
		extract( $args );
		$default = ! empty( $default ) ? $default : '';
		$value   = get_option( $name, $default );
		$value   = ! empty( $value ) ? $value : $default;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<input type="text" name="' . $name .'" id="' . $name .'" value="' . $value .'" />' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";

		echo $output;
	}

	public function textarea_field( $args ) {
		extract( $args );
		$default = ! empty( $default ) ? $default : '';
		$value   = get_option( $name, $default );
		$value   = ! empty( $value ) ? $value : $default;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<textarea name="' . $name .'" rows="10" cols="50" id="' . $name .'" class="large-text code">' . $value . '</textarea>' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";
		echo $output;
	}

	public function check_field( $args ) {
		extract( $args );
		$default = ! empty( $default ) ? $default : '';
		$value   = get_option( $name, $default );
		$value   = ! empty( $value ) ? $value : $default;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<label for="' . $name . '">' . "\n";
		$output  .= '<input name="' . $name . '" type="checkbox" id="' . $name . '" value="1"' . checked( $value, 1, false ) . '>' . "\n";
		if ( $desc )
			$output .= $desc . "\n";
		$output  .= '</label>' . "\n";

		echo $output;
	}

	public function add_custom_whitelist_options_fields() {
		register_setting( self::OPTION_PAGE, 'custom-next-page-filter' );
		register_setting( self::OPTION_PAGE, 'custom-next-page-before-text' );
		register_setting( self::OPTION_PAGE, 'custom-next-page-after-text' );
		register_setting( self::OPTION_PAGE, 'custom-next-page-nextpagelink' );
		register_setting( self::OPTION_PAGE, 'custom-next-page-previouspagelink' );
		//register_setting( self::OPTION_PAGE, 'custom-next-page-enable-previous' );
	}

}
