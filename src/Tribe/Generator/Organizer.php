<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

use Faker\Factory;

class Organizer {
	/**
	 * Creates randomly generated Organizers.
	 *
	 * @since 1.0.0
	 * @since TBD Added support for the `$tick` parameter.
	 *
	 * @param int                           $quantity The number of Organizers to create.
	 * @param array<string,string|int|bool> $args     An array of arguments to customize the Organizer creation.
	 * @param callable|null                 $tick     An optional callback that will be fired after each Organizer creation;
	 *                                                the callback will receive the just created Organizer post object as
	 *                                                argument.
	 *
	 * @throws \Tribe__Repository__Usage_Error If the arguments do not make sense in the context of the ORM Organizer
	 *                                         creation.
	 *
	 * @return array<\WP_Post> An array of the generated Organizers post objects.
	 */
	public function create( $quantity = 1, array $args = [], callable $tick = null ) {
		for ( $i = 1; $i <= $quantity; $i++ ) {
			$organizers[] = tribe_organizers()->set_args( $this->random_organizer_data() )->create();

			if ( is_callable( $tick ) ) {
				$tick( end( $organizers ) );
			}
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
