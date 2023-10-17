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
	 * Transformed rest request route.
	 *
	 * @var string
	 */
	private string $route;


	/**
	 * Construct
	 *
	 * @param \WP_REST_Response $response The response object.
	 * @param \WP_REST_Request  $request Request object.
	 */
	public function __construct( \WP_REST_Response $response, \WP_REST_Request $request ) {
		$this->data    = $response->get_data();
		$this->request = $request;

		$this->route = $this->convert_key_to_title( $this->request->get_route() );
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

		$data = $this->data;
		if ( ! $this->is_indexed_array( $this->data ) ) {
			$data = array( $this->data );
		}

		$updates_data   = ( ! empty( $root_key ) ) ? $data[ $root_key ] : $data;
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
				$result[ $this->convert_key_to_title( $new_key ) ] = $this->validate_value( $value, $new_key );
			}
		}

		return $result;
	}

	/**
	 * Convert key to Name.
	 *
	 * @param string $key Object key.
	 *
	 * @return string
	 */
	private function get_name( string $key ): string {
		$transformed_key = $this->convert_key_to_title( $key );

		$updated_key = str_replace( '/', ' ', $transformed_key );
		$updated_key = str_replace( '_', ' ', $updated_key );
		$updated_key = str_replace( '.', ' ', $updated_key );
		$updated_key = str_replace( '-', ' ', $updated_key );
		$updated_key = str_replace( ':', ' ', $updated_key );
		$updated_key = ucwords( str_replace( '_', ' ', $updated_key ) );
		$updated_key = str_replace( ':', '_', $updated_key );
		$updated_key = str_replace( '.', '_', $updated_key );

		return apply_filters( 'acj_wpcc_report_name' . $this->route . '_' . $transformed_key, trim( $updated_key ), $key );
	}

	/**
	 * Convert key to Description.
	 *
	 * @param string $key Object key.
	 *
	 * @return string
	 */
	private function get_description( string $key ): string {
		return $this->get_name( $key );
	}

	/**
	 * Object key.
	 *
	 * @param string $key Object key.
	 *
	 * @return string
	 */
	private function convert_key_to_title( string $key ): string {
		$updated_key = str_replace( '_', ' ', trim( $key ) );
		$updated_key = str_replace( ' ', '_', trim( $updated_key ) );
		$updated_key = str_replace( '.', '_', trim( $updated_key ) );
		$updated_key = str_replace( '-', '_', trim( $updated_key ) );
		$updated_key = str_replace( ':', '_', trim( $updated_key ) );
		$updated_key = str_replace( '/', '_', trim( $updated_key ) );

		return trim( $updated_key );
	}

	/**
	 * Key validation.
	 *
	 * @param mixed  $value Value.
	 * @param string $key Key.
	 *
	 * @return mixed
	 */
	private function validate_value( mixed $value, string $key = '' ): mixed {
		$inline_data = $this->request->get_param( 'inline_data' );
		if ( ! $inline_data ) {
			return strtotime( $value ) !== false ? strtotime( $value ) : $value;
		}

		$return = array(
			'name'        => $this->get_name( $key ),
			'description' => $this->get_description( $key ),
			'formula'     => '',
			'type'        => '',
			'aggregation' => '',
		);

		/**
		 * Check Data.
		 */
		if ( strtotime( $value ) !== false ) {
			return array_merge(
				$return,
				array(
					'value' => strtotime( $value ),
					'type'  => 'DateTime',
				)
			);
		}

		/**
		 * Check URL.
		 */
		if ( filter_var( $value, FILTER_VALIDATE_URL ) !== false ) {
			return array_merge(
				$return,
				array(
					'value' => $value,
					'type'  => 'url',
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
