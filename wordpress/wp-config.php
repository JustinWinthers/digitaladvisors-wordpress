<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'financialgroup');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'winthers01');

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
define('AUTH_KEY',         '9$>[H-%r.4(_!#-{OY}zOX|3[2C15gUR{vG2P,Q/`+:pw25NKA?wYBbc0N=X+$bA');
define('SECURE_AUTH_KEY',  'J,LmQ<mXh|#xdD#*A(q*d4uG<1jB%{}]_Rk>Szo|YOmWils0Jfq^-mv7fW(T+:]%');
define('LOGGED_IN_KEY',    '=? 3Za0ESRMRe:A:?)%WE0R)u(m?|6A3yY!Snv;-XJo2>tH-L[Ma>?#,v||y1(k9');
define('NONCE_KEY',        '%~0AqLL/NCC6U)n^2iLok2aQK$|:=y-<-n|-zD.Fc2LYQgelFo$Bz@23c9=y+Wc3');
define('AUTH_SALT',        'R?KWS9k4%)@|yx_SV<C+e).xAvydC}e u1Nx(WjlolxC/[2s/=/2? +cw}=G@XL!');
define('SECURE_AUTH_SALT', '@Ix@sni4cPkq8Hj2*c8$cpb<%O]+QmVtRb`cd.Qy)rlARp6REr&<5mpI[ZI5Bw3V');
define('LOGGED_IN_SALT',   'y*Plx-scofn^r5&?%P2jQ,)<}SK=crKf1P#snpX!#N{R.`;Ve!}R#+1Amj@ffEsQ');
define('NONCE_SALT',       'q|^n!z/&8DOX#I)]--wGQ4Y%ZqU#c)%M;[%Ev`-H?.o+D|^)4+)h%E[!*W?+;(}-');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_financial_group';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
