<?php
/**
 * codeablex extended fields bnswfields
 * @since 1.0.1
 */  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
 * Get currently editing user ID (frontend account/edit profile/edit other user).
 *
 *
 * @return int
 */
function bnswfields_get_edit_user_id() {
    return isset( $_GET['user_id'] ) ? (int) $_GET['user_id'] : get_current_user_id();
}
/**
 * Add fields to admin area.
 *
 * @see https://iconicwp.com/blog/the-ultimate-guide-to-adding-custom-woocommerce-user-account-fields/
 */
function bnswfields_print_user_admin_fields() 
{

	$fields = bnswfields_get_account_fields();
	ob_start(); 
	?>
	<h2><?php _e( 'Additional Information', 'bnswfields' ); ?></h2>
	<table class="form-table" id="bnswfields-additional-information">
		<tbody>
		<?php foreach ( $fields as $key => $field_args ) { ?>
			<tr>
				<th>
					<label for="<?php echo $key; ?>"><?php echo $field_args['label']; ?></label>
				</th>
				<td>
					<?php $field_args['label'] = false; ?>
					<?php woocommerce_form_field( $key, $field_args ); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php
	$htm = ob_get_clean();
	echo $htm;
}

//add_action( 'show_user_profile', 'bnswfields_print_user_admin_fields', 30 ); // admin: edit profile
//add_action( 'edit_user_profile', 'bnswfields_print_user_admin_fields', 30 ); // admin: edit other users

/**
 * Get additional account fields.
 *
 *
 * @return array
 */
function bnswfields_get_account_fields() 
{

	return apply_filters( 'bnswfields_account_fields', 
		array(
			'billing_mobile_phone' => 
			array(
				'type'        => 'text',
				'label'       => __( 'Billing Mobile Phone', 'bnswfields' ),
				'placeholder' => __( 'optional', 'bnswfields' ),
				'required'    => false,
		),
	) );
}
