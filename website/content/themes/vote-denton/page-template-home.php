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

									<button type="submit" id="map-button" class="btn btn-primary">Submit</button>
									
									<div id="your_district"></div>

								</div><!-- .container -->

							</fieldset>

						</form><!-- #map-yourself -->
						
						<div class="accordion" id="accordion">

							<section id="district-map" class="accordion-group">
	
								<header class="accordion-heading">
									
									<div class="container">
									
										<h1><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">District Map<a></a></h1>
	      							
	      							</div><!-- .container -->
	      								
								</header>
	
								<div id="collapseOne" class="accordion-body collapse in container">


																		
									<div class="row-fluid">
										<div class="span12">
											<div class="flex-map">
												<div id="map-canvas"></div>
											</div>
										</div>
									</div>
		
								</div><!-- .container -->
	
							</section><!-- #what-district -->

							<section id="candidates" class="accordion-group">
	
								<header class="accordion-heading">
									
									<div class="container">
									
										<h1><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Candidates<a></a></h1>
	      								
	      							</div><!-- .container -->
	      		
								</header>

								<div id="collapseTwo" class="accordion-body collapse container">
									
									<div class="row-fluid">
										<div class="span12">
											<div class="flex-map">
												<div id="map-canvas"></div>
											</div>
										</div>
									</div>
		
								</div><!-- .container -->
											
							</section><!-- #what-district -->
										
						</div><!-- #accordian -->

							<section id="candidates">
	
								<header>
									
									<div class="container">
									
										<h1>Double Check</h1>
	      								
	      							</div><!-- .container -->
	      		
								</header>

								<div class="container">
									
									<div class="row-fluid">
										
										<div class="span12">

											<p><a href="https://elections.dentoncounty.com/goVR.asp?Dept=82&Link=292">Click here to double check your voter registration status</a>ß and make sure you are registered to vote in this district. Deadline to register is April 11."</p>
										
										</div>
									
									</div>
		
								</div><!-- .container -->
											
							</section><!-- #what-district -->
							
						
						<!-- WE'LL RESURRECT THIS SOON 

						<section id="am-i-registered">

							<header class="question">

								<form id="find-yourself" class="form-inline">

									<fieldset>

										<div class="container">

											<h1>Am I registered to vote?</h1>

											<input type="text" class="input-medium" id="fname" placeholder="First name...">
											<input type="text" class="input-medium" id="lname" placeholder="Last name...">

											<button type="submit" id="name-button" class="btn">Submit</button>

										</div>

									</fieldset>

								</form>

							</header>

						</section><!-- #am-i-registered -->

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