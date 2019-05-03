<?php 
/**
 * @package bnswfields
 *
 */
defined( 'ABSPATH' ) or exit;
// Hook in
add_filter( 'woocommerce_checkout_fields' , 'bnsw_custom_override_checkout_fields', 30, 1 );

// Our hooked in function - $fields is passed via the filter!
function bnsw_custom_override_checkout_fields( $fields ) {
     $fields['billing']['billing_mobile_phone'] = array(
        'label'     => __('Billing Mobile Phone', 'woocommerce'),
    'placeholder'   => __('optional', 'woocommerce'),
    'required'  => false,
    'class'     => array('form-row-wide'),
    'clear'     => true
     );

     return $fields;
}

/**
 * Display field value on the order edit page
 */
 
add_action( 'woocommerce_admin_order_data_after_shipping_address', 
    'bnsw_custom_checkout_field_display_admin_order_meta', 10, 1 );

function bnsw_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Billing Mobile Phone').':</strong> ' 
    . get_post_meta( $order->get_id(), '_billing_mobile_phone', true ) . '</p>';
}

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
