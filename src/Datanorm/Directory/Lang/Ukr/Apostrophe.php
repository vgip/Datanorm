<?php

declare(strict_types=1);

namespace Vgip\Datanorm\Directory\Lang\Ukr;

use Vgip\Datanorm\Directory\Lang\Ukr\Pattern;

class Apostrophe
{
    /**
     * Main apostrophe
     * 
     * @var string
     */
    private static string $apostropheMain = Pattern::APOSTROPHE_MAIN;

    /**
     * Possible apostrophe
     * 
     * @var array
     */
    private static array $apostrophePossible = Pattern::APOSTROPHE_POSSIBLE;

    /**
     * Replace counter last run of method convertApostrophe()
     *
     * @var int
     */
    private static int $replaceCounter = 0;

    /**
     * Convert possible apostrophe to main symbol apostrophe
     * 
     * @param string $stringRaw
     * @return string
     */
    public static function convertApostrophe(string $stringRaw): string
    {
        $string = str_replace(self::$apostrophePossible, self::$apostropheMain, $stringRaw, $counter);

        self::$replaceCounter = $counter;

        return $string;
    }

    /**
     * Return counter of replaced aphostrophe from possible to main symbol
     * 
     * @return int
     */
    public static function getReplaceCounter(): int
    {
        return self::$replaceCounter;
    }

    /**
     * Add possible symbols of apostrophes
     * 
     * @param type $aphostrophePossibleList
     * @return void
     */
    public static function addApostrophePossible(array $aphostrophePossibleList): void
    {
        self::$apostrophePossible = array_merge(self::$apostrophePossible, $aphostrophePossibleList);
    }
    
    /**
     * Get all possible sybols of apostrophes
     * 
     * @param type $aphostrophePossibleList
     * @return array
     */
    public static function getApostrophePossible($aphostrophePossibleList): array
    {
        return self::$apostrophePossible;
    }
}
