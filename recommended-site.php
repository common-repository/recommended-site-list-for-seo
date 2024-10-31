<?php
/**
* Plugin Name: Recommended Site for SEO
* Plugin URI: http://mistermon.com/
* Description: Add a site description to the list
* Version: 1.0.1
* Author: mistermon
* Author URI: http://mistermon.com/
**/

// Register the Custom Music Review Post Type
 
function register_cp_recommended_site() {
 
    $labels = array(
        'name' => _x( 'Recommended Sites', 'recommended_site' ),
        'singular_name' => _x( 'Recommended Site', 'recommended_site' ),
        'add_new' => _x( 'Add New', 'music_review' ),
        'add_new_item' => _x( 'Add New Recommended Site', 'recommended_site' ),
        'edit_item' => _x( 'Edit Recommended Site', 'recommended_site' ),
        'new_item' => _x( 'New Recommended Site', 'recommended_site' ),
        'view_item' => _x( 'View Recommended Site', 'recommended_site' ),
        'search_items' => _x( 'Search Recommended Site', 'recommended_site' ),
        'not_found' => _x( 'No Recommended Site found', 'recommended_site' ),
        'not_found_in_trash' => _x( 'No Recommended Site found in Trash', 'recommended_site' ),
        'parent_item_colon' => _x( 'Parent Recommended Site:', 'recommended_site' ),
        'menu_name' => _x( 'Recommended Sites', 'recommended_site' ),
    );
 
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Music reviews filterable by genre',
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
        'taxonomies' => array( 'site_types' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-list-view',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );
 
    register_post_type( 'recommended_site', $args );
}
 
add_action( 'init', 'register_cp_recommended_site' );

function site_types_taxonomy() {
    register_taxonomy(
        'site_types',
        'recommended_site',
        array(
            'hierarchical' => true,
            'label' => 'Types',
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'recommended',
                'with_front' => false
            )
        )
    );
}
add_action( 'init', 'site_types_taxonomy');
// Function used to automatically create Music Reviews page.
function create_recommended_site_pages()
  {
   //post status and options
    $post = array(
          'comment_status' => 'open',
          'ping_status' =>  'closed' ,
          'post_date' => date('Y-m-d H:i:s'),
          'post_content' => '[list-posts-basic]',
          'post_name' => 'recommended',
          'post_status' => 'publish' ,
          'post_title' => 'Recommended Site',
          'post_type' => 'page',
    );
    //insert page and save the id
    $newvalue = wp_insert_post( $post, false );
    //save the id in the database
    update_option( 'mrpage', $newvalue );
  }
// // Activates function if plugin is activated
register_activation_hook( __FILE__, 'create_recommended_site_pages');

function enqeue_stylesheet() 
{
    wp_enqueue_style( 'style', plugins_url( '/css/style.css', __FILE__ ) );
}

add_action('init', 'enqeue_stylesheet');

// create shortcode to list all clothes which come in blue
add_shortcode( 'list-posts-basic', 'recommended_site_shortcode' );
function recommended_site_shortcode( $atts ) {
    ob_start();
    $query = new WP_Query( array(
        'post_type' => 'recommended_site',
        'posts_per_page' => -1,
        'order' => 'DESC',
        'orderby' => 'title',
    ) );
    if ( $query->have_posts() ) { ?>
    <?php while ( $query->have_posts() ) : $query->the_post(); 
    $postid = $query->post->ID;
    $type_list = get_the_term_list( $post->ID, 'site_types', '', ', ', '' );
    ?>
    <article id="post-<?php the_ID(); ?>" class="list-site">
    	<h3 class="box-header"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    	<?php echo $type_list ?>
    	<?php $keywords = get_post_meta($postid, 'site_keyword', false);
    	foreach ($keywords as $keyword) { ?>
    	, <a href="<?php echo get_post_meta($postid, 'site_url', true); ?>" alt="<?php echo $keyword ?>"><?php echo $keyword ?></a>
    	<?php } unset($keyword);?>
    	<div class="box-content">
    		<div><?php the_content(); ?></div>
    	</div>
    </article>
    <?php endwhile;
    wp_reset_postdata(); ?>
    <?php $myvariable = ob_get_clean();
    return $myvariable;
    }
}

