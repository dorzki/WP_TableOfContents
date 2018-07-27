<?php
/**
 * Plugin Name: Page Table of Contents
 * Plugin URI: https://www.dorzki.co.il
 * Description: Add automatically a dynamic table of contents to your page or post.
 * Version: 1.0.0
 * Author: dorzki
 * Author URI: https://www.dorzki.co.il
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-toc
 *
 * @package    dorzki\TableOfContents
 * @subpackage Plugin
 * @version    1.0.0
 */

namespace dorzki\TableOfContents;

// Block if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'TOC_PATH', plugin_dir_path( __FILE__ ) );
define( 'TOC_URL', plugin_dir_url( __FILE__ ) );


/**
 * Class Plugin
 *
 * @package dorzki\TableOfContents
 */
class Plugin {

	/**
	 * Total headings counter.
	 *
	 * @var int
	 */
	private $counter = 0;

	/**
	 * Article headings.
	 *
	 * @var array
	 */
	private $headings = [];


	/* ------------------------------------------ */


	/**
	 * Plugin constructor.
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'register_textdomain' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_assets' ] );

		add_filter( 'the_content', [ $this, 'build_toc' ] );

	}


	/* ------------------------------------------ */


	/**
	 * Register the plugin's text domain.
	 */
	public function register_textdomain() {

		load_plugin_textdomain( 'wp-toc', false, TOC_PATH . 'languages' );

	}


	/**
	 * Load plugin CSS and JS
	 */
	public function load_assets() {

		wp_enqueue_style( 'toc_styles', TOC_URL . 'assets/styles.css' );

		wp_enqueue_script( 'toc_scripts', TOC_URL . 'assets/scripts.js', [ 'jquery' ], false, true );

	}


	/* ------------------------------------------ */


	/**
	 * Extracts and builds TOC.
	 *
	 * @param string $content the post content.
	 *
	 * @return string
	 */
	public function build_toc( $content ) {

		$content = preg_replace_callback( '/<(h2)>(.*?)<\/h2>/i', [ $this, 'extract_headings' ], $content );

		$toc = $this->generate_toc();

		return $toc . $content;

	}


	/* ------------------------------------------ */


	/**
	 * Adds title id number and saves the heading.
	 *
	 * @param array $match regex match array.
	 *
	 * @return string
	 */
	public function extract_headings( $match ) {

		$tag = sprintf( '<%1$s id="title_%2$s">%3$s</%1$s>', $match[1], $this->counter, $match[2] );

		$this->counter ++;
		$this->headings[] = $match[2];

		return $tag;

	}


	/**
	 * Generates a table of contents from the headings.
	 *
	 * @return string|boolean
	 */
	public function generate_toc() {

		if ( empty( $this->headings ) ) {
			return false;
		}

		$html = '';

		$html .= '<div class="post_toc">';
		$html .= '  <strong>' . esc_html__( 'Table of Contents', 'wp-toc' ) . '</strong>';
		$html .= '  <ol>';

		foreach ( $this->headings as $id => $text ) {

			$html .= "<li><a href='#title_{$id}'>{$text}</a></li>";

		}

		$html .= '  </ol>';
		$html .= '</div>';

		return $html;

	}

}

// Initiate the class.
new Plugin();
