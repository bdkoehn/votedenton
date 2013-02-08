<?php
/**
 * Primary Menu Template
 *
 * Displays the Primary Menu if it has active menu items.
 *
 * @package Enterprise
 * @subpackage Template
 */

if ( has_nav_menu( 'primary' ) ) : ?>

	<?php do_atomic( 'before_menu_primary' ); // enterprise_before_menu_primary ?>

	<div id="menu-primary" class="menu-container row">

		<div class="span12">

			<?php do_atomic( 'open_menu_primary' ); // enterprise_open_menu_primary ?>
		
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'depth' => 2, 'container' => 'nav', 'walker' => new twitter_bootstrap_nav_walker(), 'container_class' => 'nav-collapse', 'menu_class' => 'nav', 'menu_id' => 'menu-primary-items', 'fallback_cb' => '' ) ); ?>
			
			<?php do_atomic( 'close_menu_primary' ); // enterprise_close_menu_primary ?>

		</div><!-- .span12 -->

	</div><!-- #menu-primary.menu-container.row -->

	<?php do_atomic( 'after_menu_primary' ); // enterprise_after_menu_primary ?>

<?php endif; ?>