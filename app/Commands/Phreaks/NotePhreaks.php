<?php

/**
 *
 * Autogenerated with this command
 *
 * php phreak make:a phreak-cmd NotePhreak
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

use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\DataPhreaks;
use App\Commands\Phreaks\FilePhreaks;

use Carbon\Carbon;
use League\CLImate\CLImate;

/**
 * This PHREAK Command [NotePhreaks] provides the backend logic and scripts.
 * Often for a front facing NoteCommand, but not always. A phreak can be
 * by itself and not have a sister command.
 */

class NotePhreaks
{

    /**
     *  GLOBAL VARIABLES
     */

    public static $climate;

    public static $globalWidth  = 60;

    public static $Notebooks = array();

    /**
     * constructStatic
     *
     * NotePhreak static constructor
     *
     * @return void
     */
    public static function __constructStatic()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        self::$climate = new CLImate;
    }


    public static function getNotebookData($id = 1)
    {
        // @TODO: add in some error checking / validation

        $RowData = DataPhreaks::getRowByID("notebooks", $id);

        $result = array(
            "id" => $RowData->id,
            "notebook_id" => $RowData->notebook_id,
            "name" => $RowData->name
        );

        return $result;
    }


    /**
     * noteLister
     *
     * TODO: splitLine is hella helpful. Replace all the top and bottom shit with the splitLine like in notebookLister.
     *
     * @param  mixed $notes
     * @return void
     */
    public static function noteLister($notes)
    {
        $r = array();

        foreach ($notes as $n) {

            if (!isset(self::$Notebooks[$n["notebook_id"]]))
                self::$Notebooks[$n["notebook_id"]] = self::getNotebookData($n["notebook_id"]);

            // PREVIEW
            if (strlen($n["content"]) > 22) {
                //$textTop = substr($n["content"], 0, 23);
                $tTA = TUI::phreakWordwrap($n["content"], 23, "\n");
                $parts = explode("\n", $tTA);
                $textTop = self::evenOut($parts[0], 23);
                $tBtm = $parts[1];
                $textBtm = (strlen($tBtm) > 23) ? substr($tBtm, 0, 23) . '...' : self::evenOut($tBtm, 23);
            } else {
                $textTop = self::evenOut($n["content"], 23);
                $textBtm = self::evenOut(" ", 23);
            }

            // TAGS
            $n["nb_name"] = self::$Notebooks[$n["notebook_id"]]["name"];

            $tagTop = (strlen($n["nb_name"]) > 9) ? substr($n["nb_name"], 0, 7) . "..." : self::evenOut($n["nb_name"], 10);
            $tagBtm = (strlen($n["tags"]) > 9) ? substr($n["tags"], 0, 7) . "..." : self::evenOut($n["tags"], 10);

            // AUTHOR
            if (strlen($n["author"]) > 9) {
                $authorTop = substr($n["author"], 0, 9);
                $authorBtm = (strlen($n["author"]) > 19) ? substr($n["content"], 9, 16) . '...' : self::evenOut(substr($n["content"], 9, 19), 10);
            } else {
                $authorTop = self::evenOut($n["author"], 10);
                $authorBtm = self::evenOut(" ", 10);
            }

            // DATE
            $dateTop = self::evenOut(Carbon::parse($n["created"])->format('M.d y'), 9);
            $dateBtm = " ";

            if (isset($n["updated"]) && strlen($n["updated"] > 5))
                $dateBtm = Carbon::parse($n["updated"])->diffForHumans();

            $dateBtm = self::evenOut($dateBtm, 9);

            $se = "   ";
            $sp = " • ";
            $topROW = self::evenOut($n["id"], 2) . $se . $textTop . $sp . strtoupper($tagTop) . $sp . $authorTop . $sp . $dateTop;

            $btmROW = "  " . $se . $textBtm . $sp . $tagBtm . $sp . $authorBtm . $sp . $dateBtm;

            $r[] = $topROW;
            $r[] = $btmROW;
            $r[] = " ";
        }

        //$results = implode(PHP_EOL, $r);

        return $r;
    }

    /**
     * notebookLister
     *
     * TODO: Finish this function. Add in formatted Dates. Possibly more...
     *
     * @param  mixed $nbData
     * @return void
     */
    public static function notebookLister($nbData)
    {
        $r = array();

        foreach ($nbData as $n) {

            // PREVIEW
            $topandbtm = self::splitLine($n["name"], 23);
            $tagstopbtm = self::splitLine($n["tags"], 10);
            $authortopbtm = self::splitLine($n["author"], 10);

            $textTop    = $topandbtm["top"];
            $tagTop     = $tagstopbtm["top"];
            $authorTop  = $authortopbtm["top"];
            $dateTop    = "";

            $textBtm    = $topandbtm["btm"];
            $tagBtm     = $tagstopbtm["btm"];
            $authorBtm  = $authortopbtm["btm"];
            $dateBtm    = "";


            $se = "   ";
            $sp = " • ";
            $topROW = self::evenOut($n["id"], 2) . $se . $textTop . $sp . $tagTop . $sp . $authorTop . $sp . $dateTop;

            $btmROW = "  " . $se . $textBtm . $sp . $tagBtm . $sp . $authorBtm . $sp . $dateBtm;

            $r[] = $topROW;
            $r[] = $btmROW;
            $r[] = " ";
        }

        return $r;
    }

    /**
     * splitLine
     *
     * Split one long line into two lines, with padded spacing.
     *
     * @param  string $string that is going to be split at given $length
     * @param  integer $length how long each string is limited to
     * @return array top and bottom text results
     */
    public static function splitLine($string, $length = 23)
    {
        $textTop = "";
        $textBtm = "";

        $ellipse = "...";
        $limit = $length - 1;

        if (strlen($string) > $limit) {
            $tTA        = TUI::phreakWordwrap($string, $length, "\n");
            $parts      = explode("\n", $tTA);
            $textTop    = self::evenOut($parts[0], $length);
            $tBtm       = $parts[1];
            $textBtm    = (strlen($tBtm) > $length) ? substr($tBtm, 0, ($length - 3)) . $ellipse : self::evenOut($tBtm, $length);
        } else {
            $textTop = self::evenOut($string, $length);
            $textBtm = self::evenOut(" ", $length);
        }

        return array("top" => $textTop, "btm" => $textBtm);
    }

    public static function evenOut($string, $total)
    {
        $padString = str_pad($string, $total, " ");

        return $padString;
    }
}

NotePhreaks::__constructStatic();