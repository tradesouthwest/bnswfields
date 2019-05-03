<?php
/**
 * codeablex admin setting page
 * @since 1.0.0
 */  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/** a.) Register new settings
 *  $option_group (page), $option_name, $sanitize_callback
 *  --------
 ** b.) Add sections
 *  $id, $title, $callback, $page
 *  --------
 ** c.) Add fields 
 *  $id, $title, $callback, $page, $section, $args = array() 
 *  --------
 ** d.) Options Form Rendering. action="options.php"
 *
 */

//$page_title, $menu_title, $capability, $menu_slug, $function-to-render, $icon_url, $position
function codeablex_add_admin_menu_page() {

    add_menu_page( 
        __( 'CodeableX Add ons' ),
        __( 'CodeableX Addons' ), 
        'manage_options',
        'codeablex_menu', 
        'codeablex_admin_section',
    'dashicons-admin-tools'
    );
}
add_action( 'admin_menu', 'codeablex_add_admin_menu_page' );
add_action( 'admin_init', 'codeablex_settings_init' );
 
function codeablex_settings_init( ) {

    register_setting( 'codeablex_admin', 'codeablex_admin' ); //options pg

    /**
     * b1.) options section
     */       
        add_settings_section(
            'codeablex_admin_section',
            'CodeableX',
            'codeablex_admin_section_cb',
            'codeablex_admin'
        ); 
    // c1.) settings 
    add_settings_field(
        'codeablex_checkbox_1',
        __('Enable Agree on Checkout Page', 'codeablex'),
        'codeablex_checkbox_1_cb',
        'codeablex_admin',
        'codeablex_admin_section'
    );
    add_settings_field(
        'codeablex_textfield_1',
        __('Verbage for renewal select label.', 'codeablex'),
        'codeablex_textfield_1_cb',
        'codeablex_admin',
        'codeablex_admin_section'
    );
    add_settings_field(
        'codeablex_textfield_2',
        __('Verbage for referral cart message.', 'codeablex'),
        'codeablex_textfield_2_cb',
        'codeablex_admin',
        'codeablex_admin_section'
    );
    add_settings_field(
        'codeablex_textfield_3',
        __('Verbage for My Account page Referral dialog.', 'codeablex'),
        'codeablex_textfield_3_cb',
        'codeablex_admin',
        'codeablex_admin_section'
    );
    add_settings_field(
        'codeablex_textfield_4',
        __('Maximum Number of Referrals.', 'codeablex'),
        'codeablex_textfield_4_cb',
        'codeablex_admin',
        'codeablex_admin_section'
    );
    add_settings_field(
        'codeablex_textfield_5',
        __('Verbage for MyAccount before acount number.', 'codeablex'),
        'codeablex_textfield_5_cb',
        'codeablex_admin',
        'codeablex_admin_section'
    );
}
/* checkbox for 'annual renewal checkbox' field
 * @since 1.0.0
 * @package codeablex
 * @subpackage inc/codeablex-settings
 */
function codeablex_checkbox_1_cb() 
{
    $options = get_option('codeablex_admin'); 
    $checkbox = (empty($options['codeablex_checkbox_1'] )) 
         ? 0 : absint( $options['codeablex_checkbox_1'] ); ?>
    
    <input type="hidden" name="codeablex_admin[codeablex_checkbox_1]" 
           value="0" />
    <input name="codeablex_admin[codeablex_checkbox_1]" 
           value="1" 
           type="checkbox" <?php echo esc_attr( 
           checked( 1, $checkbox, true ) ); ?> /> 	
    <?php esc_html_e( 'Check to activate checkout page notice ', 'codeablex' ); ?>
    <?php 
}
/** 
 * Text of label
 * @since 1.0.0
 * I agree and accept the renewal of my annual membership.
 */
function codeablex_textfield_1_cb()
{
    $options = get_option('codeablex_admin'); 
    $codeablex_textfield_1 = (empty($options['codeablex_textfield_1'] )) 
 ? 'I Agree' : sanitize_text_field( $options['codeablex_textfield_1'] ); 
    ?>

    <input type="text" 
           name="codeablex_admin[codeablex_textfield_1]" 
           value="<?php print( $codeablex_textfield_1 ); ?>" 
           size="40">
    <?php 
}
/** 
 * Text of label
 * @since 1.0.1
 * 
 */
function codeablex_textfield_2_cb()
{
    $options = get_option('codeablex_admin'); 
    $codeablex_textfield_2 = (empty($options['codeablex_textfield_2'] )) 
 ? 'If Applicable Referral Discount on Checkout page' : 
 sanitize_text_field( $options['codeablex_textfield_2'] ); 
    ?>

    <input type="text" 
           name="codeablex_admin[codeablex_textfield_2]" 
           value="<?php echo $codeablex_textfield_2; ?>" 
           size="40">
    <?php 
}
/** 
 * Referral notice to display
 * @since 1.0.1
 * 
 */
function codeablex_textfield_3_cb()
{
    $options = get_option('codeablex_admin'); 
    $codeablex_textfield_3 = (empty($options['codeablex_textfield_3'] )) 
 ? 'As a premium customer, you can share this number with a friend. The person will receive $10 off new Membership and you will save $10 on your next Renewal.' : 
  $options['codeablex_textfield_3']; 

echo "<textarea id='plugin_textarea_string' name='codeablex_admin[codeablex_textfield_3]' 
rows='7' cols='50' type='textarea'>{$codeablex_textfield_3}</textarea>";
}

/** 
 * Text of label
 * @since 1.0.1
 * 
 */
function  codeablex_textfield_4_cb()
{
    $options = get_option('codeablex_admin'); 
    $codeablex_textfield_4 = (empty($options['codeablex_textfield_4'] )) 
 ? 'If Applicable Referral Discount on Checkout page' : 
 sanitize_text_field( $options['codeablex_textfield_4'] ); 
    ?>

    <input type="text" 
           name="codeablex_admin[codeablex_textfield_4]" 
           value="<?php echo $codeablex_textfield_4; ?>" 
           size="40">
    <?php 
}

/** 
 * Text of label
 * @since 1.0.1
 * 
 */
function codeablex_textfield_5_cb()
{
    $options = get_option('codeablex_admin'); 
    $codeablex_textfield_5 = (empty($options['codeablex_textfield_5'] )) 
 ? 'Your unique referral code: ' : 
 sanitize_text_field( $options['codeablex_textfield_5'] ); 
    ?>

    <input type="text" 
           name="codeablex_admin[codeablex_textfield_5]" 
           value="<?php echo $codeablex_textfield_5; ?>" 
           size="40">
    <?php 
}

/**
 ** Section Callbacks
 *  $id, $title, $callback, $page
 */
function codeablex_admin_section_cb()
{
    $html = '<hr>';
    echo $html;
}

// d.) render admin page
function codeablex_admin_section() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    
    // show error/update messages
    //settings_errors( 'codeablex_messages' );
    //$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'codeablex_admin';
    ?>
    <div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'codeablex_admin' );
        do_settings_sections( 'codeablex_admin' );
        submit_button( 'Save Settings' );
        ?>
    </form>
    <table><tbody>
    <tr><td><p>Export field is referral_plan. Verify numbers by accessing the user's profile (WP not members) and look at the last line.... the 'string' referral_plan is the NUMBER OF PLANS THAT ARE GIVING CREDIT TO. (every time someone uses your id to sign up, you get one credit---max 10)</p>
    <p>If you decide to NOT have a referral for a plan, then do not put the $10 referral amount in the product editor field. The referral will not work without a price added to the product.</p><p> This way you can control which products get referrals discounts.</p></td></tr>
    <tr><td><p>Selector style name for text is <code>#codeablex_checkout_field label</code>. This is the class name that is located in /css/codeablex-style.css.</p></td></tr>
    </tbody></table>
    <hr>
   
    </div>
    <?php 
} 