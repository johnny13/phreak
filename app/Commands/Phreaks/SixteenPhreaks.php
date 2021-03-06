<?php

/**
 * SixteenPhreaks
 *
 * This PHREAK Command [SixteenPhreaks] provides the backend logic and scripts.
 * Often for a front facing SixteenCommand, but not always. A phreak can be
 * by itself and not have a sister command.
 *
 * @category Description
 * @package  Category
 * @author   Derek <derek@huement.com>
 * @license  MIT http://MITLicense.com
 * @link     https://huement.com
 */

//
// Autogenerated with this command
//
// php phreak make:a phreak-cmd Sixteen
//
// No sympathy for the devil, keep that in mind.
// Buy the ticket, take the ride...
// Tune in, freak out, get beaten.
//

namespace App\Commands\Phreaks;

use App\Commands\Phreaks\TUI;

use Dallgoot\Yaml;
use MikeAlmond\Color\Color as ColorTweak;
use League\CLImate\CLImate;

require_once 'libraries/AnsiToRgb.php';

use AnsiToRgb;

class SixteenPhreaks
{

    /**
     *  GLOBAL VARIABLES
     *
     *  NAME              EXAMPLE
     *  --------------|--------------------------------
     *
     *  $climate      =>  self::$climate->out('blah');
     *  $globalWidth  =>  $x = self::$globalWidth;
     */

    public static $climate;
    public static $globalWidth = 60;
    public static $B16Resources;

    /**
     * CONSTANTS
     */

    const B16_DIR = "palettes/base16/";


    /**
     * ConstructStatic
     *
     * Sixteen static constructor
     *
     * @return void
     */
    public static function _constructStatic()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        self::$climate = new CLImate;
        self::$B16Resources = resource_path(self::B16_DIR . "*.{yaml,yml,YAML,YML}");
    }

    public static function sixteenNameFormatter($string)
    {

        if (is_file($string)) {
            $bName = basename($string);
            $extLess = explode(".", $bName);
            $fix = $extLess[0];
        } else {
            $trim = strtolower(trim($string));
            $clean = str_replace(' ', '-', $trim);
            $fix = str_replace('_', '-', $clean);
        }

        return $fix;
    }


    /**
     * GetAttr
     *
     * Get an Attribute
     *
     * @param string $value
     *
     * @return string
     */
    public static function getAttr($value = "")
    {
        $value = ($value !== "" ? "zero" : "one");

        return $value;
    }

    /**
     * loadB16Theme
     *
     * @abstract Load YAML Theme file colors
     * @param    boolean $name    Load a specific Base16 theme based on the given name
     * @param    boolean $shuffle This shuffle applies to the theme COLORS themselves. If not name is given, a random (shuffled)
     *                            theme will chosen.
     * @return   array $results   CSS Color Strings & Background Color
     */
    public static function loadB16Theme($name = false, $shuffle = false)
    {
        $colors  = array();
        $file    = "";

        if ($name === false) {
            // Load a random theme
            $themes = glob(self::$B16Resources, GLOB_BRACE);

            $shoveOff = rand(3, 6);
            $shoveOn = rand(0, 2);
            while ($shoveOn <= $shoveOff) {
                shuffle($themes);
                $shoveOn++;
            }

            $file = $themes[0];
            $fName = self::sixteenNameFormatter($file);
        } else {
            // OR Load theme by name
            $file = resource_path(self::B16_DIR) . $name . ".yaml";
            $fName = $name;

            if (is_file($file) !== true) {
                TUI::Message("Invalid THEME name. Loading Random theme instead...", "WARN");
                self::loadB16Theme();
                return false;
            }
        }

        $yaml = Yaml::parseFile($file, 0, 0);

        $colors["00"] = $yaml->base00;
        $colors["01"] = $yaml->base01;
        $colors["02"] = $yaml->base02;
        $colors["03"] = $yaml->base03;
        $colors["04"] = $yaml->base04;
        $colors["05"] = $yaml->base05;
        $colors["06"] = $yaml->base06;
        $colors["07"] = $yaml->base07;
        $colors["08"] = $yaml->base08;
        $colors["09"] = $yaml->base09;
        $colors["0A"] = $yaml->base0A;
        $colors["0B"] = $yaml->base0B;
        $colors["0C"] = $yaml->base0C;
        $colors["0D"] = $yaml->base0D;
        $colors["0E"] = $yaml->base0E;
        $colors["0F"] = $yaml->base0F;

        $themeImg = resource_path(self::B16_DIR) . $fName . ".png";
        $img = (is_file($themeImg) ? $themeImg : false);

        $finalSixteen = array(
            "colors" => $colors,
            "file" => $file,
            "name" => $yaml->scheme,
            "image" => $img
        );

        // Now we take the Loaded & Parsed Base16 Theme Info
        // and convert it to command line ANSI codes
        $results = self::b16ToANSI($finalSixteen, $shuffle);

        return $results;
    }

    /**
     * loadBase16Theme : Used to generate a spectrum image
     *
     * @param [type] $themeName
     * @return void
     */
    public static function spectrumSorter($themeName, $shuffle = false, $displayLog = false)
    {
        $colors = array();
        $grays  = array();

        $file = resource_path(self::B16_DIR) . $themeName . ".yaml";
        $display_name = basename($file, ".yaml");

        if ($displayLog === true)
            TUI::Speaks("  <green>THEME</green> <light_blue>" . $display_name . "</light_blue>");

        $debug = 0;
        $yaml = Yaml::parseFile($file, 0, $debug);

        $grays[]  = $yaml->base00;
        $grays[]  = $yaml->base01;
        $grays[]  = $yaml->base02;
        $grays[]  = $yaml->base03;
        $grays[]  = $yaml->base04;
        $grays[]  = $yaml->base05;
        $grays[]  = $yaml->base06;
        $grays[]  = $yaml->base07;

        $colors[] = $yaml->base08;
        $colors[] = $yaml->base09;
        $colors[] = $yaml->base0A;
        $colors[] = $yaml->base0B;
        $colors[] = $yaml->base0C;
        $colors[] = $yaml->base0D;
        $colors[] = $yaml->base0E;
        $colors[] = $yaml->base0F;

        $bgColor  = $yaml->base00;

        if ($shuffle !== false)
            shuffle($colors);

        $final = array(
            "colors" => $colors,
            "grays" => $grays,
            "background" => $bgColor,
            "theme" => $display_name
        );

        return $final;
    }

    /**
     * b16ToANSI
     *
     * Foreach theme color transform HEX codes to approximated ANSI codes.
     *
     * @param  array $B16Data  Theme data from loadB16Theme
     * @param  mixed $shuffle  Should we shuffle the color codes?
     * @return array $rC       The converted ANSI codes, as well as all the original $B16Data
     */
    private static function b16ToANSI($B16Data, $shuffle = false)
    {

        // Check to make sure we are using valid Base16 data
        if (!isset($B16Data["colors"]) || count($B16Data["colors"]) < 3) {
            TUI::Message("Fatal error. No Base16 colors were passed!", "FAIL");
        }

        // baseSixteen Colors
        $sC = array();
        foreach ($B16Data["colors"] as $Key => $Color) {
            // Convert  Hex -> RGB -> ANSI
            $baseColor  = ColorTweak::fromHex($Color);
            $rgb        = $baseColor->getRgb();
            $sC[$Key]   = AnsiToRgb::toAnsi($rgb["r"], $rgb["g"], $rgb["b"]);
        }

        // if ($sC["00"] === 16) {
        //     $sC["00"] = 238;
        // }

        // ANSI Colors (Shuffled or Standard)
        if ($shuffle === true) {
            $aC = array($sC["08"], $sC["09"], $sC["0A"], $sC["0B"], $sC["0C"], $sC["0D"]);
            shuffle($aC);

            $mainColor   = $aC[0];
            $secondColor = $aC[1];
            $highColor   = $aC[2];
            $altColor    = $aC[3];
        } else {
            $mainColor   = $sC["08"];
            $secondColor = $sC["09"];
            $highColor   = $sC["0A"];
            $altColor    = $sC["0B"];
        }

        // Resulting Colors
        $rC           = array();
        $rC["main"]   = $mainColor;
        $rC["second"] = $secondColor;
        $rC["high"]   = $highColor;
        $rC["alt"]    = $altColor;
        $rC["bg"]     = $sC["00"];
        $rC["all"]    = $sC;
        $rC["name"]   = $B16Data["name"];
        $rC["file"]   = $B16Data["file"];
        $rC["hex"]    = $B16Data["colors"];

        return $rC;
    }

    public static function displayThemeImage($theme)
    {
        $themeImg = resource_path(self::B16_DIR) . $theme . ".png";

        if (is_file($themeImg) !== true) {
            TUI::Message("No theme image was found for: " . $themeImg, "FAIL");
        }

        TUI::cliImgDisplay($themeImg);
    }

    /**
     * loadAllBaseThemes
     *
     * @abstract Creates an array of all Base16 Themes locally stored in the colors directory.
     * @return   array of Theme Files.
     */
    public static function loadAllBaseThemes()
    {
        $themes = array();

        foreach (glob(self::$B16Resources, GLOB_BRACE) as $filename) {
            $yaml = Yaml::parseFile($filename, 0, 0);

            $fName = self::sixteenNameFormatter($filename);
            $themeImg = resource_path(self::B16_DIR) . $fName . ".png";
            $img = (is_file($themeImg) ? $themeImg : false);

            $themes[] = array(
                "name" => $yaml->scheme,
                "file" => $filename,
                "id" => $fName,
                "color" => $yaml->base08,
                "bg" => $yaml->base00,
                "image" => $img
            );
        }

        return $themes;
    }
}

SixteenPhreaks::_constructStatic();
