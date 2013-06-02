/* 
 * JS for WPEC and EDD
 */
jQuery(document).ready(function($) { 
  $("input.edd-add-to-cart").click(function(){
      WPMenucart_Timeout();
  });
  $("div.wpsc_buy_button_container > input.wpsc_buy_button").click(function(){
      WPMenucart_Timeout();
  });
    
  function WPMenucart_Timeout() {
      setTimeout(function () { WPMenucart_Load_JS(); }, 1000);
  }
    
  function WPMenucart_Load_JS() {
    $('#wpmenucartli').load(wpmenucart_ajax.ajaxurl+'?action=wpmenucart_ajax&_wpnonce='+wpmenucart_ajax.nonce);
  } 
});