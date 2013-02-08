<?php
/**
 * Subsidiary Menu Template
 *
 * Displays the Subsidiary Menu if it has active menu items.
 *
 * @package Enterprise
 * @subpackage Template
 */

if ( has_nav_menu( 'subsidiary' ) ) : ?>

	<?php do_atomic( 'before_menu_subsidiary' ); // enterprise_before_menu_subsidiary ?>

	<div id="menu-subsidiary" class="menu-container row">

		<div class="span12">

			<?php do_atomic( 'open_menu_subsidiary' ); // enterprise_open_menu_subsidiary ?>
		
				<?php wp_nav_menu( array( 'theme_location' => 'subsidiary', 'depth' => 3, 'container' => 'nav', 'menu_class' => 'nav', 'menu_id' => 'menu-subsidiary-items', 'fallback_cb' => '' ) ); ?>
			
			<?php do_atomic( 'close_menu_subsidiary' ); // enterprise_close_menu_subsidiary ?>

		</div><!-- .span12 -->

	</div><!-- #menu-subsidiary.menu-container.row -->

	<?php do_atomic( 'after_menu_subsidiary' ); // enterprise_after_menu_subsidiary ?>

<?php endif; ?>