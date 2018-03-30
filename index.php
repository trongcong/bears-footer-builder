<?php
/*
Plugin Name:  Footer Builder
Plugin URI:   https://bearsthemes.com/
Description:  Support build footer with WPBakery Page Builder plugin
Version:      1.0.1
Author:       Trong Cong
Author URI:   https://2dev4u.com/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  bears-footer-builder
Domain Path:  /languages
*/
ob_start();

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( ! function_exists( 'btfb_check_visual_is_active' ) ) {
	function btfb_check_visual_is_active() {
		return ( ! is_plugin_active( 'js_composer/js_composer.php' ) );
	}
}

if ( ! btfb_check_visual_is_active() ) {
	require_once 'includes/init.php';
} else {
	if ( ! function_exists( 'btfb_when_active_plugin_notice' ) ) {
		function btfb_when_active_plugin_notice() {
			?>
            <div class="notice updated is-dismissible">
                <img style="width: 80px; margin-top: 10px;"
                     src="<?php echo get_bloginfo( 'url' ) . '/wp-content/themes/yolo/assets/images/bears-message-icon.png'; ?>"
                     alt="notice" />
                <p><strong><?php _e( 'NOTICE !!!', 'btfb' ); ?></strong></p>
                <p><?php _e( 'Please installing and active WPBakery Visual Composer Plugin before use Footer Builder.', 'btfb' ); ?></p>
            </div>
			<?php
		}

		add_action( 'admin_notices', 'btfb_when_active_plugin_notice' );
	}
}

file_put_contents( __DIR__ . '/log.html', ob_get_contents() );