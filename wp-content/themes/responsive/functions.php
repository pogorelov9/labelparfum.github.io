<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 *
 * WARNING: Please do not edit this file in any way
 *
 * load the theme function files
 */
require ( get_template_directory() . '/core/includes/functions.php' );
require ( get_template_directory() . '/core/includes/theme-options.php' );
require ( get_template_directory() . '/core/includes/post-custom-meta.php' );
require ( get_template_directory() . '/core/includes/tha-theme-hooks.php' );
require ( get_template_directory() . '/core/includes/hooks.php' );
require ( get_template_directory() . '/core/includes/version.php' );


function remove_comment_author_class( $classes ) {
	foreach( $classes as $key => $class ) {
		if(strstr($class, "comment-author-")) {
			unset( $classes[$key] );
		}
	}
	return $classes;
}
add_filter( 'comment_class' , 'remove_comment_author_class' );

add_filter('login_errors',create_function('$a', "return null;"));


function remove_version_data( $src ){
$parts = explode( '?ver', $src );

return $parts[0];

}
add_filter( 'script_loader_src', 'remove_version_data', 15, 1 );
add_filter( 'style_loader_src', 'remove_version_data', 15, 1 );


add_shortcode('guest', 'guest_check_shortcode');
 
function guest_check_shortcode( $atts, $content = null ) {
if ( !is_user_logged_in() && !is_null( $content ) && !is_feed() )
return $content;
return '';
}

add_shortcode( 'member', 'member_check_shortcode' );
function member_check_shortcode( $atts, $content = null ) {
if ( is_user_logged_in() && !is_null( $content ) && !is_feed() )
return $content;
return '';
}

if( !is_admin()){
wp_deregister_script('jquery');
wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"), false, '1.8.3');
wp_enqueue_script('jquery');
}

add_action( 'wp_head', 'mobile_menu_fix' );
function mobile_menu_fix() { ?> <script type="text/javascript">$( document ).ready(function() {$("#responsive_current_menu_item").html("Меню");	});</script><?php }


add_action('wp_print_styles','remove_styles',100);
function remove_styles() {
wp_deregister_style( 'cptchStylesheet' );
wp_deregister_style( 'contact-form-7' );
}

add_action('wp_print_scripts','remove_javascript',100);
function remove_javascript() {
	wp_deregister_script('contact-form-7');
}

add_filter('widget_text', 'do_shortcode');

function woocommerce_output_related_products() {
woocommerce_related_products(4,4); // Показать 4 товара а 4 колонки
}

add_action('woocommerce_share','wooshare');
function wooshare(){
echo'<br><link rel="stylesheet" href="/social-likes/social-likes.css">
<script src="/social-likes/social-likes.min.js"></script>
<ul class="social-likes">
	<li class="facebook" title="Поделиться ссылкой на Фейсбуке">Facebook</li>
	<li class="twitter" title="Поделиться ссылкой в Твиттере">Twitter</li>
	<li class="vkontakte" title="Поделиться ссылкой во Вконтакте">Вконтакте</li>
	<li class="plusone" title="Поделиться ссылкой в Гугл-плюсе">Google+</li>
</ul>';?>
<?php
}



add_action( 'woocommerce_email_after_order_table', 'add_payment_method_to_admin_new_order', 15, 2 );
 
function add_payment_method_to_admin_new_order( $order, $is_admin_email ) {
  if ( $is_admin_email ) {
    echo '<p><strong>Способ оплаты:</strong> ' . $order->payment_method_title . '</p>';
  }
}