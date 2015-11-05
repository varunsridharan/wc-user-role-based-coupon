<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 *
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WC_User_Role_Based_Coupon_Admin_Functions  {
    
    public function __construct() {
        add_action('woocommerce_coupon_options_usage_restriction',array($this,'add_box'));
		add_action('woocommerce_coupon_options_save',array($this,'save_restriction'));
    }
	
	public function add_box(){
		global $post;
		$allowed = get_post_meta($post->ID,WC_URBC_DB.'_allowed_roles',true);
		if(empty($allowed)){$allowed = array();}
		$roles = WC_URBC()->get_registered_roles();
		echo '<div class="options_group"> ';
			echo '<p class="form-field "> ';
				echo '<label for="">Allowed Roles</label>';
				echo '<select class="wc-enhanced-select" multiple="multiple" style="width:50%;" name="allowed_roles[]"> ';
				foreach($roles as $roleKey => $role){ 
					$selected = '';
					if(in_array($roleKey, $allowed)){$selected = 'selected';}
					echo '<option value="'.$roleKey.'" '.$selected.' > '.$role['name'].'</option>';
				}
				echo '<select > ';
			echo '</p>';
		echo '</div>';
		
	}
	 
	
	public function save_restriction($post_id){
		if(isset($_POST['allowed_roles'])){
			update_post_meta($post_id,WC_URBC_DB.'_allowed_roles',$_POST['allowed_roles']);
		}
	}
	
}



?>