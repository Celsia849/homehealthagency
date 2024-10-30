<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'homehealthagency' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'E;c2u6k:qVab=<E7F)VX*mUZ;4:0_6aW/v.&7SaET}eKS-BUosS!g_N<~Vp3/6(5' );
define( 'SECURE_AUTH_KEY',  '.UME3Xc)efEDC|M&g3Um,]k~0Y!>SG+_SEhMVpy[4]a>,|RyD^h(}Er*fvOH^AgW' );
define( 'LOGGED_IN_KEY',    ',*iv=>Jsl}H<fFkwh]y7K^]>DMq4cnU@8)Ww m2MqaH8h4un2#uqvDpV%zs[AL,<' );
define( 'NONCE_KEY',        'M<}!Hd[s[0EC<tJ_A8~rk~G,Gmiz&k!gP$Y_R3wFw;Nag?z`E`ca;!m&T{(5b|!Q' );
define( 'AUTH_SALT',        'O?|AT5a1x-)A:K0z@#p>$DxdF79tA`x-{e)]ZZ1Q_rs/Kd6GSA&]k1@qruGCpf4Q' );
define( 'SECURE_AUTH_SALT', '1[tm=XC;||@,qw*x:Eum(aEWoH^GU7Q$=;fPXr$qoI;*Yb3b=V}o~Io$)>03.NFh' );
define( 'LOGGED_IN_SALT',   'po9MjVCHnNopm3$2D #Z>4+FaV;^0?-kqoAOzZZ-o0z|{nzksbQ^2.d/_)YWhoz*' );
define( 'NONCE_SALT',       '*jf<(u05JaVl[<hXtNZ.e=}OlyheFjQGOkZ.-^06}CPG>F2j5bS|N>pB8bIdlluC' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);


/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_MEMORY_LIMIT', '256M' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
