<?php	
    if (isset($_POST["postcheck"]) === true) {
        // If it is not active, make it zero.
        if (isset($_POST["option_active"]) === false) {
            $_POST["option_active"] = "0";
        }

        // Update the options given in the POST.
        update_option(_ALPRE."theme",         $_POST["option_theme"]);
        update_option(_ALPRE."message",       addslashes(urlencode($_POST["option_message"])));
        update_option(_ALPRE."active",        $_POST["option_active"]);
        update_option(_ALPRE."cookie",        $_POST["option_cookie"]);
        update_option(_ALPRE."cookie_expire", $_POST["option_expire"]);
    }

    // Get the options available.
    $options["theme"]   = get_option(_ALPRE. "theme");
    $options["active"]  = (bool)get_option(_ALPRE. "active");
    $options["cookie"]  = get_option(_ALPRE. "cookie");
    $options["message"] = stripslashes(urldecode(get_option(_ALPRE. "message")));
    $options["expire"]  = get_option(_ALPRE. "cookie_expire");

    /**
     * Theme options
     */

    $dir        = opendir(dirname(__FILE__)."/themes");
    $form_theme = '<select name="option_theme">';

    while (false !== ($file = readdir($dir))) {
        if (strpos($file, '.css',1)) {
            if ($file == $options["theme"]) {
                $form_theme .= '<option selected="selected" value="'. $file. '">'. $file. '</option>'. PHP_EOL;
            } else {
                $form_theme .= '<option value="'. $file. '">'. $file. '</option>'. PHP_EOL;
            }
        }
    }
    
    $form_theme .= "</select>";

    /**
     * Cookie/Session expire time setter.
     */

    $zero  = "";
    $one   = "";
    $two   = "";
    $three = "";
    $four  = "";
    $five  = "";
    $six   = "";
    $seven = "";

    switch ($options["expire"]) {
            case 0:	 $zero =  'selected="selected"'; break;
            case 1800:   $one =   'selected="selected"'; break;
            case 3600:   $two =   'selected="selected"'; break;
            case 5400:   $three = 'selected="selected"'; break;
            case 7200:   $four =  'selected="selected"'; break;
            case 86400:  $five =  'selected="selected"'; break;
            case 172800: $six =   'selected="selected"'; break;
            case 604800: $seven = 'selected="selected"'; break;
    }

    $form_expire = '<select name="option_expire"> '
                 . '  <option "'. $zero.  '" value="0">On Browser Close (15 minutes if session)</option> '
                 . '  <option "'. $one.   '" value="1800">30 minutes </option> '
                 . '  <option "'. $two.   '" value="3600">1 hour </option> '
                 . '  <option "'. $three. '" value="5400">90 minutes </option> '
                 . '  <option "'. $four.  '" value="7200">2 hours </option> '
                 . '  <option "'. $five.  '" value="86400">1 day </option> '
                 . '  <option "'. $six.   '" value="172800">2 days </option> '
                 . '  <option "'. $seven. '" value="604800">1 week </option> '
                 . '</select>';

    unset($zero, $one, $two, $three, $four, $five, $six, $seven);

    /**
     * Form activated button.
     */

    if ($options["active"] === true) {
            $checked = ' checked="checked" ';
    } else {
            $checked = '';
    }

    $form_active =  '<input type="checkbox"'. $checked. 'name="option_active" value="1">&nbsp;Active';

    unset($checked);

    /**
     * Set the cookie selectbox.
     */

    $cookie = array('cookie' => '', 'session' => '', 'nothing' => '');
    if ($options["cookie"] === true || $options["cookie"] === "cookie") {
        $cookie['cookie'] = 'selected="selected" ';
    } else if ($options["cookie"] === "session") {
        $cookie['session'] = 'selected="selected" ';
    } else {
        $cookie['nothing'] = 'selected="selected" ';
    }
    
    $form_cookie = '<select name="option_cookie">'
                 . '  <option '. $cookie['cookie'].  'value="cookie">Cookie</option>'
                 . '  <option '. $cookie['session']. 'value="session">Session</option>'
                 . '  <option '. $cookie['nothing']. 'value="nothing">Nothing</option>'
                 . '</select>';

    unset($cookie);

    /**
     * Fill in the message.
     */

    $form_message = '<textarea name="option_message" cols=40 rows=12>'. $options["message"]. '</textarea>';
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32">
        <br/>
    </div>

    <h2>WP-AlertBox settings</h2>
    
    <br/>

    <form action="?page=wp-alertbox/options.php" method="POST">
        <input type="hidden" name="postcheck" value="true">

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="option_theme">Theme selection</label>
                </th>
                <td>
                    <?php echo $form_theme; ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="option_theme">Display message</label>
                </th>
                <td>
                    <?php echo $form_message; ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="option_theme">Use cookie, session or nothing</label>
                </th>
                <td>
                    <?php echo $form_cookie; ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="option_theme">If cookie/session, when does it expire</label>
                </th>
                <td>
                    <?php echo $form_expire; ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="option_theme">Active</label>
                </th>
                <td>
                    <?php echo $form_active; ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                </th>
                <td>
                    <button type="submit">Save settings</button> <button type="reset">Undo</button>
                </td>
            </tr>

        </table>
    </form>
</div>