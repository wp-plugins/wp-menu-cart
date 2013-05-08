<?php
if ( ! class_exists( 'WPMenuCart_WooCommerce' ) ) {
	class WPMenuCart_WooCommerce {     
	
	    /**
	     * Construct.
	     */
	    public function __construct() {
	    }
	
		public function menu_item() {
			global $woocommerce;
	
			$menu_item = array(
				'cart_url'				=> $woocommerce->cart->get_cart_url(),
				'shop_page_url'			=> get_permalink( woocommerce_get_page_id( 'shop' ) ),
				'cart_contents_count'	=> $woocommerce->cart->cart_contents_count,
				'cart_total'			=> $woocommerce->cart->get_cart_total(),
			);
		
			return $menu_item;		
		}
	}
}