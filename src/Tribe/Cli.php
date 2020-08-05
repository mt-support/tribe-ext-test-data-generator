<?php
/**
 * Handles the integration with wp-cli.
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\Test_Data_Generator
 */

namespace Tribe\Extensions\Test_Data_Generator;

use Tribe\Extensions\Test_Data_Generator\Cli\Command;

/**
 * Class Cli
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\Test_Data_Generator
 */
class Cli extends \tad_DI52_ServiceProvider {

	/**
	 * Registers the filters, actions and bindings required to provide wp-cli support to the extension.
	 *
	 * @since TBD
	 *
	 * @throws \Exception
	 */
	public function register() {
		$command = new Command();
		\WP_CLI::add_command( 'tec-test-data events generate', [ $command, 'generate_events' ] );
		\WP_CLI::add_command( 'tec-test-data organizers generate', [ $command, 'generate_organizers' ] );
		\WP_CLI::add_command( 'tec-test-data venues generate', [ $command, 'generate_venues' ] );
		\WP_CLI::add_command( 'tec-test-data image generate', [ $command, 'generate_images' ] );
		\WP_CLI::add_command( 'tec-test-data delete', [ $command, 'delete' ] );
		\WP_CLI::add_command( 'tec-test-data reset', [ $command, 'reset' ] );
	}
}
