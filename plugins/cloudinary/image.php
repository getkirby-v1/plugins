<?php

/*
 * Simple cloudinary plugin for Kirby
 * 
 * Este puede ser usado para subir y manipular tus imagenes en la nuve con cloudinary.com. 
 * Recórta, crear thumbnails, crea sprites sheets, rota, detecta rostros, aplica efectos y mucho más de una forma muy sencilla.
 * Todas las imágenes son almacenadas y distribuidas por el mundo entero con los CDN administrador por cloudinary.
 * 
 * Si el plugin esta desactivado usa el pluging thumb como un respaldo
 *
 * Config:
 *
 * // set to false for you localhost config file
 * c::set('cloudinary.support', true);
 * c::set('cloudinary.cloudName', 'your_cloud_name');	
 * c::set('cloudinary.apiKey', 'your_api_key');
 * c::set('cloudinary.apiSecret', 'your_api_secret');
 *
 * Usage:
 * 
 * Creates and gets a thumbnail url of 200x200px using the cropping option
 * $url = imageURL($page->images()->first(), array('width' => 200, 'height' => 200, 'crop' => true);
 * Returns: http://res.cloudinary.com/YOUR_CLOUD_NAME/image/fetch/c_fill,h_200,w_200/http://yourkirbysite.com/a-page/a-image.jpg
 *
 * To see more image transformations/effects options see the cloudnary documentation: https://cloudinary.com/documentation
 *
 * Note: It require the Cloudinary php library, download from here: https://github.com/cloudinary/cloudinary_php
 *
 * Author: Alexander Ruiz <fenixkim@gmail.com>
 * License: MIT License
 * 
 */

load::plugins('cloudinary');

Cloudinary::config(array(
    "cloud_name" => c::get('cloudinary.cloudName'),
    "api_key" => c::get('cloudinary.apiKey'),
    "api_secret" => c::get('cloudinary.apiSecret')
));

function imageURL ($image, $options=array()) {
	
	$cloudinary = c::get('cloudinary.support');
	
	// Apply predefine settings here
	$defaults = array(
		"type" => "fetch",
		//"default_image" => "default",
		//'sharpen' => true
		"crop" => "fill",
	);
	
	$options = array_merge($defaults, $options);
	
	if ($w = a::get($options, 'width')) $options['width'] = floor($w);
	if ($h = a::get($options, 'height')) $options['height'] = floor($h);
	
	$crop = a::get($options, 'crop');
	
	if ($cloudinary) {
		// Validates variables for cloudinary mode
		
		// Validates the crop prperty: true is fill and false = fit.
		if (!is_string($crop)) $options['crop'] = $crop ? 'fill' : 'fit';
		
		return cloudinary_url($image->url(), $options);
		
	} else {
		// Validate variables for thumb mode
		if ($crop == 'limit') $options['crop'] = false;
	}
	
	return thumb($image, $options, false);
}