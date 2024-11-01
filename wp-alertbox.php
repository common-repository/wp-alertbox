<?php
/*
Plugin Name: WP-AlertBox
Plugin URI: http://boycallaars.wordpress.com/wordpress-plugin-wp-alertbox/
Description: A simple - customizable - alert box for displaying messages by using the <a href="http://prototype-window.xilinus.com/">Prototype Window</a> scripts.
Version: 1.3
Author: Boy Callaars
Author URI: http://www.mrcetrac.nl/
*/

/* Copyright 2010  Boy Callaars  (email: info@mrcetrac.nl)

   This plugin is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported License.
   To be found @ http://creativecommons.org/licenses/by-sa/3.0/
*/

/* ================================== MAIN CODE ================================== */

define("_ALPRE",  "wp_alertbox_");
define("_THEMES", dirname(__FILE__)."/themes/");

// Default options
add_option(_ALPRE. "message", "This is the default alert message, you can change this in anything you like.");
add_option(_ALPRE. "active",  0);
add_option(_ALPRE ."theme",   "alphacube.css");

// If the message box is active AND we use cookies, which doesn't need to the show it.. or not.

if ((bool)get_option(_ALPRE. "active") === true) {
    $cookieOption = (get_option(_ALPRE. "cookie") === true) ? 'cookie' : get_option(_ALPRE. "cookie");

    if ($cookieOption == 'cookie') {
        if (isset($_COOKIE["wpalertboxalreadyshown"]) === false) {
            $expire_cookie = get_option(_ALPRE. "cookie_expire");

            if ($expire_cookie != 0) {
                $expire_cookie = time() + $expire_cookie;
            }

            setcookie("wpalertboxalreadyshown", time(), $expire_cookie);
            define('_SHOW', true);
        } else {
            define('_SHOW', false);
        }
    } else if ($cookieOption == 'session') {
        // Dirty, dirty, dirty, dirty.
        if (floatval(phpversion()) > 5) {
            if (session_start() === false) {
                // Fine.
            } else {
                // Fine too.
            }
        } else {
            @session_start();
        }

        if ($_SESSION['wpalertboxalreadyshown'] === null) {
            $_SESSION['wpalertboxalreadyshown'] = '';
        }

        $sessionId      = $_SESSION['wpalertboxalreadyshown'];
        $expire_session = (int)get_option(_ALPRE. "cookie_expire");

        if ($expire_session != 0) {
            $expire_session = time() + $expire_session;
        } else {
            $expire_session = time() + 900;
        }

        if ($sessionId == '') {
            $_SESSION['wpalertboxalreadyshown'] = $expire_session;
            define('_SHOW', true);
        } else {
            if ($sessionId <= time()) {
                $_SESSION['wpalertboxalreadyshown'] = $expire_session;
                define('_SHOW', true);
            } else {
                define('_SHOW', false);
            }
        }
    } else {
        define('_SHOW', true);
    }
}

// Adds our admin options under "Options"
function wp_alertbox_add_options_page() {
    add_options_page('AlertBox Options', 'WP-AlertBox', 10, 'wp-alertbox/options.php');
}

// Adds the needed headers.. God, that is a nice function name.
function wp_alertbox_header_adder() {
    $theme       = get_option(_ALPRE."theme");
    $thisdir     = get_bloginfo('wpurl')."/wp-content/plugins/wp-alertbox/";

    /* The header code needed for wp-alertbox to work: */
    $echo_script = '<!-- begin wp-alertbox scripts --> '. PHP_EOL
                 . '<link rel="stylesheet" href="'. $thisdir. '/themes/'. $theme. '" type="text/css" media="screen" /> '. PHP_EOL
                 . '<script type="text/javascript" src="'. $thisdir. 'javascripts/prototype.js"> </script> '. PHP_EOL
                 . '<script type="text/javascript" src="'. $thisdir. 'javascripts/effects.js"> </script> '. PHP_EOL
                 . '<script type="text/javascript" src="'. $thisdir. 'javascripts/window.js"> </script> '. PHP_EOL
                 . '<link href="'. $thisdir. 'themes/default.css" rel="stylesheet" type="text/css"/> '. PHP_EOL
                 . '<link href="'. $thisdir. 'themes/alert.css" rel="stylesheet" type="text/css"/> '. PHP_EOL
                 . '<script type="text/javascript" src="'. $thisdir. 'javascripts/debug.js"> </script> '. PHP_EOL
                 . '<!-- end wp-alertbox scripts -->'. PHP_EOL;

    /* Output $echo_script as text for our web pages: */
    echo $echo_script;
}

// The functions which displays the whole thing.
function wp_alertbox_display_alert() {
    if ((bool)get_option(_ALPRE. "active") === true) {
        if (_SHOW === true) {
            $options["theme"] =   get_option(_ALPRE."theme");
            $options["message"] = nl2br(str_replace("\'", "'", urldecode(get_option(_ALPRE."message"))));
            $options["message"] = str_replace("\r\n", "", $options["message"]);

            $model = explode(".", $options["theme"]);
            $model = $model[0];

            $echo_string = '<script type="text/javascript"> '. PHP_EOL
                         . '    Dialog.alert("'. $options["message"]. '", {top: 100, width:400, className: "'. $model. '"}); '. PHP_EOL
                         . '</script> '. PHP_EOL;

            echo $echo_string;
        }

    }
}

/* we want to add the above html to the header of our pages: */
add_action('wp_head',    'wp_alertbox_header_adder');
add_action('admin_menu', 'wp_alertbox_add_options_page');
add_action('wp_footer',  'wp_alertbox_display_alert');
?>
