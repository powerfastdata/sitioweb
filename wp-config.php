<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u572999072_0TESB' );

/** MySQL database username */
define( 'DB_USER', 'u572999072_ecUxB' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Power21DB@' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'RbO R#KAxIe.=Z:1>9=j-jiDO83SUQ|-+;G;?xU_#Pn3)^2:L@#HOe5ni?BpQmAF' );
define( 'SECURE_AUTH_KEY',  'qqY>5-o.P&bmn!SzE>frz@<V$)Kr3&2FhIpop|??xMg[9pl,,6H=VDTjW,/dWYJq' );
define( 'LOGGED_IN_KEY',    'X<b#e#gP{A_=SUzqtAI@dZh)JIl6Ayz.w86+fa^}lm/J::vhAU#fNzRr%Dw9+8+Z' );
define( 'NONCE_KEY',        'Z>0.itS4c/JJc9}1t2awxsOR9A/+NR(Cw?M7BRbQR1u5Rp@R4=j{QW*@xb2d>9`D' );
define( 'AUTH_SALT',        'X3Vr7_!!LM:qspauHAs9^Q9R8AlTE]i/jKFhi)kMG-aC8`c|#0]#bq8c8@v~4J,j' );
define( 'SECURE_AUTH_SALT', '5 v:4-76B|cYi?r}uGZQB#?OjrSGvl|5Wc!c6NkHF9eFPTl@9rN@]ju@1i)SGjiO' );
define( 'LOGGED_IN_SALT',   'RY|WAKb= @tffd0:NH#4 X~LY%Q<)>P]ej]@`BT2(LO?UzZg|p@-S_n>rWTrZt^3' );
define( 'NONCE_SALT',       'H`m:xY]a{wi)(NVD~xI~X%ENp_QFoaicQbH4XB9.>z2>ZS0,5Ro$jjLb7xFZWk=g' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
