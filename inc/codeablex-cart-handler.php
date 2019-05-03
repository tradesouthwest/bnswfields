<?php
/**
* codeablex-cart-handler 
* @since 1.0.1
*/  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * a.) display info on cart page 
 * b.) render custom value in checkout after totals
 * c.) check if referral id is existing member
 * d.) add referral discount to checkout if passes 
 * e.) process field and make cookie
 * *********************************************************
 */
/**
 * a.)
 * Display info on cart page about discount
 * 
 * @param string $cart bool  
 * @param string $options
 * @return print html
 * @since 1.0.1
 */
add_action( 'woocommerce_after_cart_table', 'codeabelx_referral_get_referral_html' );
function codeabelx_referral_get_referral_html( $cart )
{ 
if ( !WC()->cart->is_empty() ):
   
    $options = get_option('codeablex_admin'); 
    $codeablex_textfield_2 = (empty($options['codeablex_textfield_2'] )) 
    ? 'Referral Discount on Checkout page' : 
    sanitize_text_field( $options['codeablex_textfield_2'] ); 

    ob_start();
    echo '
    <div id="referralDiv" class="referral_code">
    <p><label>' . esc_html( $codeablex_textfield_2 ) . '</label></p>
    </div>';
    print( ob_get_clean() );

endif;    
}

/**
 * b.)
 * Render referral id input field
 * 
 * @param string|COOKIE $chosen Session retrieve 
 * @param string|input  $args   Checkout field 
 * @since 1.0.1
 */
add_action( 'woocommerce_review_order_before_payment', 'codeabelx_checkout_input_choice' );
function codeabelx_checkout_input_choice() {
     
    $chosen = WC()->session->get('referral_id');
    $chosen = empty( $chosen ) ? WC()->checkout->get_value('referral_id') : $chosen;
         
    $args = array(
    'type'     => 'text',
    'class'    => array( 'form-row-wide input-short' ),
    'required' => false,
    'label'    => __( 'Referral ID' ),
    'default'  => ''
    );
     
    echo '<div id="checkout-text">';
    echo '<h3>Referral Id for Your Order!</h3>';
    woocommerce_form_field( 'referral_id', $args, $chosen );
    echo '</div>';
     
}
 

/**
 * c.)
 * Does this user/referral exist?
 *
 * @param  int|string|WP_User $user_id User ID or object.
 * @return bool               Whether the user exists.
 * @since 1.0.1
 *
 */
function codeablex_validate_does_user_exist( $referral_id ) 
{

    if( isset( $_POST['referral_id'] ) ) : 
        $referral_id =  WC()->session->get( 'referral_id' );
            if ( $referral_id instanceof WP_User ) 
            {
		        $referral_id = $referral_id->ID;
            }
    endif;
    return (bool) get_user_by( 'id', $referral_id );
}
/**
 * d.)
 * Add Fee and Calculate Total
 * Based on session's "token"
 * 
 * @param string $cart 
 */  
add_action( 'woocommerce_cart_calculate_fees', 'codeabelx_checkout_input_choice_fee', 20, 1 );
 
function codeabelx_checkout_input_choice_fee() 
{
     
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    
    // Check for the custom field value
    $referral_id = WC()->session->get( 'referral_id' );
    $ref = codeablex_validate_does_user_exist($referral_id);
    if( $ref === true ) : 

        foreach( WC()->cart->get_cart() as $cart_item ){
            $product_in_cart[] = $cart_item['product_id'];
            break;
        }
 
        $ref_fee = get_post_meta( $product_in_cart[0], 'codeabelx_referral_fee', true );
            if( empty( $ref_fee ) ) $ref_fee = '';
        //$subtotal = WC()->cart->get_cart_subtotal();
        $ref_fee = floatval( $ref_fee );
            WC()->cart->add_fee( __('Referral Discount' ), -$ref_fee );

    endif;

}
 

/**
 * d.)
 * Refresh Checkout if Input Changes
 * 
 * @uses jQuery to Ajax
 * Also adding footer scripts with styles
 */ 
 
add_action( 'wp_footer', 'codeablex_checkout_input_choice_refresh' );
 
function codeablex_checkout_input_choice_refresh() {
if ( ! is_checkout() ) return;
    ?>
    <script type="text/javascript">
    jQuery( function($){
        $('form.checkout').on('change', 'input[name=referral_id]', function(e){
            e.preventDefault();
            var p = $(this).val();
            $.ajax({
                type: 'POST',
                url: wc_checkout_params.ajax_url,
                data: {
                    'action': 'woo_get_ajax_data',
                    'referral_id': p,
                },
                success: function (result) {
                    $('body').trigger('update_checkout');
                }
            });
        });
    });
    </script>
    <?php
}
 

/**
 * e.)
 * Add Refferal ID to Session
 * Call the Ajax
 */
add_action( 'wp_ajax_woo_get_ajax_data', 'codeabelx_checkout_input_choice_set_session' );
add_action( 'wp_ajax_nopriv_woo_get_ajax_data', 'codeabelx_checkout_input_choice_set_session' );
 
function codeabelx_checkout_input_choice_set_session() {
    if ( isset($_POST['referral_id']) )
    {
        $referral = sanitize_key( $_POST['referral_id'] );
        WC()->session->set('referral_id', $referral );
        echo json_encode( $referral );
    }
    die();
}