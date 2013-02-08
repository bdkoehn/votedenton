<?php
/**
 * Secondary Menu Template
 *
 * Displays the Secondary Menu if it has active menu items.
 *
 * @package Enterprise
 * @subpackage Template
 */

if ( has_nav_menu( 'secondary' ) ) : ?>

	<?php do_atomic( 'before_menu_secondary' ); // enterprise_before_menu_secondary ?>

	<div id="menu-secondary" class="menu-container row">

		<div class="span12">

			<?php do_atomic( 'open_menu_secondary' ); // enterprise_open_menu_secondary ?>
		
			<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'depth' => 2, 'container' => 'nav', 'walker' => new twitter_bootstrap_nav_walker(), 'container_class' => 'nav-collapse', 'menu_class' => 'nav nav-pills', 'menu_id' => 'menu-secondary-items', 'fallback_cb' => '' ) ); ?>

			<?php do_atomic( 'close_menu_secondary' ); // enterprise_close_menu_secondary ?>

		</div><!-- .span12 -->

	</div><!-- #menu-secondary.menu-container.row -->

	<?php do_atomic( 'after_menu_secondary' ); // enterprise_after_menu_secondary ?>

<?php endif; ?>