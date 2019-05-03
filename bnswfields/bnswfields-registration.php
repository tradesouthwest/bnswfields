<?php
//
function bnsw_woocommerce_edit_my_account_page() 
{
    return apply_filters( 'woocommerce_forms_field', array(
        'billing_mobile_phone' => array(
            'type'        => 'text',
            'class' => array('billing_mobile_phone form-row-wide'),     
            'label'       => __( 'Billing Mobile Phone', 'woocommerce' ),
            'placeholder' => __( 'optional', 'woocommerce' ),
            'required'    => false,
        ),
    ) );
}
function bnsw_edit_my_account_page_woocommerce() 
{
    $fields = bnsw_woocommerce_edit_my_account_page();
    foreach ( $fields as $key => $field_args ) {
        woocommerce_form_field( $key, $field_args );
    }
}
add_action( 'woocommerce_register_form', 'bnsw_edit_my_account_page_woocommerce', 15 );

/**
 * register fields Validating.
 */
function bnsw_validate_extra_register_fields( $username, $email, $validation_errors ) {
    if ( isset( $_POST['billing_mobile_phone'] ) && empty( $_POST['billing_mobile_phone'] ) ) {
        $validation_errors->add( 'billing_mobile_phone_error', __( '<strong>Error</strong>: mobile_phone is required!', 'woocommerce' ) );
    }
        return $validation_errors;
}
//add_action( 'woocommerce_register_post', 'bnsw_validate_extra_register_fields', 10, 3 );