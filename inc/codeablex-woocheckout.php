<?php 
/**
 * Agree to autorenewal checkout
 * 1. Render checkout field
 * 2. Validate answer
 * 3. Update to wpdb
 * 4. Add field to admin
 * 5. Save (again) if changed
 * 6. Add to memebers account page
 */
// Render checkout page dropdown.
add_action('woocommerce_after_checkout_billing_form',
'codeablex_add_agree_checkout_field', 9);
function codeablex_add_agree_checkout_field( $checkout ) 
{   
    $codeablex_textfield_1 = get_option( 'codeablex_admin' )['codeablex_textfield_1'];
    ?>
    <div id="codeablex_checkout_field" class="codeablex-block">
    <label>
    <?php 
    woocommerce_form_field( 
        'codeablex_select_agree', array(
        'type'          => 'select',
        'class'         => array( 'wps-drop' ),
        'required'      => true,
        'label'         => $codeablex_textfield_1,
        'options'       => array(
            'blank'		=> __( 'Select an answer please', 'codeablex' ),
            'yes'	=> __( 'YES', 'codeablex' ),
            'no'	=> __( 'NO', 'codeablex' )
        )
    ),
    $checkout->get_value( 'codeablex_select_agree' ) );
    ?></label></div>
    <?php 
}

//validation
add_action('woocommerce_checkout_process', 'codeablex_checkout_field_process');
function codeablex_checkout_field_process()
{
	// if the field is set, if not then show an error message.
    if (!$_POST['codeablex_select_agree']) 
    wc_add_notice(__('Please check agree notice.') , 'error');
}

/**
 * Update value of field
 */
add_action( 'woocommerce_checkout_update_order_meta', 
        'codeablex_checkout_field_update_order_meta' );
function codeablex_checkout_field_update_order_meta($order_id)
{
	if (!empty($_POST['codeablex_select_agree'])) {
        update_post_meta($order_id, 'codeablex_select_agree', 
        sanitize_text_field($_POST['codeablex_select_agree']));
	}
}

/**
* Add field to Admin orders page
*/
add_action( 'woocommerce_admin_order_data_after_billing_address', 
'codeablex_agree_field_display_admin_order_meta', 10, 1 );
function codeablex_agree_field_display_admin_order_meta($order)
{
    // Get the custom field value
    
$select_agree = get_post_meta( $order->get_id(), 'codeablex_select_agree', true );
$args = array( 'name'  => 'codeablex_select_agree',
               'value'   => esc_attr( $select_agree ),
               'options'   => array(
                                  "yes" => "YES", 
                                  "no"  => "NO" ),
            );
if( ! empty ( $args['options'] && is_array( $args['options'] ) ) )
    {
    print( '<p><form action="" method="post">
    <label for="codeable_select_agree"><strong>Agree to Renewal </strong></label>');
     
    $options_markup = '';
    $value          = $args['value'];
        foreach( $args['options'] as $key => $label )
        {
            $options_markup .= sprintf( '<option value="%s" %s>%s</option>', 
            $key, selected( $value, $key, false ), $label );
        }
        printf( '<br><span class="selection">
        <select name="%1$s" id="%1$s">%2$s</select>
        </span>',  
        $args['name'],
        $options_markup );
    }

        wp_nonce_field( 'codeablex_agree', 'codeablex_agree' );
    printf( '<input type="hidden" name="order_id" value="%1$s">', 
            $order->get_id() );
    print( '</form></p>' );    
} 
//save routine
add_action( 'save_post', 'codeablex_select_agree_field_save');
function codeablex_select_agree_field_save($order_id) 
{   
    global $post;

    $post = $order_id;

    //$order_id = absint( $_POST[ 'cdx_order_id' ] );
    $is_autosave = wp_is_post_autosave( $order_id );
    $is_revision = wp_is_post_revision( $order_id );
    $is_valid_nonce = ( isset( $_POST[ 'codeablex_agree' ] ) && 
    wp_verify_nonce( $_POST[ 'codeablex_agree' ], basename( __FILE__ ) ) 
    ) ? 'true' : 'false';
 
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    if( ! current_user_can( 'edit_post', $order_id ) ) 
    {
    return;
    }
    //update post meta
    if( isset( $_POST[ 'codeablex_select_agree' ] ) ) {
        update_post_meta($order_id, 'codeablex_select_agree', 
        sanitize_text_field($_POST[ 'codeablex_select_agree' ] ) );
        }
} 

//add checkout data to user_meta - Members 
function codeablex_checkout_update_user_meta( $order_id ) {
    $order = new WC_Order($order_id);
    $customer_id = $order->customer_user;    

    if (isset($_POST['codeablex_select_agree'])) {
        $value = sanitize_text_field( $_POST['codeablex_select_agree'] );
        update_user_meta( $customer_id, 'codeablex_select_agree', $value);
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'codeablex_checkout_update_user_meta', 10, 2 );