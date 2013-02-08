<?php
/**
 * Header Template
 *
 * The header template is generally used on every page of your site. Nearly all other templates call it 
 * somewhere near the top of the file. It is used mostly as an opening wrapper, which is closed with the 
 * footer.php file. It also executes key functions needed by the theme, child themes, and plugins. 
 *
 * @package Enterprise
 * @subpackage Template
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html <?php language_attributes(); ?> class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
	
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<title><?php hybrid_document_title(); ?></title>
	
	<?php wp_head(); // wp_head ?>
	<?php if( $post ) echo get_post_meta( $post->ID, 'enterprise-css', true ); ?>

</head>

<body class="<?php hybrid_body_class(); ?>">

	<?php do_atomic( 'open_body' ); // enterprise_open_body ?>

	<div class="container">

		<?php do_atomic( 'before_header' ); // enterprise_before_header ?>

		<div id="header" class="row">

			<?php do_atomic( 'open_header' ); // enterprise_open_header ?>

			<div id="branding" class="span6">
				<?php hybrid_site_title(); ?>
				<?php hybrid_site_description(); ?>
			</div><!-- #branding -->
			
			<?php get_template_part( 'menu', 'primary' ); // Loads the menu-primary.php template. ?>

			<?php do_atomic( 'header' ); // enterprise_header ?>

			<?php do_atomic( 'close_header' ); // enterprise_close_header ?>

		</div><!-- #header.row -->

		<?php do_atomic( 'after_header' ); // enterprise_after_header ?>

		<?php do_atomic( 'before_main' ); // enterprise_before_main ?>

		<div id="main"  class="row">

			<?php do_atomic( 'open_main' ); // enterprise_open_main ?>