<?php
function display_contributors_box( $content ) {
    // Get the list of contributors for this post
    $contributors = get_post_meta( get_the_ID(), '_contributors', true );
    // If no contributors, return the original content
    if ( empty( $contributors ) ) {
        return $content;
    }
    // Start building the contributors box HTML
    $html = '<div class="contributors-box">'; 
    $html .= '<h3>Contributors</h3>';
    $html .= '<ul>';
    // Loop through the contributors and display their names and Gravatars
    foreach ( $contributors as $contributor_id ) {
        $contributor = get_user_by( 'id', $contributor_id );
        if ( $contributor ) {
            $html .= '<li>';
            $html .= '<a href="' . get_author_posts_url( $contributor_id ) . '">' . get_avatar( $contributor_id, 35 ) .  '</a>';
            $html .= '</br>';
            $html .= '<div class="contributor-name">';
            $html .= '<a href="' . get_author_posts_url( $contributor_id ) . '"> <button class="button button-1">' .esc_html( $contributor->display_name ) . '</button></a>';
            $html .= '</div>';            
            $html .= '</li>';
        }
    }
    $html .= '</ul>';
    $html .= '</div>';
    // Add the contributors box to the end of the post content
    $content .= $html;
    return $content;
}

function my_plugin_enqueue_styles() {
    wp_enqueue_style( 'my-plugin-style', plugins_url( '/css/button.css', __FILE__ ) );
    wp_enqueue_style( 'my-plugin-box-style', plugins_url( '/css/box.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'my_plugin_enqueue_styles' );

add_filter( 'the_content', 'display_contributors_box' );
?>
