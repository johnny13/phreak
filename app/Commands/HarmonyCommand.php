<?php

namespace App\Commands;

use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\Boxer;
use App\Commands\Phreaks\HarmonyPhreaks;
use App\Commands\Phreaks\ColorPhreaks;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class HarmonyCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'harmony
                            {hex? : Hexidecimal color starting point}
                            {--P|palette= : Color palette to harmonize}
                            {--F|flag : Unused option...}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Color Harmonies and Palette Builder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // TODO: Implement the --palette flag functionality!
        // TODO: Export results, or save as new palette.
        // TODO: Generate complete color styleguide. with text color, hover / action states etc.

        

        $HEX = $this->argument("hex");

        if (!isset($HEX) || ColorPhreaks::checkHexCode($HEX) !== true)
            TUI::Message("Invalid Hexcode argument!", "FAIL");

        $ColorNames = base_path("libraries/colornames.json");

        // $complement = HarmonyPhreaks::getColorComplement($HEX);

        $rows = array();

        $rows[] = implode(PHP_EOL, ColorPhreaks::miniColorReport($HEX, 4));
        $rows[] = "";

        $types = array("comp", "analog", "tri", "split", "tetra", "square");
        foreach ($types as $type) {
            $ColorData = HarmonyPhreaks::harmonyColors($HEX, $type, $ColorNames);
            $harmony = $ColorData["list"];
            $rows[] = implode(PHP_EOL, HarmonyPhreaks::harmonyReport($harmony, $HEX, 2));
        }

        // Make a Box
        $Boxer = new Boxer();
        $Boxer->setBoxTITLE("Harmony Report");
        $Boxer->setColorOutput(false);
        $Boxer->setBoxCONTENT(false);
        $Boxer->setBoxWIDTH(80);
        $Boxer->setBoxHEIGHT(60);
        $Boxer->setBoxTYPE("folklore");
        $Boxer->setShowCONTENT(false);
        $Boxer->setBoxMARGIN(2);
        $Boxer->setBoxPADDING(0);
        $Boxer->setBoxBtmTITLE(false);
        $Boxer->setBoxTHEME(false);

        $forceTall = $Boxer->generateHeader();

        TUI::Break(2);
        TUI::Speaks($forceTall);

        // Fill the box
        //TUI::Speaks($rows);

        $row = implode(PHP_EOL, $rows);
        TUI::Speaks($row);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
