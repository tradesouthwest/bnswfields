<?php
/**
 * codeablex admin and checkout functions
 * @since 1.0.1
 * @param $referral_fee  = amount _product_meta
 * @param $referral_id   = referrer _user_meta
 * @param $referral_plan = referrers count _user_meta
 * TODO _referral_credit = amount _user_meta['account_field']
 * 
 * a.) render product field
 * b.) save product field
 * c.) public view render on product page
 * d.) add field to admin orders
 * e.) add field to members profile (referral_plan (count))
 * ************************************************************
 */  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * a.)
 * *****************************************
 * Post meta and Cart meta
 * Add Number Field to admin General tab
 * *****************************************
 */
add_action( 'woocommerce_product_options_general_product_data', 'codeabelx_referral_general_product_data_field' );
function codeabelx_referral_general_product_data_field() 
{
    woocommerce_wp_text_input( array( 
        'id'        => 'codeabelx_referral_fee', 
        'label'     => __( 'Referral Discount', 'codeablex' ), 
        'data_type' => 'price' 
    ) );
}


/**
 * b.)
 * Save the whales... I mean save post meta
 * Hook callback functions to save custom fields 
 *
 * meta_id[26117] post_id[1696] meta_key[_referral_fee] meta_value[int]
 * https://www.ibenic.com/how-to-add-woocommerce-custom-product-fields/
 */
add_action( 'woocommerce_process_product_meta', 'codeabelx_referral_save_referral_fee' );
function codeabelx_referral_save_referral_fee( $post_id ) 
{
//global $product;
    $custom_field_value = isset( $_POST['codeabelx_referral_fee'] ) 
                               ? $_POST['codeabelx_referral_fee'] : '';
    $custom_field_clean = sanitize_text_field( $custom_field_value );
    $product = wc_get_product( $post_id );
    $product->update_meta_data( 'codeabelx_referral_fee', $custom_field_clean );
    $product->save();
}


/**
 * c.)
 * Display custom field on the product page
 * @since 1.0.1
 */
add_action( 'woocommerce_before_add_to_cart_button', 'codeabelx_referral_display_custom_field' );
function codeabelx_referral_display_custom_field() 
{
    global $post;
    
    // Check for the custom field value
    $product = wc_get_product( $post->ID );
    $codeabelx_referral = $product->get_meta( 'codeabelx_referral_fee' );
    if( $codeabelx_referral ) {
    // Only display our field if we've got a value for the field
        printf(
            '<div style="price referral">
            <label for="codeabelx-referral-fee-field">
            Save %s with referral!</label>
            <input type="hidden" 
            id="codeabelx-referral-fee-field" 
            name="codeabelx_referral_fee" 
            value="' . $codeabelx_referral . '"></div>',
            esc_html( '$' . $codeabelx_referral )
        );
    }
} 


/**
 * add field to member profile
 * 
 * @param string|input $fields WC_input
 */
function codeablex_add_customer_field_toprofile( $fields )
{
$fields['referral_plan'] = array(
    'title' => __( 'Referrals', 'codeablex' ),
    'fields' => array(
        'referral_plan' => array(
            'label' => __( 'Number of: ', 'codeablex' ),
            'hide_in_checkout'     => true,
            'hide_in_registration' => true,
            'sanitize' => 'wc_clean',
            'class' => 'regular-text readonly readonly-disabled',
        )
    )
);        
return $fields;
}
add_filter( 'woocommerce_customer_meta_fields', 'codeablex_add_customer_field_toprofile', 99, 1 ); 
/**
 * Disable form fields to make read-only
 * To apply, add html attribute via jQuery if on correct page.
 *
 */
function codeablex_input_disable_field() {
    
    global $pagenow;
    if( !is_admin() ) return; 
    if( $pagenow == "user-edit.php" ) :
	?>
	<script type="text/javascript">
		jQuery(function($) {
			$('input[name="referral_plan"]').attr('disabled', true);
		});
	</script>
	<?php
    endif;
}
add_action( 'admin_footer-user-edit.php', 'codeablex_input_disable_field' );