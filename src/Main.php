<?php
namespace Modern_Tribe\Purple_Team\LiveAgent_Tools;

class Main {
	private $config;
	private $api;

	public function __construct( $config ) {
		$this->config = $config;
	}

	public function api(): API {
		return empty( $this->api ) ? $this->api = new API : $this->api;
	}

	public function get_config( $key, $default = null ) {
		return isset( $this->config->$key )
			? $this->config->$key
			: $default;
	}
}