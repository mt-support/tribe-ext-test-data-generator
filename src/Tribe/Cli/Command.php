<?php
/**
 * Proxies wp-cli command to the generators.
 *
 * @since   1.0.1
 *
 * @package Tribe\Extensions\Test_Data_Generator\Cli
 */

namespace Tribe\Extensions\Test_Data_Generator\Cli;

use Tribe\Extensions\Test_Data_Generator\Generator\Event;
use Tribe\Extensions\Test_Data_Generator\Generator\Organizer;
use Tribe\Extensions\Test_Data_Generator\Generator\Utils;
use Tribe\Extensions\Test_Data_Generator\Generator\Venue;
use function WP_CLI\Utils\make_progress_bar;

/**
 * Class Command
 *
 * @since   1.0.1
 *
 * @package Tribe\Extensions\Test_Data_Generator\Cli
 */
class Command {
	/**
	 * A map from the command associative arguments to the Event generator input arguments.
	 *
	 * @since TBD
	 *
	 * @var array<string,string>
	 */
	protected $events_generator_translation_map = [
		'from-date'    => 'fromDate',
		'to-date'      => 'toDate',
		'with-rsvp'    => 'rsvp',
		'with-tickets' => 'tickets',
		'virtual'      => 'virtual',
		'recurring'    => [ 'recurring', 'recurring_type' ]
	];

	/**
	 * Create a set of test events.
	 *
	 * ## OPTIONS
	 *
	 * [<quantity>]
	 * : The number of events to generate.
	 * ---
	 * default: 1
	 * ---
	 *
	 * [--from-date=<from-date>]
	 * : The `strtotime` parseable start date of the first generated event.
	 * ---
	 * default: -1 month
	 * ---
	 *
	 * [--to-date=<to-date>]
	 * : The `strtotime` parseable start date of the last generated event.
	 * ---
	 * default: +1 month
	 * ---
	 *
	 * [--with-organizers=<with-organizers>]
	 * : The number of Organizers to generate along with the events.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--with-venues=<with-venues>]
	 * : The number of Venues to generate along with the events.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--with-images=<with-images>]
	 * : The number of Images to upload along with the events.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--with-rsvp=<with-rsvp>]
	 * : Whether to generate RSVP tickets for the events or not.
	 * ---
	 * default: false
	 * ---
	 *
	 * [--with-tickets=<with-tickets>]
	 * : Whether to generate tickets for the events or not.
	 * ---
	 * default: false
	 * ---
	 *
	 * [--virtual]
	 * : Whether to make the generated events Virtual or not.
	 * Does NOT require "The Events Calendar: Virtual Events" plugin.
	 * ---
	 * default: false
	 * ---
	 *
	 * [--recurring[=<recurring_type>]]
	 * : Whether to create the events as recurring and, if so, what recurrence pattern to use.
	 * Requires "The Events Calendar PRO" plugin to be installed and active on the site.
	 * ---
	 * default: all
	 * options:
	 *   - all
	 *   - yearly
	 *   - monthly
	 *   - weekly
	 *   - daily
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp tec-test-data events generate
	 *     wp tec-test-data events generate 23
	 *     wp tec-test-data events generate 23 --from-date="-1 year" --to-date=2020-12-31
	 *     wp tec-test-data events generate 23 --with-rsvp
	 *     wp tec-test-data events generate 23 --with-tickets
	 *     wp tec-test-data events generate 23 --with-venues 2 --with-organizers=5 --with-images=10
	 *     wp tec-test-data events generate 23 --virtual
	 *     wp tec-test-data events generate 23 --virtual --with-venues=2 --with-organizers=2 --with-images=2
	 *     wp tec-test-data events generate 23 --recurring
	 *     wp tec-test-data events generate 23 --recurring=all
	 *     wp tec-test-data events generate 23 --recurring=weekly
	 *
	 * @when after_wp_load
	 */
	public function generate_events( array $args = [], array $assoc_args = [] ) {
		if ( ! empty( $assoc_args['recurring'] ) ) {
			$this->check_recurring_support();
		}

		if ( ! empty( $assoc_args['with-images'] ) ) {
			$images = (int) $assoc_args['with-images'];
			$this->generate_images( [ $images ] );
		}

		if ( ! empty( $assoc_args['with-venues'] ) ) {
			$venue_quantity = (int) $assoc_args['with-venues'];
			$this->generate_venues( [ $venue_quantity ] );
		}

		if ( ! empty( $assoc_args['with-organizers'] ) ) {
			$organizer_quantity = (int) $assoc_args['with-organizers'];
			$this->generate_organizers( [ $organizer_quantity ] );
		}

		$generator_args = $this->translate_assoc_args_to_generator_args(
			$assoc_args,
			$this->events_generator_translation_map
		);

		$quantity = isset( $args[0] ) ? (int) $args[0] : 1;

		$progress_bar = make_progress_bar( 'Creating events...', $quantity );
		$tick         = static function () use ( $progress_bar ) {
			$progress_bar->tick();
		};

		try {
			( new Event() )->create( $quantity, $generator_args, $tick );
			$progress_bar->finish();
		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}
		$event_attributes = [];
		foreach ( [ 'recurring', 'virtual' ] as $attribute ) {
			if ( ! empty( $generator_args[ $attribute ] ) ) {
				$event_attributes[] = $attribute;
			}
		}
		$event_attributes = count( $event_attributes ) ? implode( ', ', $event_attributes ) . ' ' : '';
		\WP_CLI::success(
			sprintf(
				'Generated %d %s%s',
				$quantity,
				$event_attributes,
				_n( 'event', 'events', $quantity )
			)
		);
	}

	/**
	 * Upload a set of test images to the site.
	 *
	 * ## OPTIONS
	 *
	 * [<quantity>]
	 * : The number of images to upload.
	 * ---
	 * default: 1
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp tec-test-data images generate
	 *     wp tec-test-data images generate 23
	 *
	 * @when after_wp_load
	 */
	public function generate_images( array $args = [], array $assoc_args = [] ) {
		$quantity  = isset( $args[0] ) ? (int) $args[0] : 1;
		$generator = new Utils();
		$map       = [
			// @todo update as we support more arguments.
		];

		$generator_args = [];

		foreach ( array_diff_key( $assoc_args, $map ) as $key => $value ) {
			$generator_args[ $map[ $key ] ] = $value;
		}

		$progress_bar = make_progress_bar( 'Importing images...', $quantity );
		$tick         = static function () use ( $progress_bar ) {
			$progress_bar->tick();
		};

		try {
			$generator->upload( $quantity, $args, $tick );
			$progress_bar->finish();
		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}
		\WP_CLI::success( "Uploaded {$quantity} " . _n( 'image', 'images', $quantity ) );
	}

	/**
	 * Create a set of test venues.
	 *
	 * ## OPTIONS
	 *
	 * [<quantity>]
	 * : The number of venues to generate.
	 * ---
	 * default: 1
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp tec-test-data venues generate
	 *     wp tec-test-data venues generate 23
	 *
	 * @when after_wp_load
	 */
	public function generate_venues( array $args = [], array $assoc_args = [] ) {
		$quantity  = isset( $args[0] ) ? (int) $args[0] : 1;
		$map       = [
			// @todo update as we support more arguments.
		];

		$generator_args = [];
		foreach ( array_diff_key( $assoc_args, $map ) as $key => $value ) {
			$generator_args[ $map[ $key ] ] = $value;
		}

		$progress_bar = make_progress_bar( 'Creating Venues...', $quantity );
		$tick         = static function () use ( $progress_bar ) {
			$progress_bar->tick();
		};

		try {
			( new Venue() )->create( $quantity, $args, $tick );
			$progress_bar->finish();
		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}
		\WP_CLI::success( "Generated {$quantity} " . _n( 'venue', 'venues', $quantity ) );
	}

	/**
	 * Create a set of test organizers.
	 *
	 * ## OPTIONS
	 *
	 * [<quantity>]
	 * : The number of organizers to generate.
	 * ---
	 * default: 1
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp tec-test-data organizers generate
	 *     wp tec-test-data organizers generate 23
	 *
	 * @when after_wp_load
	 */
	public function generate_organizers( array $args = [], array $assoc_args = [] ) {
		$map       = [
			// @todo update as we support more arguments.
		];

		$generator_args = [];
		foreach ( array_diff_key( $assoc_args, $map ) as $key => $value ) {
			$generator_args[ $map[ $key ] ] = $value;
		}

		$quantity  = isset( $args[0] ) ? (int) $args[0] : 1;
		$progress_bar = make_progress_bar( 'Creating Organizers...', $quantity );
		$tick         = static function () use ( $progress_bar ) {
			$progress_bar->tick();
		};

		try {
			( new Organizer() )->create( $quantity, $args, $tick );
			$progress_bar->finish();
		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}
		\WP_CLI::success( "Generated {$quantity} " . _n( 'organizer', 'organizers', $quantity ) );
	}

	/**
	 * Deletes event, venue and organizer data from the site.
	 *
	 * ## OPTIONS
	 *
	 * [--all]
	 * : Whether to delete all Event, Venue and Organizer data from the site on, or only the generated one.
	 * ---
	 * default:
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp tec-test-data delete
	 *     wp tec-test-data delete --all
	 *
	 * @when after_wp_load
	 */
	public function delete( array $args = [], array $assoc_args = [] ) {
		$utils = new Utils();
		if ( isset( $assoc_args['all'] ) ) {
			$utils->clear_all( 'on' );
			\WP_CLI::success('Deleted all Events, Venues and Organizers from the site.');
			return;
		}

		$utils->clear_generated( 'on' );
		\WP_CLI::success('Deleted all generated Events, Venues and Organizers from the site.');
	}

	/**
	 * Deletes all saved options and settings for TEC, TEC Widgets and TEC-related Transients from the db.ite.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp tec-test-data reset
	 *
	 * @when after_wp_load
	 */
	public function reset() {
		$utils = new Utils();
		$utils->reset_tec_settings( 'on' );

		\WP_CLI::success(
			'Deleted all saved options and settings for TEC, TEC Widgets and TEC-related transients from the db.'
		);
	}

	/**
	 * Translates the command associative arguments to the keys and format a generator will be able to consume.
	 *
	 * @since TBD
	 *
	 * @param array<string,string|float|int> $assoc_args      The command input associative arguments.
	 * @param array<string,string|float|int|array<string>> $translation_map The command input associative arguments.
	 *
	 * @return array<string,string|int|float> The generator args, translated from the command associative args.
	 */
	protected function translate_assoc_args_to_generator_args( array $assoc_args, array $translation_map ) {
		// Remove any argument that is not one supported by the Events generator.
		$generator_args  = array_intersect_key( $assoc_args, $translation_map );
		$translated_args = [];
		// Populate an array of generator arguments using the keys the generator supports.
		foreach ( $generator_args as $assoc_args_key => $value ) {
			// Allow for one associative argument to map to multiple generator keys.
			foreach( (array)$translation_map[ $assoc_args_key ] as $generator_key){
				$translated_args[ $generator_key ] = $value;
			}
		}

		return $translated_args;
	}

	/**
	 * Checks whether recurrence is supported, by means of The Events Calendar PRO plugin, or not.
	 *
	 * @since TBD
	 *
	 * @throws \WP_CLI\ExitException If the Main class of The Events Calendar PRO plugin cannot be found.
	 */
	protected function check_recurring_support() {
		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			return;
		}

		\WP_Cli::error( 'The "--recurring" option requires The  Events Calendar PRO plugin to be installed' );
	}
}
