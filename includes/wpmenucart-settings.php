<?php
class WpMenuCart_Settings {
	
	public function __construct() {
		add_action( 'admin_init', array( &$this, 'init_settings' ) ); // Registers settings
		add_action( 'admin_menu', array( &$this, 'wpmenucart_add_page' ) );

		//Menu admin, not using for now (very complex ajax structure...)
		//add_action( 'admin_init', array( &$this, 'wpmenucart_add_meta_box' ) );
	}
	/**
	 * User settings.
	 */
	public function init_settings() {
		wp_register_style( 'wpmenucart-admin', plugins_url( 'css/wpmenucart-icons.css', dirname(__FILE__) ), array(), '', 'all' );

		$option = 'wpmenucart';
	
		// Create option in wp_options.
		if ( false == get_option( $option ) ) {
			add_option( $option );
		}
	
		// Section.
		add_settings_section(
			'plugin_settings',
			__( 'Plugin settings', 'wpmenucart' ),
			array( &$this, 'section_options_callback' ),
			$option
		);

			add_settings_field(
				'shop_plugin',
				__( 'Select which e-commerce plugin you would like Menu Cart to work with', 'wpmenucart' ),
				array( &$this, 'select_element_callback' ),
				$option,
				'plugin_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'shop_plugin',
					'options'		=> $this->get_shop_plugins(),
				)
			);			
		
		//could be disabled when no menus are active, though the notice should be enough
		//if ($this->get_menu_array()) {
		add_settings_field(
			'menu_names',
			__( 'Select the menu(s) in which you want to display the Menu Cart', 'wpmenucart' ),
			array( &$this, 'multiple_select_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu_name_1'	=> array(
					'menu'			=> $option,
					'options' 		=> $this->get_menu_array(),
				),
				'menu_name_2'	=> array(
					'menu'			=> $option,
					'options' 		=> $this->get_menu_array(),
					'disabled'		=> true,
				),
				'menu_name_3'	=> array(
					'menu'			=> $option,
					'options' 		=> $this->get_menu_array(),
					'disabled'		=> true,
				),
			)
		);
		//}

		add_settings_field(
			'always_display',
			__( "Always display cart, even if it's empty", 'wpmenucart' ),
			array( &$this, 'checkbox_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'always_display',
			)
		);

		add_settings_field(
			'icon_display',
			__( 'Display shopping cart icon.', 'wpmenucart' ),
			array( &$this, 'checkbox_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'icon_display',
			)
		);

		add_settings_field(
			'flyout_display',
			__( 'Display cart contents in menu fly-out.', 'wpmenucart' ),
			array( &$this, 'checkbox_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'flyout_display',
				'disabled'		=> true,
			)
		);
		
		add_settings_field(
			'flyout_itemnumber',
			__( 'Set maximum number of products to display in fly-out', 'wpmenucart' ),
			array( &$this, 'select_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'flyout_itemnumber',
				'options'		=> array(
						'0'		 => '0',
						'1'			=> '1',
						'2'			=> '2',
						'3'			=> '3',
						'4'			=> '4',
						'5'			=> '5',
						'6'			=> '6',
						'7'			=> '7',
						'8'			=> '8',
						'9'			=> '9',
						'10'		=> '10',
				),
				'disabled'		=> true,
			)
		);			

		add_settings_field(
			'cart_icon',
			__( 'Choose a cart icon.', 'wpmenucart' ),
			array( &$this, 'icons_radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'cart_icon',
				'options' 		=> array(
					'0'			=> '0',
					'1'			=> '1',
					'2'			=> '2',
					'3'			=> '3',
					'4'			=> '4',
					'5'			=> '5',
					'6'			=> '6',
					'7'			=> '7',
					'8'			=> '8',
					'9'			=> '9',
					'10'		=> '10',
					'11'		=> '11',
					'12'		=> '12',
					'13'		=> '13',
				),
			)
		);


		add_settings_field(
			'items_display',
			__( 'What would you like to display in the menu?', 'wpmenucart' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'items_display',
				'options' 		=> array(
					'1'			=> __( 'Items Only.' , 'wpmenucart' ),
					'2'			=> __( 'Price Only.' , 'wpmenucart' ),
					'3'			=> __( 'Both price and items.' , 'wpmenucart' ),
				),
			)
		);
		
		add_settings_field(
			'items_alignment',
			__( 'Select the alignment that looks best with your menu.', 'wpmenucart' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'items_alignment',
				'options' 		=> array(
					'left'			=> __( 'Align Left.' , 'wpmenucart' ),
					'right'			=> __( 'Align Right.' , 'wpmenucart' ),
					'standard'		=> __( 'Default Menu Alignment.' , 'wpmenucart' ),
				),
			)
		);

		add_settings_field(
			'custom_class',
			__( 'Enter a custom CSS class (optional)', 'wpmenucart' ),
			array( &$this, 'text_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'custom_class',
				'disabled'		=> true,
			)
		);
		
		// Register settings.
		register_setting( $option, $option, array( &$this, 'wpmenucart_options_validate' ) );
	}

	/**
	 * Add menu page
	 */
	public function wpmenucart_add_page() {
		$wpmenucart_page = add_submenu_page(
			'options-general.php',
			__( 'Menu Cart', 'wpmenucart' ),
			__( 'Menu Cart Setup', 'wpmenucart' ),
			'manage_options',
			'wpmenucart_options_page',
			array( $this, 'wpmenucart_options_do_page' )
		);
		add_action( 'admin_print_styles-' . $wpmenucart_page, array( &$this, 'wpmenucart_admin_styles' ) );
	}
	
	/**
	 * Styles for settings page
	 */
	public function wpmenucart_admin_styles() {
		wp_enqueue_style( 'wpmenucart-admin' );
	}
	 
	/**
	 * Default settings.
	 */
	public function default_settings() {
		// code below is similar to get_shop_plugins,
		// but this cannot be called outside of the object context
		// Fatal error: Using $this when not in object context
		// quick fix for now... (any ideas?)
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		
		$shop_plugins = array (
			'WooCommerce'				=> 'woocommerce/woocommerce.php',
			'Jigoshop'					=> 'jigoshop/jigoshop.php',
			'WP e-Commerce'				=> 'wp-e-commerce/wp-shopping-cart.php',
			'eShop'						=> 'eshop/eshop.php',
			'Easy Digital Downloads'	=> 'easy-digital-downloads/easy-digital-downloads.php',
		);
			
		$active_shop_plugins = array_intersect($shop_plugins,$active_plugins);
		
		//switch keys & values, then strip plugin path to folder
		foreach ($active_shop_plugins as $key => $value) {
			$filtered_active_shop_plugins[] = dirname($value);
		}

		$active_shop_plugins = $filtered_active_shop_plugins[0];
			
		$default = array(
			'menu_name_1'		=> '0',
			'menu_name_2'		=> '0',
			'menu_name_3'		=> '0',
			'always_display'	=> '',
			'icon_display'		=> '1',
			'items_display'		=> '3',
			'items_alignment'	=> 'standard',
			'custom_class'		=> '',
			'flyout_display'	=> '',
			'flyout_itemnumber' => '5',
			'cart_icon'			=> '0',
			'shop_plugin'		=> $active_shop_plugins,
		);

		add_option( 'wpmenucart', $default );
	}

	/**
	 * Build the options page.
	 */
	public function wpmenucart_options_do_page() {		
		?>
	
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br /></div>
			<h2><?php _e('WP Menu Cart','wpmenucart') ?></h2>
				<?php 
				//global $options;
				//print_r($options); //for debugging
				//print_r($this->get_shop_plugins());
				//print_r(apply_filters( 'active_plugins', get_option( 'active_plugins' )));
				if (!$this->get_menu_array()) {
				?>
				<div class="error" style="width:400px; padding:10px;">
					You need to create a menu before you can use Menu Cart. Go to <strong>Appearence > Menus</strong> and create menu to add the cart to.
				</div>
				<?php } ?>
				<form method="post" action="options.php">
				<?php
									
					settings_fields( 'wpmenucart' );
					do_settings_sections( 'wpmenucart' );

					submit_button();
				?>

			</form>
			<script type="text/javascript">
			jQuery('.hidden-input').click(function() {
				jQuery(this).closest('.hidden-input').prev('.pro-feature').show('slow');
				jQuery(this).closest('.hidden-input').hide();
			});
			jQuery('.hidden-input-icon').click(function() {
				jQuery('.pro-icon').show('slow');
			});
			</script>
			<div style="line-height: 20px; background: #F3F3F3;-moz-border-radius: 3px;border-radius: 3px;padding: 10px;-moz-box-shadow: 0 0 5px #ff0000;-webkit-box-shadow: 0 0 5px#ff0000;box-shadow: 0 0 5px #ff0000;padding: 10px;margin:0px auto; font-size: 13.8px;width: 60%;float: left"> 
				<h2><?php _e('Get WP Menu Cart Pro!','wpmenucart') ?></h2>
				<br>
				<strong><?php _e('Limited Offer:','wpmenucart') ?> <span style="color: red"><?php _e('20% off!','wpmenucart') ?></span></strong>			
				<br>
				<br>
				<?php _e('Includes all the great standard features found in this free version plus:','wpmenucart') ?>
				<br>
				<ul style="list-style-type:circle;margin-left: 40px">
					<li><?php _e('A choice of over 10 cart icons','wpmenucart') ?></li>
					<li><?php _e('A fully featured cart details flyout','wpmenucart') ?></li>
					<li><?php _e('Ability to add cart + flyout for up to 3 menus','wpmenucart') ?></li>
					<li><?php _e('Ability to add a custom css class','wpmenucart') ?></li>
					<li><?php _e('Automatic updates on any great new features','wpmenucart') ?></li>
				</ul>
				<?php
				$menucartadmore = '<a href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartadmore">';
				printf (__('Need to see more? %sClick here%s to check it out. Add a product to your cart and watch what happens!','wpmenucart'), $menucartadmore,'</a>'); ?><br><br>
				<a class="button button-primary" style="text-align: center;margin: 0px auto" href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartadbuy"><?php _e('Buy Now','wpmenucart') ?></a>
			</div>
			<div style="line-height: 20px; background: #F3F3F3;-moz-border-radius: 3px;border-radius: 3px;padding: 10px;-moz-box-shadow: 0 0 5px #ff0000;-webkit-box-shadow: 0 0 5px#ff0000;box-shadow: 0 0 5px #ff0000;padding: 10px;margin:0px auto; margin-left: 30px; font-size: 13.8px;width: 30%;float: left">
				<h2><?php _e('Want your CSS customized?','wpmenucart') ?></h2>
				<br>
				<?php _e('We can do that for you! Just click the button below to check it out.','wpmenucart') ?>
				<br><br>
				<a class="button button-primary" style="text-align: center" href="https://wpovernight.com/shop/menu-cart-custom-css/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartcustomcss"><?php _e('Customize my CSS!','wpmenucart') ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Get menu array.
	 * 
	 * @return array menu slug => menu name
	 */
	public function get_menu_array() {
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
	
		foreach ( $menus as $menu ) {
			$menu_list[$menu->slug] = $menu->name;
		}
		
		if (!empty($menu_list)) return $menu_list;
	}
	
	/**
	 * Get array of active shop plugins
	 * 
	 * @return array plugin slug => plugin name
	 */
	public function get_shop_plugins() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		
		$shop_plugins = array (
			'WooCommerce'				=> 'woocommerce/woocommerce.php',
			'Jigoshop'					=> 'jigoshop/jigoshop.php',
			'WP e-Commerce'				=> 'wp-e-commerce/wp-shopping-cart.php',
			'eShop'						=> 'eshop/eshop.php',
			'Easy Digital Downloads'	=> 'easy-digital-downloads/easy-digital-downloads.php',
		);
			
		$active_shop_plugins = array_intersect($shop_plugins,$active_plugins);
		
		//switch keys & values, then strip plugin path to folder
		foreach ($active_shop_plugins as $key => $value) {
			$filtered_active_shop_plugins[dirname($value)] = $key;
		}

		$active_shop_plugins = $filtered_active_shop_plugins;
				
		return $active_shop_plugins;
	}

	/**
	 * Text field callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Text field.
	 */
	public function text_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
		$size = isset( $args['size'] ) ? $args['size'] : '25';
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		$html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" size="%4$s"%5$s/>', $id, $menu, $current, $size, $disabled );
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	
		if (isset( $args['disabled'] )) {
			$html .= ' <span style="display:none;" class="pro-feature"><i>'. __('This feature only available in', 'wpmenucart') .' <a href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartcustomclass">Menu Cart Pro</a></i></span>';
			$html .= '<div style="position:absolute; left:0; right:0; top:0; bottom:0; background-color:white; -moz-opacity: 0; opacity:0;filter: alpha(opacity=0);" class="hidden-input"></div>';
			$html = '<div style="display:inline-block; position:relative;">'.$html.'</div>';
		}
	
		echo $html;
	}
	
	/**
	 * Displays a selectbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function select_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
		
		$options = get_option( $menu );
		
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		
		$html = sprintf( '<select name="%1$s[%2$s]" id="%1$s[%2$s]"%3$s>', $menu, $id, $disabled );
		$html .= sprintf( '<option value="%s"%s>%s</option>', '0', selected( $current, '0', false ), '' );
		
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
		}
		$html .= sprintf( '</select>' );

		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
		
		if (isset( $args['disabled'] )) {
			$html .= ' <span style="display:none;" class="pro-feature"><i>'. __('This feature only available in', 'wpmenucart') .' <a href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartflyout">Menu Cart Pro</a></i></span>';
			$html .= '<div style="position:absolute; left:0; right:0; top:0; bottom:0; background-color:white; -moz-opacity: 0; opacity:0;filter: alpha(opacity=0);" class="hidden-input"></div>';
			$html = '<div style="display:inline-block; position:relative;">'.$html.'</div>';
		}

		echo $html;
	}

	/**
	 * Displays a multiple selectbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function multiple_select_element_callback( $args ) {
		$html = '';
		foreach ($args as $id => $boxes) {
			$menu = $boxes['menu'];
			
			$options = get_option( $menu );
			
			if ( isset( $options[$id] ) ) {
				$current = $options[$id];
			} else {
				$current = isset( $boxes['default'] ) ? $boxes['default'] : '';
			}
			
			$disabled = (isset( $boxes['disabled'] )) ? ' disabled' : '';
			
			$box = sprintf( '<select name="%1$s[%2$s]" id="%1$s[%2$s]"%3$s>', $menu, $id, $disabled);
			$box .= sprintf( '<option value="%s"%s>%s</option>', '0', selected( $current, '0', false ), '' );
			
			foreach ( (array) $boxes['options'] as $key => $label ) {
				$box .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
			}
			$box .= '</select>';
	
			if ( isset( $boxes['description'] ) ) {
				$box .= sprintf( '<p class="description">%s</p>', $boxes['description'] );
			}
			if (isset( $boxes['disabled'] )) {
				$box .= ' <span style="display:none;" class="pro-feature"><i>'. __('This feature only available in', 'wpmenucart') .' <a href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartmultiplemenus">Menu Cart Pro</a></i></span>';
				$box .= '<div style="position:absolute; left:0; right:0; top:0; bottom:0; background-color:white; -moz-opacity: 0; opacity:0;filter: alpha(opacity=0);" class="hidden-input"></div>';
				$box = '<div style="display:inline-block; position:relative;">'.$box.'</div>';
			}

			$html .= $box.'<br />';
		}
		
		
		echo $html;
	}

	/**
	 * Checkbox field callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Checkbox field.
	 */
	public function checkbox_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}
	
		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		$html = sprintf( '<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1"%3$s %4$s/>', $id, $menu, checked( 1, $current, false ), $disabled );
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	
		if (isset( $args['disabled'] )) {
	        $html .= ' <span style="display:none;" class="pro-feature"><i>'. __('This feature only available in', 'wpmenucart') .' <a href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartflyout">Menu Cart Pro</a></i></span>';
			$html .= '<div style="position:absolute; left:0; right:0; top:0; bottom:0; background-color:white; -moz-opacity: 0; opacity:0;filter: alpha(opacity=0);" class="hidden-input"></div>';
			$html = '<div style="display:inline-block; position:relative;">'.$html.'</div>';
		}
	    	
	    echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function radio_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$html = '';
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $menu, $id, $key, checked( $current, $key, false ) );
			$html .= sprintf( '<label for="%1$s[%2$s][%3$s]"> %4$s</label><br>', $menu, $id, $key, $label);
		}
		
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}

		if (isset( $args['disabled'] )) {
			$html .= ' <span style="display:none;" class="pro-feature"><i>'. __('This feature only available in', 'wpmenucart') .' <a href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucartflyout">Menu Cart Pro</a></i></span>';
			$html .= '<div style="position:absolute; left:0; right:0; top:0; bottom:0; background-color:white; -moz-opacity: 0; opacity:0;filter: alpha(opacity=0);" class="hidden-input"></div>';
			$html = '<div style="display:inline-block; position:relative;">'.$html.'</div>';
		}
			
		echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function icons_radio_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$icons = '';
		$radios = '';
		
		foreach ( $args['options'] as $key => $iconnumber ) {
			if ($key == 0) {
				$icons .= sprintf( '<td style="padding-bottom:0;font-size:16pt;" align="center"><label for="%1$s[%2$s][%3$s]"><i class="wpmenucart-icon-shopping-cart-%4$s"></i></label></td>', $menu, $id, $key, $iconnumber);
				$radios .= sprintf( '<td style="padding-top:0" align="center"><input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s /></td>', $menu, $id, $key, checked( $current, $key, false ) );
			} else {
				$icons .= sprintf( '<td style="padding-bottom:0;font-size:16pt;" align="center"><label for="%1$s[%2$s][%3$s]"><img src="%4$scart-icon-%5$s.png" /></label></td>', $menu, $id, $key, plugins_url( 'images/', dirname(__FILE__) ), $iconnumber);
				$radio = sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" disabled />', $menu, $id, $key);
				$radio .= '<div style="position:absolute; left:0; right:0; top:0; bottom:0; background-color:white; -moz-opacity: 0; opacity:0;filter: alpha(opacity=0);" class="hidden-input-icon"></div>';
				$radio = '<div style="display:inline-block; position:relative;">'.$radio.'</div>';
				
				$radios .= '<td style="padding-top:0" align="center">'.$radio.'</td>';
			}
		}

		$profeature = '<span style="display:none;" class="pro-icon"><i>'. __('Additional icons are only available in', 'wpmenucart') .' <a href="https://wpovernight.com/shop/wp-menu-cart-pro/?utm_source=wordpress&utm_medium=menucartfree&utm_campaign=menucarticons">Menu Cart Pro</a></i></span>';

		$html = '<table><tr>'.$icons.'</tr><tr>'.$radios.'</tr></table>'.$profeature;
		
		echo $html;
	}

	/**
	 * Section null callback.
	 *
	 * @return void.
	 */
	public function section_options_callback() {
	
	}

	/**
	 * Validate/sanitize options input
	 */
	public function wpmenucart_options_validate( $input ) {
		// Create our array for storing the validated options.
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[$key] ) ) {

				// Strip all HTML and PHP tags and properly handle quoted strings.
				$output[$key] = strip_tags( stripslashes( $input[$key] ) );
			}
		}

		// Return the array processing any additional functions filtered by this action.
		return apply_filters( 'wpmenucart_validate_input', $output, $input );
	}

	public function wpmenucart_add_meta_box() {
		add_meta_box(
			'wpmenucart-meta-box',
			__('Menu Cart'),
			array( &$this, 'wpmenucart_menu_item_meta_box' ),
			'nav-menus',
			'side',
			'default'
			);
	}
	
	public function wpmenucart_menu_item_meta_box() {
		global $_nav_menu_placeholder, $nav_menu_selected_id;
		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

		?>
		<p>
			<input value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" type="text" />
			<input id="custom-menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" type="text" value="" />
			<input id="custom-menu-item-name" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" title="<?php esc_attr_e('Menu Item'); ?>" />
		</p>

		<p class="wpmenucart-meta-box" id="wpmenucart-meta-box">
			<span class="add-to-menu">
				<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="menucart-menu-item" id="menucart-menu-item" />
				<span class="spinner"></span>
			</span>
		</p>
		<?php
	}


}