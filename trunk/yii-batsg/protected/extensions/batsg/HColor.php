<?php
class HColor
{
  const RGB_INDEX_RED = 0;
  const RGB_INDEX_GREEN = 1;
  const RGB_INDEX_BLUE = 2;

  const COLOR_BLACK = '#000000';
  const COLOR_BLUE = '#0000FF';
  const COLOR_CYAN = '#00FFFF';
  const COLOR_GRAY = '#808080';
  const COLOR_GREEN = '#008000';
  const COLOR_LIME = '#00FF00';
  const COLOR_MAGENTA = '#FF00FF';
  const COLOR_MAROON = '#800000';
  const COLOR_NAVY = '#000080';
  const COLOR_OLIVE = '#808000';
  const COLOR_ORANGE = '#FFA500';
  const COLOR_PINK = '#FFC0CB';
  const COLOR_PURPLE = '#800080';
  const COLOR_RED = '#FF0000';
  const COLOR_SILVER = '#C0C0C0';
  const COLOR_TEAL = '#008080';
  const COLOR_WHITE =	'#FFFFFF';
  const COLOR_YELLOW = '#FFFF00';

  private $_red = 0;
  private $_green = 0;
  private $_blue = 0;

  /**
   * Constructor.
   * @param mixed $color A HColor instance, an int[3] (r, g, b) or a hex string (with or without #).
   */
  public function __construct($color)
  {
    if ($color instanceof HColor) {
      $color = $color->toRgb();
    } else if (is_string($color)) {
      $color = self::hexToRgb($color);
    }
    $this->_red = $color[self::RGB_INDEX_RED];
    $this->_green = $color[self::RGB_INDEX_GREEN];
    $this->_blue = $color[self::RGB_INDEX_BLUE];
  }

  /**
   * Return array of (r, g, b).
   * @return int[3];
   */
  public function toRgb()
  {
    return array(
        self::RGB_INDEX_RED => $this->_red,
        self::RGB_INDEX_GREEN => $this->_green,
        self::RGB_INDEX_BLUE => $this-_blue,
    );
  }

  /**
   * Convert decimal number to two digits heximal number (adding leading zero if necessary).
   * @param int $number
   */
  protected static function decHex($number)
  {
    return sprintf('%02X', $number);
  }

  /**
   *
   * @param string $sharp Add leading sharp or not.
   * @return string
   */
  public function toHex($sharp = TRUE)
  {
    $colorHex = self::decHex($this->_red) . self::decHex($this->_green) . self::decHex($this->_blue);
    if ($sharp) {
      $colorHex = "#{$colorHex}";
    }
    return $colorHex;
  }

  public function __toString()
  {
    return $this->toHex();
  }

  /**
   * Convert a intensity color (value of 0~255) to int value.
   * @param mixed $color int or hex string (#8F for example)
   * @return int
   */
  private static function colorIntensityToInt($color)
  {
    // Remove # if color is hex.
    if (is_string($color) && $color[0] == '#') {
      $color = substr($color, 1, 2);
    }
    return (int) $color;
  }

  /**
   * Generate a random color intensity (0~255).
   * @param mixed $range If $range is NULL or an array ($min, $max), then a random value is generated,
   *                      othewise, it return the $range value itself.
   *                      If hex string is converted to int.
   * @return int
   */
  private static function generateRandomColorIntensity($range = NULL)
  {
    if ($range === NULL || is_array($range)) {
      if (is_array($range)) {
        $min = self::colorIntensityToInt($range[0]);
        $max = self::colorIntensityToInt($range[1]);
      } else {
        $min = 0;
        $max = 255;
      }
      $range = mt_rand($min, $max);
    }
    return self::colorIntensityToInt($range);
  }

  /**
   * Generate random number in hex (include #).
   * @param mixed $red Fix color (Int or hex). If NULL, then created randomly.
   * @param mixed $green Fix color (Int or hex). If NULL, then created randomly.
   * @param mixed $blue Fix color (Int or hex). If NULL, then created randomly.
   * @return string
   */
  public static function generateRandomColorHex($red = NULL, $green = NULL, $blue = NULL, $sharp = TRUE)
  {
    $hexColor = ($red === NULL && $green === NULL && $blue === NULL) ?
        sprintf('%06X', mt_rand(0, 0xFFFFFF)) :
        self::rgbToHex(self::generateRandomColorRgb($red, $green, $blue), FALSE);
    if ($sharp) {
      $hexColor = "#$hexColor";
    }
    return $hexColor;
  }

  /**
   * Generate random number in rgb.
   * @param mixed $red Fix color (Int or hex). If NULL, then created randomly.
   * @param mixed $green Fix color (Int or hex). If NULL, then created randomly.
   * @param mixed $blue Fix color (Int or hex). If NULL, then created randomly.
   * @return int[3]
   */
  public static function generateRandomColorRgb($red = NULL, $green = NULL, $blue = NULL)
  {
    $red = self::generateRandomColorIntensity($red);
    $green = self::generateRandomColorIntensity($green);
    $blue = self::generateRandomColorIntensity($blue);

    return array($red, $green, $blue);
  }

  /**
   * Remove sharp if exists.
   * @param string $hexColor
   * @return string
   */
  public static function removeSharpOfHex($hexColor)
  {
    if ($hexColor[0] == '#') {
      $hexColor = substr($hexColor, 1);
    }
    return $hexColor;
  }

  /**
   * Convert hex color (including # or not) to rgb color.
   * @param string $hexColor May contain sharp or not.
   * @return int[3]
   */
  public static function hexToRgb($hexColor)
  {
    $hexColor = self::removeSharpOfHex($hexColor);
    return array(
        hexdec(substr($hexColor, 0, 2)),
        hexdec(substr($hexColor, 2, 2)),
        hexdec(substr($hexColor, 4, 2))
    );
  }

  /**
   * Convert rgb color to hex color (with #).
   * @param int[3] $rgbColor
   * @return string
   */
  public static function rgbToHex($rgbColor, $sharp = TRUE)
  {
    $colorHex = self::decHex($rgbColor[self::RGB_INDEX_RED]) . self::decHex($rgbColor[self::RGB_INDEX_GREEN]) . self::decHex($rgbColor[self::RGB_INDEX_BLUE]);
    if ($sharp) {
      $colorHex = "#$colorHex";
    }
    return $colorHex;
  }

  /**
   * Convert $color to (r, g, b) if it is a hex.
   * @param mixed $color Hex color (including # or not) or (r, g, b).
   * @return int[3]
   */
  public static function getRgb($color)
  {
    if (is_string($color)) {
      $color = self::hexToRgb($color);
    }
    return $color;
  }

  /**
   * Calculate color brightness.
   * @param mixed $color A hex color (including # or not) or (r, g, b) color.
   * @return int
   */
  public static function colorBrightness($color)
  {
    $color = self::getRgb($color);
    $brightness = (($color[self::RGB_INDEX_RED] * 299) + ($color[self::RGB_INDEX_GREEN] * 587) + ($color[self::RGB_INDEX_BLUE] * 114)) / 1000;
    return $brightness;
  }

  /**
   * Check if a color is light color.
   * @param mixed $color A hex color (including # or not) or (r, g, b) color.
   * @return boolean TRUE if it is a light color.
   */
  public static function isLightColor($color)
  {
    return self::colorBrightness($color) > 130;
  }

  /**
   * Check if a color is dark color.
   * @param mixed $color A hex color (including # or not) or (r, g, b) color.
   * @return boolean TRUE if it is a dark color.
   */
  public static function isDarkColor($color)
  {
    return !self::isLightColor($color);
  }

  /**
   * Check if two colors are high contrast.
   * Reference: http://www.w3.org/WAI/ER/WD-AERT/#color-contrast
   * @param mixed $color1 Hex color (including # or not) or (r, g, b).
   * @param mixed $color2 Hex color (including # or not) or (r, g, b).
   * @return boolean TRUE if two colors are high contrast.
   */
  public static function isHighContrast($color1, $color2)
  {
    $color1 = self::getRgb($color1);
    $color2 = self::getRgb($color2);
    $brightnessDifference = self::colorBrightness($color1) - self::colorBrightness($color2);
    $colorDifferece =
      (max($color1[self::RGB_INDEX_RED], $color2[self::RGB_INDEX_RED]) - min($color1[self::RGB_INDEX_RED], $color2[self::RGB_INDEX_RED])) +
      (max($color1[self::RGB_INDEX_GREEN], $color2[self::RGB_INDEX_GREEN]) - min($color1[self::RGB_INDEX_GREEN], $color2[self::RGB_INDEX_GREEN])) +
      (max($color1[self::RGB_INDEX_BLUE], $color2[self::RGB_INDEX_BLUE]) - min($color1[self::RGB_INDEX_BLUE], $color2[self::RGB_INDEX_BLUE]));
    return abs($brightnessDifference) > 125 && abs($colorDifferece) > 500;
  }

  /**
   * Get a contrast color.
   * @param mixed $backgroundColor Hex color (including # or not) or (r, g, b).
   * @return string Hex color.
   */
  public static function getContrastTextColor($backgroundColor)
  {
    return self::isDarkColor($backgroundColor) ? self::COLOR_WHITE : self::COLOR_BLACK;
  }
}
?>