<?php

/**
 * @package contributorplugin
 */

/*
  Plugin Name:       Contributor Plugin
  Description:       Goal is to create a plugin so that we can display more than one author-name on a post.
  Version:           1.0.0
  Author:            Chakshu
  License:           GPL v2 or later
  License URI:       https://www.gnu.org/licenses/gpl-2.0.html
  
 */




 

function contributor_metabox() {
    add_meta_box(
        'contributors', // Metabox ID
        'Contributors', // Metabox title
        'contributors_callback', // Callback function
        'post', // Post type
        'normal', // Metabox position
        'high' // Metabox priority
    );
}

function contributors_callback($post) {
    wp_nonce_field( 'contributors', 'contributors_nonce' );
    $users = get_users();
    $contributors = get_post_meta( $post->ID, '_contributors', true );
    echo 'Select contributors:<br>';
    foreach ( $users as $user ) {
        $checked = '';
        if ( is_array( $contributors ) && in_array( $user->ID, $contributors ) ) {
            $checked = 'checked';
        }
        echo '<label><input type="checkbox" name="contributors[]" value="' . $user->ID . '" ' . $checked . '> ' . esc_html( $user->display_name ) . '</label><br>';
    }
}

function save_contributors( $post_id ) {
    if ( ! isset( $_POST['contributors_nonce'] ) || ! wp_verify_nonce( $_POST['contributors_nonce'], 'contributors' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['contributors'] ) ) {
        $contributors = array_map( 'intval', $_POST['contributors'] );
        update_post_meta( $post_id, '_contributors', $contributors );
    } else {
        delete_post_meta( $post_id, '_contributors' );
    }
}

function contributor_enqueue_scripts() {
    wp_enqueue_script( 'contributor-metabox', plugin_dir_url( __FILE__ ) . 'metabox.js', array( 'jquery' ), '1.0', true );
}

function display_all_authors_in_post_section( $user_query ) {
    // Display all users in the Author meta box
    if ( isset( $user_query->query_vars['who'] ) && $user_query->query_vars['who'] == 'authors' ) {
        $user_query->query_vars['who'] = '';
    }
}
add_action( 'pre_user_query', 'display_all_authors_in_post_section' );
add_action( 'add_meta_boxes', 'contributor_metabox' );
add_action( 'save_post', 'save_contributors' );
add_action( 'admin_enqueue_scripts', 'contributor_enqueue_scripts' );

include( plugin_dir_path( __FILE__ ) . 'display.php' );
