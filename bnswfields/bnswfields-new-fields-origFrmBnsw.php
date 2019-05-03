<?php 

 
add_action( 'woocommerce_after_checkout_billing_form', 'bnsw_add_user_field_to_checkout' );
  
function bnsw_add_user_field_to_checkout( $checkout ) {
 
$current_user = wp_get_current_user();
$saved_phn = $current_user->user_url;
 
woocommerce_form_field( 'billing_mobile_phone', array(        
'type' => 'text',        
'class' => array('billing_mobile_phone form-row-wide'),        
'label' => __('Mobile Phone for Billing Questions'),        
'placeholder' => __('optional'),        
'required' => false
), 
$saved_phn ); 
  
}
 
// ------------------------
// 2. Save Field Into User Meta
  
add_action( 'woocommerce_checkout_update_user_meta', 'bnsw_checkout_field_update_user_meta' );
  
function bnsw_checkout_field_update_user_meta( $user_id ) { 
 
if ( $user_id && $_POST['billing_mobile_phone'] ) {

   $args = array(
                'ID' => $user_id,
                'billing_mobile_phone' => esc_attr( $_POST['billing_mobile_phone'] )
        );      
       
    wp_update_user( $args );
}
 
}

/*
member_phone, member_mobile, dbem_phone, member_join_date, billing_mobile,
member_number, member_type, _stripe_customer_id
*/


/**
 * Save the whales... I mean save post meta
 * Hook callback functions to save custom fields 
 *
 * meta_id[26117] post_id[1696] meta_key[_billing_mobile_phone] meta_value[int]
 * https://www.ibenic.com/how-to-add-woocommerce-custom-product-fields/
 */
 
add_action( 'woocommerce_process_product_meta', 'bnsw_save_billing_mobile_phone' );
function bnsw_save_billing_mobile_phone( $post_id ) 
{
//global $product;

    $custom_field_value = isset( $_POST['_billing_mobile_phone'] ) 
                               ? $_POST['_billing_mobile_phone'] : '';
    $custom_field_clean = sanitize_text_field( $custom_field_value );
    $product = wc_get_product( $post_id );
    $product->update_meta_data( '_billing_mobile_phone', $custom_field_clean );
    $product->save();
}
/**
 * Show custom field in order overview
 * @param array $cart_item
 * @param array $order_item
 * @return array
 */ 
add_filter( 'woocommerce_order_item_product', 'bnsw_order_item_product', 10, 2 ); 
 function bnsw_order_item_product( $cart_item, $order_item )
{
    if( isset( $order_item['bnsw_custom_option'] ) ){ 
        $cart_item_meta['bnsw_custom_option'] = 
        $order_item['bnsw_custom_option']; 
    }
    if( isset( $order_item['billing_mobile_phone'] ) ){ 
        $cart_item_meta['billing_mobile_phone'] = 
        $order_item['billing_mobile_phone']; 
    }
    return $cart_item;
}

/**
 * Add meta to order item
 * @param  int $item_id
 * @param  array $values
 * @return void
 */
add_action( 'woocommerce_add_order_item_meta', 'bnsw_add_order_item_meta', 10, 2 );
 function bnsw_add_order_item_meta( $item_id, $values ) {
 
    if ( ! empty( $values['bnsw_custom_option'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'bnsw_custom_option', 
                          $values['bnsw_custom_option'] );           
    }
    //billing_mobile_phone
    if ( ! empty( $values['billing_mobile_phone'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'billing_mobile_phone', 
                          $values['billing_mobile_phone'] );           
    }

}

// ------------------------
// 2. Save Field Into User Meta
  
add_action( 'woocommerce_checkout_update_user_meta', 'bnsw_checkout_field_update_user_meta' );
  
function bnsw_checkout_field_update_user_meta( $user_id ) { 
 
    if ( $user_id && $_POST['billing_mobile_phone'] ) {

    $args = array(
                'ID' => $user_id,
                'billing_mobile_phone' => esc_attr( $_POST['billing_mobile_phone'] )
            );      
       
    wp_update_user( $args );
    }
}

/*
member_phone, member_mobile, dbem_phone, member_join_date, billing_mobile,
member_number, member_type, _stripe_customer_id
*/

