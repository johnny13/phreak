<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\Boxer;
use App\Commands\Phreaks\DataPhreaks;
use App\Commands\Phreaks\NotePhreaks;

class NotebookListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'notebook:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all created notebooks';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notebooks = json_decode(DataPhreaks::getAllFromTable("notebooks"), true);
        $nbList = $this->assembleList($notebooks);
        $nbRows = NotePhreaks::notebookLister($nbList);
        $totalNotes = count($notebooks);
        $totalUp = $totalNotes * 4;

        $this->noteBox($totalNotes);

        TUI::moveCursor($this->output, "up", $totalUp);

        TUI::outMove($nbRows, array("right" => 5), $this->output);

        TUI::moveCursor($this->output, "down", 5);
        TUI::Break(3);
    }

    private function assembleList($data)
    {
        $results = array();

        foreach ($data as $d) {
            $results[] = array(
                "id" => $d["id"],
                "name" => $d["name"],
                "tags" => $d["tags"],
                "author" => $d["author"],
                "date"  => $d["created"]
            );
        }

        return $results;
    }

    public function noteBox($total = 1)
    {
        $height = $total * 4;

        $Boxer = new Boxer();

        $Boxer->setBoxTITLE("Notebooks");
        $Boxer->setColorOutput(false);
        $Boxer->setBoxCONTENT(false);
        $Boxer->setBoxWIDTH(80);
        $Boxer->setBoxHEIGHT($height);
        $Boxer->setBoxTYPE("folklore");
        $Boxer->setShowCONTENT(false);
        $Boxer->setBoxMARGIN(2);
        $Boxer->setBoxPADDING(0);
        $Boxer->setBoxBtmTITLE(false);
        $Boxer->setBoxTHEME(false);

        $forceTall = $Boxer->generateBox();

        TUI::Speaks($forceTall);
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
