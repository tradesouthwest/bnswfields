<?php 
/**
* codeablex-checkout-order data and order data
* @since 1.0.0
*/  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * a.) validation of submit - create line item in checkout
 * b.) save data to parse later as meta data
 * c.) calculate credit to referrer
 * d.) add referral info after_user_membership_billing_details 
 * e.) add referral info to order email
 * f.) get your id to display on My Account
 * g.) display message on My Account
 * *********************************************************
 */
/**
 * a.)
 * Create order line item
 * 
 * @param string $item   Adds meta data if not empty
 * @param string $values Meta data for line item
 */
add_action( 'woocommerce_checkout_create_order_line_item', 'codeablex_meta_to_line_item', 20, 4 );
 
function codeablex_meta_to_line_item( $item, $cart_item_key, $values, $order )
{
    if ( empty( $values['codeablex_referral_id'] ) ) {
        return;
    }
 
    $item->add_meta_data( __( 'Referrer', 'codeablex' ), 
                $values['codeablex_referral_id'] );          
}


/**
 * b.)
 * 
 * And save to checkout as order_item_meta/post_meta
 * (Products are Posts)
 * 
 * @param string $order_id Globals
 * @param string $posted   wp global
 */
add_action( 'woocommerce_checkout_update_order_meta', 'codeablex_save_extra_checkout_fields', 10, 2 );
function codeablex_save_extra_checkout_fields( $order_id, $posted )
{
    if( isset( $posted['codeablex_referral_id'] ) ) {
        update_post_meta( $order_id, '_codeablex_referral_id', sanitize_text_field( 
            $posted['codeablex_referral_id'] ) );
    }
}


/**
 * c.)
 * Give member one credit per referral.
 * 
 * @param $referral_id     user_id to check
 * @param $referral_plan   running count of referrals
 * @uses update_user_meta
 * @since 1.0.1 
 */
add_action( 'woocommerce_checkout_update_order_meta', 'codeablex_validate_referral_id_now', 12, 1 );
function codeablex_validate_referral_id_now( $order ) 
{

    $referral_id = WC()->session->get('referral_id');    
    if ( ! $referral_id )  return;

    $order     = wc_get_order( $order_id ); 
    $max_plans = (empty($options['codeablex_textfield_4'] )) 
               ? '10' : $options['codeablex_textfield_4']; 
    //using raw admin value +1 to equal less than expression
    $max_plan = intval( $max_plans + 1 );
    
        $key       = 'referral_plan';
        $single    = true;
        $ref_plans = get_user_meta( $referral_id, $key, $single ); 
        // Update customer source.
        if( $ref_plans < $max_plan ) : 

            $ref_nums = ( empty( $ref_plans )) ? '0' : absint( $ref_plans );
            update_user_meta( $referral_id, 'referral_plan', $ref_nums + 1 );            
        
        endif;
}


/**
 * d.)
 * Display number of referrals in Edit User membership
 * 
 * @param string $user_membership 
 * @since 1.0.1
 */
function codeablex_display_referral_count_inadmin($user_membership)
{

    //$order = new WC_Order( $order_id );
    $ref_plans = get_user_meta(  $user_membership->get_user_id(), 'referral_plan', true );
    if( empty( $ref_plans ) ) $ref_plans = 'none';
    printf( '<p class="form-field billing-detail"><label>Referrals: </label> %s </p>',          
        $ref_plans 
        );
}
add_filter( 'wc_memberships_after_user_membership_billing_details', 'codeablex_display_referral_count_inadmin', 10, 1 );


/**
 * e.)
 * Add the field to order emails
 * 
 * @since 1.0.1
 */
add_filter('woocommerce_email_order_meta_keys', 
        'codeablex_custom_checkout_field_order_meta_keys' );
function codeablex_custom_checkout_field_order_meta_keys( $keys ) {
    $keys[] = 'Referral Fee Discount';
    return $keys;
} 

/**
 * f.)
 * Displays ID on My Account page
 * Gives users a referral number to use for referrals
 * 
 * @since 1.0.1
 */
function codeablex_show_user_idon_account_page()
{

    $user_id = get_current_user_id();
    $args = array( 
        'status' => array( 
            'active', 
            'complimentary', 
            'pending' 
        ),
    );  
    $active_memberships = wc_memberships_get_user_memberships( $user_id, $args );
    if( $active_memberships )
        return absint($user_id);
}


/**
 * g.)
 * Add text information to My Account Dashboard
 * Display credits on My Account Dashboard
 * 
 * @param string
 * @return HTML to page
 * @uses plugin options
 * @since 1.0.1
 */
function codeablex_referral_support_content() {
    $options               = get_option('codeablex_admin'); 
    $codeablex_textfield_3 = (empty($options['codeablex_textfield_3'] )) 
                             ? '' : $options['codeablex_textfield_3'];
    $codeablex_textfield_5 = (empty($options['codeablex_textfield_5'] )) 
                             ? '' : $options['codeablex_textfield_5'];
    $max_plans             = (empty($options['codeablex_textfield_4'] )) 
                           ? '10' : $options['codeablex_textfield_4']; 
    $ref_plans = get_user_meta( get_current_user_id(), 'referral_plan', true );
    if( empty( $ref_plans ) ) $ref_plans = 'none';
    
    ob_start();
    echo '
    <div class="referral-div">
        <div class="referral-textarea">
            ' . html_entity_decode( $codeablex_textfield_3 ) . '
        </div>
        <p class="account-number">' . esc_html( $codeablex_textfield_5 ) . '<strong> ' . codeablex_show_user_idon_account_page() . '</strong></p>
        <p>Current number of referrals: ' . intval( $ref_plans ) . '</p>
        <p>Maximum number per year: ' . intval( $max_plans ) . '</p>
    </div>';
    $html = ob_get_clean();
        
        echo $html;
}
add_action( 'woocommerce_account_dashboard', 'codeablex_referral_support_content' );
