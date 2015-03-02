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
	 * Initialize all the things
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'set' ) );
		add_shortcode( 'cookie', array( $this, 'output' ) );
	}
	
	/**
	* set
	*/
	public function set() {
		if ( isset($_GET['ref'])) {
			$value = $_GET['ref'];
			setcookie("refer", $value, time()+3600);
		}
	}

	public function output() {
		if ( isset($_COOKIE['refer'])) {
			return $_COOKIE['refer'];
		}
	}
}
$external_affiliate_cookie = new external_affiliate_cookie();