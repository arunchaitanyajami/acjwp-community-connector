<?php
/**
 * Response Parser.
 *
 * @package wp-community-connector
 * @sub-package WordPress
 */

namespace Acj\Wpcc;

/**
 * Parser class
 */
class ResponseParser {

	/**
	 * Mixed data.
	 *
	 * @var mixed
	 */
	private mixed $data;

	/**
	 * WP Rest request.
	 *
	 * @var \WP_REST_Request
	 */
	private \WP_REST_Request $request;


	/**
	 * Construct
	 *
	 * @param \WP_REST_Response $response The response object.
	 * @param \WP_REST_Request  $request Request object.
	 */
	public function __construct( \WP_REST_Response $response, \WP_REST_Request $request ) {
		$this->data    = $response->get_data();
		$this->request = $request;
	}

	/**
	 * Returns Parse Data.
	 *
	 * @param string $root_key Root key.
	 *
	 * @return array
	 */
	public function init( string $root_key = '' ): array {
		if ( empty( $this->data ) ) {
			return array();
		}

		if ( ! str_contains( $this->request->get_route(), '/' . ACJ_WPCC_REPORTS_ENDPOINT ) ) {
			return $this->data;
		}

		$updates_data   = ( ! empty( $root_key ) ) ? $this->data[ $root_key ] : $this->data;
		$get_draft_data = array();
		foreach ( $updates_data as $n_key => $n_data ) {
			if ( ! is_array( $n_data ) ) {
				$get_draft_data[ $this->convert_key_to_title( $n_key ) ] = $this->validate_value( $n_data );
			} else {
				$get_draft_data[ $n_key ] = $this->convert_keys_to_strings( $n_data );
			}
		}

		return $get_draft_data;
	}

	/**
	 * Converted data.
	 *
	 * @param array  $data Data Set.
	 * @param string $prefix Data Prefix.
	 *
	 * @return array
	 */
	private function convert_keys_to_strings( array $data, string $prefix = '' ): array {
		$result = array();
		foreach ( $data as $key => $value ) {
			$new_key = $prefix . ( $prefix ? '.' : '' ) . $key;
			if ( is_array( $value ) ) {
				$result = array_merge( $result, $this->convert_keys_to_strings( (array) $value, $new_key ) );
			} else {
				$result[ $this->convert_key_to_title( $new_key ) ] = $this->validate_value( $value );
			}
		}

		return $result;
	}

	/**
	 * Object key.
	 *
	 * @param string $key Object key.
	 *
	 * @return string
	 */
	private function convert_key_to_title( string $key ): string {
		$inline_convert = $this->request->get_param( 'convert' );
		$updated_key    = str_replace( '/', ' ', $key );
		$updated_key    = str_replace( '_', ' ', $updated_key );

		if ( $inline_convert ) {
			$updated_key = str_replace( '.', ' ', $updated_key );
			$updated_key = str_replace( '-', ' ', $updated_key );
			$updated_key = str_replace( ':', ' ', $updated_key );
			$updated_key = ucwords( str_replace( '_', ' ', $updated_key ) );
		}

		return trim( $updated_key );
	}

	/**
	 * Key validation.
	 *
	 * @param mixed $value Value.
	 *
	 * @return mixed
	 */
	private function validate_value( mixed $value ): mixed {
		$inline_data = $this->request->get_param( 'inline_data' );
		if ( ! $inline_data ) {
			return strtotime( $value ) !== false ? strtotime( $value ) : $value;
		}

		$return = array(
			'name'        => '',
			'description' => '',
			'formula'     => '',
			'type'        => '',
			'aggregation' => '',
		);

		if ( strtotime( $value ) !== false ) {
			return array_merge(
				$return,
				array(
					'value' => strtotime( $value ),
					'type'  => 'DateTime',
				)
			);
		}

		return array_merge(
			$return,
			array(
				'value' => $value,
				'type'  => getType( $value ),
			)
		);
	}


	/**
	 * Check is Index array.
	 *
	 * @param array $data Data.
	 *
	 * @return bool
	 */
	private function is_indexed_array( array $data ): bool {
		$keys  = array_keys( $data );
		$count = count( $keys );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( $keys[ $i ] !== $i ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check is associative array.
	 *
	 * @param array $data Data.
	 *
	 * @return bool
	 */
	private function is_associative_array( array $data ): bool {
		$keys = array_keys( $data );
		foreach ( $keys as $key ) {
			if ( ! is_int( $key ) ) {
				return true;
			}
		}

		return false;
	}
}
