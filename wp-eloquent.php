<?php
/**
 * Plugin Name: WordPress Eloquent ORM
 * Description: A single-page WordPress plugin with Composer support and testing.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPLv2 or later
 */
use \Emneslab\ORM\Support\Facades\DB;

use \Emneslab\ORM\WP\Post;

use Illuminate\Database\Schema\Blueprint;

use \Emneslab\ORM\Support\Facades\Schema;


// Autoload Composer dependencies.
require __DIR__ . '/vendor/autoload.php';

// Hook for adding admin menu
add_action('admin_menu', 'composer_plugin_menu');

function composer_plugin_menu() {
    add_menu_page(
        __('Composer Plugin', 'composer-plugin'),
        __('Composer Plugin', 'composer-plugin'),
        'manage_options',
        'composer-plugin',
        'composer_plugin_page',
        'dashicons-admin-generic'
    );
}

// Callback function for the admin page
function composer_plugin_page() {
    echo '<div class="wrap"><h1>' . __('Composer Plugin Page', 'composer-plugin') . '</h1>';
    echo '<p>' . __('This is a single-page plugin example with Composer support.', 'composer-plugin') . '</p></div>';

    $db = \Emneslab\ORM\Database\Connection::instance();

    dump( $db->table('users')->find(1) );
    dump( $db->select('SELECT * FROM wp_users WHERE id = ?', [1]) );
    dump( $db->table('users')->where('user_login', 'john')->first() );
    
    // OR with DB facade
    
    dump( DB::table('users')->find(1) );
    dump( DB::select('SELECT * FROM wp_users WHERE id = ?', [1]) );
    dump( DB::table('users')->where('user_login', 'john')->first() );


    dump(Post::all() ); //returns only posts with WordPress post_type "post"
    dump(Post::type('page')->get()->toArray()); // get pages
    dump(Post::status('publish')->get()->toArray()); // get posts with publish status
    dump(Post::type('page')->status('publish')->get()->toArray()); // get pages with publish status


    Schema::drop('wp_test_database_table');


    Schema::create('test_database_table', function (Blueprint $table)  {
        $table->id();
    });

}
