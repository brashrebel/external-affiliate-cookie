<?php

/*
Plugin Name: External Affiliate Cookie
Plugin URI: http://realbigplugins.com
Description: Sets a cookie which can then allow the value to be appended to links so that the cookie will again be set on another site.
Version: 0.1
Author: Kyle Maurer
Author URI: http://kyleblog.net
License: GPL2
*/

/**
 * Class external_affiliate_cookie
 */
class external_affiliate_cookie {

	/**
	 * The name of the cookie to check against.
	 *
	 * @access private
	 *
	 * @var string
	 */
	private static $cookie_name = 'rbp_refer';

	/**
	 * Initialize all the things
	 */
	public function __construct() {

		add_action( 'init', array( __CLASS__, 'set' ) );
		add_shortcode( 'cookie', array( __CLASS__, 'output' ) );

		add_filter( 'the_content', array( __CLASS__, '_add_query_arg' ) );
	}
	
	/**
	* set
	*/
	public static function set() {

		if ( isset( $_GET['ref'] ) ) {

			$value = $_GET['ref'];
			setcookie( self::$cookie_name, $value, time() + ( WEEK_IN_SECONDS * 3 ) );

			// Also set here for immediate page load cookie presence
			$_COOKIE[ self::$cookie_name ] = $value;
		}
	}

	public static function output() {
		if ( isset( $_COOKIE[ self::$cookie_name ] ) ) {
			return $_COOKIE[ self::$cookie_name ];
		}
	}

	/**
	 * Checks if the cookie is set or not.
	 *
	 * @access private
	 *
	 * @return bool If the cookie is set.
	 */
	private static function _check_cookie() {
		return isset( $_COOKIE[ self::$cookie_name ] );
	}

	/**
	 * Filters the content based on the presence of a cookie.
	 *
	 * @access private
	 *
	 * @param string $content The current content to filter.
	 *
	 * @return string The filtered content.
	 */
	static function _add_query_arg( $content ) {

		// Bail if cookie is not set
		if ( ! self::_check_cookie() ) {
			return $content;
		}

		// Replace specific url's with added query arg
		preg_replace_callback( '/href="(.*?)"/g', function ( $matches ) {

			// Get the url and bail if it's not set
			if ( ( $url = isset( $matches[1] ) ? $matches[1] : false ) === false ) {
				return $matches[0];
			}

			$total_match = $matches[0];

			// Add query arg
			$new_url = add_query_arg( 'ref', external_affiliate_cookie::$cookie_name, $url );

			// Add the url back in
			$total_match = preg_replace( "/$url/", $new_url, $total_match );

			return $total_match;
		}, $content );

		return $content;
	}
}
$external_affiliate_cookie = new external_affiliate_cookie();