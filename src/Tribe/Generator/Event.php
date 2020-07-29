<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

use DateInterval;
use Faker\Factory;
use Tribe__Tickets__RSVP;
use WP_Query;
use Tribe__Date_Utils as Dates;

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
		$fromDate = empty( $args['fromDate'] ) ? '-1 month' : $args['fromDate'];
		$toDate = empty( $args['toDate'] ) ? '+1 month' : $args['toDate'];
		$is_virtual = empty( $args['virtual'] ) ? false : true;
		$is_recurring = empty( $args['recurring'] ) ? false : true;
		$recurring_type = empty( $args['recurring-type'] ) ? 'all' : $args['recurring-type'];
		$events = [];
		for ( $i = 1; $i <= $quantity; $i++ ) {
			$event = tribe_events()->set_args( $this->random_event_data( $fromDate, $toDate, $is_virtual, $is_recurring, $recurring_type) )->create();

			if( ! empty( $args['rsvp'] ) ) {
				$this->add_rsvp( $event );
			}

			if( ! empty( $args['ticket'] ) ) {
				$this->add_ticket( $event );
			}

			$events[] = $event;
		}
		return $events;
	}

	/**
	 * Generate pseudo-randomized Event data.
	 *
	 * @param string $fromDate
	 * @param string $toDate
     * @param boolean $is_virtual
     * @param boolean $is_recurring
     * @param string $recurring_type
	 * @since 1.0.0
	 * @return string[]
	 */
	public function random_event_data( $fromDate, $toDate, $is_virtual, $is_recurring, $recurring_type ) {
		$event_date = $this->generate_event_date_data( $fromDate, $toDate );
		$venue_id = $this->get_random_venue();
		$organizer_id = $this->get_random_organizer();
		$timezone = $this->determine_timezone($venue_id);
		$event_title = $this->generate_event_title();
		$event_description = $this->generate_event_description( $event_title, $organizer_id, $venue_id );
		$featured_image = $this->get_random_image_from_library();
		$category = '';
		$cost = '';
		$currency_symbol = '';
		$currency_position = 'prefix';
		$event_url = '';

		$random_event_data = [
			'post_title'         => $event_title,
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
			'post_content'       => $event_description,
			'_thumbnail_id'      => $featured_image,
			'post_status'        => 'publish',
			'tribe_test_data_gen'=> '1'
			];

		if( $is_virtual ) {
            $random_event_data = array_merge( $random_event_data, [
                '_tribe_events_is_virtual'                  => 'yes',
                '_tribe_events_virtual_embed_video'         => 'yes',
                '_tribe_events_virtual_linked_button'       => 'yes',
                '_tribe_events_virtual_show_embed_at'       => 'immediately',
                '_tribe_events_virtual_show_on_event'       => 'yes',
                '_tribe_events_virtual_show_on_views'       => 'yes',
                '_tribe_events_virtual_rsvp_email_link'     => 'yes',
                '_tribe_events_virtual_ticket_email_link'   => 'yes'
            ] );

		    if ( mt_rand(0,1) ) {
                $random_event_data = array_merge( $random_event_data, [
                    '_tribe_events_virtual_url'                 => 'https://www.youtube.com/watch?v=W74FxZwhisM',
                    '_tribe_events_virtual_linked_button_text'  => 'Watch Now'
                    ] );
            } else {
                $random_event_data = array_merge( $random_event_data, [
                    '_tribe_events_virtual_url'                 => '',
                    '_tribe_events_virtual_linked_button_text'  => 'Join Session',
                    '_tribe_events_zoom_display_details'        => 'yes',
                    '_tribe_events_zoom_meeting_data'           => 'a:13:{s:10:"created_at";s:20:"2019-09-05T16:54:14Z";s:8:"duration";i:60;s:7:"host_id";s:9:"AbcDefGHi";s:2:"id";i:1100000;s:8:"join_url";s:25:"https:\/\/zoom.us\/j\/1100000";s:8:"settings";a:20:{s:17:"alternative_hosts";s:0:"";s:13:"approval_type";i:2;s:5:"audio";s:4:"both";s:14:"auto_recording";s:5:"local";s:18:"close_registration";b:0;s:10:"cn_meeting";b:0;s:13:"enforce_login";b:0;s:21:"enforce_login_domains";s:0:"";s:24:"global_dial_in_countries";a:1:{i:0;s:2:"US";}s:22:"global_dial_in_numbers";a:3:{i:0;a:5:{s:4:"city";s:8:"New York";s:7:"country";s:2:"US";s:12:"country_name";s:2:"US";s:6:"number";s:13:"+1 1000200200";s:4:"type";s:4:"toll";}i:1;a:5:{s:4:"city";s:8:"San Jose";s:7:"country";s:2:"US";s:12:"country_name";s:2:"US";s:6:"number";s:13:"+1 6699006833";s:4:"type";s:4:"toll";}i:2;a:5:{s:4:"city";s:8:"San Jose";s:7:"country";s:2:"US";s:12:"country_name";s:2:"US";s:6:"number";s:12:"+1 408000000";s:4:"type";s:4:"toll";}}s:10:"host_video";b:0;s:10:"in_meeting";b:0;s:16:"join_before_host";b:1;s:15:"mute_upon_entry";b:0;s:17:"participant_video";b:0;s:30:"registrants_confirmation_email";b:1;s:7:"use_pmi";b:0;s:12:"waiting_room";b:0;s:9:"watermark";b:0;s:30:"registrants_email_notification";b:1;}s:10:"start_time";s:20:"2019-08-30T22:00:00Z";s:9:"start_url";s:75:"https:\/\/zoom.us\/s\/1100000?iIifQ.wfY2ldlb82SWo3TsR77lBiJjR53TNeFUiKbLyCvZZjw";s:6:"status";s:7:"waiting";s:8:"timezone";s:16:"America\/New_York";s:5:"topic";s:8:"API Test";s:4:"type";i:2;s:4:"uuid";s:24:"ng1MzyWNQaObxcf3+Gfm6A==";}',
                    '_tribe_events_zoom_meeting_id'             => '1100000',
                    '_tribe_events_zoom_join_url'               => 'https:\/\/zoom.us\/j\/1100000',
                    '_tribe_events_zoom_join_instructions'      => 'https:\/\/support.zoom.us\/hc\/en-us\/articles\/201362193-Joining-a-Meeting',
                    '_tribe_events_zoom_global_dial_in_numbers' => ['+1 1000200200' => 'US', '+1 6699006833' => 'US']
                ] );
            }
        }

		if( $is_recurring ) {
		    //Force recurring events creation in foreground
            $lots_of_them = static function () {
                return PHP_INT_MAX;
            };
            add_filter( 'tribe_events_pro_recurrence_small_batch_size', $lots_of_them );
            add_filter( 'tribe_events_pro_recurrence_batch_size', $lots_of_them );

		    $type = $this->get_recurrence_type( $recurring_type );
		    $count = $this->get_recurrence_count( $type );
			$recurrence_date = Dates::build_date_object( $event_date['start'] );

			$default_data = [
				'recurrence' => [
					'rules'  => [
						[
							'type'   => $type,
							'custom' => [
								'same-time'  => 'yes',
								'interval'   => '1'
							],
							'end-type'       => 'After',
							'end-count'      => $count
						],
					],
					'exclusions' => [],
					'description' => ""
				]
			];

			if ( 'Yearly' === $type )  {
				$month = $recurrence_date->format('n');
				$default_data['recurrence']['rules']['custom']['year'] = [
					'month'    => [ $month ],
					'same-day' => 'yes'
				];
			}

			if ( 'Weekly' === $type ) {
				$weekday = (int) $recurrence_date->format('w') + 1;
				$default_data['recurrence']['rules']['custom']['week'] = [ 'day' => [ $weekday ] ];
			}

			$random_event_data = array_merge( $random_event_data, $default_data );
        }

		return $random_event_data;
	}

    /**
     * Gets recurrence type
     *
     * @since 1.0.3
     * @param string $recurring_type
     * @return string
     */
	public function get_recurrence_type( $recurring_type ) {
        $faker = Factory::create();
	    $all_types = [ 'Daily', 'Weekly', 'Monthly', 'Yearly' ];
	    $type = 'Daily';

	    if( $recurring_type == "all" ) {
	        $type = $faker->randomElement( $all_types );
        } else {
	        switch( $recurring_type ) {
                case 'daily':
                    $type = 'Daily';
                    break;
                case 'weekly':
                    $type = 'Weekly';
                    break;
                case 'monthly':
                    $type = 'Monthly';
                    break;
                case 'yearly':
                    $type = 'Yearly';
            }
        }

	    return $type;
    }

    /**
     * Gets number of recurring instances.
     *
     * @since 1.0.3
     * @param string $type
     * @return string
     */
    public function get_recurrence_count( $type ) {
	    $count = '5';

	    switch( $type ) {
            case 'Weekly':
                $count = '8';
                break;
            case 'Monthly':
                $count = '10';
                break;
            case 'Yearly':
                $count = '2';
        }

        return $count;
    }

	/**
	 * Generate event dates and times.
	 *
	 * @param string $fromDate
	 * @param string $toDate
	 * @since 1.0.0
	 * @return array
	 */
	public function generate_event_date_data( $fromDate, $toDate ) {
		$faker = Factory::create();
		$all_day = $faker->optional(0.95, 'yes')->randomElement(['no']);
		if ( $all_day == 'no' ) {
			$start = $faker->dateTimeBetween( $fromDate, $toDate );
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

	/**
	 * Generates event title.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function generate_event_title() {
		$faker = Factory::create();
		$title = '';

		switch ( $faker->numberBetween( 1, 4 ) ) {
			case 1:
				$title = ucwords( $faker->bs );
				break;
			case 2:
				$title = $faker->catchPhrase;
				break;
			case 3:
				$title = $faker->randomElement( ['Discussing ', 'Talking ', 'Dissecting ', 'Analyzing '] ) . $faker->jobTitle;
				break;
			case 4:
				$title = $faker->name . $faker->randomElement( [' Discusses ', ' Talks ', ' Dissects ', ' Analyzes '] ) . $faker->jobTitle;
		}
		return $title;
	}

	/**
	 * Generate event description for WYSIWYG editor (post content).
	 *
	 * @since 1.0.0
	 * @param $event_title
	 * @param $organizer_id
	 * @param $venue_id
	 * @return string
	 */
	public function generate_event_description( $event_title, $organizer_id, $venue_id ) {
		$faker = Factory::create();
		$venue = tribe_venues()->by( 'ID', $venue_id )->first();
		$venue_name = empty( $venue ) ? 'The Venue' : $venue->post_title;
		$venue_meta_city = get_post_meta( $venue_id )['_VenueCity'][0];
		$venue_city = empty( $venue_meta_city ) ? 'your city' : $venue_meta_city;
		$organizer = tribe_organizers()->by( 'ID', $organizer_id )->first();
		$organizer_name = empty( $organizer ) ? 'a Premium Organizer' : $organizer->post_title;
		gc_collect_cycles();

		$description =
			'<p>' . $venue_name . ' hosts ' . $event_title . ', an event by ' . $organizer_name . ' coming to '
			. $venue_city . '! </p><p>' . $faker->realText( $faker->numberBetween( 200, 300 ) ) . '</p>';

		return $description;
	}

	/**
	 * Get random image from library
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_random_image_from_library() {
		$faker = Factory::create();
		$attachment_query = new WP_Query( [ 'post_type' => 'attachment',
			'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
			'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids' ] );

		$image_id = $faker->randomElement( $attachment_query->get_posts() );

		return $image_id;
	}

	/**
	 * Creates RSVP for Event.
	 *
	 * @since 1.0.0
	 *
	 * @param $event
	 */
	public function add_rsvp( $event ) {
		$data = [
			'ticket_name'             => 'Free Entrance',
			'ticket_description'      => 'RSVP to join us!',
			'ticket_show_description' => 'yes',
			'tribe-ticket'            => [
				'capacity'                => '70',
				'stock'                   => '70'
			],
		];

		tribe( 'tickets.rsvp' )->ticket_add( $event->ID, $data );
	}

	/**
	 * Creates Ticket for Event.
	 *
	 * @since 1.0.0
	 *
	 * @param $event
	 */
	public function add_ticket( $event ) {
		$provider = \Tribe__Tickets__Tickets::get_event_ticket_provider( $event->ID );

        // Prior to 4.12.2, ET will return a string rather than an instance.
        if ( is_string( $provider ) ) {
            $provider = new $provider;
        }

		// If we don't have a paid provider as default, bail.
		if ( Tribe__Tickets__RSVP::class === $provider->class_name ) {
			return;
		}

		$faker      = Factory::create();
		$price_list = [ 9.99, 15, 25, 35, 49.99, 75, 150 ];
		$type_list  = [ 'Standard', 'General', 'Basic', 'Student' ];
		$price      = $faker->randomElement( $price_list );
		$type       = ( $price > 70 ) ? 'VIP' : $faker->randomElement( $type_list );
		$capacity   = ( $price > 70 ) ? 50 : 100;
		$data       = [
			'ticket_name'             => $type,
			'ticket_price'            => $price,
			'ticket_description'      => 'Ticket for ' . $type . ' access to the event.',
			'ticket_show_description' => 'yes',
			'tribe-ticket'            => [
				'capacity'                => $capacity,
				'stock'                   => $capacity
			],
		];

		$provider->ticket_add( $event->ID, $data );
	}
}
