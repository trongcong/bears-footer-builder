<?php
/**
 * Created by PhpStorm.
 * User: NTC
 * Date: 9/13/2017
 * Time: 14:02
 */

if ( ! function_exists( 'btfb_setup_post_types' ) ) {
	function btfb_setup_post_types() {
		/*
		 * Biến $label để chứa các text liên quan đến tên hiển thị của Post Type trong Admin
		*/
		$labels = array(
			'name'               => _x( 'Footers', 'Post Type General Name', 'btfb' ),
			//Tên post type dạng số nhiều
			'singular_name'      => _x( 'Footer', 'Post Type Singular Name', 'btfb' ),
			//Tên post type dạng số ít
			'menu_name'          => __( 'Footer', 'btfb' ),
			'parent_item_colon'  => __( 'Parent Footer', 'btfb' ),
			'all_items'          => __( 'All Footer', 'btfb' ),
			'view_item'          => __( 'View Footer', 'btfb' ),
			'add_new_item'       => __( 'Add New Footer', 'btfb' ),
			'add_new'            => __( 'Add New', 'btfb' ),
			'edit_item'          => __( 'Edit Footer', 'btfb' ),
			'update_item'        => __( 'Update Footer', 'btfb' ),
			'search_items'       => __( 'Search Footer', 'btfb' ),
			'not_found'          => __( 'Not Found', 'btfb' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'btfb' ),
		);
		/*
		 * Biến $args là những tham số quan trọng trong Post Type
		*/
		$args = array(
			'label'               => __( 'Footer', 'btfb' ),
			//Mô tả của post type
			'description'         => __( 'Post type Footer Builder', 'btfb' ),
			//Gọi các label trong biến $label ở trên
			'labels'              => $labels,
			//Các tính năng được hỗ trợ trong post type
			'supports'            => array(
				'title',
				'editor',
				//            'excerpt',
				'author',
				//                'thumbnail',
				//            'comments',
				//            'trackbacks',
				'revisions',
				//            'custom-fields'
			),
			//Các taxonomy được phép sử dụng để phân loại nội dung
			//            'taxonomies' => array('post_tag'),
			//Cho phép phân cấp, nếu là false thì post type này giống như Post, true thì giống như Page
			'hierarchical'        => false,
			//Kích hoạt post type
			'public'              => true,
			//Hiển thị khung quản trị như Post/Page
			'show_ui'             => true,
			//Hiển thị trên Admin Menu (tay trái)
			'show_in_menu'        => true,
			//Hiển thị trong Appearance -> Menus
			'show_in_nav_menus'   => true,
			//Hiển thị trên thanh Admin bar màu đen.
			'show_in_admin_bar'   => true,
			//Thứ tự vị trí hiển thị trong menu (tay trái)
			'menu_position'       => 4,
			//Đường dẫn tới icon sẽ hiển thị
			// 'menu_icon' => '',
			//Có thể export nội dung bằng Tools -> Export
			'can_export'          => true,
			//Cho phép lưu trữ (month, date, year)
			'has_archive'         => false,
			//Loại bỏ khỏi kết quả tìm kiếm
			'exclude_from_search' => false,
			//Hiển thị các tham số trong query, phải đặt true
			'publicly_queryable'  => true,
			// Có thể làm gì, ví dụ: đọc, sửa, xóa..
			'capability_type'     => 'post'
		);
		//Tạo post type với slug tên là footer-builder và các tham số trong biến $args ở trên
		register_post_type( 'footer-builder', $args );
	}

	add_action( 'init', 'btfb_setup_post_types' );
}
if ( ! function_exists( 'btfb_set_options_custom_layout_footer' ) ) {
	function btfb_set_options_custom_layout_footer() {
		$options_custom_footer_layout = array();
		$posts                        = btfb_post_footer_builder_query();
		$footer_post_option           = array( '' => __( '— Select —', 'yolo' ) );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$footer_post_option[ $post->ID ] = html_entity_decode( get_the_title( $post->ID ) );
			}
		}
		if ( defined( 'FW' ) ) {
			$options_custom_footer_layout = array(
				'custom_footer_layout' => array(
					'type'         => 'multi-picker',
					'label'        => false,
					'desc'         => false,
					'picker'       => array(
						'selected_value' => array(
							'label'        => esc_html__( 'Custom Footer', 'yolo' ),
							'desc'         => esc_html__( 'Use custom footer layout?', 'yolo' ),
							'type'         => 'switch',
							'right-choice' => array(
								'value' => 'yes',
								'label' => esc_html__( 'Yes', 'yolo' )
							),
							'left-choice'  => array(
								'value' => 'no',
								'label' => esc_html__( 'No', 'yolo' )
							),
							'value'        => 'no',
						)
					),
					'choices'      => array(
						'yes' => array(
							'custom_footer_layout_value' => array(
								'type'    => 'select',
								'label'   => false,
								'desc'    => false,
								'choices' => $footer_post_option,
							)
						)
					),
					'show_borders' => false,
				)
			);
		}

		return $options_custom_footer_layout;
	}

	add_filter( 'yolo_options_custom_footer_layout', 'btfb_set_options_custom_layout_footer', 11 );
}

if ( ! function_exists( 'btfb_show_footer_custom_layout' ) ) {
	function btfb_show_footer_custom_layout() {
		$yolo_footer_settings = defined( 'FW' ) ? fw_get_db_customizer_option( 'footer_settings' ) : array();
		$use_custom_footer    = isset( $yolo_footer_settings['custom_footer_layout']['selected_value'] ) ? $yolo_footer_settings['custom_footer_layout']['selected_value'] : 'no';
		$footer_global_id     = ( isset( $yolo_footer_settings['custom_footer_layout']['yes']['custom_footer_layout_value'] ) ) ? $yolo_footer_settings['custom_footer_layout']['yes']['custom_footer_layout_value'] : '';
		$footer_page_id       = get_post_meta( get_the_ID(), '_layout_footer_builder_page', true );

		if ( ! empty( $footer_page_id ) )  :
			$post    = get_post( $footer_page_id );
			$slug    = $post->post_name;
			$content = $post->post_content;
			echo '<div class="bt-footer-custom-layout ' . join( ' ', get_post_class( $slug, $footer_page_id ) ) . '"><div class="bt-inner">' . do_shortcode( $content ) . '</div></div>';
		elseif ( $use_custom_footer == 'yes' && ! empty( $footer_global_id ) ) :
			$post    = get_post( $footer_global_id );
			$slug    = $post->post_name;
			$content = $post->post_content;
			echo '<div class="bt-footer-custom-layout ' . join( ' ', get_post_class( $slug, $footer_global_id ) ) . '"><div class="bt-inner">' . do_shortcode( $content ) . '</div></div>';
		endif;
	}

	add_filter( 'yolo_before_footer_widgets', 'btfb_show_footer_custom_layout', 11 );
}

if ( ! function_exists( 'btfb_add_css_footer_builder' ) ) {
	function btfb_add_css_footer_builder() {
		$yolo_footer_settings = defined( 'FW' ) ? fw_get_db_customizer_option( 'footer_settings' ) : array();
		$use_custom_footer    = isset( $yolo_footer_settings['custom_footer_layout']['selected_value'] ) ? $yolo_footer_settings['custom_footer_layout']['selected_value'] : 'no';
		$footer_global_id     = ( isset( $yolo_footer_settings['custom_footer_layout']['yes']['custom_footer_layout_value'] ) ) ? $yolo_footer_settings['custom_footer_layout']['yes']['custom_footer_layout_value'] : '';
		$footer_page_id       = get_post_meta( get_the_ID(), '_layout_footer_builder_page', true );

		if ( is_home() ) {
			if ( $use_custom_footer == 'yes' && ! empty( $footer_global_id ) ) {
				echo '<style id="yolo-footer-builder-css">' . get_post_meta( $footer_global_id, '_wpb_shortcodes_custom_css', true ) . get_post_meta( $footer_global_id, '_wpb_post_custom_css', true ) . '</style>';
			}
		} elseif ( ! empty( $footer_page_id ) ) {
			echo '<style id="yolo-footer-builder-css">' . get_post_meta( $footer_page_id, '_wpb_shortcodes_custom_css', true ) . get_post_meta( $footer_page_id, '_wpb_post_custom_css', true ) . '</style>';
		} elseif ( $use_custom_footer == 'yes' && ! empty( $footer_global_id ) ) {
			echo '<style id="yolo-footer-builder-css">' . get_post_meta( $footer_global_id, '_wpb_shortcodes_custom_css', true ) . get_post_meta( $footer_global_id, '_wpb_post_custom_css', true ) . '</style>';
		}
	}

	add_action( 'wp_head', 'btfb_add_css_footer_builder', 1111 );
}

if ( ! function_exists( 'btfb_create_meta_box' ) ) {
	function btfb_create_meta_box() {
		add_meta_box( 'select-custom-footer-layout', __( 'Select Custom Footer Layout', 'btfb' ), 'btfb_create_meta_box_callback', array(
			'page',
			'product',
			'post'
		) );
	}

	add_action( 'add_meta_boxes', 'btfb_create_meta_box' );
}

if ( ! function_exists( 'btfb_create_meta_box_callback' ) ) {
	function btfb_create_meta_box_callback( $post ) {
		$layout = get_post_meta( $post->ID, '_layout_footer_builder_page', true );
		echo( '<div class="layout_footer_builder">Choose footer layout</div>' );
		wp_nonce_field( 'save_layout_footer_builder_none', 'layout_footer_builder_nonce' );
		echo( '<select name="layout_footer_builder" id="layout_footer_builder">' );
		echo( '<option value="" ' . ( ( empty( $layout ) ) ? " selected" : "" ) . '>— Select —</option>' );
		$posts = btfb_post_footer_builder_query();
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post_tem ) {
				echo( '<option value="' . $post_tem->ID . '"' . ( ( $layout == $post_tem->ID ) ? " selected" : "" ) . '>' . get_the_title( $post_tem->ID ) . '</option>' );
			}
		}
		echo( '</select>' );
	}
}

if ( ! function_exists( 'btfb_save_meta_box_callback' ) ) {

	function btfb_save_meta_box_callback( $post_id ) {
		if ( isset( $_POST['layout_footer_builder_nonce'] ) ) {
			$layout_footer_builder_nonce = $_POST['layout_footer_builder_nonce'];
		}
		if ( ! isset( $layout_footer_builder_nonce ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $layout_footer_builder_nonce, 'save_layout_footer_builder_none' ) ) {
			return;
		}

		$layout_footer_builder = sanitize_text_field( $_POST['layout_footer_builder'] );
		update_post_meta( $post_id, '_layout_footer_builder_page', $layout_footer_builder );
	}

	add_action( 'save_post', 'btfb_save_meta_box_callback' );
}

if ( ! function_exists( 'btfb_post_footer_builder_query' ) ) {
	/**
	 * @return array
	 */
	function btfb_post_footer_builder_query() {
		$query = new WP_Query( array(
			'post_type'      => 'footer-builder',
			'orderby'        => 'post_date',
			'order '         => 'DESC',
			'posts_per_page' => - 1,
		) );
		$posts = $query->get_posts();

		return $posts;
	}
}

if ( ! function_exists( 'btfb_install' ) ) {
	function btfb_install() {
		// trigger our function that registers the custom post type
		btfb_setup_post_types();
		// clear the permalinks after the post type has been registered
		flush_rewrite_rules();
		if ( btfb_check_visual_is_active() ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}

	register_activation_hook( __FILE__, 'btfb_install' );
}
if ( ! function_exists( 'btfb_deactivation' ) ) {
	function btfb_deactivation() {
		flush_rewrite_rules();
	}

	register_deactivation_hook( __FILE__, 'btfb_deactivation' );
}