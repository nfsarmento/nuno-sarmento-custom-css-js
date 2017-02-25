<?php
/*
Plugin Name: Nuno Sarmento Custom CSS - JS
Plugin URI: http://www.nuno-sarmento.com
Description: Nuno Sarmento Custom CSS - JS Simple plugin allows you to add custom CSS and Javascript to pages and posts.
Version: 1.0.0
Author: Nuno Sarmento
Author URI: http://www.nuno-sarmento.com
Text Domain: tend-custom-css-js
Domain Path: /languages
License:     GPL2

Nuno Sarmento Custom CSS - JS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Nuno Sarmento Custom CSS - JS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/
defined('ABSPATH') or die('°_°’');

/* ------------------------------------------
// Constants --------------------------------
--------------------------------------------- */

/* Set plugin version constant. */

if( ! defined( 'NUNO_SARMENTO_CUSTOM_CSS_JS_VERSION' ) ) {
	define( 'NUNO_SARMENTO_CUSTOM_CSS_JS_VERSION', '1.0.0' );
}

/* Set plugin name. */

if( ! defined( 'NUNO_SARMENTO_CUSTOM_CSS_JS_NAME' ) ) {
	define( 'NUNO_SARMENTO_CUSTOM_CSS_JS_NAME', 'Nuno Sarmento Custom CSS - JS' );
}

/* Set constant path to the plugin directory. */

if ( ! defined( 'NUNO_SARMENTO_CUSTOM_CSS_JS_PATH' ) ) {
	define( 'NUNO_SARMENTO_CUSTOM_CSS_JS_PATH', plugin_dir_path( __FILE__ ) );
}

/* ------------------------------------------
// i18n ----------------------------
--------------------------------------------- */

load_plugin_textdomain( 'nuno-sarmento-custom-css-js', false, basename( dirname( __FILE__ ) ) . '/languages' );

/* ------------------------------------------
// Require services --------------
--------------------------------------------- */

if ( ! @include( 'nuno-sarmento-custom-css-js-services.php' ) ) {
	require_once( NUNO_SARMENTO_CUSTOM_CSS_JS_PATH . 'admin/nuno-sarmento-custom-css-js-services.php' );
}
