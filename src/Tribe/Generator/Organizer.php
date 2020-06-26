<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

use Faker\Factory;

class Organizer {
	/**
	 * Creates randomly generated Organizers
	 *
	 * @since 1.0.0
	 * @param int $quantity
	 * @param array $args
	 * @return mixed
	 * @throws \Tribe__Repository__Usage_Error
	 */
	public function create( $quantity = 1, array $args = [] ) {
		for ( $i = 1; $i <= $quantity; $i++ ) {
			$organizers[] = tribe_organizers()->set_args( $this->random_organizer_data() )->create();
		}
		return $organizers;
	}

	/**
	 * Generating random data for each organizer.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function random_organizer_data() {

		$faker = Factory::create();

		$firstname = $faker->firstName;
		$lastname = rand( 0, 11 ) < 10 ? $faker->lastName : $faker->lastName . '-' . $faker->lastName;
		$website = tribe_strtolower( $lastname ) . '-qa.tri.be';
		$fullname = $firstname . ' ' . $lastname;
		$email = tribe_strtolower( $firstname ) . '@' . $website;
		$phone = $faker->phoneNumber;
		$description =
			$fullname . ' crafts event experiences that are more upbeat and modern than some other tenured companies. '
			. 'The team at ' . $lastname . ' & Co is a “creative-led experiential” event company that’s worked with luxury,'
			. ' travel, retail, technology, and other brand verticals for over ' . rand( 8, 20 ) . ' years.';

		$data = [
			'organizer' => $fullname,
			'phone' => $phone,
			'website' => 'https://' . $website,
			'email' => $email,
			'post_content' => $description,
			'post_status' => 'publish',
			'tribe_test_data_gen'=> '1'
		];

		return $data;
	}
}
