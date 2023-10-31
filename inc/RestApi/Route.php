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

					if ( in_array( $route, $this->__excluded_routes(), true ) ) {
						continue;
					}

					$method_types = wp_list_pluck( $methods, 'GET' );
					if ( empty( $method_types ) ) {
						continue;
					}

					if ( ! in_array( true, $method_types, true ) ) {
						continue;
					}

					if ( $route === '/' ) {
						continue;
					}

					if ( str_contains( $route, '?P<' ) ) {
						continue;
					}

					$filtered_args       = wp_list_pluck( $args, 'args' );
					$required            = wp_list_pluck( $filtered_args[0], 'required' );
					$permission_callback = wp_list_pluck( $args, 'permission_callback' );
					if ( ! empty( array_filter( $required ) ) ) {
						continue;
					}

					if ( $this->__check_permission( array_filter( $permission_callback ) ) ) {
						continue;
					}

					$list[] = $route;
				}
			}
		}

		return rest_ensure_response( $list );
	}

	public function __excluded_routes(): array {
		return [
			"/oembed/1.0",
			"/wp/v2",
			"/wp/v2/taxonomies",
			"/wp/v2/menu-items",
			"/wp/v2/media",
			"/wp/v2/blocks",
			"/wp/v2/templates",
			"/wp/v2/template-parts",
			"/wp/v2/types",
			"/wp/v2/statuses",
			"/wp/v2/block-types",
			"/wp/v2/settings",
			"/wp/v2/themes",
			"/wp/v2/plugins",
			"/wp/v2/sidebars",
			"/wp/v2/widget-types",
			"/wp/v2/widgets",
			"/wp/v2/pattern-directory",
			"/wp/v2/pattern-directory/patterns",
			"/wp/v2/pattern-directory/patterns",
			"/wp/v2/block-patterns/patterns",
			"/wp/v2/block-patterns/categories",
			"/wp-block-editor/v1",
			"/wp-block-editor/v1/url-details",
			"/wp-block-editor/v1/navigation-fallback",
			"/wp-block-editor/v1/export",
			"/wp-site-health/v1",
			"/wp-site-health/v1/tests/background-updates",
			"/wp-site-health/v1/tests/loopback-requests",
			"/wp-site-health/v1/tests/https-status",
			"/wp-site-health/v1/tests/dotorg-communication",
			"/wp-site-health/v1/tests/authorization-header",
			"/wp-site-health/v1/directory-sizes",
			"/wp-site-health/v1/tests/page-cache"
		];
	}

	public function __check_permission( array $data ): bool {
		if ( empty( $data ) ) {
			return false;
		}

		foreach ( $data as $permission_object => $args ){
			if ( 'string' === getType( $args ) ) {
				return call_user_func( $args, true );
			}
		}

		return false;
	}
}
