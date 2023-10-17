<?php
/**
 * Setup Custom rest api.
 *
 * @package           wp-community-connector
 * @sub-package       WordPress
 */

namespace Acj\Wpcc\RestApi;

/**
 * Class
 */
class Route {

	/**
	 * Route namespace.
	 *
	 * @var string
	 */
	protected string $name_space = 'wpcc/v1';

	/**
	 * Init class actions.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Register Custom Endpoint.
	 *
	 * @return void
	 */
	public function register(): void {
		register_rest_route(
			$this->name_space,
			'routes',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'callback' ),
			)
		);
	}

	/**
	 * Callback function.
	 *
	 * @return \WP_REST_Response
	 */
	public function callback(): \WP_REST_Response {
		$routes = rest_get_server()->get_routes();
		$list   = array();
		foreach ( $routes as $route => $args ) {
			if ( ! str_contains( $route, '/' . ACJ_WPCC_REPORTS_ENDPOINT ) ) {
				if ( ! str_contains( $route, $this->name_space ) ) {
					$methods = wp_list_pluck( $args, 'methods' );
					if ( empty( $methods ) ) {
						continue;
					}

					$method_types = wp_list_pluck( $methods, 'GET' );
					if ( empty( $method_types ) ) {
						continue;
					}

					if ( ! in_array( true, $method_types, true ) ) {
						continue;
					}

					$list[] = $route;
				}
			}
		}

		return rest_ensure_response( $list );
	}
}
