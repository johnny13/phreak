<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\DataPhreaks;
use App\Commands\Phreaks\FilePhreaks;

class NoteNewCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'note:new
                                {data : The content of the note}
                                {--N|nb= : Optional notebook. Will default to last created}
                                {--A|author= : Optional author name (defaults to hostname)}
                                {--T|tags= : Optional comma seperated list}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Compose a new text/markdown note w/ your CLI Editor.';

    protected $help = 'Create a new note using existing CLI Editor. Set via \$EDITOR environmental variable.';

    public $NBPATH = "Phreak";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setHelp($this->help);

        $homeDir = FilePhreaks::shellUserData("dir");
        if (!is_dir($homeDir))
            TUI::Message("Home directory could not be determined! Please set in .env", "FAIL");

        $author = (null !== $this->option("author") ? $this->option("author") : FilePhreaks::shellUserName());

        $check = DataPhreaks::checkTable("notes");

        // Table does not exist, template file not found to make it. major error!
        if (!$check)
            TUI::Message("Serious Error. Note table template file not found. Cannot continue!", "FAIL");

        // Required Data to Create New Note (in a notebook)
        $content = $this->argument("data");

        $contentSnippet = trim($content);

        // First we check for a given Notebook ID. Otherwise we get the last used notebook ID.
        $notebooks = json_decode(DataPhreaks::getAllFromTable("notebooks"), true);

        if (null !== $this->option("nb")) {

            $nbSlug = intval(trim($this->option("nb")));
            $result = false;

            foreach ($notebooks as $nbs) {
                $nbInt = intval($nbs["id"]);
                if ($nbInt === $nbSlug) {
                    $result = true;
                    $nbPath = $nbs["path"];
                    echo $nbs["id"] . " " . $nbSlug . PHP_EOL;
                }
            }


            // Verify thats a valid NB ID
            if (!$result)
                TUI::Message("Invalid Notebook ID! Cannot continue!", "FAIL");
        } else {
            $myLastElement = end($notebooks);
            $nbSlug = $myLastElement["id"];
            $nbPath = $myLastElement["path"];
        }

        $noteID = FilePhreaks::randomTxtString(4);

        $filename = FilePhreaks::beautify_filename($contentSnippet);

        $path = $nbPath . "/" . $filename . ".md";
        FilePhreaks::newMarkdown($path, $contentSnippet);

        $hash = hash_file('sha256', $path);

        $tags = is_null($this->option('tags')) ? false : $this->option('tags');

        // Ask for tags
        if ($tags === false)
            $tags = trim($this->ask("  Note Tag(s)? "));

        // New note data
        $now = TUI::UTCTime("now");

        $noteData = array(
            "tags"        => $tags,
            "notebook_id" => $nbSlug,
            "note_id"     => $noteID,
            "created"     => $now,
            "content"     => $contentSnippet,
            "path"        => $path,
            "hash"        => $hash,
            "author"      => $author
        );

        //TUI::printDEBUG($noteData);

        DataPhreaks::addRow('notes', $noteData);


        // OPEN NOTE MSG DISPLAY
        TUI::Break(1);
        TUI::Speaks("Note Created!", false, true);
        TUI::Break(1);
        TUI::buildFiglet("OPENING", "pagga", 80, true);
        TUI::Break(1);
        sleep(2);

        // OPEN EDITOR
        system("/snap/bin/micro " . $path . " > `tty`");
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
