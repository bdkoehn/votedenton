<?php
/**
 * The functions file is used to initialize everything in the theme.  It controls how the theme is loaded and 
 * sets up the supported features, default actions, and default filters.  If making customizations, users 
 * should create a child theme and make changes to its functions.php file (not this one).  Friends don't let 
 * friends modify parent theme files. ;)
 *
 * Child themes should do their setup on the 'after_setup_theme' hook with a priority of 11 if they want to
 * override parent theme features.  Use a priority of 9 if wanting to run before the parent theme.
 *
 * @package Enterprise
 * @subpackage Functions
 * @version 0.1.0
 * @author Patrick Daly <patrick@developdaly.com>
 * @copyright Copyright (c) 2012, Develop Daly
 */

/* Load the core theme framework. */
require_once( trailingslashit( get_template_directory() ) . 'core/hybrid.php' );
new Hybrid();

/* Load the required plugins framework. */
require_once( trailingslashit( get_template_directory() ) . 'library/class-tgm-plugin-activation.php' );
require_once( trailingslashit( get_template_directory() ) . 'library/require-plugins.php' );

/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'enterprise_theme_setup' );

/**
 * Theme setup function.  This function adds support for theme features and defines the default theme
 * actions and filters.
 *
 * @since 0.1.0
 */
function enterprise_theme_setup() {

	/* Get action/filter hook prefix. */
	$prefix = hybrid_get_prefix();

	/* Enables styling of the visual editor with editor-style.css to match the theme style. */
	add_editor_style();
	
	/* Add theme support for core framework features. */
	add_theme_support( 'hybrid-core-menus', array( 'primary', 'secondary', 'subsidiary' ) );
	add_theme_support( 'hybrid-core-sidebars', array( 'primary', 'secondary', 'subsidiary', 'header', 'before-content', 'after-content', 'after-singular' ) );
	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-core-theme-settings', array( 'about', 'footer' ) );
	add_theme_support( 'hybrid-core-seo' );
	add_theme_support( 'hybrid-core-template-hierarchy' );

	/* Add theme support for framework extensions. */
	add_theme_support( 'theme-layouts', array( '1c', '2c-l', '2c-r', '3c-l', '3c-r', '3c-c' ) );
	add_theme_support( 'post-stylesheets' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'breadcrumb-trail' );
	add_theme_support( 'cleaner-gallery' );

	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );

	/* Enqueue scripts and styles. */
	add_action( 'wp_enqueue_scripts', 'enterprise_enqueue_scripts' );

	/* Embed width/height defaults. */
	add_filter( 'embed_defaults', 'enterprise_embed_defaults' );
	
	/* Reconfigure some WordPress settings to work with Bootstrap. */
	add_action( 'init', 'enterprise_bootstrap_setup' );

	/* Filter the sidebar widgets. */
	add_filter( 'sidebars_widgets', 'enterprise_disable_sidebars' );
	add_action( 'template_redirect', 'enterprise_one_column' );

	/* Set the content width. */
	hybrid_set_content_width( 870 );
}

/**
 * Queue static resources
 *
 * @since 0.1.0
 */
function enterprise_enqueue_scripts() {

	// Queue CSS
	wp_enqueue_style( 'style',		trailingslashit( get_stylesheet_directory_uri() ) . 'style.less' );

	// Queue JS
	wp_enqueue_script( 'modernizr',	trailingslashit( get_stylesheet_directory_uri() ) . 'js/vendor/modernizr-2.6.2.min.js' );
	wp_enqueue_script( 'respond',	trailingslashit( get_stylesheet_directory_uri() ) . 'js/vendor/respond.min.js' );
	wp_enqueue_script( 'bootstrap',	trailingslashit( get_stylesheet_directory_uri() ) . 'js/vendor/bootstrap.min.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'app',		trailingslashit( get_stylesheet_directory_uri() ) . 'js/app.js', array( 'jquery' ), false, true );

}


/**
 * Function for deciding which pages should have a one-column layout.
 * 
 * If the Primary and Secondary sidebars aren't being used, make the
 * page one column.
 *
 * @since 0.1.0
 */
function enterprise_one_column() {

	if ( !is_active_sidebar( 'primary' ) && !is_active_sidebar( 'secondary' ) )
		add_filter( 'get_theme_layout', 'enterprise_theme_layout_one_column' );

	elseif ( is_attachment() && 'layout-default' == theme_layouts_get_layout() )
		add_filter( 'get_theme_layout', 'enterprise_theme_layout_one_column' );
}

/**
 * Filters 'get_theme_layout' by returning 'layout-1c'.
 *
 * @since 0.2.0
 */
function enterprise_theme_layout_one_column( $layout ) {
	return 'layout-1c';
}

function enterprise_get_layout( $id = '' ) {
	
	$layout = theme_layouts_get_layout();

	if ( !empty($id) ) {
		if( $layout == 'layout-default' ) {
			if( $id == 'content' )
				$output = 'span9';
			elseif( $id == 'sidebar' )
				$output = 'span3';
			else
				return false;
		} elseif( $layout == 'layout-1c' ) {
			if( $id == 'content' )
				$output = 'span12';
			else
				return false;
		} elseif( $layout == 'layout-2c-l' || $layout == 'layout-2c-r' ) {
			if( $id == 'content' )
				$output = 'span9';
			elseif( $id == 'sidebar' )
				$output = 'span3';
			else
				return false;
		} elseif( 'layout-3c-l' == $layout || 'layout-3c-r' == $layout || 'layout-3c-c' == $layout ) {
			if( $id == 'content' )
				$output = 'span6';
			elseif( $id == 'sidebar' )
				$output = 'span3';
			else
				return false;
		} else {
			return false;
		}
	}
	
	return $output;
}

/**
 * Disables sidebars if viewing a one-column page.
 *
 * @since 0.1.0
 */
function enterprise_disable_sidebars( $sidebars_widgets ) {
	global $wp_query;

	if ( current_theme_supports( 'theme-layouts' ) ) {

		if ( 'layout-1c' == theme_layouts_get_layout() ) {
			$sidebars_widgets['primary'] = false;
			$sidebars_widgets['secondary'] = false;
		}
	}

	return $sidebars_widgets;
}

/**
 * Overwrites the default widths for embeds.  This is especially useful for making sure videos properly
 * expand the full width on video pages.  This function overwrites what the $content_width variable handles
 * with context-based widths.
 *
 * @since 0.1.0
 */
function enterprise_embed_defaults( $args ) {

	if ( current_theme_supports( 'theme-layouts' ) ) {

		$layout = theme_layouts_get_layout();

		if ( 'layout-3c-l' == $layout || 'layout-3c-r' == $layout || 'layout-3c-c' == $layout )
			$args['width'] = 570;
		elseif ( 'layout-1c' == $layout )
			$args['width'] = 1170;
		else
			$args['width'] = 870;
	}
	else
		$args['width'] = 870;

	return $args;
}

function enterprise_bootstrap_setup(){
	
	/**
	 * Class Name: twitter_bootstrap_nav_walker
	 * GitHub URI: https://github.com/twittem/wp-bootstrap-navwalker
	 * Description: A custom Wordpress nav walker to implement the Twitter Bootstrap 2 (https://github.com/twitter/bootstrap/) dropdown navigation using the Wordpress built in menu manager.
	 * Version: 1.2
	 * Author: Edward McIntyre - @twittem
	 * Licence: WTFPL 2.0 (http://sam.zoy.org/wtfpl/COPYING)
	 */
	class twitter_bootstrap_nav_walker extends Walker_Nav_Menu {
	
		/**
		 * @see Walker::start_lvl()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int $depth Depth of page. Used for padding.
		 */
		function start_lvl( &$output, $depth ) {
			$indent = str_repeat( "\t", $depth );
			$output	   .= "\n$indent<ul class=\"dropdown-menu\">\n";		
		}
	
		/**
		 * @see Walker::start_el()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param int $current_page Menu item ID.
		 * @param object $args
		 */
	
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
			if (strcasecmp($item->title, 'divider')) {
				$class_names = $value = '';
				$classes = empty( $item->classes ) ? array() : (array) $item->classes;
				$classes[] = ($item->current) ? 'active' : '';
				$classes[] = 'menu-item-' . $item->ID;
				$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
	
				if ($args->has_children && $depth > 0) {
					$class_names .= ' dropdown-submenu';
				} else if($args->has_children && $depth === 0) {
					$class_names .= ' dropdown';
				}
	
				$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
	
				$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
				$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
	
				$output .= $indent . '<li' . $id . $value . $class_names .'>';
	
				$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
				$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
				$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
				$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
				$attributes .= ($args->has_children) 	    ? ' data-toggle="dropdown" data-target="#" class="dropdown-toggle"' : '';
	
				$item_output = $args->before;
				$item_output .= '<a'. $attributes .'>';
				$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
				$item_output .= ($args->has_children) ? ' <span class="caret"></span></a>' : '</a>';
				$item_output .= $args->after;
	
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			} else {
				$output .= $indent . '<li class="divider"></li>';
			}
		}
	
	
		/**
		 * Traverse elements to create list from elements.
		 *
		 * Display one element if the element doesn't have any children otherwise,
		 * display the element and its children. Will only traverse up to the max
		 * depth and no ignore elements under that depth. 
		 *
		 * This method shouldn't be called directly, use the walk() method instead.
		 *
		 * @see Walker::start_el()
		 * @since 2.5.0
		 *
		 * @param object $element Data object
		 * @param array $children_elements List of elements to continue traversing.
		 * @param int $max_depth Max depth to traverse.
		 * @param int $depth Depth of current element.
		 * @param array $args
		 * @param string $output Passed by reference. Used to append additional content.
		 * @return null Null on failure with no changes to parameters.
		 */
	
		function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
	
			if ( !$element ) {
				return;
			}
	
			$id_field = $this->db_fields['id'];
	
			//display this element
			if ( is_array( $args[0] ) ) 
				$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
			else if ( is_object( $args[0] ) ) 
				$args[0]->has_children = ! empty( $children_elements[$element->$id_field] ); 
			$cb_args = array_merge( array(&$output, $element, $depth), $args);
			call_user_func_array(array(&$this, 'start_el'), $cb_args);
	
			$id = $element->$id_field;
	
			// descend only when the depth is right and there are childrens for this element
			if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {
	
				foreach( $children_elements[ $id ] as $child ){
	
					if ( !isset($newlevel) ) {
						$newlevel = true;
						//start the child delimiter
						$cb_args = array_merge( array(&$output, $depth), $args);
						call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
					}
					$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
				}
					unset( $children_elements[ $id ] );
			}
	
			if ( isset($newlevel) && $newlevel ){
				//end the child delimiter
				$cb_args = array_merge( array(&$output, $depth), $args);
				call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
			}
	
			//end this element
			$cb_args = array_merge( array(&$output, $element, $depth), $args);
			call_user_func_array(array(&$this, 'end_el'), $cb_args);
		}
	}

}