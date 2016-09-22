<?php

/**
 * Plugin Name: Timber
 * Description: Song and artist logging for radio stations.
 * Version: 1.2.5
 * Author: Samuel Coleman/Seeside Productions
 * Author URI: http://seesideproductions.com
 */

// Grab some global things.
global $wpdb;
define('AXEPATH', ABSPATH . 'wp-content/plugins/timber/');

// Set up the Timber class autoloader.
require_once('engine/ca/acadiau/axeradio/common/Autoloader.php');

$autoloader = new ca\acadiau\axeradio\common\Autoloader(AXEPATH . 'engine');
spl_autoload_register(function ($name) use ($autoloader)
{
    $autoloader->load($name);
});

add_action('admin_menu', 'timber_menus');
add_action('admin_print_scripts', 'timber_scripts');
add_action('admin_print_styles', 'timber_styles');

register_activation_hook(__FILE__, 'timber_install');
add_action('plugins_loaded', 'timber_update');

// AJAX call for editing a logged song. Used by the click-field-to-edit songs interface.
add_action('wp_ajax_timber_edit_song',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\songs\ajax\EditSong(
            new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb));
        $controller->show();
    }
);

// AJAX call for adding a logged song. Currently not used.
add_action('wp_ajax_timber_add_song',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\songs\ajax\AddSong(
            new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
            new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb),
            new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
        $controller->show();
    }
);

// AJAX call for retrieving a selection of recently-played songs. Currently not used.
add_action('wp_ajax_timber_list_songs',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\songs\ajax\GetSongsPage(
            new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb));
        $controller->show();
    }
);

// Non-privileged AJAX call for retrieving a selection of recently-played songs. Used by the player window.
add_action('wp_ajax_nopriv_timber_list_songs',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\songs\ajax\GetSongsPage(
            new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb));
        $controller->show();
    }
);

// AJAX call to get station broadcast day cutoffs. Currently not used.
add_action('wp_ajax_timber_get_cutoffs',
    function ()
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\shows\ajax\GetStationCutoffs();
        $controller->show();
    }
);

// Non-privileged AJAX call to get station broadcast day cutoffs. Currently not used.
add_action('wp_ajax_nopriv_timber_get_cutoffs',
    function ()
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\shows\ajax\GetStationCutoffs();
        $controller->show();
    }
);

// AJAX call to get the current show. Currently not used.
add_action('wp_ajax_timber_get_current_show',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\shows\ajax\GetCurrentShow(
            new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
            new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
        $controller->show();
    }
);

// Non-privileged AJAX call to get the current show. Currently not used.
add_action('wp_ajax_nopriv_timber_get_current_show',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\shows\ajax\GetCurrentShow(
            new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
            new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
        $controller->show();
    }
);

// AJAX call to get the next show. Currently not used.
add_action('wp_ajax_timber_get_next_show',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\shows\ajax\GetNextShow(
            new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
            new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
        $controller->show();
    }
);

// Non-privileged AJAX call to get the next show. Currently not used.
add_action('wp_ajax_nopriv_timber_get_next_show',
    function () use ($wpdb)
    {
        $controller = new ca\acadiau\axeradio\timber\controllers\shows\ajax\GetNextShow(
            new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
            new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
        $controller->show();
    }
);

// Add XML-RPC methods.
add_filter('xmlrpc_methods',
    function ($methods)
    {
        global $wp_xmlrpc_server, $wpdb;

        $methods['timber.getCurrentShow'] = function ($args) use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\shows\XmlRpcShowsController(
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
            return $controller->getCurrentShow();
        };

        $methods['timber.getNextShow'] = function ($args) use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\shows\XmlRpcShowsController(
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
            return $controller->getNextShow();
        };

        $methods['timber.getStationCutoffs'] = function ($args) use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\shows\XmlRpcShowsController(
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
            return $controller->getStationCutoffs();
        };

        $methods['timber.logSong'] = function ($args) use ($wpdb, $wp_xmlrpc_server)
        {
            $wp_xmlrpc_server->escape($args);

            $username = $args[0];
            $password = $args[1];
            if (!$user = $wp_xmlrpc_server->login($username, $password))
                return $wp_xmlrpc_server->error;

            $controller = new ca\acadiau\axeradio\timber\controllers\songs\XmlRpcSongsController(
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
            if (isset($args[4]))
                return $controller->addSong($args[2], $args[3]);
            else
                return $controller->addSong($args[2], $args[3], $args[4]);
        };

        return $methods;
    }
);

/**
 * Define admin menu items. See http://codex.wordpress.org/Function_Reference/add_menu_page and
 * http://codex.wordpress.org/Function_Reference/add_submenu_page.
 */
function timber_menus()
{
    global $wpdb;

    add_menu_page(
        _('Timber'),
        _('Timber'),
        'read',
        'timber',
        function () use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\Timber(
                new \ca\acadiau\axeradio\timber\repositories\Icecast($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Charts($wpdb));
            $controller->show();
        }
    );

    add_submenu_page(
        'timber',
        _('All Shows'),
        _('All Shows'),
        'edit_users',
        'timber_shows',
        function () use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\shows\AllShows(
                new \ca\acadiau\axeradio\timber\repositories\Categories($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb));
            $controller->show();
        }
    );

    add_submenu_page(
        'timber_shows',
        _('Show'),
        _('Show'),
        'edit_users',
        'timber_admin_show',
        function () use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\shows\AdminShow(
                new \ca\acadiau\axeradio\timber\repositories\Categories($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Users($wpdb));
            $controller->show();
        }
    );

    add_submenu_page(
        'timber',
        _('All Genres'),
        _('All Genres'),
        'edit_users',
        'timber_all_categories',
        function () use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\categories\AllCategories(
                new \ca\acadiau\axeradio\timber\repositories\Categories($wpdb));
            $controller->show();
        }
    );

    add_submenu_page(
        'timber_all_categories',
        _('Genre'),
        _('Genre'),
        'edit_users',
        'timber_admin_category',
        function () use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\categories\AdminCategory(
                new \ca\acadiau\axeradio\timber\repositories\Categories($wpdb));
            $controller->show();
        }
    );

    add_submenu_page(
        'timber',
        _('Songs'),
        _('Songs'),
        'edit_posts',
        'timber_songs',
        function () use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\songs\AllSongs(
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Timeslots($wpdb));
            $controller->show();
        }
    );

    add_submenu_page(
        'timber',
        _('Charts'),
        _('Charts'),
        'read',
        'timber_charts',
        function () use ($wpdb)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\songs\Charts(
                new \ca\acadiau\axeradio\timber\repositories\Charts($wpdb));
            $controller->show();
        }
    );
}

/**
 * Custom tags.
 */
add_filter('the_content', function ($content) use ($wpdb)
    {
        if (strpos($content, '[timber_archives]') !== false)
        {
            $controller = new ca\acadiau\axeradio\timber\controllers\songs\Archives(
                new \ca\acadiau\axeradio\timber\repositories\Shows($wpdb),
                new \ca\acadiau\axeradio\timber\repositories\Songs($wpdb),
                false);
            $content = str_replace('[timber_archives]', $controller->render(), $content);
        }
        return $content;
    }
);

/**
 * User preferences.
 */
add_action('show_user_profile',
    function ()
    {
        $template = new \ca\acadiau\axeradio\common\Template('profile');

        $ordering = get_user_meta(wp_get_current_user()->ID, 'timber_log_form_field_ordering', true);
        if ($ordering === '')
            $ordering = 'name-artist';
        $template->set('ordering', $ordering);

        $instantaneous_stats = (boolean) get_user_meta(wp_get_current_user()->ID, 'timber_instantaneous_stats', true);
        $template->set('instantaneous_stats', $instantaneous_stats);
        $template->display();
    }
);

add_action('personal_options_update',
    function ()
    {
        update_user_meta(wp_get_current_user()->ID, 'timber_log_form_field_ordering',
            (isset($_POST['timber_log_form_field_ordering'])
                ? $_POST['timber_log_form_field_ordering']
                : 'name-artist'));
        if (is_admin())
            update_user_meta(wp_get_current_user()->ID, 'timber_instantaneous_stats',
                (boolean) $_POST['timber_instantaneous_stats']);
    }
);

/**
 * Admin area scripts required by Timber.
 */
function timber_scripts()
{
    wp_enqueue_script('farbtastic'); // Colour picker
    wp_enqueue_script('editable', plugins_url('js/jquery.jeditable.mini.js', __FILE__)); // Edit-on-click library
    wp_enqueue_script('songs', plugins_url('js/songs.js', __FILE__)); // Edit-on-click implementation
}

/**
 * CSS required by Timber.
 */
function timber_styles()
{
    wp_enqueue_style('farbtastic');
}

/**
 * Database installation.
 */
function timber_install()
{
    global $wpdb;
    $repository = new \ca\acadiau\axeradio\timber\repositories\Repository($wpdb);
    $repository->install();
}

/**
 * Database update.
 */
function timber_update()
{
    if (get_option('timber_db_version') != \ca\acadiau\axeradio\timber\repositories\Repository::DB_VERSION)
        timber_install();
}