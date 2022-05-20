<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use League\CLImate\CLImate;

// PHREAK CLASSES
use App\Commands\Wallpaper\CMD;
use App\Commands\Wallpaper\Generator;
use App\Commands\Phreaks\PalettePhreaks;
use App\Commands\Phreaks\SixteenPhreaks;
use App\Commands\Phreaks\WallpaperPhreaks;
use App\Commands\Phreaks\TUI;
use App\Commands\Phreaks\Boxer;
use App\Commands\Phreaks\BoxerMini;


class WallpaperCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallpaper
                            {template? : select which style of wallpaper}
                            {palette? : what color palette to apply}
                            {--L|list : list all wallpaper templates}
                            {--S|select : use a select driven menu to create a wallpaper}
                            {--R|random : shuffle the palette colors. off by default}
                            {--T|texture : add a random grunge texture to the background}
                            {--B|bg= : load a file/url as the background. will be automatically resized/cropped}
                            {--bgo= : specify opacity for the given --bg param}
                            {--D|dontdisplay : do not display generated wallpaper in the terminal}
                            {--E|export= : output format. any or all options: png,jpg,svg,pdf. png by default}
                            {--I|info : display info about given command}';


    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Make & View Theme\'d Wallpapers';

    // Public Variables
    public $width       = 70;
    public $shuffle     = false;
    public $texture     = false;
    public $wallWidth   = 1920;

    public $DEBUG       = false;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setParams();

        if ($this->DEBUG !== false) {
            $this->debugWallpaper();
            exit;
        }

        $cmds = $this->option();

        if (isset($cmds["info"]) && $cmds["info"] !== false) {
            TUI::phreakHeader($this->output, "wallpaper", 40);
            $this->wallpaperHelp($cmds["info"]);
            TUI::Break(2);
            $ran = true;
        } else if (isset($cmds["select"]) && $cmds["select"] !== false) {
            $this->runWallpaperCMD("select", $cmds["select"]);
        } else if (isset($cmds["list"]) && $cmds["list"] !== false) {
            $this->runWallpaperCMD("list", $cmds["list"]);
        } else if (isset($cmds["select"]) && $cmds["select"] !== false) {
            $this->runWallpaperCMD("select", $cmds["select"]);
        } else {
            $this->wallpaperHelp();
        }
    }

    public function setParams()
    {
        $cmds = $this->option();

        if (isset($cmds["texture"]) && $cmds["texture"] !== false)
            $this->texture = true;

        if (isset($cmds["random"]) && $cmds["random"] !== false)
            $this->shuffle = true;
    }


    /**
     * runWallpaperCMD
     *
     * Main Brain of the Wallpaper Command. Handles the resulting action of 'handle()' function
     *
     * @param  mixed $cmd
     * @param  mixed $param
     * @return void
     */
    public function runWallpaperCMD($cmd, $param = false)
    {
        switch ($cmd) {
            case "select":
                TUI::phreakHeader($this->output, "wallpaper", 40);

                // Get all wallpaper templates
                $templates = WallpaperPhreaks::getTemplates("all", false);

                // Colors from two sources

                // 1. user palettes
                $palettes = PalettePhreaks::findSavedPalettes();
                $paletteList = $this->processPaletteList($palettes);

                // 2. base16 themes
                $base16 = SixteenPhreaks::loadAllBaseThemes();
                $base16List = $this->processSixteenList($base16);

                // combine into one list
                $themes = \array_merge($paletteList, $base16List);


                // LIST SELECTION
                // --------------

                $selectedTemplate = $this->selectableList($templates, "Select template to get started:", "name");
                TUI::Break(2);

                $selectedTheme = $this->selectableList($themes, "Select theme to use with template");
                TUI::Break(2);

                // FINALIZE & GENERATE WALLPAPER
                // -----------------------------
                $this->buildWallpaper($selectedTemplate, $selectedTheme, $templates);

                break;
            case "list":
                $catList = WallpaperPhreaks::getTemplates();

                $total = 0;

                foreach ($catList as $cat) {
                    $total = $total + count($cat);
                }

                $string = WallpaperPhreaks::printList($catList);
                $totalUp = $total * 2;

                $this->wallpaperBox($total);

                TUI::moveCursor($this->output, "up", $totalUp);
                TUI::outMove($string, array("right" => 5), $this->output);
                TUI::moveCursor($this->output, "down", 5);
                TUI::Break(2);

                break;
        }
    }

    // ---------------------------------------------------------------------------
    // SELECTION FUNCTIONS
    // ---------------------------------------------------------------------------
    public function processPaletteList($palettes)
    {
        $results = array();
        $themes = $palettes["palettes"];

        foreach ($themes as $t) {
            $results[] = array("name" => $t["title"], "function" => $t["id"]);
        }

        return $results;
    }

    public function processSixteenList($palettes)
    {
        $results = array();

        foreach ($palettes as $p) {
            $results[] = array("name" => $p["name"], "function" => $p["id"]);
        }

        return $results;
    }

    public function selectableList($templates, $docString, $return = "function")
    {
        $climate = new CLImate;

        $output = array();

        foreach ($templates as $t) {
            $output[] = $t["name"];
        }

        $input    = $climate->radio($docString, $output);
        $response = $input->prompt();

        foreach ($templates as $t) {
            if ($t["name"] === $response)
                $result = $t[$return];
        }

        return $result;
    }

    // ---------------------------------------------------------------------------
    // CLI DISPLAY FUNCTIONS
    // ---------------------------------------------------------------------------
    public function wallpaperBox($total = 1, $title = "Wallpapers")
    {
        $height = $total * 2;

        $Boxer = new Boxer();

        $Boxer->setBoxTITLE($title);
        $Boxer->setColorOutput(false);
        $Boxer->setBoxCONTENT(false);
        $Boxer->setBoxWIDTH($this->width);
        $Boxer->setBoxHEIGHT($height);
        $Boxer->setBoxTYPE("reputation");
        $Boxer->setShowCONTENT(false);
        $Boxer->setBoxMARGIN(2);
        $Boxer->setBoxPADDING(0);
        $Boxer->setBoxBtmTITLE(false);
        $Boxer->setBoxTHEME(false);

        $forceTall = $Boxer->generateBox();

        TUI::Speaks($forceTall);
    }

    public function wallpaperHelp($cmd = false)
    {
        // figlet -f "ANSI Regular" -w 80 -Ssk Wallpaper | lolcatjs -f >> Wallpaper.txt
        TUI::phreakHeader($this->output, "wallpaper", 40);
        switch ($cmd) {
            case 'new':
                $result = "Create a new palette using palette editor UI.  EX: php phreak palette -N ";
                break;
            default:
                $result = PHP_EOL . "You didnt give the command anything to do. Try running one of these commands:" . PHP_EOL . "  $ php phreak wallpaper --help" . PHP_EOL . "  $ php phreak wallpaper --info=<command>" . PHP_EOL;
                break;
        }

        $b = new BoxerMini($result);
        $b->width = 70;
        $b->align = "center";
        $b->type = "line_thick";
        $b->padding = 4;

        TUI::moveCursor($this->output, "up", 2);
        TUI::Speaks($b->render());

        TUI::Break(2);
    }

    public function debugWallpaper()
    {
        // "Compound Circle Bursts", "Circle Bursts",
        $ts2 = array("Cool Circles", "Ring of Cool");
        $ts3 = array("Rows & Rows of Circles", "Random Circle Grid");
        $ts0 = array("Bunch of Random Dots", "Circle Party");
        $ts1 = array("Rainbow Stack", "Rainbow Rings");
        $ts = array_merge($ts0, $ts1, $ts2, $ts3);

        $cs = array("chalk", "yesterday-night", "papercolor-dark",  "outrun-dark", 3, 4);

        foreach ($ts as $w) {
            $templateName = "Cool Circles";

            shuffle($cs);
            $theme = $cs[2];

            $this->buildWallpaper($templateName, $theme);
        }
    }

    // ---------------------------------------------------------------------------
    // BUILD WALLPAPER
    // ---------------------------------------------------------------------------
    public function buildWallpaper($templateName = false, $theme = false, $allTemplates = false)
    {
        // Options applied after the general setup. Non=critical
        $shuffle    = $this->shuffle;
        $texture    = $this->texture;
        $save       = false;
        $display    = true;

        // Theme Config
        $theme_type = "base16";
        if (is_int($theme))
            $theme_type = "palette";

        // CHECK FOR ANY PARAMS W/ SELECTED TEMPLATEf
        // DEBUG
        if ($this->DEBUG === true && $allTemplates === false)
            $allTemplates = WallpaperPhreaks::getTemplates("all", false);

        $param     = false;
        $template  = false;
        $category  = false;
        foreach ($allTemplates as $t) {
            if ($t["name"] === $templateName) {
                $template = $t["function"];
                $category = $t["category"];
                $param = $t["params"];
            }
        }

        // TAKE FINALIZED VARIABLES AND GET DOWN TO BUSINESS
        // Dynamically call the generator function. very hacky lol.
        if ($template) {
            TUI::Message($template . " Starting....", " " . $category . " ");

            $WGen = new Generator();
            $WGen->$template($theme, $theme_type, $shuffle, $texture, $param);
        } else
            TUI::Message("Invalid Wallpaper configuration!", "FAIL");
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
