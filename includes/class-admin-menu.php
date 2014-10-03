<?php
class CustomNextPageAdmin extends CustomNextPageInit {

	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( &$this, 'add_general_custom_fields' ) );
		add_filter( 'admin_init', array( &$this, 'add_custom_whitelist_options_fields' ) );
		add_action( 'wp_ajax_reset_css', array( $this, 'reset_css') );
	}

	public function admin_menu() {
		add_options_page( __( 'Custom Nextpage', $this->domain ), __( 'Custom Nextpage', $this->domain ), 'create_users', $this->plugin_basename, array( &$this, 'add_admin_edit_page' ) );
	}

	public function admin_enqueue_scripts( $hook ) {

		if ( stristr( $hook, $this->plugin_basename ) ) {
			wp_enqueue_style( 'options-customnextpage', CUSTOM_NEXTPAGE_URL . '/css/options.css', array(), $this->version );
			wp_enqueue_style( 'codemirror', CUSTOM_NEXTPAGE_URL . '/includes/codemirror/lib/codemirror.css', array(), $this->version );
			wp_enqueue_style( 'codemirror-show-hint', CUSTOM_NEXTPAGE_URL . '/includes/codemirror/addon/hint/show-hint.css', array(), $this->version );
			wp_enqueue_style( 'codemirror-style', CUSTOM_NEXTPAGE_URL . '/css/codemirror-style.css', array(), $this->version );

			wp_enqueue_script( 'codemirror', CUSTOM_NEXTPAGE_URL . '/includes/codemirror/lib/codemirror.js', array(), $this->version, true );
			wp_enqueue_script( 'codemirror-show-hint', CUSTOM_NEXTPAGE_URL . '/includes/codemirror/addon/hint/show-hint.js', array('codemirror'), $this->version, true );
			wp_enqueue_script( 'codemirror-css-hint', CUSTOM_NEXTPAGE_URL . '/includes/codemirror/addon/hint/css-hint.js', array('codemirror'), $this->version, true );
			wp_enqueue_script( 'codemirror-mode-css', CUSTOM_NEXTPAGE_URL . '/includes/codemirror/mode/css/css.js', array('codemirror'), $this->version, true );
			wp_enqueue_script( 'options-customnextpage', CUSTOM_NEXTPAGE_URL . '/js/options.js', array('jquery'), $this->version, true );

			wp_localize_script(
				'options-customnextpage',
				'resetCss',
				array(
					'url'      => admin_url( 'admin-ajax.php' ),
					'security' => wp_create_nonce( 'reset-css' )
				)
			);
		}
	}

	public function add_admin_edit_page() {
		$title = __( 'Set Custom Nextpage', $this->domain );
		echo '<div class="wrap" id="custom-next-page-options">' . "\n";
		screen_icon();
		echo '<h2>' . esc_html( $title ) . '</h2>' . "\n";
		echo '<form method="post" action="options.php">' . "\n";
		settings_fields( $this->plugin_basename  );
		do_settings_sections( $this->plugin_basename  );
		echo '<div class="submit">' . "\n";
		submit_button();
		if ( get_option( 'custom-next-page-previouspagelink' ) ) {
			echo '<h2>' . esc_html__( 'Convert to new options', $this->domain ) . '</h2>' . "\n";
			submit_button( __( 'Convert', $this->domain ), 'primary', 'custom-next-page-convert' );
		}
		echo '<h2>' . esc_html__( 'Setting initialization', $this->domain ) . '</h2>' . "\n";
		submit_button( __( 'Initialization', $this->domain ), 'primary', 'custom-next-page-initialization' );
		echo '</div>' . "\n";
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
					'value' => $this->filter,
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
				'value' => $this->beforetext,
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
				'value' => $this->aftertext,
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
				'value' => $this->show_boundary,
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
				'value' => $this->show_adjacent,
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
				'value' => $this->firstpagelink,
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
				'value' => $this->lastpagelink,
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
				'value' => $this->nextpagelink,
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
				'value' => $this->previouspagelink,
			)
		);

		add_settings_section(
			'style',
			__( 'Style', $this->domain ),
			'',
			$this->domain
		);

		add_settings_field(
			'custom-next-page-style-type',
			__( 'Select Style type', $this->domain ),
			array( &$this, 'select_field' ),
			$this->plugin_basename ,
			'style',
			array(
				'name'   => 'custom-next-page[styletype]',
				'option' => array(
					'0'  => __( 'Default', $this->domain ),
					'1'  => __( 'Style Edit', $this->domain ),
					'2'  => __( 'Disable', $this->domain ),
				),
				'id'    => 'styletype',
				'value'  => $this->styletype,
			)
		);
		add_settings_field(
			'custom-next-page-style',
			__( 'Style Edit', $this->domain ),
			array( &$this, 'textarea_field' ),
			$this->plugin_basename ,
			'style',
			array(
				'id'    => 'style-editor',
				'name'  => 'custom-next-page[style]',
				'value' => $this->style,
				'desc'  => __( 'Press ctrl-space to activate autocompletion. <span class="button button-primary" id="reset-css">Reset</span>', $this->domain ),
			)
		);
	}

	public function text_field( $args ) {
		extract( $args );

		$id      = ! empty( $id ) ? $id : $name;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<input type="text" name="' . $name .'" id="' . $id .'" class="regular-text" value="' . $value .'" />' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";

		echo $output;
	}

	public function textarea_field( $args ) {
		extract( $args );

		$id      = ! empty( $id ) ? $id : $name;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<textarea name="' . $name .'" rows="10" cols="50" id="' . $id .'" class="large-text code">' . $value . '</textarea>' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";
		echo $output;
	}

	public function check_field( $args ) {
		extract( $args );

		$id      = ! empty( $id ) ? $id : $name;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<label for="' . $name . '">' . "\n";
		$output  .= '<input name="' . $name . '" type="checkbox" id="' . $id . '" value="1"' . checked( $value, 1, false ) . '>' . "\n";
		if ( $desc )
			$output .= $desc . "\n";
		$output  .= '</label>' . "\n";

		echo $output;
	}

	public function select_field( $args ) {
		extract( $args );

		$id             = ! empty( $id ) ? $id : $name;
		$desc           = ! empty( $desc ) ? $desc : '';
		$multi          = ! empty( $multi ) ? ' multiple' : '';
		$multi_selected = ! empty( $multi ) ? true : false;
		$output = '<select name="' . $name . '" id="' . $id . '"' . $multi . '>' . "\n";
			foreach ( $option as $key => $val ) {
				$output .= '<option value="' . $key . '"' . selected( $value, $key, $multi_selected ) . '>' . $val . '</option>' . "\n";
			}
		$output .= '</select>' . "\n";
			if ( $desc )
			$output .= $desc . "\n";

		echo $output;
	}

	public function selected( $value = '', $val = '', $multi = false ) {
		$select = '';
		if ( $multi ) {

			$select = selected( true, in_array( $val, $value ), false );
		} else {
			$select = selected( $value, $val, false );
		}
		return $select;
	}

	public function reset_css() {
		check_ajax_referer( 'reset-css', 'security' );
		$return = array(
			'style' => $this->css
		);
		wp_send_json( $return );
	}

	public function add_custom_whitelist_options_fields() {
		register_setting( $this->plugin_basename , 'custom-next-page', array( &$this, 'register_setting_check' ) );
		register_setting( $this->plugin_basename , 'custom-next-page-convert', array( &$this, 'register_setting_convert' ) );
		register_setting( $this->plugin_basename , 'custom-next-page-initialization', array( &$this, 'register_setting_initialization' ) );
	}

	public function register_setting_check( $value ) {
		$value['filter'] = (int) $value['filter'];
		$value['style'] = preg_replace( '/(\&lt;(.*)\&gt;)/ism', '', esc_textarea( $value['style'] ) );

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
