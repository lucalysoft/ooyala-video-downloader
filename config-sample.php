<?php
/**
 * Configuration file for Ooyala Video Downloader.
 */

$config = new Config(
	'api_key from your Ooyala account',
	'secret_key from Ooyala',
	'localhost',
	'username',
	'password',
	'database',
	'3306',
	'/tmp/mysql.sock' );

class Config {
	public $api_key;
	public $secret_key;
	public $db_server;
	public $db_user;
	public $db_password;
	public $db_database;
	public $db_port;
	public $db_socket;

	function __construct( $api_key, $secret_key, $db_server, $db_user, $db_password, $db_database, $db_port = '3306', $db_socket = '' ) {
		$this->api_key = $api_key;
		$this->secret_key = $secret_key;
		$this->db_server = $db_server;
		$this->db_user = $db_user;
		$this->db_password = $db_password;
		$this->db_database = $db_database;
		$this->db_port = $db_port;
		$this->db_socket = $db_socket;
	}

	public function db_connect() {
		$mysqli = new mysqli( $this->db_server, $this->db_user, $this->db_password, $this->db_database, $this->db_port, $this->db_socket );

		if ( $mysqli->connect_errno ) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}

		return $mysqli;
	}
}
