<?php

/**
 *
 * Autogenerated with this command
 *
 * php phreak make:a phreak-cmd @phreakname
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
use League\CLImate\CLImate;

/**
 * This PHREAK Command [@phreaknamePhreaks] provides the backend logic and scripts.
 * Often for a front facing @phreaknameCommand, but not always. A phreak can be
 * by itself and not have a sister command.
 */

class @phreaknamePhreaks
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


    /**
     *
     * CONSTANTS
     *
     */

    const PALETTE_TEMPLATE = './resources/tables/';


    /**
     * constructStatic
     *
     * @phreakname static constructor
     *
     * @return void
     */
    public static function __constructStatic()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        self::$climate = new CLImate;
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
    public function getAttr($value = "")
    {
        $value = ($value !== "" ? "zero" : "one");

        return $value;
    }

}

@phreaknamePhreaks::__constructStatic();
