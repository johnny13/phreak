<?php

/**
 *
 * Autogenerated with this command
 *
 * php phreak make:a phreak-cmd HarmonyPhreak
 *
 *
 * No sympathy for the devil; keep that in mind. Buy the ticket, take the ride...
 * and if it occasionally gets a little heavier than what you had in mind, well...
 * maybe chalk it off to forced conscious expansion:
 *      Tune in, freak out, get beaten.
 *
 *
 */

namespace App\Commands\Phreaks;

use OzdemirBurak\Iris\Color\Hex;

use Phim\Color;
use Phim\Color\RgbColor;
use Phim\Color\HslaColor;

use Phim\Color\Scheme\AnalogousScheme;
use Phim\Color\Scheme\ComplementaryScheme;
use Phim\Color\Scheme\SplitComplementaryScheme;
use Phim\Color\Scheme\TriadicScheme;
use Phim\Color\Scheme\TetradicScheme;
use Phim\Color\Scheme\SquareScheme;
use Phim\Color\Scheme\NamedMonochromaticScheme;
use Phim\Color\Scheme\HueRotationScheme;
use Phim\Color\Scheme\TintScheme;
use Phim\Color\Scheme\ShadeScheme;
use Phim\Color\Scheme\ToneScheme;

use MikeAlmond\Color\Color as MAColor;
use ourcodeworld\NameThatColor\ColorInterpreter as NameThatColor;
use League\CLImate\CLImate;

use App\Commands\Phreaks\TUI;

require_once 'libraries/roundcolor/PKRoundColor.php';
require_once 'libraries/AnsiToRgb.php';
require_once 'libraries/ANSI.php';

use PKRoundColor;
use AnsiToRgb;
use ANSI;

/**
 * This PHREAK Command [HarmonyPhreakPhreaks] provides the backend logic and scripts.
 * Often for a front facing HarmonyPhreakCommand, but not always. A phreak can be
 * by itself and not have a sister command.
 */

class HarmonyPhreaks
{

    /**
     *
     *  GLOBAL VARIABLES
     *
     *  NAME              EXAMPLE
     *  --------------|--------------------------------
     *
     *  $climate      =>  self::$climate->out('blah');
     *  $globalWidth  =>  $x = self::$globalWidth;
     *
     */

    public static $climate;
    public static $globalWidth  = 60;
    public static $counter = 1;
    public static $colornamefile = "libraries/colornames.json";

    /**
     * constructStatic
     *
     * HarmonyPhreak static constructor
     *
     * @return void
     */
    public static function __constructStatic()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        self::$climate = new CLImate;
    }

    public static function harmonyColors($primaryHex, $type)
    {
        $HD = array(); // Resulting Harmony Details

        $HEX = Color::get("#" . $primaryHex);

        $Harmony = array();

        switch ($type) {
            case 'comp':
                $Harmony["comp"]    = new ComplementaryScheme($HEX); //2 colors
                break;
            case 'analog':
                $Harmony["analog"]  = new AnalogousScheme($HEX); //3 colors
                break;
            case 'split':
                $Harmony["split"]   = new SplitComplementaryScheme($HEX); //3 colors
                break;
            case 'tri':
                $Harmony["tri"]   = new TriadicScheme($HEX); //3 colors
                break;
            case 'tetra':
                $Harmony["tetra"]   = new TetradicScheme($HEX); //4 colors
                break;
            case 'square':
                $Harmony["square"]   = new SquareScheme($HEX); //4 colors
                break;
            default:
                TUI::Message("invalid type!", "FAIL");
                break;
        }

        $Gradient = array();

        foreach ($Harmony as $iden => $group) {
            $HD[$iden] = array();

            foreach ($group as $c) {

                $rgb = $c->toRGB();
                $r   = $rgb->getRed();
                $g   = $rgb->getGreen();
                $b   = $rgb->getBlue();

                $hex  = ColorPhreaks::rgb2hex($r, $g, $b);

                $hexes[] = $primaryHex;

                if (strtolower($hex) !== strtolower($primaryHex)) {
                    $hsl  = ColorPhreaks::rgb2hsl($r, $g, $b);
                    $hsv  = ColorPhreaks::rgb2hsv($r, $g, $b);
                    $ansi = AnsiToRgb::toAnsi($r, $g, $b);
                    $name = self::getColorName($hex);

                    $HD[$iden][] = array(
                        "name" => $name,
                        "hex"  => $hex,
                        "hsl"  => $hsl,
                        "hsv"  => $hsv,
                        "rgb"  => array("r" => round($r), "g" => round($g), "b" => round($b)),
                        "ansi" => $ansi
                    );
                }
            }
        }

        return array("list" => $HD, "gradient" => $Gradient);
    }

    public static function harmonyReport($Groups, $PrimaryHex, $padding = 4)
    {
        $cRows  = array();
        $cInfo  = array();

        $pad = str_repeat(" ", $padding);

        foreach ($Groups as $K => $G) {
            $title = self::expandTitle($K);

            $hexes = array($PrimaryHex);

            $tLen = strlen($title);
            $pdLen = ceil(self::$counter * 2.5);
            $dLen = round(((68 - $pdLen) - $tLen) * 0.5);
            $dots = str_repeat(" •", $dLen);
            $preDots = str_repeat("• ", round($pdLen * 0.5));
            $cRows[] = "";
            $cRows[] = $pad . "  " . $preDots . "❪❨ " . strtoupper($title) . " ❩❫" . $dots;
            $cRows[] = "";

            foreach ($G as $cData) {
                $string = self::phreakColorBar();

                $hexes[] = $cData["hex"];

                $paddedHex = (strlen($cData["hex"]) < 7 ? $cData["hex"] . "   " : $cData["hex"]);

                $stringC = ANSI::color256($cData["ansi"]) . $string . ANSI::reset();

                $cInfo["hex"][] = self::propFmt("HEX", $paddedHex, "❶");
                $cInfo["rgb"][] = self::propFmt("RGB", $cData["rgb"]["r"] . ", " . $cData["rgb"]["g"] . ", " . $cData["rgb"]["b"], "❷");

                $cInfo["ansi"][] = $stringC;
            }

            self::$counter++;
        }

        $cRows[] = $pad . implode("  ", $cInfo["ansi"]);
        $cRows[] = $pad . implode("  ", $cInfo["ansi"]);
        $cRows[] = "";
        $cRows[] = $pad . implode("  ", $cInfo["hex"]);
        $cRows[] = $pad . implode("  ", $cInfo["rgb"]);
        $cRows[] = "";

        // GRADIENT
        $hexes[] = $PrimaryHex;
        $HexList = implode(" ", $hexes);
        $Gradient = "gradient -m rgb -i linear -W 76 -H 2 -c " . $HexList;
        exec($Gradient, $output);

        $cRows[] = $pad . "  " . $output[0];
        $cRows[] = $pad . "  " . $output[1];

        // Blank Spaces at the end
        $cRows[] = "";
        $cRows[] = "";
        //$cRows[] = TUI::generateLine(60);

        return $cRows;
    }

    public static function getColorComplement($hex, $amount = 180)
    {
        $complement   = Color::complement(Color::get("#" . $hex), $amount)->toRgb();
        $hex          = Color::toHexString($complement);
        $ansi         = AnsiToRgb::toAnsi($complement->getRed(), $complement->getGreen(), $complement->getBlue());
        $name         = self::getColorName($hex);

        $results = array(
            "name" => $name,
            "hex" => $hex,
            "ansi" => $ansi,
            "rgb" => array("r" => $complement->getRed(), "g" => $complement->getGreen(), "b" => $complement->getBlue())
        );

        return $results;
    }

    public static function getColorName($hex = false)
    {
        $name = false;
        $file = base_path(self::$colornamefile);

        if ($file !== false && is_file($file) && $hex !== false) {
            $JSON = json_decode(file_get_contents($file), true);

            foreach ($JSON as $j) {
                if ($j["hex"] === \strtolower("#" . $hex))
                    $name = $j["name"];
            }
        }

        if ($name === false && $hex !== false) {
            $baseColor    = MAColor::fromHex($hex);
            $instance     = new NameThatColor();
            $name         = $instance->name($baseColor);
        }

        if (is_array($name))
            $name = $name["name"];

        return $name;
    }

    /**
     * propFmt
     *
     * pads the string so things line up nicely
     *
     * @param  string $key
     * @param  string $value
     * @param  string $iden
     * @return string
     */
    public static function propFmt($key, $value, $iden = "⦿")
    {
        $paddedVal = str_pad($value, 13, " ");
        $string = "  " . $iden . " " . $key . " | " . $paddedVal;

        return $string;
    }

    /**
     * phreakColorBar
     *
     * silly function that generates a string to use for color display
     *
     * @param  string $pre
     * @param  string $post
     * @return string
     */
    public static function phreakColorBar($pre = "  ", $post = "")
    {
        $bars = array();
        // Generated via randomBarSmasher()
        // Trying to do this as unique each time results in
        // errors w/ printing certain chars. not worth the time sink.
        $bars[] = "███░░▒▓▓███████░▒▒▒▒█▒";
        $bars[] = "▒▒████████▓▓▒▒░░███░█";
        $bars[] = "██▒███▒▒▒▒░░░▓▓██▒▒██";
        $bars[] = "░░░██▒▒▓▓███▒████▒▒▒░";
        $bars[] = "▒▒░░▓▓████▒██▒▒████▒░";
        $bars[] = "██▓▓█▒░░░▒██▒░█▒█████";
        $bars[] = "████▒▒▒███▓▓░░███▒░█▒";
        $bars[] = "▒▒▒████░▓▓████░▒▒████";
        $bars[] = "██▒▒▒████▒██░░▓▓█████";
        $bars[] = "░░██▒████░▒▒▒▓▓██████";
        $bars[] = "█████░░███▒▒▓▓█████░█";
        $bars[] = "██▒▒▓▓░░████▒▒█████▒░";

        shuffle($bars);
        shuffle($bars);

        $choice = rand(0, (count($bars) - 1));
        $choice = (($choice < 1) ? $choice++ : $choice--);

        return $pre . $bars[$choice] . $post;
    }

    public static function randomBarSmasher($length = 21)
    {
        $solid = "█";
        $faded = "▒";
        $shade = "░";

        $len = $length - 2;

        $p1 = rand(1, round($len * .2));
        $p2 = rand($p1, ($p1 * 2));
        $p3 = rand($p2, ($p1 + $p2));

        $s1 = str_repeat($shade, $p1);
        $s2 = str_repeat($faded, $p2);
        $s3 = str_repeat($solid, $p3);

        $string = str_pad($s1 . $s2 . $s3, $len, $solid, STR_PAD_BOTH);

        return $shade . $string . $shade;
    }

    public static function expandTitle($string)
    {
        $result = "";
        switch ($string) {
            case 'comp':
                $result = "Complementary";
                break;
            case 'analog':
                $result = "Analogous";
                break;
            case 'tri':
                $result = "Triadic";
                break;
            case 'split':
                $result = "Split-Complementary";
                break;
            case 'tetra':
                $result = "Tetradic";
                break;
            case 'square':
                $result = "Square";
                break;
            default:
                $result = "Harmonious";
                break;
        }

        return $result;
    }

    /**
     * getAttr
     *
     * Get an Attribute
     *
     * @param  string $value
     *
     * @return string
     */
    public static function getAttr($value = "")
    {
        $value = ($value !== "" ? "zero" : "one");

        return $value;
    }
}

HarmonyPhreaks::__constructStatic();