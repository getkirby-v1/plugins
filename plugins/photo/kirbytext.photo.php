<?php
    // A plugin for Kirby CMS, which reads EXIF data from photos and displays them below the photo.
    // Currently, the following EXIF attributes are displayed: Aperture, Focal Length, Shutter Speed, ISO.
    // Additionally, the user can specify a title and description (alt) for the photo which will be displayed as well.
    // Author: Simon Albrecht <hello at albrecht dot me>
    // Version: 1.0
    class kirbytextExtended extends kirbytext {
    
        // Constructor of the class
        function __construct($text = false, $markdown = true) {
            // Sets up the parent
            parent::__construct($text, $markdown);
            
            // Define the custom tag and it's attributes
            $this->addTags('photo');
            $this->addAttributes('class', 'title', 'alt', 'link');
        }
    
        // The function which handles our custom tags
        function photo($params) {                                                   
            $photo      = a::get($params, 'photo', '');
            $class      = a::get($params, 'class', '');
            $title      = a::get($params, 'title', '');
            $alt        = a::get($params, 'alt', '');
            $link       = a::get($params, 'link', '');
            
            $photo_url  = $this->url($photo);
            $local_url  = uri::raw($photo_url);

            // Use the file name as the title in case the title attribute is not set
            if (empty($title)) {
                $title = $photo;
            }

            if (!empty($photo) && !empty($photo_url)) {
                // Read the EXIF data
                $exif = $this->getExifData($local_url);
                                
                // Construct the HTML
                $html  = '';
                $html .= '<figure' . (!empty($class) ? (' class="' . $class . (!empty($link) ? ' linked' : '') . '"') : '') . '>';  
                $html .= '    ' . (!empty($link) ? ('<a href="' . $link . '">') : '') 
                                    . '<img src="' . $photo_url . '"' . (!empty($alt) ? (' alt="' . html($alt) . '"') : '') . '>' 
                                . (!empty($link) ? ('</a>') : '');
                $html .= '    <figcaption>';
                $html .= '        <p>' 
                                    . (!empty($title)   ? ('<span class="photo-title">' . html($title) . '</span>') : '')
                                    . (!empty($alt)     ? ('<span class="photo-alt">' . html($alt) . '</span>') : '')
                                    . (!empty($exif)    ? ('<span class="photo-exif">' . html($exif) . '</span>') : '') .
                                 '</p>';
                $html .= '    </figcaption>';
                $html .= '</figure>';

                return str::trim($html);
            } else return '';
        }
        
        // Reads the EXIF data from the photo
        function getExifData($photo = '') {
            if (function_exists('exif_read_data')) {
                $raw_data = exif_read_data($photo, 'EXIF', 0);
                                
                if (is_array($raw_data)) {
                    $make           = a::get($raw_data, 'Make', '');
                    $model          = a::get($raw_data, 'Model', '');
                    $aperture       = a::get($raw_data, 'FNumber', '');
                    $focal_length   = a::get($raw_data, 'FocalLength', '');
                    $exposure_time  = a::get($raw_data, 'ExposureTime', '');
                    $iso            = a::get($raw_data, 'ISOSpeedRatings', '');             

                    // Format the values
                    $aperture   = $this->formatExifValue($aperture);
                    $focal_length   = $this->formatExifValue($focal_length);
                    $exposure_time  = $this->formatExposureTime($exposure_time);

                    // Build EXIF string
                    $exif  = (!empty($make)             ? ($make . ' ') : '');
                    $exif .= (!empty($model)            ? ($model . ', ') : '');
                    $exif .= (!empty($focal_length)     ? ($focal_length . 'mm, ') : '');
                    $exif .= (!empty($aperture)         ? ('F/' . $aperture . ', ') : '');
                    $exif .= (!empty($exposure_time)    ? ($exposure_time . 's, ') : '');
                    $exif .= (!empty($iso)              ? ('ISO ' . $iso) : '');

                    return $exif;
                }
            }

            return '';
        }   

        function formatExifValue($value = '') {
            if (strpos($value, '/') !== false) {
                list($a, $b) = explode('/', $value);
                return ($a / $b);
            }

            return $value;
        }

        function formatExposureTime($exposure = '') {
            if (strpos($exposure, '/') !== false) {
                list($a, $b) = explode('/', $exposure);

                if ($a >= $b) {
                    return ($a / $b);
                }

                return implode('/', array(1, ($b / $a)));
            }

            return $exposure;
        }
    }
?>