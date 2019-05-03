<?php 
/**
 * WooCommerce 
 * Custom fields added to the "Member Order Area"
 */
/**
 * Adds a new column to the "My Orders" table in the account.
 *
 * @param string[] $columns the columns in the orders table
 * @return string[] updated columns
 */
function codeablex_wc_add_new_account_orders_column( $columns ) {

    $new_columns = array();

    foreach ( $columns as $key => $name ) {

        $new_columns[ $key ] = $name;

        // add ship-to after order status column
        if ( 'order-status' === $key ) {
            $new_columns['codeablex-select-agree'] = 
            __( 'Auto Renewal', 'codeablex' );
        }
    }

    return $new_columns;
}
add_filter( 'woocommerce_my_account_my_orders_columns', 'codeablex_wc_add_new_account_orders_column' ); 

/**
 * Adds data to the custom column in "My Account > Orders".
 *
 * @param \WC_Order $order the order object for the row
 */
add_action( 'woocommerce_my_account_my_orders_column_codeablex-select-agree', 
            'codeablex_wc_orders_update_codeablex_select_agree' ); 

function codeablex_wc_orders_update_codeablex_select_agree( $order ) 
{

    global $post, $order_id; 
    $order_id = absint($order->get_id());
    $select_agree = '';
    $select_agree = get_post_meta( $order->get_id(), 
                              'codeablex_select_agree', true );
    $args = array( 'name'  => 'codeablex_select_agree',
                   'value'   => esc_attr( $select_agree ),
                   'options'   => array(
                                      'yes' => 'YES', 
                                      'no'  => 'NO' 
                                    ), );
    if( ! empty ( $args['options'] && is_array( $args['options'] ) ) )
        {
        print( '<p><form method="POST" action=""><span style="margin: 0 auto">');
         
        $options_markup = '';
        $value          = $args['value'];
            foreach( $args['options'] as $key => $label )
            {
                $options_markup .= sprintf( '<option value="%s" %s>%s</option>', 
                $key, selected( $value, $key, false ), $label );
            }
            printf( '<span class="selection">
            <select name="%1$s" id="%1$s" %2$s>%3$s</select>
            </span></span>',  
            $args['name'],
            'onchange="this.form.submit()"',
            $options_markup );
        }
    ?>
      <?php  wp_nonce_field( 'codeablex_agree', 'codeablex_agree' ); ?>
    <?php 
        printf( '<input type="hidden" name="order_id" value="%1$s">', 
        $order->get_id() );
        print( '</form></p>' );

    if ( isset( $_POST['codeablex_select_agree'] ) )
    {   
        
        $is_valid_nonce = ( isset( $_POST[ 'codeablex_agree' ] ) && 
        wp_verify_nonce( $_POST[ 'codeablex_agree' ], basename( __FILE__ ) ) 
        ) ? 'true' : 'false';
        if($is_valid_nonce ) 
        {

        $success = update_post_meta( $order->get_id(), 
                                           'codeablex_select_agree', 
               sanitize_text_field( $_POST['codeablex_select_agree'] ) 
                );
        if( $success ) :    
            //F3
    codeablex_email_renew_agree_notice(
    $order_id, $posted= $_POST['codeablex_select_agree']);        
   
   
    wp_redirect($_SERVER['HTTP_REFERER']); 
           
        else: 
                    echo 'please try sending again.'; 
                           
            endif; 
            

        }
    }
}


//array('Content-Type: text/html; charset=UTF-8');
function codeablex_set_html_mail_content_type( $content_type ) 
{
    return 'text/html';
}

function codeablex_set_html_mail_charset( $charset ) 
{
    return 'UTF-8';
}

/**
 * send notice of changes
 */
function codeablex_email_renew_agree_notice($order_id, $posted) 
{

    global $woocommerce, $post;
    $order_meta    = get_post_meta( $order_id );
    $customer_email = $order_meta["_billing_email"][0];
    add_filter( 'wp_mail_content_type', 'codeablex_set_html_mail_content_type' );
    add_filter( 'wp_mail_charset', 'codeablex_set_html_mail_charset' );
    
    $to_emails  = array();
    //$customer_email = get_post_meta( $order_id, '_billing_email', true );
    $send_to    = get_option('admin_email');
    $to_emails  = array( $send_to, $customer_email );
    $headers = '';
    $headers   .= "From: Bicycle NSW Membership";
    $headers   .= "Reply to: Bicycle NSW <info@bicyclensw.org.au>"; 
    $subject    = __( 'Updated Renewal Agreement', 'woocommerce' );
    $message    = '';
    $message    .= '
    <p>Membership AutoRenewal Updated to: <strong>Renew ' . $posted . '</strong></p> 
    <h4>Order: ' . $order_id . '</h4>
    <p></p>';
    $body = wp_specialchars_decode( $message, ENT_QUOTES );

    $sendSuccess = wp_mail( $to_emails, $subject, $body, $headers );
    remove_filter( 'wp_mail_charset', 'codeablex_set_html_mail_charset' );
    remove_filter( 'wp_mail_content_type', 'codeablex_set_html_mail_content_type' );
    if ( $sendSuccess ) {
    
    echo 'Updated';
    }
}
