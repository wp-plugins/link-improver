<?php
/*
Plugin Name:Link Improver
Plugin URI: http://wordpress.org/extend/plugins/link-improver
Description:By using the 'Link Improver',you can automatically enhance your casual links to look and work better.
Author: Luke
Version: 0.3
Author URI: http://geeklu.com
*/

/** l10n */
function load_language(){
    load_plugin_textdomain('link-improver', "/wp-content/plugins/link-improver/");
}


function link_improver() {
    $link_improver = get_option('link_improver');
    $link_improver_notargets = get_option('link_improver_notargets');
    $link_improver_notitles = get_option('link_improver_notitles');
    $link_improver_nomailevents = get_option("link_improver_nomailevents");
    $link_improver_internal=get_option('link_improver_internal');
    $link_improver_external=get_option('link_improver_external');
    $link_improver_subdomain=get_option('link_improver_subdomain');
    $link_improver_email=get_option('link_improver_email');
    if ($link_improver == 1)
    {
        $param_existed=0;
        $notargets='';
        $notitles='';
        $nomailevents='';
        if($link_improver_notargets==1)
        {
            $param_existed=1;
            $notargets='?notargets';
        }
        if($link_improver_notitles==1)
        {  if($param_existed==1){$notitles='&notitles';}
            else{$param_existed=1;$notitles='?notitles';}
        }
        if($link_improver_nomailevents==1)
        {
            if($param_existed==1){ $nomailevents='&nomailevents';}
            else{
                $param_existed=1;$nomailevents='?nomailevents';
            }

        }
        if ( !defined('WP_CONTENT_URL') ) define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
        $plugin_url = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));
        echo <<<EOF
        <STYLE type="text/css">
            a.internal  {color: $link_improver_internal;}
            a.external  {color: $link_improver_external;}
            a.subdomain {color: $link_improver_subdomain;}
            a.email     {color: $link_improver_email;}
        </STYLE>
EOF;
        echo '<script type="text/javascript" src="' . $plugin_url. '/assets/dlink/dlink.js'.$notargets.$notitles.$nomailevents.'"></script>';
    }
}


function render_dlink($text){
    $text = eregi_replace('\[dlink\s*\]', '<div class="dlink">', $text);
    $text = eregi_replace('\[/dlink\s*\]', '</div>', $text);
    return $text;
}
//creates the option variable
function set_li_options () {
    add_option('link_improver','1','active the plugin');
    add_option('link_improver_notargets','0','external links opened in the same window');
    add_option('link_improver_notitles','0','remove the tips');
    add_option('link_improver_nomailevents','0','remove the events of the email');
    add_option('link_improver_internal','#FF0000','internal links color');
    add_option('link_improver_external','#0000FF','external links color');
    add_option('link_improver_subdomain','#800000','subdomain links color');
    add_option('link_improver_email','#008000','email links color');
}
function unset_li_options () {
    delete_option('link_improver');
    delete_option('link_improver_notargets');
    delete_option('link_improver_notitles');
    delete_option('link_improver_nomailevents');
    delete_option('link_improver_internal');
    delete_option('link_improver_external');
    delete_option('link_improver_subdomain');
    delete_option('link_improver_email');
}
function li_options () {
    echo '<div class="wrap"><h2>Link Improver Options</h2>';
    if ($_REQUEST['submit']) {
        update_li_options();
    }
    print_li_form();
    echo '</div>';
}
function modify_menu_for_li () {
    $pageName=add_options_page(
        'Link Improver',         //Title
        'Link Improver',         //Sub-menu title
        'manage_options', //Security
        __FILE__,         //File to open
        'li_options'  //Function to call
    );
    add_action("admin_head-" . $pageName, "addAdminHeaderCode", 12);
}
function update_li_options() {
    $updated = false;
    if ($_REQUEST['internalColor']) {
        update_option('link_improver_internal', $_REQUEST['internalColor']);
        $updated = true;
    }
    if ($_REQUEST['externalColor']) {
        update_option('link_improver_external', $_REQUEST['externalColor']);
        $updated = true;
    }
    if ($_REQUEST['subdColor']) {
        update_option('link_improver_subdomain', $_REQUEST['subdColor']);
        $updated = true;
    }
    if ($_REQUEST['emailColor']) {
        update_option('link_improver_email', $_REQUEST['emailColor']);
        $updated = true;
    }
    if ($_REQUEST['notargets']) {
        update_option('link_improver_notargets', '1');
        $updated = true;
    }
    else{
        update_option('link_improver_notargets', '0');
        $updated = true;
    }

    if ($_REQUEST['notitles']) {
        update_option('link_improver_notitles', '1');
        $updated = true;
    }else{
         update_option('link_improver_notitles', '0');
        $updated = true;
    }
    if ($_REQUEST['nomailevents']) {
        update_option('link_improver_nomailevents', '1');
        $updated = true;
    }
    else{
        update_option('link_improver_nomailevents', '0');
        $updated = true;
    }
    if ($updated) {
        echo '<div id="message" class="updated fade">';
        echo '<p>'.__('Options Updated','link-improver').'</p>';
        echo '</div>';
    } else {
        echo '<div id="message" class="error fade">';
        echo '<p>'.__('Unable to update options','link-improver').'</p>';
        echo '</div>';
    }
}
function print_li_form () {
    $link_improver = get_option("link_improver");
    $link_improver_notargets = get_option("link_improver_notargets");
    $link_improver_notitles = get_option("link_improver_notitles");
    $link_improver_nomailevents = get_option("link_improver_nomailevents");
    $link_improver_internal=get_option('link_improver_internal');
    $link_improver_external=get_option('link_improver_external');
    $link_improver_subdomain=get_option('link_improver_subdomain');
    $link_improver_email=get_option('link_improver_email');
    if ($link_improver_notargets == 1)
    {
        $checked1 = 'checked="checked"';
    }
    if($link_improver_notitles==1)
    {
        $checked2 = 'checked="checked"';
    }
    if ($link_improver_nomailevents == 1)
    {
        $checked3 = 'checked="checked"';
    }

    $style_the_color_txt =__('Style Color Header','link-improver');
    $internal_color_txt=__('Internal Color','link-improver');
    $external_color_txt=__('External Color','link-improver');
    $subdomain_color_txt=__('Subdomain Color','link-improver');
    $email_color_txt=__('Email Color','link-improver');
    $custom_features_txt=__('Custom Features','link-improver');
    $notargets_txt=__('Notargets','link-improver');
    $notitles_txt=__('Notitles','link-improver');
    $nomailevents_txt=__('Nomailevents','link-improver');
    $about_txt=__('About','link-improver');
    $about_content_txt=__('About Content','link-improver');
    $save_changes_text=__('Save Changes','link-improver');
    
    echo <<<EOF
<form method="post">

    <tr>
        <td><h4>$about_txt</h4></td>
        <td>
           $about_content_txt  <br />
        </td>
    </tr>
    <br/>
    <tr>
        <td><h4>$style_the_color_txt</h4></td>
        <td>
         <label>$internal_color_txt</label><input type="text" value=$link_improver_internal name="internalColor" id="internalColor"/><br />
         <label>$external_color_txt</label><input type="text" value =$link_improver_external name="externalColor" id="externalColor"/><br/>
         <label>$subdomain_color_txt</label><input type="text" value =$link_improver_subdomain name="subdColor" id="subdColor"/><br/>
         <label>$email_color_txt</label><input type="text" value =$link_improver_email name="emailColor" id="emailColor"/><br/>
        </td>
    </tr>
    <br />
    <tr>
        <td><h4> $custom_features_txt</h4></td>
        <td>
            <input type="checkbox" value="1"  $checked1  name="notargets"/><label>$notargets_txt</label> <br /><br />
            <input type="checkbox" value ="1"  $checked2  name="notitles"/><label>$notitles_txt</label> <br/><br />
            <input type="checkbox" value="1"  $checked3 name="nomailevents"/><label>$nomailevents_txt</label> <br /><br />
             </td>
    </tr>
    <br />
   <p class="submit">
    <input type="submit" name="submit" value="$save_changes_text" />
    </p>
    </form>
EOF;
}


function addAdminHeaderCode() {
    global $wp_version;

    if ( !defined('WP_CONTENT_URL') ) define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
    $plugin_url = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));

    echo '<link href="' . $plugin_url . '/assets/cpicker/colorPicker.css" rel="stylesheet" type="text/css" />';
    echo "\n";
    // Include jquery library if we are not running WP 2.5 or above
    if (version_compare($wp_version, "2.5", "<")) {
        echo '<script type="text/javascript" src="' .$plugin_url. '/assets/cpicker/jquery.js"></script>';
        echo "\n";
    }

    echo '<script type="text/javascript" src="' . $plugin_url. '/assets/cpicker/jquery.colorPicker.js"></script>';
    echo "\n";
    echo <<<EOF
<script type="text/javascript">
  //Run the code when document ready
  jQuery(function() {
   //use this method to add new colors to pallete
   //jQuery.fn.colorPicker.addColors(['D47700', '0074D4', 'D43500', '00B235']);

   jQuery('#internalColor').colorPicker();
   jQuery('#externalColor').colorPicker();
   jQuery('#subdColor').colorPicker();
   jQuery('#emailColor').colorPicker();
  });
</script>

EOF;
}
//Add and Remove vars when creating/removing plugin
register_activation_hook(__FILE__,'set_li_options');
register_deactivation_hook(__FILE__,'unset_li_options');
//Adds the admin menu
add_action('admin_menu','modify_menu_for_li');
//Adds the li to the right location
add_action('wp_head', 'link_improver');
add_action('init','load_language');
add_filter('the_content', 'render_dlink', 10);
add_filter('the_excerpt', 'render_dlink', 10);
?>