<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

use DateInterval;
use DateTimeZone;
use Faker\Factory;
use Tribe__Date_Utils as Dates;
use Tribe__Tickets__RSVP;
use Tribe__Timezones as Timezones;
use WP_Post;
use WP_Query;
use Tribe__Utils__Array as Arr;

class Event {
	/**
	 * Whether database transactions are enabled or not.
	 *
	 * @since TBD
	 *
	 * @var bool
	 */
	private static $post_transaction_supported;

	/**
	 * Creates randomly generated Events
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Added support for Virtual Events
	 * @since 1.0.3 Added support for Recurring Events
	 * @since 1.0.4 Added support for Event Category and Tag
	 *
	 * @param int                           $quantity The number of events to generate.
	 * @param array<string,string|int|bool> $args     The event generation arguments.
	 * @param callable|null                 $tick     An optional callback that will be fired after each Event creation;
	 *                                                the callback will receive the just created Event post object as
	 *                                                argument.
	 *
	 * @return array<\WP_Post> The generated events post objects.
	 *
	 * @throws \Tribe__Repository__Usage_Error If the arguments do not make sense in the context of the ORM event
	 *                                         creation.
	 */
	public function create( $quantity = 1, array $args = [], callable $tick = null ) {
		$from_date               = ! empty( $args['from_date'] ) ? $args['from_date'] : '-1 month';
		$to_date                 = ! empty( $args['to_date'] ) ? $args['to_date'] : '+1 month';
		$is_featured             = ! empty( $args['featured'] );
		$is_virtual              = ! empty( $args['virtual'] );
		$is_recurring            = ! empty( $args['recurring'] );
		$recurring_type          = ( $is_recurring && ! empty( $args['recurring_type'] ) ) ? $args['recurring_type'] : 'all';
		$custom_cat_arg          = isset( $args['custom_category'] ) ? array( $args['custom_category'] ) : [];
		$event_cat_arg           = isset( $args['event_category'] ) ? Arr::list_to_array( $args['event_category'] ) : [];
		$custom_tag_arg          = isset( $args['custom_tag'] ) ? array( $args['custom_tag'] ) : [];
		$event_tag_arg           = isset( $args['event_tag'] ) ? Arr::list_to_array( $args['event_tag'] ) : [];
		$content_length          = ! empty( $args['content_length'] ) ? $args['content_length'] : null;
		$event_category          = array_merge( $custom_cat_arg, $event_cat_arg );
		$event_tag               = array_merge( $custom_tag_arg, $event_tag_arg );
		$events                  = [];
		$fast_occurrences_insert = $args['fastOccurrencesInsert'] ?? false;

		for ( $i = 1; $i <= $quantity; $i++ ) {
			$event_payload = $this->random_event_data( $from_date, $to_date, $is_featured, $is_virtual,
				$is_recurring, $recurring_type, $event_category, $event_tag, $content_length );

			global $wpdb;

			// Override the default Occurrence creation behavior with the fast one, if required.
			if ( $fast_occurrences_insert && $this->is_post_transaction_supported() ) {
				$wpdb->query( 'START TRANSACTION' );

				add_filter( 'tec_events_pro_recurrence_update_commit', [$this,'fast_occurrence_insert'], 10, 3 );
			}

			$event = $this->granting_the_user_edit_caps( static function () use ( $event_payload ) {
				$event_post = tribe_events()->set_args( $event_payload )->create();

				if ( $event_post instanceof WP_Post ) {
					/*
					 * The code does not always get the duration right when the Event is generated across
					 * daylight-saving changes; here we handle that case.
					 */
					$timezone = Timezones::build_timezone_object( $event_payload['timezone']
					                                              ?? get_option( 'timezone_string' ) );

					$real_duration = Dates::immutable( $event_payload['end_date'], $timezone )->getTimestamp()
					                 - Dates::immutable( $event_payload['start_date'], $timezone )->getTimestamp();
					update_post_meta( $event_post->ID, '_EventDuration', $real_duration );
				}

				return $event_post;
			} );

			if( ! empty( $args['rsvp'] ) ) {
				$this->add_rsvp( $event );
			}

			if( ! empty( $args['ticket'] ) ) {
				$this->add_ticket( $event );
			}

			if ( is_callable( $tick ) ) {
				$tick( $event );
			}

			if ( $fast_occurrences_insert && $this->is_post_transaction_supported() ) {
				$wpdb->query( 'COMMIT' );
			}

			$events[] = $event;
		}

		return $events;
	}

	/**
	 * Generate pseudo-randomized Event data.
	 *
	 * @param string $from_date
	 * @param string $to_date
	 * @param boolean $is_virtual
	 * @param boolean $is_recurring
	 * @param string $recurring_type
	 * @param string $event_category
	 * @param string $event_tag
	 * @param null|int $content_length
	 *
	 * @since 1.0.0
	 * @since 1.0.5 Added Custom Event Category and Tag functionality
	 * @since TBD Adding content length param.
	 *
	 * @return string[]
	 */
	public function random_event_data(
		$from_date, $to_date, $is_featured, $is_virtual,
		$is_recurring, $recurring_type,
		$event_category, $event_tag,
		$content_length
	) {
		$event_date = $this->generate_event_date_data( $from_date, $to_date );
		$venue_id = $this->get_random_venue();
		$organizer_id = $this->get_random_organizer();
		$timezone = $this->determine_timezone($venue_id);
		$event_title = $this->generate_event_title();
		$event_description = $this->generate_event_description( $event_title, $organizer_id, $venue_id , $content_length);
		$featured_image = $this->get_random_image_from_library();
		$cost = '';
		$currency_symbol = '';
		$currency_position = 'prefix';
		$event_url = '';

		$random_event_data = [
			'post_title'          => $event_title,
			'start_date'          => $event_date['start'],
			'end_date'            => $event_date['end'],
			'all_day'             => $event_date['all_day'],
			'timezone'            => $timezone,
			'venue'               => $venue_id,
			'organizer'           => $organizer_id,
			'cost'                => $cost,
			'currency_symbol'     => $currency_symbol,
			'currency_position'   => $currency_position,
			'show_map'            => '1',
			'show_map_link'       => '1',
			'url'                 => $event_url,
			'featured'            => $is_featured,
			'post_content'        => $event_description,
			'_thumbnail_id'       => $featured_image,
			'post_status'         => 'publish',
			'tribe_test_data_gen' => '1'
		];

		$event_category = $event_category ? (array) $event_category : [ 'Generated' ];
		$event_tag = $event_tag ? (array) $event_tag : [ 'Automated' ];
		$random_event_data['category'] = $this->upsert_tax_terms( $event_category, 'tribe_events_cat' );
		$random_event_data['tag'] = $this->upsert_tax_terms( $event_tag, 'post_tag' );

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

			try {
				$generate_video_event = (bool) random_int( 0, 1 );
			} catch ( \Exception $e ) {
				// Not enough entropy to generate the random integer.
				$generate_video_event = true;
			}

			if ( $generate_video_event ) {
				$random_event_data = array_merge( $random_event_data, [
					'_tribe_events_virtual_url'                 => 'https://www.youtube.com/watch?v=W74FxZwhisM',
					'_tribe_events_virtual_linked_button_text'  => 'Watch Now'
					] );
			} else {
				$random_event_data = array_merge( $random_event_data, [
					'_tribe_events_virtual_url'                 => 'https:\/\/zoom.us\/j\/1100000',
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
			//Force recurring events creation in foreground.
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
				foreach ( $default_data['recurrence']['rules'] as $index => $rule ) {
					$rule['custom']['year'] = [
						'month'    => [ $month ],
						'same-day' => 'yes'
					];
					$default_data['recurrence']['rules'][ $index ] = $rule;
				}
			}

			if ( 'Weekly' === $type ) {
				$weekday = (int) $recurrence_date->format('w');
				foreach ( $default_data['recurrence']['rules'] as $index => $rule ) {
					$rule['custom']['week'] = [ 'day' => [ $weekday ] ];
					$default_data['recurrence']['rules'][ $index ] = $rule;
				}
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

		if( $recurring_type === "all" ) {
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
	 * @param string $from_date
	 * @param string $to_date
	 * @since 1.0.0
	 * @return array
	 */
	public function generate_event_date_data( $from_date, $to_date ) {
		$faker = Factory::create();
		$all_day = $faker->optional(0.95, 'yes')->randomElement(['no']);
		if ( $all_day === 'no' ) {
			$start = $faker->dateTimeBetween( $from_date, $to_date );
			$start_formatted = rand( 0, 1 ) ? $start->format( 'Y-m-d H:00' ) : $start->format( 'Y-m-d H:30' );
			$end = rand( 0, 1 ) ? $start->add( new DateInterval( 'PT2H' ) ) : $start->add( new DateInterval( 'PT3H' ) );
			$end_formatted = rand( 0, 1 ) ? $end->format( 'Y-m-d H:00' ) : $end->format( 'Y-m-d H:30' );
		} else {
			$start_formatted = $end_formatted = $faker->dateTimeBetween($from_date, $to_date)->format('Y-m-d 00:00');
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
		$venue_state = get_post_meta( $venue_id, '_VenueState' );
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
	 *
	 * @param $event_title
	 * @param $organizer_id
	 * @param $venue_id
	 * @param null|int $content_length The number of chars the content will create. By default generates 200-300 char length.
	 *
	 * @return string
	 */
	public function generate_event_description( $event_title, $organizer_id, $venue_id, $content_length = null ) {
		$faker           = Factory::create();
		$venue           = tribe_venues()->by( 'ID', $venue_id )->first();
		$venue_name      = empty( $venue ) ? 'The Venue' : $venue->post_title;
		$venue_meta_city = '';
		if ( $venue_meta = get_post_meta( $venue_id ) ) {
			$venue_meta_city = $venue_meta['_VenueCity'][0] ?? $venue_meta_city;
		}
		$venue_city     = empty( $venue_meta_city ) ? 'your city' : $venue_meta_city;
		$organizer      = tribe_organizers()->by( 'ID', $organizer_id )->first();
		$organizer_name = empty( $organizer ) ? 'a Premium Organizer' : $organizer->post_title;
		gc_collect_cycles();

		$char_size = is_numeric( $content_length ) ? (int) $content_length : $faker->numberBetween( 200, 300 );
		$content   = $char_size > 10 ? $faker->realText( $char_size ) : substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ), 0, $content_length );

		// If random, let's append stuff.
		if ( ! is_numeric( $content_length ) ) {
			$description =
				'<p>' . $venue_name . ' hosts ' . $event_title . ', an event by ' . $organizer_name . ' coming to '
				. $venue_city . '! </p><p>' . $content . '</p>';
		} else {
			$description = $content;
		}

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
		if ( ! tribe()->isBound( 'tickets.rsvp' ) ) {
			return;
		}

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
		add_post_meta( $event->ID, '_EventCost', '0' );
	}

	/**
	 * Creates Ticket for Event.
	 *
	 * @since 1.0.0
	 *
	 * @param $event
	 */
	public function add_ticket( $event ) {
		if ( ! class_exists( \Tribe__Tickets__Tickets::class ) ) {
			return;
		}

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
		add_post_meta( $event->ID, '_EventCost', $price );
	}

	/**
	 * Grants the user edit capabilities for the duration of a single call to, then, revoke them.
	 *
	 * @since 1.0.4
	 *
	 * @param callable $do The callback to call granting the user the edit caps.
	 *
	 * @return mixed The result of the callback.
	 */
	protected function granting_the_user_edit_caps( callable $do ) {
		// Grant the user the capability to assign terms only in the context of this request.
		$add_caps              = [
			get_taxonomy( 'tribe_events_cat' )->cap->assign_terms   => true,
			get_taxonomy( 'post_tag' )->cap->assign_terms           => true,
			get_post_type_object( 'tribe_events' )->cap->edit_posts => true,
			get_post_type_object( 'post' )->cap->edit_posts         => true,
		];
		$allow_term_assignment = static function ( array $all_caps ) use ( $add_caps ) {
			return array_merge( $all_caps, $add_caps );
		};

		add_filter( 'user_has_cap', $allow_term_assignment );

		$result = $do();

		remove_filter( 'user_has_cap', $allow_term_assignment );

		return $result;
	}

	/**
	 * Returns whether transactions on the posts-related tables are supported or not.
	 *
	 * @since TBD
	 *
	 * @return bool Whether transactions on the posts-related tables are supported or not.
	 */
	private function is_post_transaction_supported(): bool {
		if ( self::$post_transaction_supported === null ) {
			global $wpdb;
			$tables = [ $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships ];
			$engines = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s;",
					DB_NAME
				),
				ARRAY_A
			);

			self::$post_transaction_supported = count( array_filter( $engines, static function ( array $engine_info ) use ( $tables ): bool {
					return in_array( $engine_info['TABLE_NAME'], $tables, true ) && $engine_info['ENGINE'] === 'InnoDB';
				} ) ) === count( $tables );
		}

		return self::$post_transaction_supported;
	}

	/**
	 * Faster Recurring Event insertion by leveraging the transaction support of the database.
	 *
	 * @since TBD
	 *
	 * @param bool|null $commit  Whether to apply the default Occurrence save logic or not.
	 * @param int       $post_id The ID of the Event to insert the Occurrence for.
	 * @param array     $payload The payload defining the Occurrences to create, update and delete.
	 *
	 * @return bool Whether the Occurrence fast insert logic was applied or not.
	 *
	 * @throws \WP_CLI\ExitException If any one of the Occurrence fast insert logic steps failed.
	 */
	public function fast_occurrence_insert( bool $commit = null, int $post_id, array $payload ): bool {
		if ( $commit !== null ) {
			// Something else has already set the commit mode, so we don't need to do anything.
			return $commit;
		}

		global $wpdb;
		$first_post_fields = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE ID = %d", $post_id ),
			ARRAY_A
		);
		unset( $first_post_fields['ID'] );
		$first_post_fields['post_parent'] = $post_id;
		$first_post_meta = $wpdb->get_results(
			$wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d", $post_id ),
			ARRAY_A
		);
		$first_post_meta = array_filter( $first_post_meta, static function ( $meta ) {
			return ! in_array( $meta['meta_key'], [
				'_tribe_modified_fields',
				'_EventStartDate',
				'_EventEndDate',
				'_EventStartDateUTC',
				'_EventEndDateUTC',
				'_EventDuration',
				'_EventRecurrence',
			], true );
		} );
		$first_terms = $wpdb->get_results(
			$wpdb->prepare( "SELECT term_taxonomy_id, term_order FROM {$wpdb->term_relationships} WHERE object_id = %d", $post_id ),
			ARRAY_A
		);

		// This is a new Event, there will only be Occurrences to insert.
		$to_create = $payload['to_create'];
		$event_timezone = Timezones::build_timezone_object( get_post_meta( $post_id, '_EventTimezone', true ) );
		$utc = new DateTimeZone( 'UTC' );

		foreach ( $to_create as $k => $occurrence ) {
			[ $timestamp, $duration ] = array_values( $occurrence );
			if ( ! $wpdb->insert( $wpdb->posts, $first_post_fields, array_fill( 0, count( $first_post_fields ), '%s' ) ) ) {
				$wpdb->query( 'ROLLBACK' );
				\WP_CLI::error( 'Failed to insert Event post fields in transaction, try again not using the -fast-occurrences-insert option.' );
			}
			$inserted_post_id = $wpdb->get_var( 'SELECT LAST_INSERT_ID()' );

			if ( empty( $inserted_post_id ) ) {
				$wpdb->query( 'ROLLBACK' );
				\WP_CLI::error( 'Failed to fetch inserted post ID, try again not using the -fast-occurrences-insert option.' );
			}

			$event_meta = $first_post_meta;
			$start = Dates::immutable( $timestamp, $utc );
			$end = $start->add( new DateInterval( 'PT' . $duration . 'S' ) );
			$event_meta[] = [
				'meta_key'   => '_EventStartDate',
				'meta_value' => $start->setTimezone( $event_timezone )->format( Dates::DBDATETIMEFORMAT )
			];
			$event_meta[] = [
				'meta_key'   => '_EventEndDate',
				'meta_value' => $end->setTimezone( $event_timezone )->format( Dates::DBDATETIMEFORMAT )
			];
			$utc = new DateTimeZone( 'UTC' );
			$event_meta[] = [
				'meta_key'   => '_EventStartDateUTC',
				'meta_value' => $start->format( Dates::DBDATETIMEFORMAT )
			];
			$event_meta[] = [
				'meta_key'   => '_EventEndDateUTC',
				'meta_value' => $end->format( Dates::DBDATETIMEFORMAT )
			];
			$event_meta[] = [ 'meta_key' => '_EventDuration', 'meta_value' => $duration ];
			foreach ( $event_meta as $meta ) {
				$meta['post_id'] = $inserted_post_id;
				if ( ! $wpdb->insert( $wpdb->postmeta, $meta, array_fill( 0, count( $meta ), '%s' ) ) ) {
					$wpdb->query( 'ROLLBACK' );
					\WP_CLI::error( 'Failed to insert Event meta in transaction, try again not using the -fast-occurrences-insert option.' );
				}
			}

			foreach ( $first_terms as $term ) {
				$term['object_id'] = $inserted_post_id;
				if ( ! $wpdb->insert( $wpdb->term_relationships, $term, array_fill( 0, count( $term ), '%s' ) ) ) {
					$wpdb->query( 'ROLLBACK' );
					\WP_CLI::error( 'Failed to insert Event term relationships in transaction, try again not using the -fast-occurrences-insert option.' );
				}
			}
		}

		// Update the taxonomy counts.
		$inserted = count( $to_create );
		$tt_ids = implode( ',', array_map( 'absint', array_column( $first_terms, 'term_taxonomy_id' ) ) );
		if ( ! $wpdb->query( "UPDATE {$wpdb->term_taxonomy} set count = count + {$inserted} WHERE term_taxonomy_id in ($tt_ids)" ) ) {
			$wpdb->query( 'ROLLBACK' );
			\WP_CLI::error( 'Failed to update the term count in transaction, try again not using the -fast-occurrences-insert option.' );
		}

		return true;
	}

	private function upsert_tax_terms( array $terms, string $taxonomy ): array {
		$ids = [];

		if ( ! count( $terms ) ) {
			return [];
		}

		foreach ( $terms as $term ) {
			if ( ( $existing = get_term_by( 'name', $term, $taxonomy ) ) instanceof \WP_Term ) {
				$ids[] = $existing->term_id;
				continue;
			}

			$inserted = wp_insert_term( $term, $taxonomy );

			if ( $inserted instanceof \WP_Error ) {
				\WP_CLI::error( $inserted->get_error_message() );
			}

			$ids[] = $inserted['term_id'];
		}

		return $ids;
	}
}
