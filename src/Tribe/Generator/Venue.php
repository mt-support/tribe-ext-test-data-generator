<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

use Faker\Factory;

class Venue {
	/**
	 * Creates randomly generated Venues.
	 *
	 * @since 1.0.0
	 * @since TBD Added support for the `$tick` parameter.
 *
	 * @param int                           $quantity The number of Venues to create.
	 * @param array<string,string|int|bool> $args     An array of arguments to customize the Venue creation.
	 * @param callable|null                 $tick     An optional callback that will be fired after each Venue creation;
	 *                                                the callback will receive the just created Venue post object as
	 *                                                argument.
	 *
	 * @return array<\WP_Post> An array of the generated Venues post objects.
	 *@throws \Tribe__Repository__Usage_Error If the arguments do not make sense in the context of the ORM Venue
	 *                                         creation.
	 *
	 */
	public function create( $quantity = 1, array $args = [], callable  $tick = null ) {
		for ( $i = 1; $i <= $quantity; $i++ ) {
			$venues[] = tribe_venues()->set_args( $this->random_venue_data() )->create();

			if ( is_callable( $tick ) ) {
				$tick( end( $venues ) );
			}
		}

		return $venues;
	}

	/**
	 * Generating random data for each venue.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function random_venue_data() {
		$faker = Factory::create();

		$venue = $this->generate_venue_name();
		$website = str_replace(' ', '', $venue);
		$website = tribe_strtolower( $website ) . '-qa.tri.be';
		$phone = $faker->phoneNumber;
		$address = $this->generate_venue_address();
		$description = $venue . ' is a multi-purpose space in ' . $address['city'] . ', ' . $address['state'] . ' with over ' . rand( 5,12 )
			. ' years of experience hosting events ranging from small & intimate occasions and classes to big crowd events and concerts.'
			. 'Winner of the ' . rand( 2012, 2020 ) . ' ' . $address['state'] . ' Venue Awards.';

		$data = [
			'venue' => $venue,
			'address' => $address['address'],
			'city' => $address['city'],
			'state' => $address['state'],
			'country' => $address['country'],
			'phone' => $phone,
			'website' => $website,
			'post_content' => $description,
			'_VenueShowMap'       => '1',
			'_VenueShowMapLink'   => '1',
			'post_status' => 'publish',
			'tribe_test_data_gen'=> '1'
			];

		return $data;
	}

	/**
	 * Generates random name for Venues
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function generate_venue_name() {
		$faker = Factory::create();
		$name = rand( 0, 1 ) ? $faker->lastName : 'The ' . $faker->lastName;
		switch ( rand( 1, 6 ) ) {
			case 1:
				$name = $name . ' Hall';
				break;
			case 2:
				$name = $name . ' Room';
				break;
			case 3:
				$name = $name . ' Cafe';
				break;
			case 4:
				$name = $name . ' Arena';
				break;
			case 5:
				$name = $faker->company;
				break;
			case 6:
				break;
		}
		return $name;
	}

	/**
	 * Generates pseudo-random, valid real-life Address for Venue
	 *
	 * @since 1.0.0
	 * @return string[]
	 */
	public function generate_venue_address() {

		switch ( rand( 1, 5 ) ) {
			case 1:
				$address = rand( 0, 1 ) ? rand( 25, 185 ) . ' Broadway' : rand( 18, 120 ) . ' 42nd St';
				$city = 'New York City';
				$state = 'New York';
				$country = 'United States';
				break;
			case 2:
				$address = rand( 0, 1 ) ? rand( 3, 547 ) . ' W Madison St' : rand( 2, 1150 ) . ' S Michigan Ave';
				$city = 'Chicago';
				$state = 'Illinois';
				$country = 'United States';
				break;
			case 3:
				$address = rand( 0, 1 ) ? rand( 1135, 2900 ) . ' Sunset Blvd' : rand( 4114, 5244 ) . ' Santa Monica Blvd';
				$city = 'Los Angeles';
				$state = 'California';
				$country = 'United States';
				break;
			case 4:
				$address = rand( 0, 1 ) ? rand( 346, 481 ) . ' Sunset Boulevard' : rand( 8556, 9777 ) . ' Wilshire Blvd';
				$city = 'Beverly Hills';
				$state = 'California';
				$country = 'United States';
				break;
			case 5:
				$address = rand( 0, 1 ) ? rand( 1200, 2499 ) . ' Lombard St' : rand( 13, 1335 ) . ' Columbus Ave';
				$city = 'San Francisco';
				$state = 'California';
				$country = 'United States';
				break;
		}

		return [
			'address' => $address,
			'city' => $city,
			'state' => $state,
			'country' => $country
		];
	}
}
