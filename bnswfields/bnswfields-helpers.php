<?php  
defined( 'ABSPATH' ) or exit;

// Removes ability to change Theme color for the users
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
function bnsw_remove_website_row() {

	echo "<script>
	jQuery(document).ready(function($){
	$('tr.user-rich-editing-wrap').remove();
	$('tr.user-syntax-highlighting-wrap').remove();
	$('tr.user-comment-shortcuts-wrap').remove();
	});</script>";
	
	}
add_action('admin_head-profile.php', 'bnsw_remove_website_row');
/**
 * Automatic Custom Fields removes custom meta boxes by defauult
 * 
 * Add filter to reinstall custom data boxes.
 */
//add_filter('acf/settings/remove_wp_meta_box', '__return_false');


/**
 * order_item_id which connects to the item ID  
 * 
 * @uses woocommerce_order_items table
 * meta_id       – ID of meta.
 * order_item_id – ID of the item in the order.
 * meta_key      – Key used for storing the meta (and describing it).
 * meta_value    – Value of the stored meta.
 */
add_action( 'woocommerce_account_dashboard', 'bnswfields_get_customer_number_from_order', 10, 1);
function bnswfields_get_customer_number_from_order($customer_id)
{
	$query = new WC_Order_Query( array(
        'limit' => 1,
        'customer_id' => $customer_id,
        'orderby' => 'date',
        'order' => 'ASC',
        'return' => 'ids',
    ) );
    $orders = $query->get_orders();

    return $orders[0];
}

/**
 * Saves avatar image to user_meta
 * $user_id, $meta_key, $meta_value, $prev_value = ''
 * @param $_POST uploaded file
 * @param int $user_id ID of user to assign image to
 */ 
add_action( 'personal_options_update', 'bnswfields_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'bnswfields_save_extra_user_profile_fields' );

function bnswfields_save_extra_user_profile_fields( $user_id )
{
    
    if( isset( $_POST['member_number_nonce_field'] ) && 
        wp_verify_nonce( $_POST['member_number_nonce_field'], 
                                'member_number_nonce_action') ) : 

        $user_id    = absint( $_POST['user_uid'] );
        if ( !current_user_can( 'edit_user', get_current_user_id() ) ) return false; 
        
        //$meta_value = sanitize_text_field( $_POST['member_number'] );
        $unique = sanitize_text_field( $_POST['unique'] );
        if( $unique ) {
        
            update_usermeta( $user_id, 'member_number', 
                sanitize_text_field( $_POST['member_number'] ) 
            ); 
        }
    endif;
}

/**
 * Add new fields below Account Management section.       
 *
 * @param WP_User $user User object.     
 */
add_action( 'edit_user_profile', 'bnswfields_add_member_number_inprofile' );
add_action( 'show_user_profile', 'bnswfields_add_member_number_inprofile' );
function bnswfields_add_member_number_inprofile()
{

    $user_uid    = $first_order = $member_number = $key = '';
    $user_uid    = bnswfields_get_edit_user_id();
    $first_order = bnswfields_get_customer_number_from_order( $user_uid );    
    $key         = ( empty ( 'member_number' ) ) ? 'user_id' : 'member_number';  
    
    $member_number = get_user_meta( $user_uid, $key, true ); 
    
        printf( '<table class="form-table"><tbody>
        <tr class="first_order">
        <th>
        <label for="first_order">%s</label>
        </th>
        <td>
        <input id="first_order" class="text-field" type="text" value="%s" readonly /></td>
        </td></tr>
        <tr class="member_number">
        <th>
        <label for="member_number">%s</label>
        </th>
        <td>
        <input id="member_number" class="text-field" name="member_number" type="text" 
        value="%s" />
        %s  
        <input id="unique" type="hidden" name="unique" value="%s">
        <input id="user_uid" type="hidden" name="user_uid" value="%s">
        </td></tr>
        </tbody></table>',
            __( 'Very First Order #: ', 'codeablex' ),          
            absint( $first_order ),
            __( 'Official Member #: ', 'codeablex' ),          
            esc_attr( $member_number ),
            wp_nonce_field( 'member_number_nonce_action', 'member_number_nonce_field' ),
            'uniquebnsw' . time(),
            $user_uid
        );

}
/**
 * Get currently editing user ID (frontend account/edit profile/edit other user).
 *
 *
 * @return int
 */
function bnswfields_get_edit_user_id() {
    return isset( $_GET['user_id'] ) ? (int) $_GET['user_id'] : get_current_user_id();
}

/**
 * Get list of all user meta 
 * @package WordPress
 * @uses    shortcode [bnsw_userprint] Or whatever shortcode you register it as.
 */
function bnswfields_user_list_meta($atts)
{

    if ( !isset( $atts['user_id'] ) ){
		$user = wp_get_current_user();
		$atts['user_id'] = $user->ID;
	}
    ob_start();
        print( '<h4>User Meta</h4>' );
    
    $user_info = get_user_meta($atts['user_id']);
    echo '<pre>';
    print_r($user_info);
    echo '</pre>';

    $output = ob_get_clean();
    
        return $output;
} 
