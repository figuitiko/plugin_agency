<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 04/11/2016
 * Time: 11:28
 */
class MY_Booking {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {

        $this->plugin_name = 'my-booking';
        $this->version = '1.0.0';

        $this->load_dependencies();
        //$this->set_locale();
        //$this->define_admin_hooks();
        $this->define_public_hooks();

        //$this->define_widget_hooks();

    }

    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-my-booking-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
       // require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-my-booking-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
       // require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-my-booking-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-my-booking-public.php';

        /**
         * The class responsible for loading widget.
         */
       // require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-my-booking-widget.php';

        $this->loader = new MY_Booking_Loader();

    }

    private function define_public_hooks() {

        $plugin_public = new MY_Booking_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_head', $plugin_public, 'my_wp_head_ajax_url' );
      //$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        //$this->loader->add_action( 'wp_footer', $plugin_public, 'my_registration' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'my_script_footer' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'my_script_footer_cart' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'my_script_footer_hotel' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'my_script_footer_internacional' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'my_script_footer_countries' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'ajax_contact' );
       // $this->loader->add_action( 'wp_ajax_check_username', $plugin_public, 'wp_ajax_check_username' );
        //$this->loader->add_action( 'wp_ajax_nopriv_check_username', $plugin_public, 'wp_ajax_check_username' );
        $this->loader->add_action( 'wp_ajax_book', $plugin_public, 'wp_ajax_book' );
        $this->loader->add_action( 'wp_ajax_nopriv_book', $plugin_public, 'wp_ajax_book' );
        $this->loader->add_action( 'wp_ajax_book_cart', $plugin_public, 'wp_ajax_book_cart' );
        $this->loader->add_action( 'wp_ajax_nopriv_book_cart', $plugin_public, 'wp_ajax_book_cart' );
        $this->loader->add_action( 'wp_ajax_book_hotel', $plugin_public, 'wp_ajax_book_hotel' );
        $this->loader->add_action( 'wp_ajax_nopriv_book_hotel', $plugin_public, 'wp_ajax_book_hotel' );
        $this->loader->add_action( 'wp_ajax_book_internacional', $plugin_public, 'wp_ajax_book_internacional' );
        $this->loader->add_action( 'wp_ajax_nopriv_book_internacional', $plugin_public, 'wp_ajax_book_internacional' );
        $this->loader->add_action( 'wp_ajax_select_country', $plugin_public, 'wp_ajax_select_country' );
        $this->loader->add_action( 'wp_ajax_nopriv_select_country', $plugin_public, 'wp_ajax_select_country' );
        $this->loader->add_action( 'wp_ajax_contact', $plugin_public, 'wp_ajax_contact' );
        $this->loader->add_action( 'wp_ajax_nopriv_contact', $plugin_public, 'wp_ajax_contact' );
        //$this->loader->add_action( 'admin_init', $plugin_public, 'admin_user_metabox' );
        $this->loader->add_shortcode( 'my-booking-form', $plugin_public, 'my_booking_shortcode' );
        $this->loader->add_shortcode( 'my-booking-form-cart', $plugin_public, 'my_booking_shortcode_cart' );
        $this->loader->add_shortcode( 'my-booking-form-hotel', $plugin_public, 'my_booking_shortcode_hotel' );
        $this->loader->add_shortcode( 'my-booking-form-internacional', $plugin_public, 'my_booking_shortcode_internacional' );
        $this->loader->add_shortcode( 'my-form-contact', $plugin_public, 'my_shorcode_cotact' );


        /*$this->loader->add_shortcode( 'sd-advanced-search', $plugin_public, 'advanced_searchsd_shortcode' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        //$this->loader->add_filter( 'query_vars', $plugin_public, 'registering_query_vars' );
        $this->loader->add_action( 'pre_get_posts', $plugin_public, 'override_search_query', 1000 );
        $this->loader->add_action( 'pre_get_posts', $plugin_public, 'override_search_query_meta', 1000 );*/



    }
    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }
    public function get_version() {
        return $this->version;
    }
}