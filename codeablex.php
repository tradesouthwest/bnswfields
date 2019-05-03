<?php
/*
Plugin Name: Codeablex
Plugin URI: http://themes.tradesouthwest.com/wordpress/plugins/
Description: Extendable plugin by Codeable Expert. Menu under Tools.
Version: 1.0.1
Author: tradesouthwest
Author URI: http://tradesouthwest.com/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WordPress Available:  yes
Requires License:    no
*/
if ( ! function_exists( 'add_action' ) ) {
	die( 'Nothing to see here...' );
}
/* Important constants */
if (!defined('COOKIEPATH'))    { define( 'COOKIEPATH', '/' ); } 
if (!defined('COOKIE_DOMAIN')) { define( 'COOKIE_DOMAIN', get_bloginfo('url') ); }
if (!defined('CODEABLEX_VER')) { define( 'CODEABLEX_VER', '1.0.0' ); }
if (!defined('CODEABLEX_URL')) { define( 'CODEABLEX_URL', plugin_dir_url(__FILE__)); }

//activate/deactivate hooks
function codeablex_plugin_activation() {

  // Check for WooCommerce 
  /*
  if (!class_exists('WooCommerce')) {
	echo('<div class="error">
	<p>This plugin requires that WooCommerce is installed and activated.</p>
	</div></div>');
	return;
  } */
return false;
}

function codeablex_plugin_deactivation() {
    //flush_rewrite_rules();
return false;
}

/**
 * Include loadable plugin files
 */
// Initialise - load in translations
function codeablex_loadtranslations () {
    $plugin_dir = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain( 'codeablex', false, $plugin_dir );
}
add_action('plugins_loaded', 'codeablex_loadtranslations');

/**
 * Enqueue admin only scripts */ 
function codeablex_load_admin_scripts() 
{

    wp_enqueue_style( 'codeablex-admin', CODEABLEX_URL 
                    . 'css/codeablex-admin.css', 
                    array(), CODEABLEX_VER, false 
                    );
}
add_action( 'admin_enqueue_scripts', 'codeablex_load_admin_scripts', 99 );      
            
/**
 *  Register Scripts - note: v 1.0 not using ajax but script can be used for validate
 */
function codeablex_enqueue_scripts() {

    wp_register_script( 'bnswfields-plugin', plugins_url(
                        'js/bnswfields.js', __FILE__ ), array( 'jquery' ), true ); 
    wp_enqueue_style( 'codeablex-style', CODEABLEX_URL 
    . '/css/codeablex-style.css',array(), CODEABLEX_VER, false );
    //wp_enqueue_script( 'bnswfields-plugin' );
}
add_action( 'wp_enqueue_scripts', 'codeablex_enqueue_scripts' );

// hook the plugin activation
    register_activation_hook(   __FILE__, 'codeablex_plugin_activation');
    register_deactivation_hook( __FILE__, 'codeablex_plugin_deactivation');

    // requires 
    require_once ( plugin_dir_path( __FILE__ ) . 'inc/codeablex-woocheckout.php' );
    require_once ( plugin_dir_path( __FILE__ ) . 'inc/codeablex-membership.php' );
    require_once ( plugin_dir_path( __FILE__ ) . 'inc/codeablex-settings.php' );
    // specific to referral addon
    require_once ( plugin_dir_path( __FILE__ ) . 'inc/codeablex-referral-discount.php' );
    require_once ( plugin_dir_path( __FILE__ ) . 'inc/codeablex-cart-handler.php' );
    require_once ( plugin_dir_path( __FILE__ ) . 'inc/codeablex-checkout-order.php' ); 
    require_once ( plugin_dir_path( __FILE__ ) . 'bnswfields/bnswfields-meta.php' ); 
    require_once ( plugin_dir_path( __FILE__ ) . 'bnswfields/bnswfields-new-fields.php' ); 
    //removes nag notices
?>