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

/**
 * Class Command
 *
 * @since   1.0.1
 *
 * @package Tribe\Extensions\Test_Data_Generator\Cli
 */
class Command {
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
	 * ## EXAMPLES
	 *
	 *     wp tec-test-data events generate
	 *     wp tec-test-data events generate 23
	 *     wp tec-test-data events generate 23 --from-date="-1 year" --to-date=2020-12-31
	 *     wp tec-test-data events generate 23 --with-rsvp
	 *     wp tec-test-data events generate 23 --with-tickets
	 *     wp tec-test-data events generate 23 --with-venues 2 --with-organizers=5 --with-images=10
	 *
	 * @when after_wp_load
	 */
	public function generate_events( array $args = [], array $assoc_args = [] ) {
		$quantity  = isset( $args[0] ) ? (int) $args[0] : 1;
		$generator = new Event();
		$map       = [
			'from-date'    => 'fromDate',
			'to-date'      => 'toDate',
			'with-rsvp'    => 'rsvp',
			'with-tickets' => 'tickets',
		];

		if ( isset( $assoc_args['with-images'] ) ) {
			$images = (int) $assoc_args['with-images'];
			$this->generate_images( [ $images ] );
		}

		if ( isset( $assoc_args['with-venues'] ) ) {
			$venue_quantity = (int) $assoc_args['with-venues'];
			$this->generate_venues( [ $venue_quantity ] );
		}

		if ( isset( $assoc_args['with-organizers'] ) ) {
			$organizer_quantity = (int) $assoc_args['with-organizers'];
			$this->generate_organizers( [ $organizer_quantity ] );
		}

		$generator_args = [];
		foreach ( array_diff_key( $assoc_args, $map ) as $key => $value ) {
			$generator_args[ $map[ $key ] ] = $value;
		}
		try {
			$generator->create( $quantity, $args );
		} catch ( \Exception $e ) {
			\WP_CLI::error( $e->getMessage() );
		}
		\WP_CLI::success( "Generated {$quantity} " . _n( 'event', 'events', $quantity ) );
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
		try {
			$generator->upload( $quantity, $args );
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
		$generator = new Venue();
		$map       = [
			// @todo update as we support more arguments.
		];

		$generator_args = [];
		foreach ( array_diff_key( $assoc_args, $map ) as $key => $value ) {
			$generator_args[ $map[ $key ] ] = $value;
		}
		try {
			$generator->create( $quantity, $args );
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
		$quantity  = isset( $args[0] ) ? (int) $args[0] : 1;
		$generator = new Organizer();
		$map       = [
			// @todo update as we support more arguments.
		];

		$generator_args = [];
		foreach ( array_diff_key( $assoc_args, $map ) as $key => $value ) {
			$generator_args[ $map[ $key ] ] = $value;
		}
		try {
			$generator->create( $quantity, $args );
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
}
