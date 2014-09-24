<?php
class CustomNextPageAdmin extends CustomNextPageInit {

	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'add_general_custom_fields' ) );
		add_filter( 'admin_init', array( &$this, 'add_custom_whitelist_options_fields' ) );
	}
	public function admin_menu() {
		add_options_page( __( 'Custom Nextpage', $this->domain ), __( 'Custom Nextpage', $this->domain ), 'create_users', $this->plugin_basename, array( &$this, 'add_admin_edit_page' ) );
	}

	public function add_admin_edit_page() {
		$title = __( 'Set Custom Nextpage', $this->domain );
		echo '<div class="wrap">' . "\n";
		screen_icon();
		echo '<h2>' . esc_html( $title ) . '</h2>' . "\n";
		echo '<form method="post" action="options.php">' . "\n";
		settings_fields( $this->plugin_basename  );
		do_settings_sections( $this->plugin_basename  );
		submit_button();
		if ( get_option( 'custom-next-page-previouspagelink' ) ) {
			echo '<h2>' . esc_html__( 'Convert to new options', $this->domain ) . '</h2>' . "\n";
			submit_button( __( 'Convert', $this->domain ), 'primary', 'custom-next-page-convert' );
		}
		echo '<h2>' . esc_html__( 'Setting initialization', $this->domain ) . '</h2>' . "\n";
		submit_button( __( 'Initialization', $this->domain ), 'primary', 'custom-next-page-initialization' );
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}

	public function add_general_custom_fields() {
		global $wp_version;

		add_settings_section(
			'general',
			__( 'General', $this->domain ),
			'',
			$this->domain
		);
		if ( version_compare( $wp_version, '3.6', '>=' ) ) {
			add_settings_field(
				'custom-next-page-filter',
				__( 'Automatically replace the wp_link_pages.', $this->domain ),
				array( &$this, 'check_field' ),
				$this->plugin_basename ,
				'general',
				array(
					'name'  => 'custom-next-page[filter]',
					'value' => $this->options['filter'],
				)
			);
		}
		add_settings_field(
			'custom-next-page-before-text',
			__( 'Before Text', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[beforetext]',
				'value' => $this->options['beforetext'],
			)
		);
		add_settings_field(
			'custom-next-page-after-text',
			__( 'After Text', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[aftertext]',
				'value' => $this->options['aftertext'],
			)
		);

		add_settings_field(
			'custom-next-page-boundary',
			__( 'The first and last page links displayed.', $this->domain ),
			array( &$this, 'check_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[show_boundary]',
				'value' => $this->options['show_boundary'],
			)
		);

		add_settings_field(
			'custom-next-page-adjacent',
			__( 'Next and previous page links to display.', $this->domain ),
			array( &$this, 'check_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[show_adjacent]',
				'value' => $this->options['show_adjacent'],
			)
		);

		add_settings_field(
			'custom-next-page-firstpagelink',
			__( 'Text For First Page', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[firstpagelink]',
				'value' => $this->options['firstpagelink'],
			)
		);

		add_settings_field(
			'custom-next-page-lastpagelink',
			__( 'Text For Last Page', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[lastpagelink]',
				'value' => $this->options['lastpagelink'],
			)
		);

		add_settings_field(
			'custom-next-page-nextpagelink',
			__( 'Text For Next Page', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[nextpagelink]',
				'value' => $this->options['nextpagelink'],
			)
		);

		add_settings_field(
			'custom-next-page-previouspagelink',
			__( 'Text For Previous Page', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'custom-next-page[previouspagelink]',
				'value' => $this->options['previouspagelink'],
			)
		);
	}

	public function text_field( $args ) {
		extract( $args );
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<input type="text" name="' . $name .'" id="' . $name .'" value="' . $value .'" />' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";

		echo $output;
	}

	public function textarea_field( $args ) {
		extract( $args );
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<textarea name="' . $name .'" rows="10" cols="50" id="' . $name .'" class="large-text code">' . $value . '</textarea>' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";
		echo $output;
	}

	public function check_field( $args ) {
		extract( $args );
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<label for="' . $name . '">' . "\n";
		$output  .= '<input name="' . $name . '" type="checkbox" id="' . $name . '" value="1"' . checked( $value, 1, false ) . '>' . "\n";
		if ( $desc )
			$output .= $desc . "\n";
		$output  .= '</label>' . "\n";

		echo $output;
	}

	public function add_custom_whitelist_options_fields() {
		register_setting( $this->plugin_basename , 'custom-next-page', array( &$this, 'register_setting_check' ) );
		register_setting( $this->plugin_basename , 'custom-next-page-convert', array( &$this, 'register_setting_convert' ) );
		register_setting( $this->plugin_basename , 'custom-next-page-initialization', array( &$this, 'register_setting_initialization' ) );
	}

	public function register_setting_check( $value ) {
		$value['filter'] = (int) $value['filter'];
		return $value;
	}

	public function register_setting_convert( $value ) {
		if ( __( 'Convert', $this->domain ) != $value )
			return $value;

		$convert_options                     = get_option( 'custom-next-page' );
		$convert_options['filter']           = get_option( 'custom-next-page-filter' );
		$convert_options['before-text']      = get_option( 'custom-next-page-before-text' );
		$convert_options['after-text']       = get_option( 'custom-next-page-after-text' );
		$convert_options['nextpagelink']     = get_option( 'custom-next-page-nextpagelink', __( '&#187;', $this->domain ) );
		$convert_options['previouspagelink'] = get_option( 'custom-next-page-previouspagelink', __( '&#171;', $this->domain ) );
		update_option( 'custom-next-page', $convert_options );
		delete_option( 'custom-next-page-filter' );
		delete_option( 'custom-next-page-before-text' );
		delete_option( 'custom-next-page-after-text' );
		delete_option( 'custom-next-page-firstpagelink' );
		delete_option( 'custom-next-page-lastpagelink' );
		delete_option( 'custom-next-page-nextpagelink' );
		delete_option( 'custom-next-page-previouspagelink' );
		return $value;
	}

	public function register_setting_initialization( $value ) {
		if ( __( 'Initialization', $this->domain ) != $value )
			return $value;

		delete_option( 'custom-next-page' );
		update_option( 'custom-next-page', $this->default_options );
		return $value;
	}

}
