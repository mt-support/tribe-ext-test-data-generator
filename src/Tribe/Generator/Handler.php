<?php

namespace Tribe\Extensions\Test_Data_Generator\Generator;


class Handler {

	/**
	 * Will process a batch of TEC entity generation, and queue any future batches still needed.
	 *
	 * @since 1.2.0
	 *
	 * @param $params array<string,array> Key value of entity information, such as `quantity` of a particular entity to
	 *                create.
	 *
	 * @return bool Flag whether any updates were made or not.
	 */
	public function handle_generation( $params ): bool {
		$batch_size      = 50;
		$count_processed = 0;
		$organizers      = $params['organizers'] ?? [];
		$venues          = $params['venues'] ?? [];
		$events          = $params['events'] ?? [];
		$uploads         = $params['uploads'] ?? [];
		$did_something   = null;

		// Order matters?
		if ( ! empty( $organizers['quantity'] ) ) {
			// Slice and calculate the batch size - the remainder will be queued.
			$count                  = $organizers['quantity'] > $batch_size ? ( $batch_size - $count_processed ) : $organizers['quantity'];
			$count                  = $count > 0 ? $count : 0;
			$organizers['quantity'] -= $count;
			$count_processed        += $count;

			// Now create batch of items.
			$did_something = tribe( Organizer::class )->create( $count, $organizers );
		}

		if ( ! empty( $venues['quantity'] ) ) {
			// Slice and calculate the batch size - the remainder will be queued.
			$count              = $venues['quantity'] > $batch_size ? ( $batch_size - $count_processed ) : $venues['quantity'];
			$count              = $count > 0 ? $count : 0;
			$venues['quantity'] -= $count;
			$count_processed    += $count;

			// Now create batch of items.
			$did_something = tribe( Venue::class )->create( $count, $venues );
		}

		if ( ! empty( $events['quantity'] ) ) {
			// Slice and calculate the batch size - the remainder will be queued.
			$count              = $events['quantity'] > $batch_size ? ( $batch_size - $count_processed ) : $events['quantity'];
			$count              = $count > 0 ? $count : 0;
			$events['quantity'] -= $count;
			$count_processed    += $count;

			// Now create batch of items.
			$did_something = tribe( Event::class )->create( $count, $events );
		}

		if ( ! empty( $uploads['quantity'] ) ) {
			// Slice and calculate the batch size - the remainder will be queued.
			$count               = $uploads['quantity'] > $batch_size ? ( $batch_size - $count_processed ) : $uploads['quantity'];
			$count               = $count > 0 ? $count : 0;
			$uploads['quantity'] -= $count;
			$count_processed     += $count;

			// Now create batch of items.
			$did_something = tribe( Utils::class )->upload( $count, $uploads );
		}

		// Do we have more to process?
		if ( $count_processed >= $batch_size ) {
			$params = [
				[
					'organizers' => $organizers,
					'venues'     => $venues,
					'events'     => $events,
					'uploads'    => $uploads,
				]
			];
			// AS or WP Cron?
			if ( function_exists( 'as_enqueue_async_action' ) ) {
				as_enqueue_async_action( 'tec_ext_test_data_generator_handle_batch', $params );
			} else {
				wp_schedule_single_event( time() + 5, 'tec_ext_test_data_generator_handle_batch', );
			}
		}

		return (bool) $did_something;
	}
}
