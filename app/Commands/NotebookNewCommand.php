<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\DataPhreaks;
use App\Commands\Phreaks\FilePhreaks;

class NotebookNewCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'notebook:new
                                {name : Notebook name}
                                {--A|author= : Optional author name (defaults to hostname)}
                                {--T|tags= : Optional comman seperated list of tags}';


    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new notebook to hold notes';

    protected $help = 'Create a blank notebook
                      <name>                Notebook name
                      --author, -A          Set Author name
                      --tags, -T            List of tags';


    public $NBPATH = "Phreak";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setHelp($this->help);

        // PERFORM BASIC CHECKS FOR AUTHOR, DIRECTORY, DATABASE ETC

        $homeDir = FilePhreaks::shellUserData("dir");
        if (!is_dir($homeDir))
            TUI::Message("Home directory could not be determined! Please set in .env", "FAIL");

        $name = $this->argument("name");

        $author = (null !== $this->option("author") ? $this->option("author") : FilePhreaks::shellUserName());

        $check = DataPhreaks::checkTable("notebooks");

        // Table does not exist, template file not found to make it. major error!
        if (!$check)
            TUI::Message("Serious Error. Notebook table template file not found. Cannot continue!", "FAIL");

        // Create New Notebook in Database
        $notebookCheck = DataPhreaks::checkTableRow("notebooks", "name", $name);

        if ($notebookCheck !== true) {
            $notebookID = FilePhreaks::randomTxtString(6);
            $now = TUI::UTCTime("now");
            $nbData = array("name" => $name, "created" => $now, "author" => $author, "notebook_id" => $notebookID);
            DataPhreaks::addRow('notebooks', $nbData);
        } else {

            $this->newLine();
            TUI::Message("That name has already been used", "WARN");
            $this->newLine();

            // Continue? [y/n]
            if (!$this->confirm("  [Y] Edit Existing [N] Try New Name"))
                exit;

            $this->newLine();
        }

        $tags = is_null($this->option('tags')) ? false : $this->option('tags');

        // Ask for tags
        if ($tags === false)
            $tags = trim($this->ask("  Notebook Tag(s)? "));

        $nbSlug = FilePhreaks::beautify_filename($name);
        $path = $homeDir . "/" . $this->NBPATH . "/" . $nbSlug;

        // Update / Add Data to notebook
        $tagsArray = array("tags" => $tags, "path" => $path);
        DataPhreaks::updateTableData($tagsArray, "notebooks", "name", $name);

        // Create Folder w/ name of notebook
        FilePhreaks::dir_if_none($path);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
