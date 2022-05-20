<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\DataPhreaks;
use App\Commands\Phreaks\FilePhreaks;
use App\Commands\Phreaks\NotePhreaks;

class NoteUpdateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'note:update';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Update saved notes in the database w/ filesystem changes.';

    public $NBPATH = "Phreak";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $homeDir = FilePhreaks::shellUserData("dir");
        if (!is_dir($homeDir))
            TUI::Message("Home directory could not be determined! Please set in .env", "FAIL");

        $check = DataPhreaks::checkTable("notes");
        if (!$check)
            TUI::Message("Serious Error. Note table template file not found. Cannot continue!", "FAIL");

        // Get list of Notes
        $notes  = json_decode(DataPhreaks::getAllFromTable("notes"), true);

        // Foreach Note Compute Hash
        foreach ($notes as $n) {
            $hash = $n["hash"];
            $id   = $n["id"];
            $path = $n["path"];

            //$storedData = DataPhreaks::getRowByID("notes", $id);
            $currentHash = hash_file('sha256', $path);

            TUI::Message("Checking Note [" . $id . "]", "INFO");

            if ($currentHash !== $hash) {
                TUI::Message("Updating Note [" . $id . "]", "GOOD");

                $storedData = file_get_contents($path);

                // New note data
                $now = TUI::UTCTime("now");

                $noteData = array(
                    "updated"    => $now,
                    "content"     => json_encode($storedData),
                    "hash"        => $currentHash
                );

                DataPhreaks::updateTableData($noteData, 'notes', 'id', $id);

                TUI::Break(2);
            }
        }
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
