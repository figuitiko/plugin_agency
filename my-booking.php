<?php
/**
 * @link     http://techprommux.com/figo
 * @since    1.0.0
 * @package  My-booking
 *
 * Plugin Name: My booking
 * Plugin URI:   http://techprommux.com/wordpress/plugings/my-booking
 * Description: Book in the system.
 * Version:     1.0.4
 * Author:      Frank Freeman
 * Author URI:  http://techprommux.com/figo
 * License:     GNU General Public License version 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: my-booking
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
/**
 * The code that runs during plugin activation.
 */
function activate_my_booking() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-booking-activator.php';
    My_booking_Activator::activate();
}
function deactivate_my_booking() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-booking-deactivator.php';
    My_booking_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_my_booking' );
register_deactivation_hook( __FILE__, 'deactivate_my_booking' );

require plugin_dir_path( __FILE__ ) . 'includes/class-my-booking.php';

function run_my_booking() {
    $plugin = new MY_Booking();
    $plugin->run();
}
run_my_booking();