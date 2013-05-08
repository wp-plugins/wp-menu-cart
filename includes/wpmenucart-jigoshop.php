<?php
if ( ! class_exists( 'WPMenuCart_Jigoshop' ) ) {
	class WPMenuCart_Jigoshop {     
	
	    /**
	     * Construct.
	     */
	    public function __construct() {
	    }
	
		public function menu_item() {
			$menu_item = array(
				'cart_url'				=> jigoshop_cart::get_cart_url(),
				'shop_page_url'			=> get_permalink( jigoshop_get_page_id( 'shop' ) ),
				'cart_contents_count'	=> jigoshop_cart::$cart_contents_count,
				'cart_total'			=> jigoshop_cart::get_cart_total(),
			);
		
			return $menu_item;		
		}
	}
}