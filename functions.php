<?php

// PHP debugger for Google Chrome
//include 'chromephp/ChromePhp.php';

/* 	=================================
	========== THEME SETUP ==========
	================================= */

// enqueue custom scripts and styles
function causality_scripts_enqueue() {
	wp_enqueue_style('themestyles', get_template_directory_uri().'/css/causality.css', array(), '1.0.0', 'all'); // enqueue custom CSS
	wp_enqueue_script('themescripts', get_template_directory_uri().'/js/causality.js', array(), '1.0.0', true);  // enqueue custom JS
}

add_action('wp_enqueue_scripts', 'causality_scripts_enqueue'); // add the custom CSS and JS

// set up the theme
function causality_theme_setup() {

	// theme support
	add_theme_support('menus');
	add_theme_support('post-thumbnails');
	$header_args = array(
        'default-text-color' => '000',
        'width'              => 600,
        'height'             => 285,
        'flex-width'         => true,
        'flex-height'        => true,
    );
    add_theme_support( 'custom-header', $header_args );

	// theme nav menu locations
	register_nav_menu('primary_menu', 'Main Menu');
}

add_action('init', 'causality_theme_setup'); // call the theme setup function when the theme is initialised



/* 	==================================
	======== THEME CUSTOMIZER ========
	================================== */

// set up the customize register
function causality_customize_register( $wp_customize ) {

	// function for adding an array of the settings to a section in the theme customizer
	function add_settings_to_sections($section, $settings, $wp_customize) {
		for ($i = 0; $i < count($settings); $i++) {
			$wp_customize->add_setting( $settings[$i]->id , array(
				'default'   => $settings[$i]->default_val,
				'transport' => 'refresh',
			) );

			if ($settings[$i]->type == 'color') {
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $settings[$i]->id.'_ctrl', array( 
					'label'      => __( $settings[$i]->label, 'causality' ),
					'section'    => $section,
					'settings'   => $settings[$i]->id,
					'priority'	 => ($i*1)+100,
				) ) );
			} else if ($settings[$i]->type == 'image') {
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $settings[$i]->id.'_ctrl', array(
							'label'      => __( $settings[$i]->label, 'causality' ),
							'section'    => $section,
							'settings'   => $settings[$i]->id,
							//'context'    => 'your_setting_context' 
							'priority'	 => ($i*1)+100,
						)
					)
				);
			}
			else {
				$wp_customize->add_control( new WP_Customize_Control( $wp_customize, $settings[$i]->id.'_ctrl', array(
					'label'      => __( $settings[$i]->label, 'causality' ),
					'type'		 => $settings[$i]->type,
					'section'    => $section,
					'settings'   => $settings[$i]->id,
					'priority'	 => ($i*1)+100,
				) ) );
			}
		}
	}

	// social media options
	$wp_customize->add_section( 'social_media' , array(
		'title'      => __( 'Social Media Links', 'causality' ),
		'priority'   => 61,
	) );

	$social_options = array(
		(object)['id' => 'facebook_link', 'label' => 'Facebook', 'type' => 'text', 'default_val' => ''],
		(object)['id' => 'twitter_link', 'label' => 'Twitter', 'type' => 'text', 'default_val' => ''],
		(object)['id' => 'instagram_link', 'label' => 'Instagram', 'type' => 'text', 'default_val' => ''],
		(object)['id' => 'linkedin_link', 'label' => 'Linkedin', 'type' => 'text', 'default_val' => ''],
	);

	add_settings_to_sections('social_media', $social_options, $wp_customize);

	// color customization options
	$wp_customize->add_section( 'color_scheme' , array(
		'title'      => __( 'Color Scheme', 'causality' ),
		'priority'   => 62,
	) );

	$color_options = array(
		(object)['id' => 'primary_color', 'label' => 'Primary Color', 'type' => 'color', 'default_val' => '#000000'],
		(object)['id' => 'accent_color', 'label' => 'Accent Color', 'type' => 'color', 'default_val' => '#dd3333'],
		(object)['id' => 'primary_text_color', 'label' => 'Complementary Text Color', 'type' => 'color', 'default_val' => '#ffffff']
	);

	add_settings_to_sections('color_scheme', $color_options, $wp_customize);

	// header background
	$header_options = array(
		(object)['id' => 'background_img', 'label' => 'Upload a Header Background', 'type' => 'image', 'default_val' => '']
	);

	add_settings_to_sections('header_image', $header_options, $wp_customize); // header_image is pre-defined by WP

	// Developer Options
	$wp_customize->add_section( 'dev_options' , array(
		'title'      => __( 'Developer Options', 'causality' ),
		'priority'   => 999,
	) );

	$dev_options = array(
		(object)['id' => 'scss_recompile', 'label' => 'Recompile SCSS on page load?', 'type' => 'checkbox', 'default_val' => ''],
	);

	add_settings_to_sections('dev_options', $dev_options, $wp_customize);

	// Remove unused sections
	$wp_customize->remove_section('colors');
}

add_action('customize_register', 'causality_customize_register');



/* 	===============================
	======== POST EXCERPTS ========
	=============================== */
function set_excerpt_length() {
	return 30;
}

add_filter('excerpt_length', 'set_excerpt_length');



/* 	=========================
	======== WIDGETS ========
	========================= */
function causality_init_widgets() {
	register_sidebar(array(
		'name' => 'Blog Sidebar',
		'id' => 'blog_sidebar',
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3><div class="widget-body">'
	));

	register_sidebar(array(
		'name' => 'Footer Widgets',
		'id' => 'footer_widgets',
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3><div class="widget-body">'
	));
}

add_action('widgets_init', 'causality_init_widgets');

// Woocommerce Hooks
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

function causality_woocommerce_wrapper_start() {
	echo '<main class="cy-main cy-main--shop">';
}

function causality_woocommerce_wrapper_end() {
	echo '</main>';
}

function causality_product_summary_wrapper_start() {
	echo '<section class="cy-product-summary">';
}

function causality_product_summary_wrapper_end() {
	echo '</section>';
}

add_action('woocommerce_before_main_content', 'causality_woocommerce_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'causality_woocommerce_wrapper_end', 10);
add_action('woocommerce_before_single_product_summary', 'causality_product_summary_wrapper_start', 20);
add_action('woocommerce_after_single_product_summary', 'causality_product_summary_wrapper_end', 20);
