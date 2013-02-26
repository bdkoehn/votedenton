<?php
/*
 * Plugin Name: Vote Denton
 */

add_action( 'init', 'register_cpt_candidate' );

function register_cpt_candidate() {

    $labels = array( 
        'name' => _x( 'Candidates', 'candidate' ),
        'singular_name' => _x( 'Candidate', 'candidate' ),
        'add_new' => _x( 'Add New', 'candidate' ),
        'add_new_item' => _x( 'Add New Candidate', 'candidate' ),
        'edit_item' => _x( 'Edit Candidate', 'candidate' ),
        'new_item' => _x( 'New Candidate', 'candidate' ),
        'view_item' => _x( 'View Candidate', 'candidate' ),
        'search_items' => _x( 'Search Candidates', 'candidate' ),
        'not_found' => _x( 'No candidates found', 'candidate' ),
        'not_found_in_trash' => _x( 'No candidates found in Trash', 'candidate' ),
        'parent_item_colon' => _x( 'Parent Candidate:', 'candidate' ),
        'menu_name' => _x( 'Candidates', 'candidate' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'candidate', $args );
}

add_action( 'init', 'register_taxonomy_districts' );

function register_taxonomy_districts() {

    $labels = array( 
        'name' => _x( 'Districts', 'districts' ),
        'singular_name' => _x( 'District', 'districts' ),
        'search_items' => _x( 'Search Districts', 'districts' ),
        'popular_items' => _x( 'Popular Districts', 'districts' ),
        'all_items' => _x( 'All Districts', 'districts' ),
        'parent_item' => _x( 'Parent District', 'districts' ),
        'parent_item_colon' => _x( 'Parent District:', 'districts' ),
        'edit_item' => _x( 'Edit District', 'districts' ),
        'update_item' => _x( 'Update District', 'districts' ),
        'add_new_item' => _x( 'Add New District', 'districts' ),
        'new_item_name' => _x( 'New District', 'districts' ),
        'separate_items_with_commas' => _x( 'Separate districts with commas', 'districts' ),
        'add_or_remove_items' => _x( 'Add or remove districts', 'districts' ),
        'choose_from_most_used' => _x( 'Choose from the most used districts', 'districts' ),
        'menu_name' => _x( 'Districts', 'districts' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'show_admin_column' => false,
        'hierarchical' => true,

        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'districts', array('candidate'), $args );
}