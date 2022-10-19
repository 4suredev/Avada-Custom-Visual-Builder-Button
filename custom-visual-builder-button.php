<?php
/**
 * Plugin Name: Avada Button Shortcode
 * Plugin URI: https://4sure.com.au
 * Description: Adds Avada button shortcodes to the classic editor
 * Version: 1.0.3
 * Author: 4sure
 * Requires PHP: 7.2
 * Requires at least: 5.8
 * Author URI: https://4sure.com.au
 */
define('VBB_PLUGIN_PATH', plugin_dir_url( __FILE__ ));
include_once( plugin_dir_path( __FILE__ ) . 'updater.php');
$updater = new Custom_visual_builder_button_updater( __FILE__ ); // instantiate our class
$updater->set_username( '4suredev' ); // set username
$updater->set_repository( 'Avada-Custom-Visual-Builder-Button' ); // set repo
$updater->initialize(); // initialize the updater
if( ! class_exists( 'Custom_visual_builder_button_updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}
add_action( 'wp_enqueue_scripts', 'vbb_enqueue_styles' );
function vbb_enqueue_styles(){
    wp_enqueue_style( 'vbb-widget-styles', VBB_PLUGIN_PATH.'css/frontend-button-widget-styles.css' );
}
add_shortcode('button', 'vbb_custom_visual_builder_button');
function vbb_custom_visual_builder_button($atts = array()){
    $args = shortcode_atts(
        array(
        'target'    => '',
        'link' => '#',
        'text' => 'Click Here',
        'icon' => false,
        'icon_class' => 'fas fa-angle-right',
        'icon_position' => 'right'
    ), $atts);
    if ($args['target'] == 'newtab'){
        $targetAtts = 'target="_blank"';
    }else if($args['target'] == 'lightbox'){ 
        $targetAtts = 'target="lightbox" rel="iLightbox"'; 
    }else{
        $targetAtts = '';
    }
    if($args['icon']){
        if($args['icon_position'] == 'left'){
            $icon_left = '<i class="'.$args['icon_class'].' button-icon-left" aria-hidden="true"></i>';
        }else{
            $icon_right = '<i class="'.$args['icon_class'].' button-icon-right" aria-hidden="true"></i>';
        }
    }
    $html = '<a href="'.$args['link'].'" class="fusion-button button-flat button-default fusion-button-default-size" '.$targetAtts.' data-caption="">
    '.$icon_left.'<span class="fusion-button-text">'.$args['text'].'</span>'.$icon_right.'</a>';
    return $html;
}
//add media button to visual builder
function vbb_add_shortcodes_media_button() {
    $the_page = get_current_screen();
    $current_page = $the_page->post_type;
    $allowed = array(
        'post',
        'page',
        'product',
        'tribe_events'
    );
    if (in_array($current_page, $allowed, false) || $the_page->base == 'toplevel_page_access-manager' || $the_page->base == 'post'){
        printf( '<a href="%s" class="button generate-button-shortcode">' . '<span class="wp-media-buttons-icon dashicons dashicons-shortcode"></span> %s' . '</a>', '#', __( 'Generate Button', 'textdomain' ) );
    }
    if(get_current_screen()->base == 'toplevel_page_access-manager'){
        echo '<script type="text/javascript">
        jQuery(document).ready(function($){
            $(".generate-button-shortcode").click(function(e){
                e.preventDefault();
                $("#button-shortcode-dialog").dialog("open"); 
                $("#page-mask").css({"opacity":1, "pointer-events": "auto"});
            });
        });
        </script>';
    }
}
add_action( 'media_buttons', 'vbb_add_shortcodes_media_button');
//Button shortcode admin bar widget
add_action('admin_enqueue_scripts', 'vbb_admin_scripts_enqueue');
function vbb_admin_scripts_enqueue($hook) {
    // Only add to the edit post/page admin page.
    if ('post.php' == $hook || 'post-new.php' == $hook || 'toplevel_page_access-manager' == $hook) {
        wp_enqueue_script('admin_custom_script', VBB_PLUGIN_PATH.'js/custom-admin-scripts.js');
        wp_enqueue_script('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js');
    }else{return;}
}
add_action( 'edit_form_after_editor', 'vbb_render_button_shortcode_dialog' );
add_action( 'toplevel_page_access-manager', 'vbb_render_button_shortcode_dialog', 20 );
function vbb_render_button_shortcode_dialog( $post ){
	echo '
    <style>
        #button-shortcode-dialog{display: none;}
        .ui-dialog.ui-front[aria-describedby="button-shortcode-dialog"]{
            z-index: 99999;
            padding: 0;
        }
        .ui-dialog[aria-describedby="button-shortcode-dialog"] .ui-dialog-titlebar{
            padding: 5px 20px;
            position: relative;
        }
        .ui-dialog[aria-describedby="button-shortcode-dialog"] .ui-button.ui-dialog-titlebar-close{
            height: 100%;
            top: 0;
            margin: 0;
            right: 10px;
            width: 18px;
        }
        .ui-dialog[aria-describedby="button-shortcode-dialog"] .ui-button.ui-dialog-titlebar-close .ui-button-icon-space{
            margin: unset;
            width: auto;
            position: absolute;
        }
        #page-mask{
            background: rgb(0 0 0 / 40%);
            width: 100vw;
            height: 100vw;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity ease-in .2s;    
        }
        .copy-code-btn{
            padding: 6px 15px;
            background: #1176b1;
            color: #fff !important;
            text-decoration: none;
            border-radius: 3px;
            text-transform: uppercase;
            margin-left: 12px;
            transition: all ease-in .2s;
            font-size: 12px;
        }
        .copy-code-btn:hover{
            background: #399fdb;
            color: #fff;
        }
        .ui-draggable .ui-dialog-titlebar {
            padding: 0 36px 0 16px;
            margin: 0;
            background: transparent;
            font-size: 16px;
            border: 0;
            border-bottom: 1px solid #a9a6a6;
            border-radius: 0;
            font-weight: 500;
        }
        .ui-button.ui-dialog-titlebar-close{width: 20px;}
        .ui-dialog-titlebar-close:before{
            height: 50px;
            font-size: 18px;
        }
        span.ui-button-icon.ui-icon.ui-icon-closethick{display: none;}
    </style>
    <div id="button-shortcode-dialog" title="Button Shortcodes">
        <div style="margin-bottom: 10px;">
        <table class="table-borderless" style="text-align: left;">
            <tbody>
            <tr><th style="padding: 0 20px 0 0;">Parameters</th><th style="padding: 0 20px 0 0;">Description</th></tr>
            <tr><td style="padding: 0 20px 0 0;">link</td><td style="padding: 0 20px 0 0;">url (required)</td></tr>
            <tr><td style="padding: 0 20px 0 0;">text</td><td style="padding: 0 20px 0 0;">label (required)</td></tr>
            <tr><td style="padding: 0 20px 0 0;">target</td><td style="padding: 0 20px 0 0;">leave blank / newtab / lightbox</td></tr>
            <tr><td style="padding: 0 20px 0 0;">icon</td><td style="padding: 0 20px 0 0;">true / false</td></tr>
            <tr><td style="padding: 0 20px 0 0;">icon_class</td><td style="padding: 0 20px 0 0;">font awesome class (optional)</td></tr>
            <tr><td style="padding: 0 20px 0 0;">icon_position</td><td style="padding: 0 20px 0 0;">left / right</td></tr>
            </tbody>
        </table>
        </div>
        <div style="margin-bottom: 10px;">
            <div style="margin-bottom: 5px; font-weight: 700;">Same Window</div>
            <input type="text" readonly id="samewindow-shortcode" value="[button link=\'#\' text=\'Click Here\']" style="width: 330px;"> <a class="copy-code-btn" href="#" id="samewindow">Copy Code</a>
        </div>
        <div style="margin-bottom: 10px;">
            <div style="margin-bottom: 5px; font-weight: 700;">New Tab</div>
            <input type="text" readonly id="newtab-shortcode" value="[button link=\'#\' text=\'Click Here\' target=\'newtab\']" style="width: 330px;"> <a class="copy-code-btn" href="#" id="newtab">Copy Code</a>
        </div>
        <div style="margin-bottom: 10px;">
            <div style="margin-bottom: 5px; font-weight: 700;">Lightbox</div>
            <input type="text" readonly id="lightbox-shortcode" value="[button link=\'#\' text=\'Click Here\' target=\'lightbox\']" style="width: 330px;"> <a class="copy-code-btn" href="#" id="lightbox">Copy Code</a>
        </div>
        <div style="margin-bottom: 10px;">
            <div style="margin-bottom: 5px; font-weight: 700;">With Icon (default)</div>
            <input type="text" readonly id="with-icon-shortcode" value="[button link=\'#\' text=\'Click Here\' icon=true  icon_position=\'right\']" style="width: 330px;"> <a class="copy-code-btn" href="#" id="with-icon">Copy Code</a>
        </div>
        <div style="margin-bottom: 10px;">
            <div style="margin-bottom: 5px; font-weight: 700;">With Icon (custom)</div>
            <input type="text" readonly id="with-icon-custom-shortcode" value="[button link=\'#\' text=\'Click Here\' icon=true icon_class=\'fas fa-phone-alt\' icon_position=\'right\']" style="width: 330px;"> <a class="copy-code-btn" href="#" id="with-icon-custom">Copy Code</a>
        </div>
    </div>
    <div id="page-mask"></div>
    ';
}
add_action('admin_bar_menu', 'vbb_add_toolbar_items', 100);
function vbb_add_toolbar_items($admin_bar){
    $admin_bar->add_menu( array(
        'id'    => 'generate-button-shortcode',
        'title' => 'Generate Button',
        'href'  => '',
        'meta'  => array(
            'onclick' => 'jQuery("#button-shortcode-dialog").dialog("open"); jQuery("#page-mask").css({"opacity":1, "pointer-events": "auto"}); return false;'            
        ),
    ));
}
add_action( 'admin_head', 'vbb_hide_button_widget' );
function vbb_hide_button_widget() {
    echo '<style> 
    #wp-admin-bar-generate-button-shortcode{display: none;}
    body.post-php #wp-admin-bar-generate-button-shortcode,
    body.post-new-php #wp-admin-bar-generate-button-shortcode,
    body.toplevel_page_access-manager #wp-admin-bar-generate-button-shortcode{display: list-item;}
    </style>';
}