<?php
/**
 * Template Name: Home
 *
 * This is the default page template.  It is used when a more specific template can't be found to display 
 * singular views of pages.
 *
 * @package Vote Denton
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // vote_denton_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // vote_denton_open_content ?>

		<div class="hfeed">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // vote_denton_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // vote_denton_open_entry ?>
						
						<section id="what-district" class="question">
						
							<form id="map-yourself" class="form-inline">
								
								<fieldset>
									
									<div class="container">
										
										<h1>What district am I in?</h1>
																				
										<input type="text" id="address" placeholder="What is your address?">
										
										<button type="submit" id="map-button" class="btn">Submit</button>
									
									</div><!-- .container -->
									
								</fieldset>
								
							</form><!-- #map-yourself -->
						
						</section>
						
						<div class="container">
						
							<div class="flex-map">
								<div id="map-canvas"></div>
							</div>
						
						</div><!-- .container -->

						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'enterprise' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'enterprise' ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-content -->
						
						<?php do_atomic( 'close_entry' ); // vote_denton_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // vote_denton_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // vote_denton_after_singular ?>

					<?php comments_template( '/comments.php', true ); // Loads the comments.php template. ?>

				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // vote_denton_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // vote_denton_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>