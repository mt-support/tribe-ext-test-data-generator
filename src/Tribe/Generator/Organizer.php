<?php
namespace Tribe\Extensions\Test_Data_Generator\Generator;

class Organizer {
    public function create( $quantity = 1, array $args = [] ) {
        $args      = [
            'post_status' => 'publish',
            'organizer' => 'NEW!! Test Organizer',
            'phone'     => '123-123-4567',
            'website'   => 'https://test.com',
            'email'     => 'bigcheese@test.com',
        ];
        for ( $i = 1; $i <= $quantity; $i++ ) {
            $organizers[] = tribe_organizers()->set_args( $args )->create();
        }
        return $organizers;
    }
}