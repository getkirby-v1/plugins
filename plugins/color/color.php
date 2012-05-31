<?php

/**
* Returns as new color object.
* @param string $color
* @return color 
*/
function color($color) {
  return new color($color);
}

/**
* Color object, based on TinyColor.js
* @link https://github.com/bgrins/TinyColor
* @author Brian Grinstead (Original)
*/
class color extends obj {

  /**
   * Css named colors
   * @var array 
   */
  var $namedColors = array(
     'aliceblue' => 'f0f8ff',
     'antiquewhite' => 'faebd7',
     'aqua' => '0ff',
     'aquamarine' => '7fffd4',
     'azure' => 'f0ffff',
     'beige' => 'f5f5dc',
     'bisque' => 'ffe4c4',
     'black' => '000',
     'blanchedalmond' => 'ffebcd',
     'blue' => '00f',
     'blueviolet' => '8a2be2',
     'brown' => 'a52a2a',
     'burlywood' => 'deb887',
     'burntsienna' => 'ea7e5d',
     'cadetblue' => '5f9ea0',
     'chartreuse' => '7fff00',
     'chocolate' => 'd2691e',
     'coral' => 'ff7f50',
     'cornflowerblue' => '6495ed',
     'cornsilk' => 'fff8dc',
     'crimson' => 'dc143c',
     'cyan' => '0ff',
     'darkblue' => '00008b',
     'darkcyan' => '008b8b',
     'darkgoldenrod' => 'b8860b',
     'darkgray' => 'a9a9a9',
     'darkgreen' => '006400',
     'darkgrey' => 'a9a9a9',
     'darkkhaki' => 'bdb76b',
     'darkmagenta' => '8b008b',
     'darkolivegreen' => '556b2f',
     'darkorange' => 'ff8c00',
     'darkorchid' => '9932cc',
     'darkred' => '8b0000',
     'darksalmon' => 'e9967a',
     'darkseagreen' => '8fbc8f',
     'darkslateblue' => '483d8b',
     'darkslategray' => '2f4f4f',
     'darkslategrey' => '2f4f4f',
     'darkturquoise' => '00ced1',
     'darkviolet' => '9400d3',
     'deeppink' => 'ff1493',
     'deepskyblue' => '00bfff',
     'dimgray' => '696969',
     'dimgrey' => '696969',
     'dodgerblue' => '1e90ff',
     'firebrick' => 'b22222',
     'floralwhite' => 'fffaf0',
     'forestgreen' => '228b22',
     'fuchsia' => 'f0f',
     'gainsboro' => 'dcdcdc',
     'ghostwhite' => 'f8f8ff',
     'gold' => 'ffd700',
     'goldenrod' => 'daa520',
     'gray' => '808080',
     'green' => '008000',
     'greenyellow' => 'adff2f',
     'grey' => '808080',
     'honeydew' => 'f0fff0',
     'hotpink' => 'ff69b4',
     'indianred' => 'cd5c5c',
     'indigo' => '4b0082',
     'ivory' => 'fffff0',
     'khaki' => 'f0e68c',
     'lavender' => 'e6e6fa',
     'lavenderblush' => 'fff0f5',
     'lawngreen' => '7cfc00',
     'lemonchiffon' => 'fffacd',
     'lightblue' => 'add8e6',
     'lightcoral' => 'f08080',
     'lightcyan' => 'e0ffff',
     'lightgoldenrodyellow' => 'fafad2',
     'lightgray' => 'd3d3d3',
     'lightgreen' => '90ee90',
     'lightgrey' => 'd3d3d3',
     'lightpink' => 'ffb6c1',
     'lightsalmon' => 'ffa07a',
     'lightseagreen' => '20b2aa',
     'lightskyblue' => '87cefa',
     'lightslategray' => '789',
     'lightslategrey' => '789',
     'lightsteelblue' => 'b0c4de',
     'lightyellow' => 'ffffe0',
     'lime' => '0f0',
     'limegreen' => '32cd32',
     'linen' => 'faf0e6',
     'magenta' => 'f0f',
     'maroon' => '800000',
     'mediumaquamarine' => '66cdaa',
     'mediumblue' => '0000cd',
     'mediumorchid' => 'ba55d3',
     'mediumpurple' => '9370db',
     'mediumseagreen' => '3cb371',
     'mediumslateblue' => '7b68ee',
     'mediumspringgreen' => '00fa9a',
     'mediumturquoise' => '48d1cc',
     'mediumvioletred' => 'c71585',
     'midnightblue' => '191970',
     'mintcream' => 'f5fffa',
     'mistyrose' => 'ffe4e1',
     'moccasin' => 'ffe4b5',
     'navajowhite' => 'ffdead',
     'navy' => '000080',
     'oldlace' => 'fdf5e6',
     'olive' => '808000',
     'olivedrab' => '6b8e23',
     'orange' => 'ffa500',
     'orangered' => 'ff4500',
     'orchid' => 'da70d6',
     'palegoldenrod' => 'eee8aa',
     'palegreen' => '98fb98',
     'paleturquoise' => 'afeeee',
     'palevioletred' => 'db7093',
     'papayawhip' => 'ffefd5',
     'peachpuff' => 'ffdab9',
     'peru' => 'cd853f',
     'pink' => 'ffc0cb',
     'plum' => 'dda0dd',
     'powderblue' => 'b0e0e6',
     'purple' => '800080',
     'red' => 'f00',
     'rosybrown' => 'bc8f8f',
     'royalblue' => '4169e1',
     'saddlebrown' => '8b4513',
     'salmon' => 'fa8072',
     'sandybrown' => 'f4a460',
     'seagreen' => '2e8b57',
     'seashell' => 'fff5ee',
     'sienna' => 'a0522d',
     'silver' => 'c0c0c0',
     'skyblue' => '87ceeb',
     'slateblue' => '6a5acd',
     'slategray' => '708090',
     'slategrey' => '708090',
     'snow' => 'fffafa',
     'springgreen' => '00ff7f',
     'steelblue' => '4682b4',
     'tan' => 'd2b48c',
     'teal' => '008080',
     'thistle' => 'd8bfd8',
     'tomato' => 'ff6347',
     'turquoise' => '40e0d0',
     'violet' => 'ee82ee',
     'wheat' => 'f5deb3',
     'white' => 'fff',
     'whitesmoke' => 'f5f5f5',
     'yellow' => 'ff0',
     'yellowgreen' => '9acd32'
  );

  /**
   * Hue
   * @var float 
   */
  var $hue;

  /**
   * Saturation
   * @var float 
   */
  var $saturation;

  /**
   * Lightness
   * @var float 
   */
  var $lightness;

  /**
   * Opacity
   * @var float 
   */
  var $alpha;

  /**
   *
   * @param type $color
   * @return type 
   */
  function color($color) {
     $input = $color;
     if (is_object($color) && get_class($color) === 'color') {
        $this->hue = $color->hue;
        $this->saturation = $color->saturation;
        $this->lightness = $color->lightness;
        $this->alpha = $color->alpha;
        return;
     }

     if (is_string($color)) {
        $color = $this->parseString($color);
     }

     if (isset($color['a'])) {
        $this->alpha = $color['a'];
     } else {
        $this->alpha = 1;
     }

     if (isset($color['r']) && isset($color['g']) && isset($color['b'])) {
        $color = $this->rgbToHsl($color['r'], $color['g'], $color['b']);
     } else if (isset($color['l'])) {
        $color['s'] = $this->convertToPercentage($color['s']);
        $color['l'] = $this->convertToPercentage($color['l']);
        $color = $this->hslToHsl($color['h'], $color['s'], $color['l']);
     } else if (isset($color['v'])) {
        $color['s'] = $this->convertToPercentage($color['s']);
        $color['v'] = $this->convertToPercentage($color['v']);
        $color = $this->hsvToHsl($color['h'], $color['s'], $color['v']);
     }

     $this->hue = $color['h'];
     $this->saturation = $color['s'];
     $this->lightness = $color['l'];
  }

  /**
   * Turns a float into a percentage string
   * @param mixed $n
   * @return string 
   */
  private function convertToPercentage($n) {
     if ($n <= 1) {
        $n = ($n * 100) . "%";
     }
     return $n;
  }

  /**
   * Tidies HSL-based color into float array
   * @param number $hue
   * @param number $sat
   * @param number $light
   * @return array 
   */
  private function hslToHsl($hue, $sat, $light) {
     return array(
        'h' => $this->bound01($hue, 360),
        's' => $this->bound01($sat, 100),
        'l' => $this->bound01($light, 100)
     );
  }

  private function parseString($color) {
     $trimLeft = '/^[\s,#]+/';
     $trimRight = '/\s+$/';
     $color = preg_replace($trimLeft, '', $color);
     $color = preg_replace($trimRight, '', $color);
     $color = strtolower($color);

     $named = false;
     if (isset($this->namedColors[$color])) {
        $named = true;
        $color = $this->namedColors[$color];
     } else if ($color === 'transparent') {
        return array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0);
     }

     $CSS_INTEGER = "[-\\+]?\\d+%?";
     $CSS_NUMBER = "[-\\+]?\\d*\\.\\d+%?";
     $CSS_UNIT = "(?:" . $CSS_NUMBER . ")|(?:" . $CSS_INTEGER . ")";
     $PERMISSIVE_MATCH3 = "[\\s|\\(]+(" . $CSS_UNIT . ")[,|\\s]+(" . $CSS_UNIT . ")[,|\\s]+(" . $CSS_UNIT . ")\\s*\\)?";
     $PERMISSIVE_MATCH4 = "[\\s|\\(]+(" . $CSS_UNIT . ")[,|\\s]+(" . $CSS_UNIT . ")[,|\\s]+(" . $CSS_UNIT . ")[,|\\s]+(" . $CSS_UNIT . ")\\s*\\)?";

     $rgb = '/^rgb' . $PERMISSIVE_MATCH3 . '$/';
     $rgba = '/^rgba' . $PERMISSIVE_MATCH4 . '$/';
     $hsl = '/^hsl' . $PERMISSIVE_MATCH3 . '$/';
     $hsla = '/^hsla' . $PERMISSIVE_MATCH4 . '$/';
     $hsv = '/^hsv' . $PERMISSIVE_MATCH3 . '$/';
     $hex3 = '/^([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/';
     $hex6 = '/^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/';

     if (preg_match($rgb, $color, $match)) {
        return array('r' => $match[1], 'g' => $match[2], 'b' => $match[3]);
     } else if (preg_match($rgba, $color, $match)) {
        return array('r' => $match[1], 'g' => $match[2], 'b' => $match[3], 'a' => $match[4]);
     } else if (preg_match($hsl, $color, $match)) {
        return array('h' => $match[1], 's' => $match[2], 'l' => $match[3]);
     } else if (preg_match($hsla, $color, $match)) {
        return array('h' => $match[1], 's' => $match[2], 'l' => $match[3], 'a' => $match[4]);
     } else if (preg_match($hsv, $color, $match)) {
        return array('h' => $match[1], 's' => $match[2], 'v' => $match[3]);
     } else if (preg_match($hex6, $color, $match)) {
        return array(
           'r' => hexdec($match[1]),
           'g' => hexdec($match[2]),
           'b' => hexdec($match[3]),
           'format' => ($named ? 'name' : 'hex')
        );
     } else if (preg_match($hex3, $color, $match)) {
        return array(
           'r' => hexdec($match[1] . $match[1]),
           'g' => hexdec($match[2] . $match[2]),
           'b' => hexdec($match[3] . $match[3]),
           'format' => ($named ? 'name' : 'hex')
        );
     }

     return false;
  }

  // `hslToRgb`  
  // Converts an HSL color value to RGB.  
  // *Assumes:* h is contained in [0, 1] and s and l are contained [0, 1] or [0, 100]  
  // *Returns:* { r, g, b } in the set [0, 255]
  private function hslToRgb($hue, $sat, $light) {
     if ($sat == 0) {
        $r = $g = $b = $light; // achromatic
     } else {
        $q = $light < 0.5 ? $light * (1 + $sat) : $light + $sat - $light * $sat;
        $p = 2 * $light - $q;
        $r = $this->hue2rgb($p, $q, $hue + 1 / 3);
        $g = $this->hue2rgb($p, $q, $hue);
        $b = $this->hue2rgb($p, $q, $hue - 1 / 3);
     }

     return array('r' => $r * 255, 'g' => $g * 255, 'b' => $b * 255);
  }

  private function hue2rgb($p, $q, $t) {
     if ($t < 0)
        $t += 1;
     if ($t > 1)
        $t -= 1;
     if ($t < 1 / 6)
        return $p + ($q - $p) * 6 * $t;
     if ($t < 1 / 2)
        return $q;
     if ($t < 2 / 3)
        return $p + ($q - $p) * (2 / 3 - $t) * 6;
     return $p;
  }

  private function rgbToHsl($r, $g, $b) {

     $r = $this->bound01($r, 255);
     $g = $this->bound01($g, 255);
     $b = $this->bound01($b, 255);

     $max = max($r, $g, $b);
     $min = min($r, $g, $b);
     $h;
     $s;
     $l = ($max + $min) / 2;

     $d = $max - $min;

     if ($max === $min) {
        $h = 0; // achromatic
        $s = 0;
     } else {
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        switch ($max) {
           case $r:
              $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
              break;
           case $g:
              $h = ($b - $r) / $d + 2;
              break;
           case $b:
              $h = ($r - $g) / $d + 4;
              break;
        }
        $h /= 6;
     }
     return array('h' => $h, 's' => $s, 'l' => $l);
  }

  /**
   * @url: https://gist.github.com/1337890
   */
  private function hsvToHsl($hue, $sat, $val) {
     $hue = $this->bound01($hue, 360);
     $sat = $this->bound01($sat, 100);
     $val = $this->bound01($val, 100);

     // Hue stays the same
     $h = $hue;

     // Saturation is very different between the two color spaces
     if ((2 - $sat) * $val < 1) {
        $s = $sat * $val / ((2 - $sat) * $val);
     } else {
        $s = $sat * $val / (2 - (2 - $sat) * $val);
     }

     $l = ((2 - $sat) * $val) / 2;
     return array('h' => $h, 's' => $s, 'l' => $l);
  }

  /**
   * @url: https://gist.github.com/1337890
   */
  private function hslToHsv($hue, $sat, $light) {
     // Hue stays the same
     $h = $hue;

     if ($light < 0.5) {
        $sat *= $light;
     } else {
        $sat *= 1 - $light;
     }

     $s = 2 * $sat / ($light + $sat);

     $l = $light + $sat;
     return array('h' => $h, 's' => $s, 'l' => $l);
  }

  private function bound01($n, $max) {
     if (is_string($n) && strpos($n, '.') !== -1 && floatval($n) === 1) {
        // Need to handle 1.0 as 100%, since once it is a number, there is no difference between it and 1
        $n = "100%";
     }

     $processPercent = strpos($n, '%') !== false;
     $n = min($max, max(0, floatval($n)));

     //Automatically convert percentage into number
     if ($processPercent) {
        $n = intval($n * $max) / 100;
     }
     //Handle floating point rounding errors
     if (abs($n - $max) < 0.000001) {
        return 1;
     }

     //Convert into [0, 1] range if it isn't already
     return ($n % $max) / floatval($max);
  }

  /**
   * Make sure a float is between 0 and 1
   * @param number $val
   * @return number 
   */
  private function clamp01($val) {
     return min(1, max(0, $val));
  }

  private function toRgb() {
     return $this->hslToRgb($this->hue, $this->saturation, $this->lightness);
  }

  private function toHsl() {
     return array(
        'h' => $this->hue,
        's' => $this->saturation,
        'l' => $this->lightness
     );
  }

  function hex() {
     $rgb = $this->toRgb();
     $r = str_pad(dechex($rgb['r']), 2, '0', STR_PAD_LEFT);
     $g = str_pad(dechex($rgb['g']), 2, '0', STR_PAD_LEFT);
     $b = str_pad(dechex($rgb['b']), 2, '0', STR_PAD_LEFT);
     if ($r[0] === $r[1] && $r[0] === $r[1] && $r[0] === $r[1]) {
        return '#' . substr($r, 0, 1) . substr($g, 0, 1) . substr($b, 0, 1);
     }
     return '#' . $r . $g . $b;
  }

  function rgb() {
     $rgb = $this->toRgb();
     if ($this->alpha === 1) {
        return 'rgb(' . round($rgb['r']) . ', ' . round($rgb['g']) . ', ' . round($rgb['b']) . ')';
     } else {
        return 'rgba(' . round($rgb['r']) . ', ' . round($rgb['g']) . ', ' . round($rgb['b']) . ', ' . $this->alpha . ')';
     }
  }

  function hsl() {
     $hsl = $this->toHsl();
     $hsl['h'] *= 360;
     $hsl['s'] *= 100;
     $hsl['l'] *= 100;
     if ($this->alpha === 1) {
        return 'hsl(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%)';
     } else {
        return 'hsla(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%, ' . $this->alpha . ')';
     }
  }

  function __toString() {
     if ($this->alpha === 1) {
        return $this->hex();
     } else {
        return $this->rgb();
     }
  }

  /**
   * Get most constrasting color
   * @link http://24ways.org/2010/calculating-color-contrast
   * @author Brian Suda
   * @return \color
   */
  function contrast() {
     $rgb = $this->toRgb();
     $yiq = (($rgb['r'] * 299) + ($rgb['g'] * 587) + ($rgb['b'] * 114)) / 1000;
     return new color(($yiq >= 128) ? 'black' : 'white');
  }

  /**
   * Lightens a color by $amount
   * @param float $amount
   * @return \color 
   */
  function lighten($amount = .1) {
     $hsl = $this->toHsl();
     $hsl['h'] *= 360;
     $hsl['l'] += $amount;
     $hsl['l'] = $this->clamp01($hsl['l']);
     return new color($hsl);
  }

  /**
   * Darkens a color by $amount
   * @param float $amount
   * @return \color 
   */
  function darken($amount = 0.1) {
     return $this->lighten(- $amount);
  }

  /**
   * Changes saturation by $amount
   * @param float $amount
   * @return \color 
   */
  function saturate($amount = 0.1) {
     $hsl = $this->toHsl();
     $hsl['h'] *= 360;
     $hsl['s'] += $amount;
     $hsl['s'] = $this->clamp01($hsl['s']);
     return new color($hsl);
  }

  /**
   * Desaturates color by $amount
   * @param float $amount
   * @return \color 
   */
  function desaturate($amount = 0.1) {
     return $this->saturate(- $amount);
  }

  /**
   * Shifts hue
   * @param number $hue
   * @return \color 
   */
  function spin($hue = 10) {
     $hsl = $this->toHsl();
     $hsl['h'] *= 360;
     $hsl['h'] = ($hsl['h'] + $hue) % 360;
     return new color($hsl);
  }

  /**
   * Gets the complementary color (opposite hue).
   * @return \color
   */
  function complementary() {
     $hsl = $this->toHsl();
     $hsl['h'] *= 360;
     $hsl['h'] = ($hsl['h'] + 180) % 360;
     return new color($hsl);
  }

  /**
   * Gets two complementary colors, seperated by 80 degrees.
   * @return \obj 
   */
  function splitComplementary() {
     $hsl = $this->toHsl();
     $hsl['h'] *= 360;
     $hsl['h'] = ($hsl['h'] + 180) % 360;

     $colors = array(
        $this->spin(180 - 40),
        $this->spin(180 + 40)
     );
     return new obj($colors);
  }

  private function colorWheel($steps) {
     $hsl = $this->toHsl();
     $hsl['h'] = $hsl['h'] * 360;
     $stepOffset = 360 / $steps;
     $colors = array();
     for ($i = 0; $i < $steps; $i++) {
        $colors[] = $this->spin($stepOffset * $i);
     }
     return new obj($colors);
  }

  /**
   * Gets three colors: the current one, and the other two seperated by 120 degrees
   * @return \obj 
   */
  function triad() {
     return $this->colorWheel(3);
  }

  /**
   * Gets four colors: the current one, and the other two seperated by 90 degrees
   * @return \obj 
   */
  function tetrad() {
     return $this->colorWheel(4);
  }

  /**
   * Gets five colors: the current one, and the other two seperated by 72 degrees
   * @return \obj 
   */
  function pentagram() {
     return $this->colorWheel(5);
  }

  /**
   * returns a random color
   * @return \color 
   */
  static function random() {
     return new color(array(
                'r' => rand(0, 255),
                'g' => rand(0, 255),
                'b' => rand(0, 255)
             ));
  }

}

 