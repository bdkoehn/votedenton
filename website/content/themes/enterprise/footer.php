<?php
/**
 * Footer Template
 *
 * The footer template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the bottom of the file. It is used mostly as a closing
 * wrapper, which is opened with the header.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 * @package Enterprise
 * @subpackage Template
 */
?>
			<?php get_sidebar( 'primary' ); // Loads the sidebar-primary.php template. ?>

			<?php get_sidebar( 'secondary' ); // Loads the sidebar-secondary.php template. ?>

			<?php do_atomic( 'close_main' ); // enterprise_close_main ?>

		</div><!-- #main.row -->

		<?php do_atomic( 'after_main' ); // enterprise_after_main ?>

		<?php get_sidebar( 'subsidiary' ); // Loads the sidebar-subsidiary.php template. ?>

		<?php get_template_part( 'menu', 'subsidiary' ); // Loads the menu-subsidiary.php template. ?>

		<?php do_atomic( 'before_footer' ); // enterprise_before_footer ?>

		<div id="footer" class="row">

			<?php do_atomic( 'open_footer' ); // enterprise_open_footer ?>

			<div class="span12">

				<?php echo apply_atomic_shortcode( 'footer_content', hybrid_get_setting( 'footer_insert' ) ); ?>

				<?php do_atomic( 'footer' ); // enterprise_footer ?>

			</div><!-- .span12 -->

			<?php do_atomic( 'close_footer' ); // enterprise_close_footer ?>

		</div><!-- #footer.row -->

		<?php do_atomic( 'after_footer' ); // enterprise_after_footer ?>

	</div><!-- #container -->

	<?php do_atomic( 'close_body' ); // enterprise_close_body ?>

	<?php wp_footer(); // wp_footer ?>
	<?php if( $post ) echo get_post_meta( $post->ID, 'enterprise-javascript', true ); ?>

</body>
</html>