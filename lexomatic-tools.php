<?php
/**
 * Plugin Name: Lexomatic Tools
 * Plugin URI:  https://github.com/richardbuff/Lexomatic-Tools
 * GitHub URI: richardbuff/Lexomatic-Tools
 * Description: Custom tools plugin for Lexomatic sites, not tested for general public use
 * Version:     1.2.0
 * Author:      Richard Buff
 * Author URI:  https://www.expandingdesigns.com
 * Requires at least: 5.0
 * License:     MIT
 * License URI: http://www.opensource.org/licenses/mit-license.php
 *
 * @package RB_Lexomatic_Tools
 */


/**
 * Class RB_Lexomatic_Tools
 */
class RB_Lexomatic_Tools {

	/**
	 * Instance of the class.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Plugin Version.
	 *
	 * @var string
	 */
	private $plugin_version = '1.2.0';

	/**
	 * Class Instance.
	 *
	 * @return RB_Lexomatic_Tools
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof RB_Lexomatic_Tools ) ) {
			self::$instance = new RB_Lexomatic_Tools();

			// scripts
			add_action( 'wp_enqueue_scripts', [ self::$instance, 'register_styles' ] );
			// settings page
			add_action( 'admin_menu', [ self::$instance, 'add_settings_page' ] );
			// callout boxes shortcode
      add_shortcode( 'lexomatic_callout_box', [ self::$instance, 'callout_box' ] );
		}

		return self::$instance;
	}

	/*
 	 * Add Settings Page
 	 *
 	 */
 	public function add_settings_page() {
 		add_options_page( __( 'Lexomatic Tools', 'lexomatic-tools' ), __( 'Lexomatic Tools', 'lexomatic-tools' ), 'manage_options', 'lexomatic_tools', array( $this, 'settings_page' ) );
 	}


	/**
	 * Build Settings Page
	 *
	 */
	public function settings_page() {

		$icons = scandir( plugin_dir_path( __FILE__ ) . '/assets/icons/' );
		$icons = array_diff( $icons, array( '..', '.' ) );
		$icons = array_values( $icons );
		if( empty( $icons ) )
			return $icons;
		// remove the .svg
		foreach( $icons as $i => $icon ) {
			$icons[ $i ] = substr( $icon, 0, -4 );
		}

		foreach( $icons as $icon ){
			echo '<p>' . RB_Lexomatic_Tools::get_icon( $icon ) . ' ' . $icon . '</p>';
		}

	}


	/**
	 * Icons - get a single icon and display it
	 */
	public function get_icon( $icon_slug = '' ) {
  	$icon_path = plugin_dir_path(  __FILE__ ) . '/assets/icons/' . $icon_slug . '.svg';
  	if( file_exists( $icon_path ) )
  		return file_get_contents( $icon_path );
	}



	/**
	 * Register Scripts
	 */
	public function register_styles() {
		wp_register_style(
			'lexomatic-tools',
			plugins_url( '/assets/css/main.css', __FILE__ ),
			[],
			$this->plugin_version
		);

		/**
		 * Filters whether the CSS is included.
		 *
		 * @param bool $include_css Include CSS.
		 */
		$include_css = apply_filters( 'lexomatic_tools_include_css', true );
		if ( $include_css ) {
			wp_enqueue_style( 'lexomatic-tools' );
		}
	}


  /**
	 * Callout Box : =callout
	 */
	public function callout_box( $atts ) {

    // Defaults
    $atts = shortcode_atts(
    array(
      'title' => 'Lexomatic Tools callout box with title not set',
      'content'   => 'There was no content specified in the shortcode parameters so you are seeing sample content for this Lexomatic Tools callout box',
      'color'   => 'grey',
      'icon'   => false,
      'icon_size' => 'medium',
    ), $atts, 'lexomatic_callout_box' );

    $additional_classes = [];

    // color class
    $additional_classes[] = 'lexomatic-callout-box__has-primary-color-' . esc_attr( $atts['color'] );
    // icon class
    if( $atts['icon'] ){
      $additional_classes[] = 'lexomatic-callout-box__has-icon';
      $additional_classes[] = 'lexomatic-callout-box__has-icon-' . esc_attr( $atts['icon'] );
      if( $atts['icon_size'] ) {
        $additional_classes[] = 'lexomatic-callout-box__has-icon-size-' . esc_attr( $atts['icon_size'] );
      }
    }

    $additional_classes = join( ' ', $additional_classes );;

    ob_start();
    echo '<div class="lexomatic-callout-box ' . $additional_classes . '">';
    echo '<div class="lexomatic-callout-box-inner">';

    if( $atts['icon'] ){
      echo '<div class="lexomatic-callout-box-icon">';
      echo RB_Lexomatic_Tools::get_icon( esc_attr( $atts['icon'] ) );
      echo '</div><!-- .lexomatic-callout-box-icon -->';
    }

    echo '<div class="lexomatic-callout-box__content">';
    echo '<h5 class="lexomatic-callout-box__title">' . esc_html( $atts['title'] ) . '</h5>';
    echo '<p>' . html_entity_decode( $atts['content'] ) . '</p>';
    echo '</div><!-- .lexomatic-callout-box__content -->';
    echo '</div><!-- .lexomatic-callout-box-inner -->';
    echo '</div><!-- .lexomatic-callout-box -->';
    return ob_get_clean();

	}


} // class RB_Lexomatic_Tools

/**
 * The function provides access to the class methods.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @return object
 */
function lexomatic_tools() {
	return RB_Lexomatic_Tools::instance();
}

lexomatic_tools();

// GitHub updater, in case updates are needed
include( dirname( __FILE__ ) . '/github-updater.php' );
