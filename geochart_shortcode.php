<?php
/**
 * Plugin Name: Geochart Shortcode
 * Description: Integrates Google's Geochart visualization (https://developers.google.com/chart/interactive/docs/gallery/geochart) as a shortcode.
 * Version: 1.0
 * Author: Andrew Couch
 * Author URI: http://andrew-couch.com
 * License: GPL2
 */
/*  Copyright 2014  Andrew Couch  (email : info@andrew-couch.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
add_action( 'wp_enqueue_scripts', 'ggeo_register_script' );

//Set up the scripts so we can add them when the shortcode is sent.
//Geochart.js is exported instead of inline because both scripts will end up in the footer.
//Also it lets us cache that bit as well.
function ggeo_register_script() {
	wp_register_script( 'geochart-script-google', 'https://www.google.com/jsapi' );
  wp_register_script( 'geochart-script-local', plugins_url( '/geochart.js' , __FILE__ ), array('geochart-script-google'));
	}

//Short code function [geochart regions='' color='' width='' height='']
function ggeo_shortcode_func( $atts ) {
	extract( shortcode_atts( array(
		'regions' => 'USA:1980',
		'color' => '#ffaaff',
    'width' => '600',
    'height' => '500',
    'label' => 'Year First Visited'
	), $atts ) );

  //For usability, users give a comma seperated list of countries. Needs to be put into proper format for visualization.
  $region_array = explode(',', $regions);
  $region_array = array_map("to_array",$region_array);
  
  //Add the header row
  array_unshift($region_array, array("Country",$label));
  
  //Set up data arguments for visualization and output them and the scripts.
  $data_array = array('color' => $color,'regions' => $region_array, 'height'=>$height, 'width'=>$width);
	wp_enqueue_script( 'geochart-script-local' );
  wp_localize_script( 'geochart-script-local', 'geochart_locals', $data_array );

  //Add the div for the chart to go into.
	return "<div id='chart_div' style='width: $widthpx; height: $heightpx;'></div>";
}

//Helper function to create the array structure the visualization expects
function to_array($v)
{
  $newarray = explode(':', $v);
  $newarray[1] = intval($newarray[1]);
  
  return $newarray;
}
//Attach shortcode to function
add_shortcode( 'geochart', 'ggeo_shortcode_func' );
?>