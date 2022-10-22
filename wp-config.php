<?php
define( 'WP_CACHE', true );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
    define( 'DB_NAME', 'lizado' );
    define( 'DB_USER', 'root' );
    define( 'DB_PASSWORD', '' );
    define( 'DB_HOST', 'localhost' );
}else{
    define( 'DB_NAME', 'naca_wp' );
    define( 'DB_USER', 'naca_wp' );
    define( 'DB_PASSWORD', 'devcode15' );
}

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'odAjtM177xm1nnyys7tezZ4ka4KAQW0OMOFHZJEOxqfmviVfngqtfCJRR21PiyNx' );
define( 'SECURE_AUTH_KEY',  '8vjCBLkO61fPseVVZeowmNdsz7H0oLL2dmQIS3QYs441llC54WY0fehnDJHm2Wjw' );
define( 'LOGGED_IN_KEY',    '6CrqwixZr5UOWCUFGcboaIuA088K814iykskwVoYobvF8KrIhy50DnDn9tKqxoxT' );
define( 'NONCE_KEY',        '1Td03aVXKtacENFMULqTQLbRz1LMo3FRuNSrMSJRkXrJtnogzkvF7ai1oMaHo8nD' );
define( 'AUTH_SALT',        'NiIlcKXl9fh0D62rVHK6emGkYYiYlMgjxNWYN29paSCfPuNt8wB6KUaTOwhxwOiL' );
define( 'SECURE_AUTH_SALT', 'U1CflNpQ3Bk4jzHkrGBZ7G5l3iEWPyw2JE4ELxs02m1tleXT3dafwgWkGABgwJ6b' );
define( 'LOGGED_IN_SALT',   '3y2UOMJtVXlpx5HZUNnHLKj7udQCSJi0qfNWjt2XrS4FcioaYa2PwRX8xvGZhBqk' );
define( 'NONCE_SALT',       'MiPYb55scqGaYVAo2UB0SADQzUPDV5y0EhtrxJ3gh5c3IS40EFOjlRsisnzf3Rzc' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
