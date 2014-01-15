<?php

/*
Plugin Name: Custom Nextpage
Plugin URI: http://wordpress.org/plugins/custom-nextpage/
Description:
Author: Webnist
Version: 0.7.1.0
Author URI: http://profiles.wordpress.org/webnist
*/

if ( !defined( 'CUSTOM_NEXTPAGE_DIR' ) )
	define( 'CUSTOM_NEXTPAGE_DIR', WP_PLUGIN_DIR . '/custom-nextpage' );

if ( !defined( 'CUSTOM_NEXTPAGE_URL' ) )
	define( 'CUSTOM_NEXTPAGE_URL', WP_PLUGIN_URL . '/custom-nextpage' );

class CustomNextpage {
	const VERSION = '0.7.1.0';
	const TEXT_DOMAIN = 'custom-nextpage';

	private $plugin_basename;
	private $plugin_dir_path;
	private $plugin_dir_url;

	public function __construct() {
		$this->plugin_basename = self::plugin_basename();
		$this->plugin_dir_path = self::plugin_dir_path();
		$this->plugin_dir_url  = self::plugin_dir_url();
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename(__FILE__) ) . '/languages/' );

		if ( is_admin() ) {
		} else {
			add_action( 'loop_start', array( &$this, 'change_nextpage' ) );
			add_action( 'wp_link_pages', array( &$this, 'wp_link_pages' ) );
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
			$pattern = "/\[nextpage[^\]]*\]/";
			$post    = get_post( $id );
			$content = $post->post_content;
			preg_match_all( $pattern, $content, $matches );
			$page       = $page - 1;
			$page_title = isset( $matches[0][$page] ) ? $matches[0][$page] : '';
			if ( $page_title ) {
				$pattern = '/title=["?](.*)["?]/';
				preg_match( $pattern, $page_title, $matches);
				$title = isset( $matches[1] ) ? esc_html( $matches[1] ) : '';
				$output .= '<p class="custom-page-links">' ."\n";
				$i = $page + 1;
				if ( $i <= $numpages ) {
					$output .= _wp_link_page( $i );
					$output .= $title .'</a>';
				}
				$output .= '</p>' ."\n";
			}
		}
		return $output;
	}

	public function wp_link_pages( $output ) {
		$id = get_the_id();
		$next_page_title = self::next_page_title( $id );
		$output = $next_page_title . $output;
		return $output;
	}
}

new CustomNextpage();
