<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/var/www/html/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'sitebuilder');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root#@123');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '9+-]>etCjHE^6?~e8Zqt-7&!)%Vnw`sJ*+,q,SMOmKq_%-tmfA+xX?Q88{IU8Qia');
define('SECURE_AUTH_KEY',  '1^];1u#508zd-eGs`33_#qyWk?^kz>%BDiOGP$QsQDuYZF]PIOnRLv|Q<DRJw-yJ');
define('LOGGED_IN_KEY',    'z+R4a,%TkA2{d0y&i!=3D2&FFOAL4+Dl5c/,Z,4Jf[$I.RLQM}VmNK*}3?w05_y`');
define('NONCE_KEY',        '+wYA0~+-25.yx]`G>OyCo-c#g<*_I>u[%dK-IhT<B-pu|6(`qR?-}FT[<$8_?Kzq');
define('AUTH_SALT',        'o+7Q(tT5t*ZyUvpxxQqGlgJTFz}r+JMG?Zq w-X}KNIpi-+59Km+fX)L}s(mkXaI');
define('SECURE_AUTH_SALT', '+;)}r69H-dNi-#YoF;k2Bg$V$XCj:Ua$-ow-;Ia37fWP0=UuFuL80Ow4d@JnIq-K');
define('LOGGED_IN_SALT',   'nwI)aja=dZ+T$j)oxjZ[CO*n}nkzn/_4f>o$PU}XOA/6tBsz=_)>xd[>Kj/PW~{I');
define('NONCE_SALT',       '|+N-NH=N+-l7t;&HWEP%@<[,j%-ixyB1`^4ym+J6#SDO:)G][9,y]U#6uto;?)sH');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/* Theme, plugin update */
define('FS_METHOD', 'direct');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
