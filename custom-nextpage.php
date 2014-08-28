<?php

/*
Plugin Name: Custom Nextpage
Plugin URI: http://wordpress.org/plugins/custom-nextpage/
Description:
Author: Webnist
Version: 0.9.1
Author URI: http://profiles.wordpress.org/webnist
*/

if ( !defined( 'CUSTOM_NEXTPAGE_DIR' ) )
	define( 'CUSTOM_NEXTPAGE_DIR', WP_PLUGIN_DIR . '/custom-nextpage' );

if ( !defined( 'CUSTOM_NEXTPAGE_URL' ) )
	define( 'CUSTOM_NEXTPAGE_URL', WP_PLUGIN_URL . '/custom-nextpage' );

if ( !class_exists('CustomNextPageAdmin') )
	require_once(dirname(__FILE__).'/includes/class-admin-menu.php');

if ( !class_exists('CustomNextPageEditor') )
	require_once(dirname(__FILE__).'/includes/class-admin-editor.php');

class CustomNextPage {
	const VERSION = '0.9.1';
	const TEXT_DOMAIN = 'custom-nextpage';

	private $plugin_basename;
	private $plugin_dir_path;
	private $plugin_dir_url;

	public function __construct() {
		$this->plugin_basename       = self::plugin_basename();
		$this->plugin_dir_path       = self::plugin_dir_path();
		$this->plugin_dir_url        = self::plugin_dir_url();
		$this->filter                = get_option( 'custom-next-page-filter' );
		$this->before_text           = get_option( 'custom-next-page-before-text' );
		$this->after_text            = get_option( 'custom-next-page-after-text' );
		$this->nextpagelink_text     = get_option( 'custom-next-page-nextpagelink', __( 'Next page', CustomNextPage::TEXT_DOMAIN ) );
		$this->previouspagelink_text = get_option( 'custom-next-page-previouspagelink', __( 'Previous page', CustomNextPage::TEXT_DOMAIN ) );
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename(__FILE__) ) . '/languages/' );

		if ( !is_admin() ) {
			add_action( 'loop_start', array( &$this, 'change_nextpage' ) );
			if ( $this->filter )
				add_filter( 'wp_link_pages', array( &$this, 'wp_link_pages' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );
		}
	}

	static public function plugin_basename() {
		return plugin_basename(__FILE__);
	}

	static public function plugin_dir_path() {
		return plugin_dir_path( self::plugin_basename() );
	}

	static public function plugin_dir_url() {
		return plugin_dir_url( self::plugin_basename() );
	}

	public function change_nextpage( $query ) {
		// no need to process
		if ( is_feed() || is_404() || !in_the_loop() )
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
		global $page, $numpages, $multipage, $more;

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
				$before = apply_filters( 'custom_next_page_before', $this->before_text );
				$after  = apply_filters( 'custom_next_page_after', $this->after_text );
				$output .= '<p class="custom-page-links">' ."\n";
				if ( $page_count <= $numpages && $more ) {
					$output .= _wp_link_page( $page_count );
					$output .= $before . $title . $after . '</a>';
				}
				$output .= '</p>' ."\n";
			}
		}
		return $output;
	}

	public function wp_link_pages( $output = '' ) {
		global $page, $numpages, $multipage, $more, $pagenow;
		$output = '';
		if ( $multipage ) {
			$nextpagelink     = apply_filters( 'custom-next-page-nextpagelink', $this->nextpagelink_text );
			$previouspagelink = apply_filters( 'custom-next-page-previouspagelink', $this->previouspagelink_text );
			$id               = get_the_id();
			$next_page_title  = self::next_page_title( $id );

			$output .= '<div class="page-link-box">' ."\n";
			$output .= $next_page_title;
			$output .= '<ul class="page-link">' ."\n";
			$i = $page - 1;
			if ( $page > 1 && $more ) {
				$link = _wp_link_page( $i );
				$output .= '<li class="previous">' . $link . $previouspagelink . '</a></li>';
			}
			for ( $i = 1; $i <= $numpages; $i++ ) {
				$class = ( $page === $i ) ? ' current': '';
				$link = '<li class="numpages'. $class . '">' . _wp_link_page( $i ) . $i . '</a></li>';
				$output .= $link;
			}
			$i = $page + 1;
			if ( $i <= $numpages && $more ) {
				$link = _wp_link_page( $i );
				$output .= '<li class="next">' . $link . $nextpagelink . '</a></li>';
			}
			$output .= '</ul>' ."\n";
			$output .= '</div>' ."\n";
		}
		return $output;
	}

	public function wp_enqueue_scripts() {
		if ( is_singular() ) {
			wp_enqueue_style( 'custom-nextpage-style', CUSTOM_NEXTPAGE_URL . '/css/custom-nextpage-style.css', array(), filemtime( CUSTOM_NEXTPAGE_DIR . '/css/custom-nextpage-style.css' ) );
		}
	}
}

new CustomNextPage();
new CustomNextPageAdmin();
new CustomNextPageEditor();

function custom_next_page_link_pages() {
	$custom_next_page = new CustomNextPage();
	echo $custom_next_page->wp_link_pages();
}