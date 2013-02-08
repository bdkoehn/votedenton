<?php
// ===================================================
// Load database info and local development parameters
// ===================================================
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/local-config.php' );
} else {
	define( 'WP_LOCAL_DEV', false );
	define( 'DB_NAME',				'local_db_name' );
	define( 'DB_USER',				'local_db_user' );
	define( 'DB_PASSWORD',			'local_db_password' );
	define( 'DB_HOST',				'localhost' ); // Probably 'localhost'
	
	define( 'ENV_DOMAIN',			'example.com' );
	define( 'PRODUCTION_DOMAIN',	'example.com' );
	define( 'DOMAIN_CURRENT_SITE',	ENV_DOMAIN );
	define( 'WP_HOME',				'http://'. ENV_DOMAIN );
	define( 'WP_SITEURL',			'http://'. ENV_DOMAIN .'/wp' );
	
}

// ========================
// Custom Content Directory
// ========================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

// =========
/* Multisite
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
define( 'SUNRISE', 'on' );
*/

// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ==============================================================
// Salts, for security
// Grab these from: https://api.wordpress.org/secret-key/1.1/salt
// ==============================================================
define( 'AUTH_KEY',         '[YR;~f>~$* ?~#4ja=Yny=%vWSNd,e9J {T]kO{+=K9WQlvo$<plW(/Evl%?u,Y+' );
define( 'SECURE_AUTH_KEY',  '^NJ)Llj<dXCJC#jgB7QA/ckuocz&$pUYTqH(m/X,?V-A!>J)Y-2qWbA7S+q{^(GL' );
define( 'LOGGED_IN_KEY',    '[N1MxlP!L>JeHd+,VdbYoJB1:yO|6V|?a9l]YR@~`wzFNK^L5#RV*_8mko9z44*8' );
define( 'NONCE_KEY',        '?@+K#o}hG:w|~-hOq@~{?B(aCfWM%g?Wgl,E;),1-IJwQD2Qf6;^#8m6|jmLVK}i' );
define( 'AUTH_SALT',        '5@D04 xB%M/1Y$o{F:-60h}OY}Yx:J>*/jL&K&n+AvMo| x!%g-[IJ_rq0&O]Ccj' );
define( 'SECURE_AUTH_SALT', 'r[USiX5Vno{zm+Z!nA>arlsW=B`f1{7QS eTuwcf}^Bfd<,J&wp&{|hF(fzrMF|R' );
define( 'LOGGED_IN_SALT',   '+}P%4O)Wdx++>w#2>aV8+-bNJET]>ZUw6F)kLofL5xQ_*Nd{o{4obi|USKOTpE!b' );
define( 'NONCE_SALT',       '(.ZP>cT9OO-otlK)b 0YxIr^D!B.yd,MB#=C56`|qQ!C6/5]g2a*]xrL-4{HX.}0' );

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================
$table_prefix  = 'wp_';

// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// =============================================================
// Memory
// Since these sites tend to be large, increase the memory limit
// =============================================================
define( 'WP_MEMORY_LIMIT', '96M' );

// ===========
// Hide errors
// ===========
ini_set( 'display_errors', 0 );
define( 'WP_DEBUG_DISPLAY', false );

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );