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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_unihawker');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**Disable auto core update **/
define('WP_AUTO_UPDATE_CORE', false);

/* Multisite */
//define('WP_ALLOW_MULTISITE', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'aL*gOpeE}mB#*oqDCbijZG.Xb},5)Rl=]Ihx[SO-<%4i-e6Zr]j;/6:2(2qDoJ2-');
define('SECURE_AUTH_KEY',  '8wM@`}ob-K38][{,WDA+7pgApY%:|&>*TR`?c+|`<]]GFu}R-)y9oYfs^6#| Y}w');
define('LOGGED_IN_KEY',    'D*Vph0ZRB:{_Yk@KSyU[*_Y0X$L!bjd+2=-KR/4W+w0Pt z/u0}LE_)$Z0x@G|Y^');
define('NONCE_KEY',        'k5yO]<-[|JI{NWa=Uu/xJry|S;C-TwGfx->0,kHMapz~O@51^Y`><`qmF|;Il]~=');
define('AUTH_SALT',        'yV@@`{Q3}L@p&gWg@%:2=8raeWMyj=A|DRC4qJeujleN(%ioDIpd*+S9Jt:Y?]$Z');
define('SECURE_AUTH_SALT', '2cZ`B1Zt|;a*1)tqBRGw>eJ;pOut6Hf|1v1tq`s^X#65@$-4GW$&Sc/=Ea 1lEm5');
define('LOGGED_IN_SALT',   '!Fl+5gLhBvGPWum5/ZaY;757|],a3Wr|VPK!6Ax&ypvK(;FzDRGP4K_Q6<1%=>zB');
define('NONCE_SALT',       '%U^ItH| V;ms8ZwWoIRe}s^^zAUV^A~c8<uOv?xns:-y z:oF7+Hq`=t=bb[N7VJ');

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

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
