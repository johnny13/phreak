<?php
 /**
     * BorderBurst style circles can be solid or complex and vary widely in size compared to complex circles
     *
     * @param [type] $imagine
     * @param string $themeName
     * @param string $themeType
     * @return void
     */
    public function makeBorderBurst($imagine, $themeName, $themeType, $shuffle, $texture)
    {

        $theme = CMD::newWallpaper($themeType, $themeName, $shuffle);

        $palette = new RGB();

        $image = $imagine->create(
            new Box(self::WALLWIDTH, self::WALLHEIGHT),
            $palette->color($theme["bg"])
        );

        $cParams = array(
            "wImg"    => $image,
            "cPal"    => $palette,
            "bgColor" => $theme["bg"],
            "colors"  => $theme["colors"]
        );

        TUI::Speaks("Border Burst Loop Starting....");

        $this->borderBurstLoop($cParams, $theme, count($theme["colors"]));

        $cwt = $theme["themeName"] . "_burst-o_";

        $result = CMD::saveWallpaperImg($image, $cwt, $texture);
        return $result;
    }
 /**
     * Loop through each color and make a "BorderBurst" styled circle
     *
     * @param [type] $cParams
     * @param [type] $tfc
     * @param [type] $tcs
     * @param integer $loopMax
     * @return void
     */
    public function borderBurstLoop($cParams, $tfc, $tcs, $loopMax = 3)
    {

        $colorLimit   = 0;
        $loopCount    = 0;
        $burstMax     = 4;
        $gCount       = 0;
        $curCs        = 0;
        $lastX        = 0;
        $lastY        = 0;
        $results = array();
        ay();

        //while ($loopCount < $loopMax) {}

        // if ($colorLimit >= $tcs) {
        //     $colorLimit = 0;
        //     $loopCount++;
        // }

        $burstCount = 0;

        foreach ($tfc["colors"] as $color) {
            //while ($burstCount < $burstMax) {

            if ($burstCount === 0) {
                $lastX = intval(rand(0, self::WALLWIDTH));
                $lastY = intval(rand(0, self::WALLHEIGHT));
            } else {
                $lastX = $results["x"];
                $lastY = $results["y"];
            }

            $cParams["currentColor"] = $color;
            $cParams["counter"] = $curCs;
            $cParams["centerX"] = $lastX;
            $cParams["centerY"] = $lastY;

            $results = $this->borderBurstCircles($cParams, $tcs);
            $lastX   = $results["x"];
            $lastY   = $results["y"];

            $burstCount++;
            $curCs++;

            //TUI::Speaks("COLOR: " . $color . " Global Count: " . $gCount);
            $gCount++;
        }

        $curCs = 1;
        $lastX = rand(intval(self::WALLWIDTH * 0.10), intval(self::WALLWIDTH * 0.90));
        $lastY = rand(intval(self::WALLHEIGHT * 0.25), intval(self::WALLHEIGHT * 0.75));

        $colorLimit++;
    }
/**
     * CircleBurst Helper Function : Create set of relative sized Circles
     *
     * @param array $bbP
     * @param int $tcs
     * @return void
     */
    public function borderBurstCircles($bbP = array(), $tcs)
    {
        // Compute Circle Size
        // First Circle size is relative to Wallpaper size / half of total colors
        // Additional Circles are relative [%]PERCENT of primary circle size
        if ($bbP["counter"] === 1) {
            $cSize = intval(round(rand(intval(self::WALLWIDTH * 0.13), intval(self::WALLWIDTH * 0.45))));
            $this->mainSize = $cSize;
        } else {
            $cSize = intval(round($this->mainSize * (($tcs - $bbP["counter"]) * .15)));
        }

        // Error checking min size & set min size

        $testSize = intval(round(self::WALLWIDTH * 0.13));
        if ($cSize < $testSize) $cSize = $testSize;
        //if ($bbP["counter"] === 1) $this->mainSize = $cSize;

        //TUI::Speaks("MainSize:" . $this->mainSize . " Count: " . $bbP["counter"]);

        // Background Outline Size 5% larger than circle size
        $borderSize = intval(round(self::BORDERSIZE + $cSize));

        $bInSize    = intval(round($this->mainSize * (($tcs - $bbP["counter"]) * .1)));
        $cInSize    = intval(round($bInSize * (($tcs - $bbP["counter"]) * .1)));

        if ($bInSize < self::BORDERMID) $bInSize = self::BORDERMID;
        if ($cInSize < self::BORDERIN) $cInSize = self::BORDERIN;

        // Compute Circle Location
        $spots = $this->circleLocation($cSize);
        $cX = $spots["spotX"];
        $cY = $spots["spotY"];

        // Add Border Circle to Wallpaper
        $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $cX, $cY, ($borderSize - 5), $bbP["bgColor"]);

        // Add Theme Color Circle to Wallpaper
        $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $cX, $cY, $cSize, $bbP["currentColor"]);

        $rChance = rand(1, 5);
        if ($rChance !== 1) {
            $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $cX, $cY, $bInSize, $bbP["bgColor"]);
            $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $cX, $cY, $cInSize, $bbP["currentColor"]);
        }

        $fA = array("x" => $cX, "y" => $cY);
        return $fA;
    }


    /**
     * Generate random data for compound circles
     *
     * @param [type] $cSize
     * @return void
     */
    public function randomCircleData($cSize)
    {
        $cData = array();
        $cData["spotX"]  = rand(0, self::WALLWIDTH);
        $cData["spotY"]  = rand(0, self::WALLHEIGHT);

        return $cData;
    }

    /**
     * circleLocation
     *
     * @param int $cSize How big of circle we are making
     * @return object of coordinates to plot on grid
     * @todo do a better job of not returning used up areas, to avoid overlapping
     */
    public function circleLocation($cSize)
    {
        //global $allSizes;
        //TUI::Speaks("Circle Location. Size:" . $cSize);

        if (is_array($this->allSizes) && count($this->allSizes) >= 1) {

            $acceptable = false;
            $logicPass  = 0;

            while ($acceptable !== true) {

                $circLoc = $this->randomCircleData($cSize);

                asort($this->xRangers);
                $min = array_values($this->xRangers)[0];
                $max = end($this->xRangers);

                $tempRanges = range($min, $max);
                foreach ($tempRanges as $k => $v) {
                    $this->xRangers[] = $v;
                }

                if (!in_array($circLoc["spotX"], $this->xRangers))
                    $acceptable = true;

                $logicPass++;
                if ($logicPass > 150 && $acceptable !== true) {
                    $acceptable = true;
                    $this->xRangers = array();
                    $this->xRangers[] = $min;
                    $this->xRangers[] = $max;
                    $logicPass = 0;
                }
            }
        } else {
            $circLoc = $this->randomCircleData($cSize);
        }

        $this->allSizes[] = array("x" => $circLoc["spotX"], "y" => $circLoc["spotY"], "size" => $cSize);
        $this->usedUpRange($cSize, $circLoc["spotX"]);
        return $circLoc;
    }


    /**
     * Complex circle made up of increasingly smaller circles
     *
     * @param [type] $imagine
     * @param string $themeName
     * @param string $themeType
     * @return void
     */
    public function makeCircleComplex($imagine, $themeName, $themeType, $shuffle, $texture)
    {

        $theme = CMD::newWallpaper($themeType, $themeName, $shuffle);

        $palette = new RGB();

        $image = $imagine->create(
            new Box(self::WALLWIDTH, self::WALLHEIGHT),
            $palette->color($theme["bg"])
        );

        $cParams = array(
            "wImg"    => $image,
            "cPal"    => $palette,
            "bgColor" => $theme["bg"],
            "colors"  => $theme["colors"]
        );

        TUI::Speaks("complexCircleLoop starting....");

        $this->complexCircleLoop($cParams);

        $cwt = $theme["themeName"] . "_complex-o_";

        $result = CMD::saveWallpaperImg($image, $cwt, $texture);
        return $result;
    }


    /**
     * CircleBurst Positioning : Generate "SLIGHTLY" random X & Y positions
     *
     * @param int $counter
     * @param int $centerX
     * @param int $centerY
     * @param int $tcs
     * @param int $baseAmount
     * @return void
     */
    public function randomOffCenterXY($counter, $centerX, $centerY, $tcs, $baseAmount)
    {

        if ($counter <= -1) {
            $offXMin = intval(round($centerX - $baseAmount));
            $offXMax = intval(round($centerX + $baseAmount));
            $offYMin = intval(round($centerY - $baseAmount));
            $offYMax = intval(round($centerY + $baseAmount));

            $randCenterX = intval(rand($offXMin, $offXMax));
            $randCenterY = intval(rand($offYMin, $offYMax));
        } else {
            // Additional Passes
            $extra    = intval($tcs * ($counter * $counter));
            $extraPos = intval($baseAmount + $extra);
            $extraNeg = intval(intval(-1 * $baseAmount) + intval(-1 * $extra));

            $randCenterX = intval($centerX + rand($extraNeg, $extraPos));
            $randCenterY = intval($centerY + rand($extraNeg, $extraPos));
        }

        $positions = array("x" => $randCenterX, "y" => $randCenterY);

        return $positions;
    }


    /**
     * Determines random coords. Attempts to avoid overlapping w/ previous circles
     *
     * @param [type] $size
     * @return void
     */
    public function computeMovement($size = 0)
    {

        $loc   = array();
        $qsize = intval(1.5 * $size);
        $psize = intval(intval(rand(1, 4)) * $size);

        //$moveVal = intval(round($size + intval($size * intval(0.1 * rand(1, 3)))));
        //$wiggle  = intval(rand(-250, 250));
        $locCheck = false;
        $lcC = 0;
        while ($locCheck === false) {
            $moveVal = intval(rand(intval(-1 * $psize), $psize));
            $wiggle  = intval(rand(intval(-1 * $qsize), $qsize));

            //TUI::Speaks("Moving:" . $this->dirMove);

            switch ($this->dirMove) {
                case "up":
                    $loc["x"] = intval($this->circSpot_x + $wiggle);
                    $loc["y"] = intval(round($this->circSpot_y + $moveVal));
                    break;
                case "down":
                    $loc["x"] = intval($this->circSpot_x - $wiggle);
                    $loc["y"] = intval(round($this->circSpot_y - $moveVal));
                    break;
                case "left":
                    $loc["x"] = intval(round($this->circSpot_x - $moveVal));
                    $loc["y"] = intval($this->circSpot_y - $wiggle);
                    break;
                case "right":
                    $loc["x"] = intval(round($this->circSpot_x + $moveVal));
                    $loc["y"] = intval($this->circSpot_y + $wiggle);
                    break;
            }

            if ((($loc["x"] > 0) && ($loc["x"] < self::WALLWIDTH)) && (($loc["y"] > 0) && ($loc["y"] < self::WALLHEIGHT)))
                $locCheck = true;

            $lcC++;
            if ($lcC > 50) {
                $locCheck = true;
            }
        }


        $directions = array("up", "down", "left", "right");
        shuffle($directions);
        $this->dirMove = $directions[0];

        //TUI::Speaks("Moved x@" . $loc["x"] . " y@" . $loc["y"]);
        return $loc;
    }

    /**
     * Compute Circle Location Coordinates
     *
     * @param int $count
     * @param int $size
     * @return void
     */
    public function compCircLoc($count, $size)
    {
        $loc = array();

        if ($count === 1) {
            $loc["x"] = intval(rand(intval(self::WALLWIDTH * 0.15), intval(self::WALLWIDTH * 0.85)));
            $loc["y"] = intval(rand(intval(self::WALLHEIGHT * 0.15), intval(self::WALLHEIGHT * 0.85)));

            $directions = array("up", "down", "left", "right");
            shuffle($directions);
            $this->dirMove = $directions[0];
        } else {

            $moveCheck = false;
            $mcC = 0;

            while ($moveCheck === false) {
                $lastMove = $this->dirMove;
                $loc = $this->computeMovement($size);
                $sSize = intval($size * 1.2);

                if ($lastMove === "up" && $loc["x"] > intval($this->circSpot_x + $sSize))
                    $moveCheck = true;

                if ($lastMove === "down" && $loc["x"] < intval($this->circSpot_x - $sSize))
                    $moveCheck = true;

                if ($lastMove === "left" && $loc["y"] > intval($this->circSpot_y + $sSize))
                    $moveCheck = true;

                if ($lastMove === "right" && $loc["y"] < intval($this->circSpot_y - $sSize))
                    $moveCheck = true;

                $mcC++;
                //TUI::Speaks("dir: " . $lastMove . " size: " . $sSize . " x: " . $loc["x"] . " y: " . $loc["y"]);
                if ($mcC > 50) {
                    $moveCheck = true;
                    TUI::Speaks(array(" ", "Move  Limit!", " "));
                    $loc["x"] = intval(rand(0, self::WALLWIDTH));
                    $loc["y"] = intval(rand(0, self::WALLHEIGHT));
                }
            }
        }

        $this->circSpot_x = $loc["x"];
        $this->circSpot_y = $loc["y"];
        return $loc;
    }

    /**
     * Compute Circle Size
     *
     * @param int $count
     * @return void
     */
    public function compCircSizes($count)
    {
        $sizes = array();

        $multiplier = "0.1" . $count;
        settype($multiplier, "float");

        $mSize = round(floatval($multiplier * self::WALLWIDTH));
        if ($count === 1) $this->mainSize = $mSize;

        $borderSize = intval($mSize + self::BORDERSIZE);

        $sizeCheck = false;
        $sCC = 0;
        while ($sizeCheck === false) {
            $rOne = rand(self::BORDERSIZE, intval(self::BORDERSIZE * 3));
            $rTwo = rand(self::BORDERSIZE, intval(self::BORDERSIZE * 3));

            $bInSize    = intval($mSize - $rOne);
            $cInSize    = intval($bInSize - $rTwo);

            // if ($bInSize < 0) $bInSize = intval($mSize = 50);
            // if ($cInSize < 0) $cInSize = intval($bInSize = 50);

            if (($bInSize > $cInSize && $cInSize > 0)) $sizeCheck = true;
            $sCC++;
            if ($sCC > 50) {
                $sizeCheck = true;
                TUI::Speaks("Size Check Limit Reached.");
            }
        }

        $sizes["main"]   = $mSize;
        $sizes["outB"]   = $borderSize;
        $sizes["inB"]    = $bInSize;
        $sizes["center"] = $cInSize;

        //TUI::Speaks("Mx: " . $multiplier . " R1: " . $rOne . " R2: " . $rTwo . " SC: " . $sCC);
        //TUI::Speaks("mS: " . $mSize . " oB: " . $borderSize . " iB: " . $bInSize . " cS: " . $cInSize);

        return $sizes;
    }

    /**
     * Complex Style Circle Loop
     *
     * @param array $bbP
     * @return void
     */
    public function complexCircleLoop($bbP)
    {
        $count = 1;
        $reversed = array_reverse($bbP["colors"]);

        //while ($count < $tcs) {
        foreach ($reversed as $color) {

            $sz = $this->compCircSizes($count);
            $pos = $this->compCircLoc($count, $sz["main"]);
            $X = $this->circSpot_x;
            $Y = $this->circSpot_y;

            $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $X, $Y, $sz["outB"],   $bbP["bg"]);
            $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $X, $Y, $sz["main"],   $color);
            $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $X, $Y, $sz["inB"],    $bbP["bg"]);
            $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $X, $Y, $sz["center"], $color);

            $count++;
        }
    }

    /**
     * Workhorse for the dots style wallpaper.
     *
     * @param array $bbP
     * @return void
     */
    public function dots($bbP)
    {
        $count = 0;
        $tcs   = intval(rand(6, 13));
        $this->xRangers = array();

        while ($count < $tcs) {
            foreach ($bbP["colors"] as $color) {

                $S = rand(4, 40);
                $halfS = intval(round($S * 0.5));

                $vX = 0;
                while ($vX === 0) {
                    $vX = $this->uniqueX();
                }

                $Y = rand(0, intval(self::WALLHEIGHT));

                $this->drawColoredCircle($bbP["wImg"], $bbP["cPal"], $vX, $Y, $S, $color);

                $numbers = range(intval(round($vX - $halfS)),  intval(round($vX + $halfS)));
                foreach ($numbers as $number) {
                    $this->xRangers[] = $number;
                }
                $this->xRangers = array_unique($this->xRangers);
                asort($this->xRangers);
            }
            $count++;
        }
    }


     // ---------------------------------------------------------------------------
    // FAILED CIRCLE WALLPAPERS
    // ---------------------------------------------------------------------------

    /**
     * makeCenterRings
     *
     * @param  mixed $imagine
     * @param  mixed $themeName
     * @param  mixed $themeType
     * @param  mixed $shuffle
     * @param  mixed $texture
     * @return void
     */
    public function makeCenterRings($imagine, $themeName, $themeType, $shuffle, $texture)
    {
        $theme   = CMD::newWallpaper($themeType, $themeName, $shuffle);

        $palette = new RGB();

        $image = $imagine->create(
            new Box(self::WALLWIDTH, self::WALLHEIGHT),
            $palette->color($theme["bg"])
        );

        $cParams = array(
            "wImg"    => $image,
            "cPal"    => $palette,
            "bg"      => $theme["bg"],
            "colors"  => $theme["colors"]
        );

        TUI::Speaks("Center Rings starting....");

        $this->centerRingGen($cParams);

        $cwt = $theme["themeName"] . "_center-ringo-s_";
        $result = CMD::saveWallpaperImg($image, $cwt, $texture);

        return $result;
    }

    /**
     * Random tiny little circles scattered all over the place
     *
     * @param [type] $imagine
     * @param string $themeName
     * @param string $themeType
     * @return void
     */
    public function makeFixedCompound($imagine, $themeName, $themeType, $shuffle, $texture)
    {
        $theme   = CMD::newWallpaper($themeType, $themeName, $shuffle);

        $palette = new RGB();

        $image = $imagine->create(
            new Box(self::WALLWIDTH, self::WALLHEIGHT),
            $palette->color($theme["bg"])
        );

        $cParams = array(
            "wImg"    => $image,
            "cPal"    => $palette,
            "bg"      => $theme["bg"],
            "colors"  => $theme["colors"]
        );

        TUI::Speaks("Cool Compounded Circles starting....");
        $this->coolCompounder($cParams);

        $cwt = $theme["themeName"] . "_coolie-o-s_";
        $result = CMD::saveWallpaperImg($image, $cwt, $texture);
        return $result;
    }
