<?php
/*
Plugin Name: Custom Nextpage
Plugin URI: http://wordpress.org/plugins/custom-nextpage/
Description: MultiPage is a customizable plugin. Can any title on the page.
Author: Webnist
Version: 0.9.9
Author URI: http://profiles.wordpress.org/webnist
License: GPLv2 or later
Text Domain: custom-nextpage
Domain Path: /languages/
*/

if ( !defined( 'CUSTOM_NEXTPAGE_DIR' ) )
	define( 'CUSTOM_NEXTPAGE_DIR', WP_PLUGIN_DIR . '/custom-nextpage' );

if ( !defined( 'CUSTOM_NEXTPAGE_URL' ) )
	define( 'CUSTOM_NEXTPAGE_URL', WP_PLUGIN_URL . '/custom-nextpage' );

if ( !class_exists('CustomNextPageAdmin') )
	require_once(dirname(__FILE__).'/includes/class-admin-menu.php');

if ( !class_exists('CustomNextPageEditor') )
	require_once(dirname(__FILE__).'/includes/class-admin-editor.php');

class CustomNextPageInit {

	public function __construct() {
		$data                  = get_file_data(
			__FILE__,
			array(
				'ver'    => 'Version',
				'domain' => 'Text Domain',
				'langs'  => 'Domain Path'
			)
		);

		$this->plugin_basename = dirname( plugin_basename(__FILE__) );
		$this->version         = $data['ver'];
		$this->domain          = $data['domain'];
		$this->langs           = $data['langs'];
		$this->default_options = array(
			'filter'           => '',
			'beforetext'       => '',
			'aftertext'        => '',
			'show_boundary'    => 1,
			'show_adjacent'    => 1,
			'firstpagelink'    => __( '&#171;', 'custom-nextpage' ),
			'lastpagelink'     => __( '&#187;', 'custom-nextpage' ),
			'nextpagelink'     => __( '&gt;', 'custom-nextpage' ),
			'previouspagelink' => __( '&lt;', 'custom-nextpage' ),
		);
		$this->options         = get_option( 'custom-next-page', $this->default_options );
	}
}

class CustomNextPage extends CustomNextPageInit {

	public function __construct() {
		parent::__construct();

		if ( !is_admin() ) {
			add_action( 'loop_start', array( &$this, 'change_nextpage' ) );
			if ( $this->options['filter'] === 1 )
				add_filter( 'wp_link_pages', array( &$this, 'wp_link_pages' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );
		}
		add_shortcode( 'nextpage', array( &$this, 'shortcode' ) );
		load_plugin_textdomain( $this->domain, false, $this->plugin_basename . $this->langs );
	}

	public function plugins_loaded() {
	}

	public function change_nextpage( $query ) {
		if ( is_feed() || is_404() )
			return;

		$posts = $query->posts;
		$pattern = "/\[nextpage[^\]]*\]/";
		$count = 0;
		foreach ( $posts as $post ) {
			$content = $post->post_content;
			$query->posts[$count]->post_content = preg_replace( $pattern, '<!--nextpage-->', $content );
			$count++;
		}
		return $query;
	}

	public function next_page_title( $id = '' ) {
		global $page, $numpages, $multipage;

		if ( !$id )
			$id = get_the_id();

		$output = '';
		if ( $multipage ) {
			$pattern      = "/\[nextpage[^\]]*\]/";
			$post         = get_post( $id );
			$content      = $post->post_content;
			$matche_count = $page - 1;
			$page_count   = $page + 1;
			preg_match_all( $pattern, $content, $matches );
			$page_title = isset( $matches[0][$matche_count] ) ? $matches[0][$matche_count] : '';
			if ( $page_title ) {
				$pattern = '/title=["?](.*)["?]/';
				preg_match( $pattern, $page_title, $matches);
				$title  = isset( $matches[1] ) ? esc_html( $matches[1] ) : '';
				$before = apply_filters( 'custom_next_page_beforetext', $this->options['beforetext'] );
				$after  = apply_filters( 'custom_next_page_aftertext', $this->options['aftertext'] );
				$output .= '<p class="custom-page-links">' ."\n";
				if ( $page_count <= $numpages ) {
					$output .= _wp_link_page( $page_count );
					$output .= $before . $title . $after . '</a>';
				}
				$output .= '</p>' ."\n";
			}
		}
		return $output;
	}

	public function wp_link_pages( $output = '' ) {
		global $page, $numpages, $multipage, $pagenow;
		$output = '';
		if ( $multipage ) {
			$show_boundary     = esc_html( apply_filters( 'custom_next_page_show_boundary', $this->options['show_boundary'] ) );
			$show_adjacent     = esc_html( apply_filters( 'custom_next_page_show_adjacent', $this->options['show_adjacent'] ) );
			$firstpagelink     = esc_html( apply_filters( 'custom_next_page_firstpagelink', $this->options['firstpagelink'] ) );
			$lastpagelink     = esc_html( apply_filters( 'custom_next_page_lastpagelink', $this->options['lastpagelink'] ) );
			$nextpagelink     = esc_html( apply_filters( 'custom_next_page_nextpagelink', $this->options['nextpagelink'] ) );
			$previouspagelink = esc_html( apply_filters( 'custom_next_page_previouspagelink', $this->options['previouspagelink'] ) );
			$id               = get_the_ID();
			$next_page_title  = self::next_page_title( $id );

			$output .= '<div class="page-link-box">' ."\n";
			$output .= $next_page_title;
			$output .= '<ul class="page-links">' ."\n";
			$i = $page - 1;

			if ( $page > 1 ) {
				$first_link = _wp_link_page( 1 );
				$link       = _wp_link_page( $i );
				if ( $show_boundary )
					$output .= '<li class="first">' . $first_link . $firstpagelink . '</a></li>';
				if ( $show_adjacent )
					$output .= '<li class="previous">' . $link . $previouspagelink . '</a></li>';
			}
			for ( $i = 1; $i <= $numpages; $i++ ) {
				if ( $page === $i ) {
					$link  = '<li class="numpages current"><span>' . $i . '</span></li>';
				} else {
					$link  = '<li class="numpages">' . _wp_link_page( $i ) . $i . '</a></li>';
				}
				$output .= $link;
			}
			$i = $page + 1;
			if ( $i <= $numpages ) {
				$last_link = _wp_link_page( $numpages );
				$link      = _wp_link_page( $i );
				if ( $show_adjacent )
					$output .= '<li class="next">' . $last_link . $nextpagelink . '</a></li>';

				if ( $show_boundary )
					$output .= '<li class="last">' . $link . $lastpagelink . '</a></li>';
			}
			$output .= '</ul>' ."\n";
			$output .= '</div>' ."\n";
		}
		return $output;
	}

	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'custom-nextpage-style', CUSTOM_NEXTPAGE_URL . '/css/custom-nextpage-style.css', array(), $this->version );
	}
	public function shortcode() {
		return;
	}
}

new CustomNextPageInit();
new CustomNextPage();
new CustomNextPageAdmin();
new CustomNextPageEditor();

function custom_next_page_link_pages() {
	$custom_next_page = new CustomNextPage();
	echo $custom_next_page->wp_link_pages();
}
