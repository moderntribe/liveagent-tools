<?php
namespace Modern_Tribe\Purple_Team\LiveAgent_Tools;

use Generator;
use GuzzleHttp\Client as HTTP_Client;
use GuzzleHttp\Exception\GuzzleException as HTTP_Exception;

class API {
	/**
	 * Attempts to return all relevant objects from the specified
	 * LiveAgent endpoint, as a single large array.
	 *
	 * @param string $path
	 * @param array $query
	 *
	 * @return array
	 */
	public function all( string $path, array $query = [] ): array {
		$results = [];

		foreach ( $this->each( $path, $query ) as $object ) {
			$results[] = $object;
		}

		return $results;
	}

	/**
	 * Attempts to return all relevant objects from the specified
	 * LiveAgent endpoint.
	 *
	 * This is done individually, therefore the expected use of
	 * this method is from a foreach() loop or some other appropriate
	 * construct.
	 *
	 * @param string $path
	 * @param array $query
	 *
	 * @return Generator
	 */
	public function each( string $path, array $query = [] ) {
		$batch_size = main()->get_config( 'default_batch_size', 100 );
		$object_count = 0;
		$page = 1;

		do {
			$query['_page'] = $page++;
			$query['_perPage'] = $batch_size;

			$objects = $this->get_array( $path, $query );

			if ( is_array( $objects ) ) {
				$object_count = count( $objects );
			}

			foreach ( $objects as $single_object ) {
				yield $single_object;
			}
		}
		while ( $object_count === $batch_size );
	}

	private function get_array( $path, array $query = [] ) {
		$params = $this->params( [
			'query' => $query,
		] );

		try {
			$http    = new HTTP_Client;
			$url     = main()->get_config( 'api_url' ) . '/' . $path;
			$objects = json_decode( $http->request( 'GET', $url, $params )->getBody() );
		}
		catch ( HTTP_Exception $exception ) {
			return [];
		}

		return is_array( $objects ) ? $objects : [];
	}

	public function update( $path, array $data ): bool {
		$params = $this->params( [
			'body' => json_encode( $data )
		] );

		try {
			$http    = new HTTP_Client;
			$url     = main()->get_config( 'api_url' ) . '/' . $path;
			$result  = $http->request( 'PATCH', $url, $params );

			if ( 200 === $result->getStatusCode() ) {
				return true;
			}
		}
		catch ( HTTP_Exception $exception ) {
			return false;
		}

		return false;
	}

	private function params( array $additional ): array {
		$base = [
			'headers' => [
				'Accept' => 'application/json',
				'apikey' => main()->get_config( 'api_key' ),
			],
			'timeout' => 10,
			'verify' => ! main()->get_config( 'https_unsafe', false ),
		];

		return array_merge( $base, $additional );
	}
}