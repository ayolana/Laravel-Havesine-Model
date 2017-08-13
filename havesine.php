<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

    /*
     *  Calculate the distance between 2 points, in Laravel/PHP.
     *  @package Laravel-Havesine-Model
     *  @subpackage Models
     *  @version 0.0.1
     *  @from Ayo Lana
     *  @from Douglas Grubba
     *  @access public
     *  @copyright 2017 Ayo Lana
     *  @link http://ayolana.com
     */
    class Haversine extends Model {

        /**
         * name of the table
         *
         * @access  public
         * @var     string
         */
        protected $table;

        public function __construct()
        {
            parent::__construct();
        }

        /*
         *  find the n closest locations
         *  @param float $lat latitude of the point of interest
         *  @param float $lng longitude of the point of interest
         *  @param integer $max_distance max distance to search our from
         *  @param integer $max_locations max number of locations to return
         *  @param string $units miles|kilometers
         *  @param string $query filter results
         *  @return array
         */

        public static function closest($lat, $lng, $max_distance = 25, $max_locations = 10, $units = 'miles', $query = '')
        {

            /*
             *  Allow for changing of units of measurement
             */
            switch ( $units ) {
                default:
                case 'miles':
                    $gr_circle_radius = 3959;
                break;
                case 'kilometers':
                    $gr_circle_radius = 6371;
                break;
            }


            $disctance_select = sprintf(
                    "*, ( %d * acos( cos( radians(%s) ) " .
                            " * cos( radians( latitude ) ) " .
                            " * cos( radians( longitude ) - radians(%s) ) " .
                            " + sin( radians(%s) ) * sin( radians( latitude ) ) " .
                        ") " .
                    ") " .
                    "AS distance",
                    $gr_circle_radius,
                    $lat,
                    $lng,
                    $lat
                );


             return  DB::table('locations')
                ->select( DB::raw($disctance_select))
                ->having( 'distance', '<', $max_distance )                
                ->take( $max_locations )
                ->orderBy( 'distance', 'ASC' )
                ->get();

        }

    }