<?php

namespace App\Commands;

use App\Commands\Phreaks\ColorPhreaks;
use App\Commands\Phreaks\DataPhreaks;
use App\Commands\Phreaks\FilePhreaks;
use App\Commands\Phreaks\PalettePhreaks;

use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\Boxer;
use App\Commands\Phreaks\BoxerMini;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class PaletteMagic extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'palette:magic
                            {id? : palette identifier}
                            {--W|websafe : create a duplicate palette w/ nearest websafe colors}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Update, Enhance, Alter and/or improve saved palette';

    public $width    = 70;
    public $margin   = 4;
    public $fgColor  = 84;
    public $bgColor  = 235;
    public $format   = "ascii";

    public $palette  = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        TUI::Message("Option for Websafe duplication...", "TODO");
        TUI::Message("Option for Adding Hover / Active States...", "TODO");
        TUI::Message("Option for Tailwind Stack addition...", "TODO");
        TUI::Message("Option for Color Harmonization...", "TODO");
    }

    public function websafe()
    {
        $id = $this->palette;

        // Call Phreak w/ ID.
        $pd = PalettePhreaks::lookupPalette($id);

        // Foreach color, find websafe version
        $colors = json_decode($pd->getField("colors"), true);

        if (count($colors) < 1)
            TUI::Message("Palette Color Error", "FAIL");

        $wsColors = array();
        foreach ($colors as $color) {
            $wsColors[] = ColorPhreaks::hex2websafe($color);
        }

        TUI::printDEBUG($wsColors);

        // TODO SAVE RESULTS TO NEW PALETTE
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
