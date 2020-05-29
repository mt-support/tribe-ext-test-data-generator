<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

use DateInterval;
use Faker\Factory;

class Event {

    /**
     * Creates randomly generated Events
     *
     * @param int $quantity
     * @param array $args
     * @return mixed
     * @throws \Tribe__Repository__Usage_Error
     */
    public function create( $quantity = 1, array $args = [] ) {
        for ( $i = 1; $i <= $quantity; $i++ ) {
            $events[] = tribe_events()->set_args( $this->random_event_data() )->create();
        }
        return $events;
    }

    /**
     * Generate pseudo-randomized Event data.
     *
     * @since 1.0.0
     * @return string[]
     */
    public function random_event_data() {
        $event_date = $this->generate_event_date_data();
        $venue_id = $this->get_random_venue();
        $organizer_id = $this->get_random_organizer();
        $timezone = $this->determine_timezone($venue_id);
        $featured_image = '';
        $category = '';
        $cost = '';
        $currency_symbol = '';
        $currency_position = 'prefix';
        $event_url = '';

        return [
            'post_title'         => 'Event #' . rand( 14, 287 ),
            'start_date'         => $event_date['start'],
			'end_date'           => $event_date['end'],
			'all_day'            => $event_date['all_day'],
			'timezone'           => $timezone,
			'venue'              => $venue_id,
			'organizer'          => $organizer_id,
			'category'           => $category,
			'cost'               => $cost,
			'currency_symbol'    => $currency_symbol,
			'currency_position'  => $currency_position,
			'show_map'           => '1',
			'show_map_link'      => '1',
			'url'                => $event_url,
			'featured'           => '0',
            'post_content'       => 'Lorem ipsum dolor sit amet',
            'post_image'         => $featured_image,
            'post_status'        => 'publish'
            ];
    }

    /**
     * Generate event dates and times.
     *
     * @since 1.0.0
     * @return array
     */
    public function generate_event_date_data() {
        $faker = Factory::create();
        $all_day = $faker->optional(0.95, 'yes')->randomElement(['no']);
        if ( $all_day == 'no' ) {
            $start = $faker->dateTimeBetween('-1 month', '+1 month');
            $start_formatted = rand( 0, 1 ) ? $start->format( 'Y-m-d H:00' ) : $start->format( 'Y-m-d H:30' );
            $end = rand( 0, 1 ) ? $start->add( new DateInterval( 'PT2H' ) ) : $start->add( new DateInterval( 'PT3H' ) );
            $end_formatted = rand( 0, 1 ) ? $end->format( 'Y-m-d H:00' ) : $end->format( 'Y-m-d H:30' );
        } else {
            $start_formatted = $end_formatted = $faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d 00:00');
        }

        return [
            'start'   => $start_formatted,
            'end'     => $end_formatted,
            'all_day' => $all_day
        ];
    }

    /**
     * Get random Organizer from db.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_random_organizer() {
        static $organizers_id = [];
        $faker = Factory::create();
        $organizers_args = [
            'posts_per_page' => -1,
        ];
        if ( empty( $organizers_id ) ) {
            $organizers_id = tribe_organizers()->by_args( $organizers_args )->get_ids();
        }
        return $faker->randomElement( $organizers_id );
    }

    /**
     * Get random Venue from db.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_random_venue() {
        static $venues_id = [];
        $faker = Factory::create();
        $venues_args = [
            'posts_per_page' => -1,
        ];
        if ( empty( $venues_id ) ) {
            $venues_id = tribe_venues()->by_args( $venues_args )->get_ids();
        }
        return $faker->randomElement( $venues_id );
    }

    /**
     * Determine timezone based on Venue.
     *
     * @since 1.0.0
     * @param $venue_id
     * @return string
     */
    public function determine_timezone( $venue_id ) {
        $venue_state = get_post_meta( $venue_id, '_VenueState' )[0];
        $timezone = 'America/New_York';
        switch( $venue_state ) {
            case 'California':
                $timezone = 'America/Los_Angeles';
                break;
            case 'Illinois':
                $timezone = 'America/Chicago';
                break;
        }
        return $timezone;
    }
}