<?php
namespace Modern_Tribe\Purple_Team\LiveAgent_Tools;

$autoloader = __DIR__ . '/vendor/autoload.php';
$config = __DIR__ . '/config.json';

if ( ! file_exists( $autoloader ) ) {
	exit( 'Autoloader not found: did you forget to run "composer install"?' );
}

if ( ! file_exists( $config ) ) {
	exit( 'The "config.json" file is missing. Did you forget to set it up?' );
}

function api(): API {
	static $api;
	return empty( $api ) ? $api = new API : $api;
}

function main( $config = null ): Main {
	static $main;
	return empty( $main ) ? $main = new Main( $config ) : $main;
}

function config( $config_path ) {
	return json_decode( file_get_contents( $config_path ) );
}

require $autoloader;
main( config( $config ) );