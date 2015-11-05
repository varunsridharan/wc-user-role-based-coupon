<?php
/**
 * Plugin Name:       WC User Role Based Coupon
 * Plugin URI:        https://wordpress.org/plugins/wc-user-role-based-coupon/
 * Description:       Sample Plugin For WooCommerce
 * Version:           0.1
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       wc-user-role-based-coupon
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * GitHub Plugin URI: @TODO
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
class WC_User_Role_Based_Coupon {
	/**
	 * @var string
	 */
	public $version = '0.1';

	/**
	 * @var WooCommerce The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;
    
    protected static $functions = null;

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->define_constant();
        $this->load_required_files();
        $this->init_class();
        add_action( 'init', array( $this, 'init' ));
    }
    
    /**
     * Triggers When INIT Action Called
     */
    public function init(){
        add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile',  array( $this, 'load_plugin_mo_files' ), 10, 2);
    }
    
    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files(){
       $this->load_files(WC_URBC_PATH.'includes/class-*.php');
        
       if($this->is_request('admin')){
           $this->load_files(WC_URBC_PATH.'includes/admin/class-*.php');
       } 

    }
    
    /**
     * Inits loaded Class
     */
    private function init_class(){
        new WC_User_Role_Based_Coupon_Activation('wc-urbc','welcome-screen-about','welcome-template.php','Welcome To WC User Role Based Coupon',__FILE__);
        //$slug = '',$plugin_url = 'welcome-screen-about',$page_html = 'page-html.php',$menu_NAME = 'Welcome To Welcome Page',$activate_File = ''
        self::$functions = new WC_User_Role_Based_Coupon_Functions;
        
        if($this->is_request('admin')){
            $this->admin = new WC_User_Role_Based_Coupon_Admin;
        }
    }
    
    
    protected function func(){
        return self::$functions;
    }
    

    protected function load_files($path,$type = 'require'){
        foreach( glob( $path ) as $files ){

            if($type == 'require'){
                require_once( $files );
            } else if($type == 'include'){
                include_once( $files );
            }
            
        } 
    }
    
    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded(){
        load_plugin_textdomain(WC_URBC_TXT, false, WC_URBC_LANGUAGE_PATH );
    }
    
    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if (WC_URBC_TXT === $domain)
            return WC_URBC_LANGUAGE_PATH.'/'.get_locale().'.mo';

        return $mofile;
    }

	/**
     * Define Required Constant
     */
    private function define_constant(){
        $this->define('WC_URBC_NAME','WooCommerce User Role Based Coupon'); # Plugin Name
        $this->define('WC_URBC_SLUG','wc-urbc'); # Plugin Slug
        $this->define('WC_URBC_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
        $this->define('WC_URBC_LANGUAGE_PATH',WC_URBC_PATH.'languages');
        $this->define('WC_URBC_TXT','wc-user-role-based-coupon'); #plugin lang Domain
        $this->define('WC_URBC_URL',plugins_url('', __FILE__ )); 
        $this->define('WC_URBC_FILE',plugin_basename( __FILE__ ));
		$this->define('WC_URBC_DB','wc_urbc');
        $this->define("WC_URBC_V",$this->version);
    }
    
    /**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
    protected function define($key,$value){
        if(!defined($key)){
            define($key,$value);
        }
    }
     

  	/**
     * Get Registered WP User Roles
     * @return Array
     */
    public function get_registered_roles(){
        $user_roles = get_editable_roles();
        $user_roles['logedout'] = array('name' => 'Visitor / LogedOut User');  
        return $user_roles;
    }  
	
	
	/**
	 * Get Current Logged In User Role
	 * @since 0.1
	 */
	public function current_role(){
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
        if($user_role == null){
            return 'logedout';
        }
		return $user_role;
	}
	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
    
    
    
}
new WC_User_Role_Based_Coupon;
function WC_URBC(){
	return WC_User_Role_Based_Coupon::get_instance();
	
}
?>