<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

use Exception;
use Faker\Factory;

class Utils {

	/**
	 * Upload random images into Media Gallery
	 *
	 * @since 1.0.0
	 * @param int $quantity
	 * @param array $args
	 * @return mixed
	 */
	public function upload( $quantity = 1, array $args = [] ) {
		for ( $i = 0; $i < $quantity; $i++ ) {
			$image_url = 'https://picsum.photos/640/360' . '#' . bin2hex(random_bytes(16));
			$uploads[] = tribe_upload_image($image_url);
		}
		return $uploads;
	}

	/**
	 * Clear Events-related data generated by this extension.
	 *
	 * @since 1.0.0
	 * @param $clear_flag
	 * @return boolean
	 */
	public function clear_generated( $clear_flag ) {
		if( $clear_flag == 'on' ) {
			while( tribe_venues()->by( 'meta_like', 'tribe_test_data_gen' )->found() ) {
				tribe_venues()->by( 'meta_like', 'tribe_test_data_gen' )->delete();
			}
			while( tribe_organizers()->by( 'meta_like', 'tribe_test_data_gen' )->found() ) {
				tribe_organizers()->by( 'meta_like', 'tribe_test_data_gen' )->delete();
			}
			while( tribe_events()->by( 'meta_like', 'tribe_test_data_gen' )->found() ) {
				tribe_events()->by( 'meta_like', 'tribe_test_data_gen' )->delete();
			}
		}
		return true;
	}

	/**
	 * Clear ALL Events-related data existing on the site.
	 *
	 * @since 1.0.0
	 * @param $clear_flag
	 * @return boolean
	 */
	public function clear_all( $clear_flag ) {
		if( $clear_flag == 'on' ) {
			while( tribe_venues()->found() ) {
				tribe_venues()->delete();
			}
			while( tribe_organizers()->found() ) {
				tribe_organizers()->delete();
			}
			while( tribe_events()->found() ) {
				tribe_events()->delete();
			}
		}
		return true;
	}

    /**
     * Deletes all TEC-related rows from wp_options.
     *
     * @since 1.0.3
     * @param $reset_flag
     * @return bool
     */
	public function reset_tec_settings( $reset_flag ) {
	    $tec_options_list = [
            'external_updates-tribe-filterbar',
            'tribe_customizer',
            'tribe_events_calendar_options',
            'tribe_events_cat_children',
            'tribe_events_filters_current_active_filters',
            'tribe_feature_support_check_lock',
            'tribe_last_event_tickets_after_create_ticket',
            'tribe_last_generate_rewrite_rules',
            'tribe_last_save_post',
            'tribe_last_updated_option',
            'tribe_pue_key_notices',
            'widget_tribe-events-adv-list-widget',
            'widget_tribe-events-countdown-widget',
            'widget_tribe-events-list-widget',
            'widget_tribe-events-venue-widget',
            'widget_tribe-mini-calendar',
            'widget_tribe-this-week-events-widget'
        ];
	    $tec_transients_list = [
            '_transient__tribe_admin_notices',
            '_transient__tribe_geoloc_fix_needed',
            '_transient_timeout__tribe_admin_notices',
            '_transient_timeout__tribe_geoloc_fix_needed',
            '_transient_timeout_tribe_feature_detection',
            '_transient_tribe_events_shortcode_tribe_events_params_799245fc',
            '_transient_tribe_events_shortcode_tribe_events_params_c5cff3b2',
            '_transient_tribe_events_shortcode_tribe_events_params_e11815bb',
            '_transient_tribe_feature_detection',
            '_transient_tribe_ticket_prefix_pool'
        ];

	    if( tribe_is_truthy( $reset_flag )  ) {
	        foreach ( $tec_options_list as $option ) {
	            delete_option( $option );
            }

	        foreach ( $tec_transients_list as $transient ) {
	            delete_transient( $transient );
            }
        }

	    return true;
    }
}
