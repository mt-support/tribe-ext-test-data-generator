<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * ```php
 *  remove_filter( 'some_filter', [ tribe( Tribe\Extensions\Test_Data_Generator\Hooks::class ), 'some_filtering_method' ] );
 *  remove_filter( 'some_filter', [ tribe( 'events-virtual.hooks' ), 'some_filtering_method' ] );
 * ```
 *
 * To remove an action:
 * ```php
 *  remove_action( 'some_action', [ tribe( Tribe\Extensions\Test_Data_Generator\Hooks::class ), 'some_method' ] );
 *  remove_action( 'some_action', [ tribe( 'events-virtual.hooks' ), 'some_method' ] );
 * ```
 *
 * @since   1.0.0
 *
 * @package Tribe\Extensions\Test_Data_Generator;
 */

namespace Tribe\Extensions\Test_Data_Generator;
use Tribe__Settings;

/**
 * Class Hooks.
 *
 * @since   1.0.0
 *
 * @package Tribe\Extensions\Test_Data_Generator;
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.test_data_generator.hooks', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'tribe_load_text_domains', [ $this, 'load_text_domains' ] );
		add_action( 'admin_menu', [ $this, 'action_add_menu' ], 15 );
		add_action( 'admin_init', [ $this, 'on_admin_init' ], 15 );
		tribe_notice(
			'tribe-ext-test-data-generator-success',
			[
				$this->container->make( Page::class ),
				'render_success_notice'
			],
			[
				'type' => 'success',
				'action' => 'tribe_ext_test_data_generator_notices'
			],
			function() {
				return tribe_is_truthy( tribe_get_request_var( 'tribe_success' ) );
			}
		);
		tribe_notice(
			'tribe-ext-test-data-generator-error',
			[
				$this->container->make( Page::class ),
				'render_error_notice'
			],
			[
				'type' => 'error',
				'action' => 'tribe_ext_test_data_generator_notices'
			],
			function() {
				return tribe_is_truthy( tribe_get_request_var( 'tribe_error' ) );
			}
		);
	}

	/**
	 * Adds the filters required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {

	}

	/**
	 * Load text domain for localization of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load_text_domains() {
		$mopath = tribe( Plugin::class )->plugin_dir . 'lang/';
		$domain = 'test-data-generator';

		// This will load `wp-content/languages/plugins` files first.
		\Tribe__Main::instance()->load_text_domain( $domain, $mopath );
	}

	/**
	 * Add menu item.
	 * @since 1.0.0
	 */
	public function action_add_menu() {
		$this->container->make( Page::class )->add_menu();
	}

	/**
	 * Executed on Admin Init.
	 *
	 * @since 1.0.0
	 */
	public function on_admin_init() {
		$page_obj = $this->container->make( Page::class );
		add_action(
			'load-' . $page_obj->get_menu_hook(),
			[ $page_obj, 'parse_request' ]
		);
	}
}
