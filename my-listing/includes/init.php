<?php

mylisting()->boot(
	MyListing\Src\Theme_Options\Theme_Options::class,
	MyListing\Controllers\User_Roles_Controller::class,
	MyListing\Controllers\Account_Details_Form_Controller::class,
	MyListing\Controllers\Register_Form_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Admin_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Dashboard_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Order_Controller::class,
	MyListing\Controllers\Maps\Maps_Controller::class,
	MyListing\Controllers\Maps\Google_Maps_Controller::class,
	MyListing\Controllers\Maps\Mapbox_Controller::class,
	MyListing\Controllers\Wp_All_Import_Controller::class,
	MyListing\Ajax::class,
	MyListing\Ext\Advanced_Custom_Fields\Advanced_Custom_Fields::class,
	MyListing\Src\Notifications\Notifications::class,
	MyListing\Post_Types::class,
	MyListing\Src\Forms\Forms::class,
	MyListing\Src\Endpoints\Endpoints::class,
	MyListing\Src\Explore::class,
	MyListing\Src\Queries\Query::class,
	MyListing\Filters::class,
	MyListing\Assets::class,
	MyListing\Ext\Buddypress\Buddypress::class,
	MyListing\Src\Admin\Admin::class,
	MyListing\Ext\Social_Login\Social_Login::class,
	MyListing\Src\Permalinks::class,
	MyListing\Elementor\Elementor::class,
	MyListing\Ext\Contact_Form_7\Contact_Form_7::class,
	MyListing\Src\Related_Listings\Related_Listings::class,
	MyListing\Ext\Visits\Visits::class,
	MyListing\Ext\Reviews\Reviews::class,
	MyListing\Src\Bookmarks::class,
	MyListing\Ext\Simple_Products\Simple_Products::class,
	MyListing\Src\Recurring_Dates\Recurring_Dates::class
);

/* @todo: refactor */
mylisting()->register( [
	'messages' => MyListing\Ext\Messages\Messages::instance(),
	'shortcodes' => MyListing\Shortcodes::instance(),
	'custom_taxonomies' => MyListing\Ext\Custom_Taxonomies\Custom_Taxonomies::instance(),
	'type_editor' => MyListing\Src\Listing_Types\Editor::instance(),
	'typography' => MyListing\Ext\Typography\Typography::instance(),
	'sharer' => MyListing\Ext\Sharer\Sharer::instance(),
	'stats' => MyListing\Ext\Stats\Stats::instance(),
] );

MyListing\Ext\WooCommerce\WooCommerce::instance();
MyListing\Src\Paid_Listings\Paid_Listings::instance();

/**
 * Fired after MyListing theme extensions have all been loaded.
 *
 * @since 2.0
 */
do_action( 'mylisting/init' );

/*
 * Configure theme textdomain, supported features, nav menus, etc.
 */
add_action( 'after_setup_theme', function() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Add support for the WooCommerce plugin.
	add_theme_support( 'woocommerce' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Set content width
	if ( ! isset( $content_width ) ) $content_width = 550;

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Remove WP Admin Bar inline styles.
	add_theme_support( 'admin-bar', [ 'callback' => '__return_false' ] );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus([
		'primary' 	  		  => esc_html__( 'Primary Menu', 'my-listing' ),
		'footer'	  		  => esc_html__( 'Footer Menu', 'my-listing' ),
		'mylisting-user-menu' => esc_html__( 'Woocommerce Menu', 'my-listing' )
	]);

	// Allow shortcodes in menu item labels.
	add_filter( 'wp_nav_menu_items', 'do_shortcode' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	]);

	add_theme_support( 'custom-background', [
		'default-color' => '#fafafa',
	]);

	// Add support for "Header, Footer & Blocks for Elementor" plugin.
	add_theme_support( 'header-footer-elementor' );
});


/*
 * Register theme sidebars.
 */
add_action( 'widgets_init', function() {
	register_sidebar( [
		'name'          => __( 'Footer', 'my-listing' ),
		'id'            => 'footer',
		'before_widget' => '<div class="col-md-4 col-sm-6 col-xs-12 c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="c_widget_title"><h5>',
		'after_title'   => '</h5></div>',
	] );

	register_sidebar( [
		'name'          => __( 'Sidebar', 'my-listing' ),
		'id'            => 'sidebar',
		'before_widget' => '<div class="element c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="pf-head"><div class="title-style-1"><h5>',
		'after_title'   => '</h5></div></div>',
	] );

	register_sidebar( [
		'name'          => __( 'Shop Page', 'my-listing' ),
		'id'            => 'shop-page',
		'before_widget' => '<div class="element c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="pf-head"><div class="title-style-1"><h5>',
		'after_title'   => '</h5></div></div>',
	] );

	register_widget( '\MyListing\Widgets\Latest_Posts' );
	register_widget( '\MyListing\Widgets\Contact_Form' );
} );

/**
 * Insert required code in site footer through get_footer hook, so it will
 * be added when using custom footer templates which completely override the theme footer.
 *
 * @since 1.6.6
 */
add_action( 'mylisting/get-footer', function() {
    c27()->get_partial( 'quick-view-modal' );
    c27()->get_partial( 'shopping-cart-modal' );
    c27()->get_partial( 'photoswipe-template' );
    c27()->get_partial( 'dialog-template' );

    // 'Back to Top' button.
    if ( c27()->get_setting( 'footer_show_back_to_top_button', false ) ): ?>
        <a href="#" class="back-to-top">
            <i class="mi keyboard_arrow_up"></i>
        </a>
    <?php endif;
}, 1 );
