<?php
/*
Plugin Name: Movie Background
Plugin URI: http://www.rodrigorusso.com.br
description: Movie Background Admin Module
Version: 1.0
Author: RussoFaccin
Author URI: http://www.rodrigorusso.com.br
License: GPL2
*/

add_action('admin_menu', 'register_movie_slider_menu_page');

function register_movie_slider_menu_page() {
    add_menu_page(
        'Movie Background - Plugin',  // page_title
        'Movie Background',  // menu_title
        'manage_options',// capability
        'movie_background',  // menu_slug
        'render_movie_slider_menu_page', // function
        'dashicons-format-video',
        6
    );
}
function render_movie_slider_menu_page() {
    ?>
    <h1>Movie Slider</h1>
  <form action='options.php' method='post' enctype="multipart/form-data">
    <?php
    settings_fields('movie_slider');
    do_settings_sections('movie_slider');
    echo '<button class="a-addNewCaption">+</button>';
    submit_button(); ?>
  </form>
  <?php
}

add_action('admin_init', 'movieSlider_settings_init');

function movieSlider_settings_init() {
    register_setting(
        'movie_slider', // option group - used to render the options page
        'movieSlider_settings', // option name - used with functions like get_option()
        'handle_file_upload' // function callback
    );

    // Settings Section
    add_settings_section(
        'movie_slider_section', //id - used to add fields to this section
        'Options', // Title
        '', // Function callback
        'movie_slider' // id - menu slug
    );

    // Movie URL
    add_settings_field(
        'movie_slider_url',
        'Video File',
        'movie_slider_url_render',
        'movie_slider',
        'movie_slider_section'
    );

    // Caption field
    add_settings_field(
        'movie_slider_caption_fld', // id
        'Caption', // Title
        'movie_slider_caption_fld_render', // Function callback
        'movie_slider', // menu slug
        'movie_slider_section' // Section id
    );
}

// Movie URL Field - Render
function movie_slider_url_render() {
    $setting = get_option('movieSlider_settings');
    $movieThumb = $setting["fld_videourl"] == null ? plugins_url('img/add-new-image.png', __FILE__) : null;
    $movieUrl = $setting["fld_videourl"] !== null ? $setting["fld_videourl"] : null;
    $html .= '<video class="a-videoPlaceholder" width="200" height="200" poster="'.$movieThumb.'" autoplay loop muted><source class="a-videoPlaceholder__source" src="'.$movieUrl.'"></video>';
    $html .= '<button class="a-videoUpload">Novo Vídeo</button>';
    $html .= '<input class="a-fldVideoUrl" type="hidden" name="movieSlider_settings[fld_videourl]" value="'.$setting["fld_videourl"].'"';
    echo $html;
}

// Caption Field - Render
function movie_slider_caption_fld_render() {
    $setting = get_option( 'movieSlider_settings' );
    $qtdCaptions = count($setting['captions']) > 0 ? count($setting['captions']) : 1;
    $html = '';
    for ($i = 0; $i < $qtdCaptions; $i++) {
        $elements = '<input type="text" name="movieSlider_settings[captions]['.$i.'][caption]" value="'.$setting["captions"][$i]['caption'].'">';
        $elements .= '<input type="text" name="movieSlider_settings[captions]['.$i.'][startTime]" value="'.$setting["captions"][$i]["startTime"].'">';
        $elements .= '<input type="text" name="movieSlider_settings[captions]['.$i.'][endTime]" value="'.$setting["captions"][$i]["endTime"].'">';
        $html .= '<div class="m-sectionCaptions__item">'.$elements.'<button class="m-sectionCaptions__itemDelete" data-index="'.$i.'">-</button></div>';
    }
    echo '<div class="m-sectionCaptions">'.$html.'</div>';
}

/*
| ===========================================================================
| Register Rest Route
| <site-address>/wp-json/api/movie_background
| ===========================================================================
*/

add_action( 'rest_api_init', 'register_movieSlider_api');

function register_movieSlider_api() {
    register_rest_route(
        'api/',
        'movie_background',
        array(
            'method' => 'GET',
            'callback' => 'get_movieSlider_data'
        )
    );
}

function get_movieSlider_data($data) {
    $setting = get_option( 'movieSlider_settings' );
    return $setting;
}

/*
| ===========================================================================
| Enqueue Styles & Scripts
| ===========================================================================
*/

add_action('admin_enqueue_scripts', 'enqueue_media', 0 );

function enqueue_media() {
  wp_enqueue_media();
  // CSS
  wp_enqueue_style( 'movie_bg_css', plugins_url('css/movie-background.css', __FILE__) );
  // JS
  $IN_FOOTER = true;
  wp_enqueue_script( 'movie_bg_js', plugins_url('js/movie-background.js', __FILE__), null, null, $IN_FOOTER);
}