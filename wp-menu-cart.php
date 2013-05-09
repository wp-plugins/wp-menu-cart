<?php
/*
Plugin Name: WP Menu Cart
Plugin URI: www.wpovernight.com/plugins
Description: Extension for your e-commerce plugin (WooCommerce, WP-Ecommerce, Easy Digital Downloads, Eshop or Jigoshop) that places a cart icon with number of items and total cost in the menu bar. Activate the plugin, set your options and you're ready to go! Will automatically conform to your theme styles.
Version: 2.1.1
Author: Jeremiah Prummer, Ewout Fernhout
Author URI: www.wpovernight.com/about
License: GPL2
*/

class WpMenuCart {	 

	public static $plugin_slug;

	/**
	 * Construct.
	 */
	public function __construct() {
		self::$plugin_slug = basename(dirname(__FILE__));

		$this->includes();
		register_activation_hook( __FILE__, array( 'WpMenuCart_Settings', 'default_settings' ) );
		$this->options = get_option('wpmenucart');
		$this->settings = new WpMenuCart_Settings();
		
		//print_r($this->options);
		
		if (isset($this->options['shop_plugin'])) {
			switch ($this->options['shop_plugin']) {
				case 'woocommerce':
					include_once( 'includes/wpmenucart-woocommerce.php' );
					$this->shop = new WPMenuCart_WooCommerce();
					break;
				case 'jigoshop':
					include_once( 'includes/wpmenucart-jigoshop.php' );
					$this->shop = new WPMenuCart_Jigoshop();
					break;
				case 'eshop':
					include_once( 'includes/wpmenucart-eshop.php' );
					$this->shop = new WPMenuCart_eShop();
					break;
			}
		}
				
		add_action( 'plugins_loaded', array( &$this, 'wpmenucart_languages' ), 0 );

		add_action('wp_print_styles', array( &$this, 'load_styles' ), 0 );

		//grab menu names
		if ( isset( $this->options['menu_name_1'] ) && $this->options['menu_name_1'] != '0' ) {
			add_filter( 'wp_nav_menu_' . $this->options['menu_name_1'] . '_items', array( &$this, 'add_itemcart_to_menu' ) , 10, 2 );
		}

		add_filter('add_to_cart_fragments', array( &$this, 'wpmenucart_add_to_cart_fragment' ) );
	}

	/**
	 * Load additional classes and functions
	 */
	public function includes() {
		include_once( 'includes/wpmenucart-settings.php' );
	}


	/**
	 * Load translations.
	 */
	public function wpmenucart_languages() {
		load_plugin_textdomain( 'wpmenucart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	 
	/**
	 * Load CSS
	 */
	public function load_styles() {				
		if (isset($this->options['icon_display'])) {
			wp_register_style( 'wpmenucart-icons', plugins_url( '/css/wpmenucart-icons.css', __FILE__ ), array(), '', 'all' );
			wp_enqueue_style( 'wpmenucart-icons' );
		}
		
		//Check for stylesheet in theme directory
		$css = file_exists( get_stylesheet_directory() . '/wpmenucart-main.css' )
			? get_stylesheet_directory_uri() . '/wpmenucart-main.css'
			: plugins_url( '/css/wpmenucart-main.css', __FILE__ );

		wp_register_style( 'wpmenucart', $css, array(), '', 'all' );
		wp_enqueue_style( 'wpmenucart' );

		//Load Stylesheet if twentytwelve is active
		if ( wp_get_theme() == 'Twenty Twelve' ) {
			wp_register_style( 'wpmenucart-twentytwelve', plugins_url( '/css/wpmenucart-twentytwelve.css', __FILE__ ), array(), '', 'all' );
			wp_enqueue_style( 'wpmenucart-twentytwelve' );
		}
	}
	
	/**
	 * Add Menu Cart to menu
	 * 
	 * @return menu items + Menu Cart item
	 */
	public function add_itemcart_to_menu( $items ) {
		$classes = 'wpmenucart-display-'.$this->options['items_alignment'];
		
		if ($this->get_common_li_classes($items) != '')
			$classes .= ' ' . $this->get_common_li_classes($items);
		
		$classes .= (isset($this->options['custom_class']) && $this->options['custom_class'] != '') ? sprintf( ' %s', $this->options['custom_class'] ) : '';

		// Filtering done here (instead of in function) to prevent issues with ajax
		$wpmenucart_menu_item = apply_filters( 'wpmenucart_menu_item_filter', $this->wpmenucart_menu_item() );

		$item_data = $this->shop->menu_item();

		if ($item_data['cart_contents_count'] > 0 || isset($this->options['always_display'])) {
			$items .= '<li class="'.$classes.'">' . $wpmenucart_menu_item . '</li>';
		}

		return $items;
	}

	public function get_common_li_classes($items) {
		$dom_items = new DOMDocument;
		$dom_items->loadHTML( $items );
		
		$lis = $dom_items->getElementsByTagName('li');
		
		foreach($lis as $li) {
			if ($li->parentNode->tagName != 'ul')
				$li_classes[] = explode( ' ', $li->getAttribute('class') );
		 }
		
		$common_li_classes = array_shift($li_classes);
		foreach ($li_classes as $li_class) {
			$common_li_classes = array_intersect($li_class, $common_li_classes);
		}
		
		$common_li_classes_flat = implode(' ', $common_li_classes);
		
		return $common_li_classes_flat;
	}

	/**
	 * Ajaxify Menu Cart
	 */
	public function wpmenucart_add_to_cart_fragment( $fragments ) {
		$fragments['a.wpmenucart-contents'] = $this->wpmenucart_menu_item();
		return $fragments;
	}

	/**
	 * Create HTML for Menu Cart item
	 */
	public function wpmenucart_menu_item() {
		$item_data = $this->shop->menu_item();

		$viewing_cart = __('View your shopping cart', 'wpmenucart');
		$start_shopping = __('Start shopping', 'wpmenucart');
		$cart_contents = sprintf(_n('%d item', '%d items', $item_data['cart_contents_count'], 'wpmenucart'), $item_data['cart_contents_count']);
	
		if ($item_data['cart_contents_count'] == 0) {
			$menu_item = '<a class="wpmenucart-contents" href="'. $item_data['shop_page_url'] .'" title="'. $start_shopping .'">';
		} else {
			$menu_item = '<a class="wpmenucart-contents" href="'. $item_data['cart_url'] .'" title="'. $viewing_cart .'">';
		}
		
		if (isset($this->options['icon_display'])) {
			$menu_item .= '<i class="wpmenucart-icon-shopping-cart-'.$this->options['cart_icon'].'"></i>';
		}
		
		switch ($this->options['items_display']) {
			case 1: //items only
				$menu_item .= '<span class="cartcontents">'.$cart_contents.'</span>';
				break;
			case 2: //price only
				$menu_item .= $item_data['cart_total'];
				break;
			case 3: //items & price
				$menu_item .= '<span class="cartcontents">'.$cart_contents.'</span> - '. $item_data['cart_total'];
				break;
		}
		$menu_item .= '</a>';

		return $menu_item;		
	}
}

/**
 * Shop plugin active, no old versions? Good to go!
 */

$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
$wpmenucart_load = 'yes';
if ( count(wpmenucart_get_shop_plugins()) == 0 ) {
	add_action( 'admin_notices', 'wpmenucart_no_shop_plugin_active' );
	$wpmenucart_load = 'no';
}

if ( count(wpmenucart_check_old_versions()) > 0 ) {
	add_action( 'admin_notices', 'wpmenucart_woocommerce_version_active' );
	$wpmenucart_load = 'no';
}

if ($wpmenucart_load != 'no') { //We're safe! :o)
	$wpMenuCart = new WpMenuCart();
} 

/**
 * Fallback notices
 *
 * @return string Fallack notice.
 */
function wpmenucart_no_shop_plugin_active() {
	$error = __( 'Menu Cart requires a shop plugin to be active' , 'wpmenucart' );
	$message = '<div class="error"><p>' . $error . '</p></div>';
	echo $message;
}

function wpmenucart_woocommerce_version_active() {
	$error = __( 'An old version of WooCommerce Menu Cart is currently activated, you need to disable or uninstall it for WP Menu Cart to function properly' , 'wpmenucart' );
	$message = '<div class="error"><p>' . $error . '</p></div>';
	echo $message;
}

/**
 * Get array of active shop plugins
 * 
 * @return array plugin name => plugin path
 */
function wpmenucart_get_shop_plugins() {
	$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	
	$shop_plugins = array (
		'WooCommerce'				=> 'woocommerce/woocommerce.php',
		'Jigoshop'					=> 'jigoshop/jigoshop.php',
		'eShop'						=> 'eshop/eshop.php',
	);
		
	$active_shop_plugins = array_intersect($shop_plugins,$active_plugins);
			
	return $active_shop_plugins;
}

/**
 * Get array of active old WooCommerce Menu Cart plugins
 * 
 * @return array plugin paths
 */
function wpmenucart_check_old_versions() {
	$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	
	$wpmenucart_old_versions = array (
		'woocommerce-menu-bar-cart/wc_cart_nav.php',				//first version
		'woocommerce-menu-bar-cart/woocommerce-menu-cart.php',		//last free version
		'woocommerce-menu-cart/woocommerce-menu-cart.php',			//never actually released? just in case...
		'woocommerce-menu-cart-pro/woocommerce-menu-cart-pro.php',	//old pro version
	);
		
	$wpmenucart_active_old_plugins = array_intersect($wpmenucart_old_versions,$active_plugins);
			
	return $wpmenucart_active_old_plugins;
}