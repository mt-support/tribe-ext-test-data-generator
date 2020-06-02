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
        $faker = Factory::create();
        $picsum_ids = [];
        for ( $i = 0; $i < $quantity; $i++ ) {
            do {
                $random_id = $faker->numberBetween(1, 1000);
            } while( in_array( $random_id, $picsum_ids ) );
            $picsum_ids[] = $random_id;
            $image_url = 'https://i.picsum.photos/id/' . $random_id . '/640/360.jpg';
            $uploads[] = tribe_upload_image($image_url);
        }
        return $uploads;
    }

    /**
     * Clear all Events-related data.
     *
     * @since 1.0.0
     * @param $clear_flag
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
    }
}
