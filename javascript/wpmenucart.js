/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function($) {
                    $("div.wpsc_buy_button_container > input.wpsc_buy_button").click(function(){
                        $('#wpmenucartli').load(wpmenucart_ajax.ajaxurl+'?action=wpmenucart_ajax&_wpnonce='+wpmenucart_ajax.nonce);                        
                    });
                    $(".edd-add-to-cart").click(function(){
                        $('#wpmenucartli').load(wpmenucart_ajax.ajaxurl+'?action=wpmenucart_ajax&_wpnonce='+wpmenucart_ajax.nonce);
                    });
                });