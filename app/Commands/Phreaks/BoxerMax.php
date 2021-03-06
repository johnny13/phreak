<?php

/**
 *
 * Autogenerated with this command
 *
 * php phreak make:a phreak-cmd BoxerMax
 * mv app/Commands/Phreaks/Wallpaper.php app/Commands/Phreaks/WallpaperPhreaks.php
 *
 * No sympathy for the devil; keep that in mind. Buy the ticket, take the ride...
 * and if it occasionally gets a little heavier than what you had in mind, well...
 * maybe chalk it off to forced conscious expansion:
 *      Tune in, freak out, get beaten.
 *
 *
 */

namespace App\Commands\Phreaks;

use App\Commands\Phreaks\TUI;

use League\CLImate\CLImate;
use Colors\Color;

/**
 * This PHREAK Command [CMDPhreaks] provides the backend logic and scripts.
 * Often for a front facing CMDPhreaksCommand, but not always. A phreak can be
 * by itself and not have a sister command.
 *
 * TODO: Add in support for colorizing the output.
 */

class BoxerMax
{

    // GLOBAL VARIABLES
    public $climate;                     // Used via $this->climate->out('blah');

    public $bWIDTH   = 60;               // The standard width for the ASCII box
    public $bPAD     = 0;                // Padding space inside the box
    public $bMARGIN  = 0;                // Margin space outside the box
    public $bINDENT  = "";               // Content Indent Amount

    public $topRows  = 0;
    public $mainRows = 0;
    public $boxWidth = 0;


    /**
     * construct
     *
     * CMDPhreaks constructor
     *
     * @return void
     */
    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        $this->climate = new CLImate;
    }

    public function figletTitle($output, $text, $mode = "rainbow")
    {
        // $cursor = new Cursor($output);
        // $cursor->moveUp(22);

        if ($mode === "rainbow") {
            exec("toilet --gay -f 'pagga' " . $text, $execOutput);
        } else {
            exec("figlet -s -w 65 -c -f 'pagga' " . $text, $execOutput);
        }

        foreach ($execOutput as $titleROW) {
            // if ($mode === "rainbow") {
            //     $cursor->moveRight(5);
            // }
            $output->write($titleROW . PHP_EOL);
        }

        //$cursor->moveDown(12);
    }

    // ---------------------------------------------------------
    //
    //  PHREAK BOX PHREAK BOX PHREAK BOX PHREAK BOX PHREAK BOX
    //
    // ---------------------------------------------------------

    public function generatePhreakBox()
    {
        // ----------------------------------------------------
        // GENERATE ROWS
        // ----------------------------------------------------

        // Top Section
        $HR_Top = $this->getParts("top");
        $HR_Mid = $this->repeatTopArea();
        $HR_Btm = $this->getParts("top_bottom");

        // Finalize Horizontal Top Rows
        $HR = array_merge($HR_Top, $HR_Mid, $HR_Btm);

        // Horizontal Bottom Rows
        $BHR = $this->getParts("bottom");

        // ----------------------------------------------------
        // PRINT ROWS
        // ----------------------------------------------------

        // Print Top Section
        $topRows = $this->buildRow($HR);

        // Print Content Section
        $midRows = $this->buildMainRows();

        // Print Bottom Section
        $btmRows = $this->buildRow($BHR);

        $final = array_merge($topRows, $midRows, $btmRows);

        return $final;
    }


    /**
     * getParts
     *
     * ???13???   ?????? ??????
     *
     * @param  string $area the type of parts requested. ie Top Middle etc
     * @return array  $parts contains the various strings used to generate the box.
     *                typically, a "beginning", "middle", and "end" area. where the "middle"
     *                section can be repeated over and over to allow the box to be resizable.
     */
    private function getParts($area)
    {
        switch ($area) {
            case 'top':
                $parts = array();
                $parts["top"] = array(
                    "b" => "????????????????????????????????????????????????????????????",
                    "m" => "???",
                    "e" => "????????????????????????????????????????????????????????????"
                );

                // "m" => array("???", "??????", "????????????", "????????????????????????"),
                $parts["top2"] = array(
                    "b" => "????????????????????????????????????????????????????????????",
                    "m" => "???",
                    "e" => "??????             ???????????????"
                );

                // "m" => array(" ", "  ", "    ", "        "),
                $parts["mid"] = array(
                    "b" => "???                   ",
                    "m" => " ",
                    "e" => "                  ??????"
                );

                // "m" => array(" ", "  ", "    ", "        "),
                $parts["mid2"] = array(
                    "b" => "??????                  ",
                    "m" => " ",
                    "e" => "                  ??????"
                );

                break;
            case 'top_bottom':
                $parts = array();

                $flipish = rand(30, 50);
                if ($flipish < 21) {
                    $parts["btm"] = array(
                        "b" => "????????????????????????????????????????????????????????????",
                        "m" => "???",
                        "e" => "????????????????????????????????????????????????????????????"
                    );
                    $parts["btm2"] = array(
                        "b" => " ??? ?????????????????????          ",
                        "m" => " ",
                        "e" => "????????????????????????          ??????"
                    );
                } else {
                    $parts["btm"] = array(
                        "b" => "????????????    ?????????????????????    ???",
                        "m" => "???",
                        "e" => "??? ?????????   ????????????????????????????????????"
                    );
                    $parts["btm2"] = array(
                        "b" => " ??? ??????????????????     ??????????????????",
                        "m" => " ",
                        "e" => "????????? ???????????????         ??????"
                    );
                }
                break;
            case 'top_repeat':
                $parts = array(
                    "b" => "??????                  ",
                    "m" => " ",
                    "e" => "                  ??????"
                );
                break;
            case 'main':
                $parts = array(
                    "in" => " ???",
                    "out" => "??? ",
                    "in2out" => "??????",
                    "out2in" => "??????",
                    "double" => array(
                        "b" => "??????",
                        "m" => "??????",
                        "e" => "??????"
                    )
                );
                break;
            case 'bottom':
                $parts = array();
                $parts[] = array(
                    "b" => "???????????? ???              ",
                    "m" => " ",
                    "e" => "              ??? ????????????"
                );
                $parts[] = array(
                    "b" => "????????????????????????????????????????????????????????????",
                    "m" => "???",
                    "e" => "????????????????????????????????????????????????????????????"
                );
                break;
            default:
                $parts = false;
                break;
        }

        if (!is_array($parts)) {
            TUI::Message("Invalid Part Request. Cannot continue", "FAIL");
        }

        return $parts;
    }

    private function buildMainRows()
    {
        $rows = array();

        $mainRows = $this->getMainRows();
        $boxWidth = $this->getBoxWidth();

        $rows = $this->repeatMainArea($mainRows, ($boxWidth + 36));

        foreach ($rows as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    private function buildRow($chars)
    {
        $rows = array();

        $boxWidth = $this->getBoxWidth();
        $topSpace = $boxWidth;

        foreach ($chars as $row) {
            $midSpace = str_repeat($row["m"], $topSpace);
            $rows[] = $row["b"] . $midSpace . $row["e"];
        }

        return $rows;
    }

    /**
     * repeatTopArea
     *
     * Generates top "title" area rows
     */
    private function repeatTopArea()
    {
        $topRows    = $this->getTopRows();
        $count      = 0;
        $Rows       = array();

        while ($count < $topRows) {
            $Rows[] = $this->getParts("top_repeat");
            $count++;
        }

        return $Rows;
    }

    /**
     * repeatMainArea
     *
     * Generates the "main content" rows
     * Uses the row builder functions: splitRowBuilder and assembleRow
     *
     * @param  int $rowCount
     * @param  int $rowWidth
     * @return array
     */
    // TODO rework the 3 x 33% row builder to be various
    // TODO combinations such as: 20% 60% 20% or 80% 20% 10%
    private function repeatMainArea($rowCount, $rowWidth)
    {
        $parts = $this->getParts("main");

        $spaces = str_repeat(" ", $rowWidth);

        $rows = array();
        $count = 0;

        if ($rowCount <= 6) {
            $rows = $this->assembleRow($parts, $spaces, $rowCount);
        } elseif ($rowCount > 6 && $rowCount <= 20) {
            $rows = $this->splitRowBuilder($parts, $spaces, $rowCount);
        } else {
            $third = ceil(($rowCount * 0.33));
            $twoThird = $rowCount - ($third * 2);
            $remainder = $rowCount - ($twoThird + $third);

            $rows1 = $this->splitRowBuilder($parts, $spaces, $third);
            $rows2 = $this->assembleRow($parts, $spaces, $twoThird);
            $rows3 = $this->splitRowBuilder($parts, $spaces, $remainder);

            $rows = array_merge($rows1, $rows2, $rows3);
        }

        return $rows;
    }


    /**
     * splitRowBuilder
     *
     * used in main content creation. instead of repeating one long pattern, breaks it into two
     * unique variations at a 50/50 split via the assembleRow function
     *
     * @param  mixed $parts
     * @param  mixed $spaces
     * @param  mixed $rowCount
     * @return array $rows
     */
    // TODO Swap out 50/50 split for random combinations, like 60/40 or 70/30
    private function splitRowBuilder($parts, $spaces, $rowCount)
    {
        $half = ceil(($rowCount * 0.5));
        $otherHalf = $rowCount - $half;

        $rows1 = $this->assembleRow($parts, $spaces, $half);
        $rows2 = $this->assembleRow($parts, $spaces, $otherHalf);

        $rows = array_merge($rows1, $rows2);

        return $rows;
    }

    /**
     * assembleRow
     *
     * Important psuedo-randomizing function for generating the "main content" area.
     * It can create two basic styles of rows, either a "double box" or a "in / out" layout.
     *
     * Each layout can subsequently have 3 different variations.
     * The variations are as follows: Left side alt, right side alt, or both alt.
     *
     * Alt simply means that side gets the alternate "double box" or "in / out" variant,
     * as opposed to the standard single straight line layout.
     *
     * This function does most of the work, all other row building functions call this
     *
     * @param  array  $parts Characters used to create the rows
     * @param  string $spaces Space between beginning and end
     * @param  int    $rowCount How many rows to create
     * @return array  $rows
     */
    private function assembleRow($parts, $spaces, $rowCount)
    {
        $rows   = array();
        $count  = 0;
        $flip   = rand(1, 100);
        $roll   = rand(1, 100);

        if ($flip <= 33) {
            // In / Out Layout
            if ($roll <= 33) {
                $top = $parts["in2out"] . $spaces . $parts["in"];
                $middle = $parts["out"] . $spaces . $parts["in"];
                $btm = $parts["out2in"] . $spaces . $parts["in"];
            } elseif ($roll >= 33 && $roll <= 66) {
                $top = $parts["in"] . $spaces . $parts["in2out"];
                $middle = $parts["in"] . $spaces .  $parts["out"];
                $btm = $parts["in"] . $spaces . $parts["out2in"];
            } else {
                $top = $parts["in2out"] . $spaces . $parts["in2out"];
                $middle = $parts["out"] . $spaces .  $parts["out"];
                $btm = $parts["out2in"] . $spaces . $parts["out2in"];
            }
        } elseif ($flip >= 34 && $flip <= 66) {
            // Double Box Layout
            if ($roll <= 33) {
                $top = $parts["double"]["b"] . $spaces . $parts["in"];
                $middle = $parts["double"]["m"] . $spaces . $parts["in"];
                $btm = $parts["double"]["e"] . $spaces . $parts["in"];
            } elseif ($roll >= 33 && $roll <= 66) {
                $top = $parts["in"] . $spaces . $parts["double"]["b"];
                $middle = $parts["in"] . $spaces . $parts["double"]["m"];
                $btm = $parts["in"] . $spaces . $parts["double"]["e"];
            } else {
                $top = $parts["double"]["b"] . $spaces . $parts["double"]["b"];
                $middle = $parts["double"]["m"] . $spaces . $parts["double"]["m"];
                $btm = $parts["double"]["e"] . $spaces . $parts["double"]["e"];
            }
        } else {
            if ($roll >= 50) {
                $top = $parts["in2out"] . $spaces . $parts["double"]["b"];
                $middle = $parts["out"] . $spaces . $parts["double"]["m"];
                $btm =  $parts["out2in"] . $spaces . $parts["double"]["e"];
            } else {
                $top = $parts["double"]["b"] . $spaces . $parts["in2out"];
                $middle = $parts["double"]["m"] . $spaces . $parts["out"];
                $btm = $parts["double"]["e"] . $spaces . $parts["out2in"];
            }
        }

        //echo " " . $flip . " " . $roll . PHP_EOL;

        $rows[] = $top;
        while ($count < $rowCount) {
            $rows[] = $middle;
            $count++;
        }
        $rows[] = $btm;

        return $rows;
    }

    // ----------------------------------------------------
    // GETTERS & SETTERS
    // ----------------------------------------------------

    /**
     * getAttr
     *
     * Get an Attribute
     *
     * @param  string $value
     *
     * @return string
     */
    public function getAttr($value = "")
    {
        $value = ($value !== "" ? "zero" : "one");

        return $value;
    }

    /**
     * Get the value of topRows
     */
    public function getTopRows()
    {
        return $this->topRows;
    }

    /**
     * Set the value of topRows
     *
     * @return  self
     */
    public function setTopRows($topRows)
    {
        $this->topRows = $topRows;

        return $this;
    }

    /**
     * Get the value of mainRows
     */
    public function getMainRows()
    {
        return $this->mainRows;
    }

    /**
     * Set the value of mainRows
     *
     * @return  self
     */
    public function setMainRows($mainRows)
    {
        $this->mainRows = $mainRows;

        return $this;
    }

    /**
     * Get the value of boxWidth
     */
    public function getBoxWidth()
    {
        return $this->boxWidth;
    }

    /**
     * Set the value of boxWidth
     *
     * @return  self
     */
    public function setBoxWidth($boxWidth)
    {
        $this->boxWidth = $boxWidth;

        return $this;
    }
}
