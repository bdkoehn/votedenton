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

						<form id="map-yourself" class="form-inline">

							<fieldset>

								<div class="container">

									<h2>What district am I in?</h2>

									<input type="text" id="address" placeholder="What is your address?">

									<input type="submit" id="map-button" class="btn btn-primary" vale="Submit">

									<div id="your_district"></div>

								</div><!-- .container -->

							</fieldset>

						</form><!-- #map-yourself -->

						<div class="accordion" id="accordion">

							<?php
							$district_map_args = array(
							  'name' => 'district-map',
							  'post_type' => 'page',
							  'post_status' => 'publish',
							  'numberposts' => 1
							);

							$district_map = get_posts( $district_map_args );
							if( $district_map ) {
							?>

							<section id="district-map" class="accordion-group">

								<header class="accordion-heading">

									<div class="container">

										<h1><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse-district-map"><?php echo $district_map[0]->post_title; ?><a></a></h1>

	      							</div><!-- .container -->

								</header>

								<div id="collapse-district-map" class="accordion-body collapse container">

									<?php echo $district_map[0]->post_content; ?>

								</div><!-- .container -->

							</section><!-- #district-map -->

							<?php } ?>

							<section id="candidates" class="accordion-group">

								<header class="accordion-heading">

									<div class="container">

										<h1><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Candidates<a></a></h1>

	      							</div><!-- .container -->

								</header>

								<div id="collapseTwo" class="accordion-body collapse container">

									<?php $candidates = new WP_Query( array( 'post_per_page' => -1, 'post_type' => 'candidate', 'orderby' => 'title', 'order' => 'ASC' ) );
									if( $candidates ): ?>
									<ul class="thumbnails">
										<?php while ($candidates->have_posts()) : $candidates->the_post();
										$do_not_duplicate = $post->ID;?>
										<li class="candidate">
											<div class="thumbnail">
												<div class="caption">

													<?php
													$terms = get_the_terms( $post->ID, 'districts' );

													if ( $terms && ! is_wp_error( $terms ) ) :

														$district_links = array();

														foreach ( $terms as $term ) {
															$district_links[] = $term->name;
														}

														$districts = join( ", ", $district_links );
													?>
													<span class="label label-info pull-right"><?php echo $districts; ?></span>

													<?php endif; ?>

													<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

													<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'thumbnail', 'image_class' => 'pull-left' ) ); ?>

													<div class="entry-summary">
														<?php the_excerpt(); ?>
														<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'enterprise' ), 'after' => '</p>' ) ); ?>
													</div><!-- .entry-summary -->

													<p><a href="<?php the_permalink(); ?>" class="btn btn-primary pull-right">Learn more</a></p>
												</div>
											</div>
										</li>
										<?php endwhile; ?>
									</ul>
									<?php endif; wp_reset_query(); ?>

								</div><!-- .container -->

							</section><!-- #candidates -->

							<?php
							$calendar_args = array(
							  'name' => 'calendar',
							  'post_type' => 'page',
							  'post_status' => 'publish',
							  'numberposts' => 1
							);

							$calendar = get_posts( $calendar_args );
							if( $calendar ) {
							?>

							<section id="calendar" class="accordion-group">

								<header class="accordion-heading">

									<div class="container">

										<h1><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $calendar[0]->ID; ?>"><?php echo $calendar[0]->post_title; ?><a></a></h1>

	      							</div><!-- .container -->

								</header>

								<div id="collapse-<?php echo $calendar[0]->ID; ?>" class="accordion-body collapse container">

									<?php echo $calendar[0]->post_content; ?>

								</div><!-- .container -->

							</section><!-- #calendar -->

							<?php } ?>

							<?php
							$why_vote_args = array(
							  'name' => 'why-vote',
							  'post_type' => 'page',
							  'post_status' => 'publish',
							  'numberposts' => 1
							);

							$why_vote = get_posts( $why_vote_args );
							if( $why_vote ) {
							?>

							<section id="why-vote" class="accordion-group">

								<header class="accordion-heading">

									<div class="container">

										<h1><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $why_vote[0]->ID; ?>"><?php echo $why_vote[0]->post_title; ?><a></a></h1>

	      							</div><!-- .container -->

								</header>

								<div id="collapse-<?php echo $why_vote[0]->ID; ?>" class="accordion-body collapse container">

									<?php echo $why_vote[0]->post_content; ?>

								</div><!-- .container -->

							</section><!-- #why-vote -->

							<?php } ?>

						</div><!-- #accordian -->

						<div class="container">

							<div class="entry-content">
								<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'enterprise' ) ); ?>
								<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'enterprise' ), 'after' => '</p>' ) ); ?>
							</div><!-- .entry-content -->

							<?php do_atomic( 'close_entry' ); // vote_denton_close_entry ?>

						</div><!-- .container -->

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
