<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/Applications/MAMP/htdocs/odoo/wp-content/plugins/wp-super-cache/' );
define('DB_NAME', 'odoo');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'root');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'H:Ff(whjuLB(w8=&;M$nP3 cOFD=D?b:.FiU#:82OKg.1o $?o<_pc1!t#GK$ZE=');
define('SECURE_AUTH_KEY',  '0zDRfl=Y(vwM)gzeoG.E/kk2IHNQr3:a(tns|{k|8N.oo>l{pH gK56F* .+;r/Q');
define('LOGGED_IN_KEY',    'x/k)?n;f#2QMXUB{@]5L$6t5Ox[vCq 6_Yy#SS;8kP{cb>lo7Ai48=3O,R7YnH>D');
define('NONCE_KEY',        '|og7O-v[;iqNeC-,mv, $2LC2(IdrT9<QwHOshdQ!}-vfU9s2)h@Ua1:eT:RJ[OY');
define('AUTH_SALT',        'e!a:E8-+/z4x~OpN2c(K&seB5:sG|BDM&:;vbg6@&|4q1{x9<jef_)iI ]p;eU-5');
define('SECURE_AUTH_SALT', 'rf94Hd)pUb8b YRJXBt;YK*p9zCg ?Y78Px&9n](Nj2%Sf}.,69H-dKum>Eqtj{Q');
define('LOGGED_IN_SALT',   '/Ej>TVd;e UQ3<JbtD$4}IDrrD3{xt!<<<iuT0w5jGiM~zYC)QT4s q-o.vvos=8');
define('NONCE_SALT',       'wg5HAAz#nM=^]`s 2|w|[?TRw@]_f1-8:z}o#$$}|#wTLTnft=}797ifMlU:(HT>');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');