<?php
/**
 * @deprecated 2.4
 */
namespace MyListing;

class Filters {

    public static function boot() {
        new self;
    }

	protected $actions = [
        'tgmpa_register',
        'case27_footer',
        'after_switch_theme',
        'pre_get_posts',
	];

	protected $filters = [
		'query_vars',
        'get_the_archive_title',
        'case27_featured_service_content',
        'body_class',
        'admin_menu',
	];

	public function __construct() {
		$this->add_actions();
		$this->add_filters();

        add_filter( 'option_category_base', function( $base ) {
            if ( ! $base || $base == 'category' ) {
                return 'post-category';
            }

            return $base;
        });

        add_filter( 'option_tag_base', function( $base ) {
            if ( ! $base || $base == 'tag' ) {
                return 'post-tag';
            }

            return $base;
        });

        add_filter( 'pre_option_job_category_base', function( $base ) {
            if ( ! $base || $base == 'listing-category' || $base == 'job-category' ) {
                return 'category';
            }

            return $base;
        });
	}

	public function register_action( $action ) {
		if ( ! in_array( $action, $this->actions ) ) {
			$this->actions[] = $action;
		}
	}

	public function register_filter( $filter ) {
		if ( ! in_array( $filter, $this->filters ) ) {
			$this->filters[] = $filter;
		}
	}

    /*
     * Register Filters.
     */
	public function add_filters() {
		foreach ($this->filters as $callback => $filter) {
			$callback = !is_numeric($callback) ? $callback : $filter;
            $priority = 10; $accepted_args = 1;

            if (is_array($filter)) {
                $_filter = $filter;

                $filter = $_filter['filter'];
                $callback = $_filter['callback'];
                $priority = $_filter['priority'];
                $accepted_args = $_filter['accepted_args'];
            }

			add_filter( $filter, array($this, "filter_{$callback}"), $priority, $accepted_args );
		}
	}

    /*
     * Register Actions.
     */
	public function add_actions() {
		foreach ( $this->actions as $callback => $action ) {
			$callback = ! is_numeric($callback) ? $callback : $action;
			add_action( $action, array( $this, "action_{$callback}" ) );
		}
	}

    public function filter_query_vars( $vars ) {
    	$vars[] = 'listing_type';
    	return $vars;
    }

    public function filter_body_class( $classes ) {
        if ( is_singular( 'job_listing' ) ) {
            global $post;
            $listing = \MyListing\Src\Listing::get( $post );

            if ( $post->_case27_listing_type ) {
                $classes[] = "single-listing";
                $classes[] = "type-{$post->_case27_listing_type}";
            }

            if ( $post->_package_id ) {
                $classes[] = "package-{$post->_package_id}";
            }

            if ( $listing->is_verified() ) {
                $classes[] = 'c27-verified';
            }

            if ( $listing->type ) {
                $layout = $listing->type->get_layout();
                $classes[] = esc_attr( sprintf( 'cover-style-%s', $layout['cover']['type'] ) );
            }
        }

        $classes[] = 'my-listing';

        return $classes;
    }

    public function action_after_switch_theme() {
        flush_rewrite_rules();
    }

    public function action_pre_get_posts( $query ) {
        if ( ! is_author() || ! $query->is_main_query() || is_admin() ) {
            return;
        }

        $query->set('post_type', 'job_listing');
    }

    public function filter_get_the_archive_title( $title ) {
        if ( ! class_exists('WooCommerce') ) return $title;

        if ( is_woocommerce() ) {
            $title = woocommerce_page_title(false);
        } elseif ( is_cart() || is_checkout() || is_account_page() || is_page() ) {
            $title = get_the_title();
        } elseif ( is_home() ) {
            $title = apply_filters( 'the_title', get_the_title( get_option( 'page_for_posts' ) ), get_option( 'page_for_posts' ) );
        }

        return $title;
    }

    /**
     * Register theme required plugins using TGM Plugin Activation library.
     *
     * @since 1.0
     */
    public function action_tgmpa_register() {
        // List of plugins to install.
        $plugins = [
            [
                'name' => __( 'Elementor', 'my-listing' ),
                'slug' => 'elementor',
                'required' => true, // If false, the plugin is only 'recommended' instead of required.
                'force_activation' => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            ],
            [
                'name' => __( 'WooCommerce', 'my-listing' ),
                'slug' => 'woocommerce',
                'required' => true,
                'force_activation' => true,
            ],
            [
                'name' => __( 'Contact Form 7', 'my-listing' ),
                'slug' => 'contact-form-7',
                'required' => false,
                'force_activation' => false,
            ],
        ];

        // Array of configuration settings.
        $config = array(
            'id'           => 'case27',
            'default_path' => c27()->template_path('includes/plugins/'),
            'dismissable'  => true,
            'is_automatic' => true,
        );

        tgmpa( $plugins, $config );
    }

    public function filter_case27_featured_service_content( $content ) {
        if ( ! trim( $content ) ) {
            return $content;
        }

        $dom = new \DOMDocument;
        $dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

        foreach ( ['h1', 'h2', 'h3'] as $tagSelector) {
            foreach ( $dom->getElementsByTagName( $tagSelector ) as $tag ) {
                $tag->setAttribute( 'class', $tag->getAttribute( 'class' ) . ' case27-primary-text' );
            }
        }

        return $dom->saveHTML();
    }

    public function action_case27_footer() {
        ?>
        <style type="text/css">
            <?php echo $GLOBALS['case27_custom_styles'] ?>
        </style>
        <?php

        if ( c27()->get_setting('custom_code') ) {
            echo c27()->get_setting('custom_code');
        }
    }

    public function filter_admin_menu() {
        $user = wp_get_current_user();

        if ( ! in_array( 'administrator', $user->roles ) ) {
            remove_menu_page( 'ai1wm_export' );
            remove_submenu_page( 'ai1wm_export', 'ai1wm_import' );
            remove_submenu_page( 'ai1wm_export', 'ai1wm_backups' );
        }
    }
}

add_filter( 'comment_form_defaults', function( $fields ) {
    $fields['must_log_in'] = '<p class="must-log-in">' . sprintf(
        __( 'You must be <a href="%s">logged in</a> to post a comment.', 'my-listing' ),
        esc_url( \MyListing\get_login_url() )
    ) . '</p>';

    return $fields;
} );

add_filter( 'comment_reply_link', function( $link, $args, $comment, $post ) {
    if ( class_exists( 'WooCommerce' ) && get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
        $link = sprintf( '<a rel="nofollow" class="comment-reply-login" href="%s">%s</a>',
            esc_url( \MyListing\get_login_url() ),
            $args['login_text']
        );
    }

    return $link;
}, 30, 4 );

/**
 * Include attachment guid in `wp.media.frames.file_frame`. Necessary
 * to add CDN and media offloading support for listing file fields.
 *
 * @since 2.4.5
 */
add_filter( 'wp_prepare_attachment_for_js', function( $response, $attachment, $meta ) {
    $response['guid'] = get_the_guid( $attachment->ID );
    $response['encoded_guid'] = 'b64:'.base64_encode( $response['guid'] );
    return $response;
}, 100, 3 );

/**
 * Add a way to link to the user profile from a WordPress menu item.
 *
 * @since 3.0
 */
add_filter( 'wp_nav_menu_objects', function( $menu_items ) {
	$username = is_user_logged_in() ? wp_get_current_user()->user_login : '';
	foreach ( $menu_items as $menu_item ) {
		$menu_item->url = str_replace( '#username#', $username, $menu_item->url );
	}

	return $menu_items;
} );
