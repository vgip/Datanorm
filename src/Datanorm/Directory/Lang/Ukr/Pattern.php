<?php

namespace Vgip\Datanorm\Directory\Lang\Ukr;

use Vgip\Datanorm\Common\Singleton;

/**
 * Description of Pattern
 */
class Pattern
{
    use Singleton;
    
    /**
     * Main apostrophe
     * 
     * UTF-8 code: U+02BC
     * https://ukrainian.stackexchange.com/questions/40/%D0%AF%D0%BA%D0%B8%D0%B9-%D1%81%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB-%D0%B2%D0%B8%D0%BA%D0%BE%D1%80%D0%B8%D1%81%D1%82%D0%BE%D0%B2%D1%83%D0%B2%D0%B0%D1%82%D0%B8-%D0%B4%D0%BB%D1%8F-%D0%BF%D0%BE%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%BD%D1%8F-%D0%B0%D0%BF%D0%BE%D1%81%D1%82%D1%80%D0%BE%D1%84%D0%B0-%D0%B2-%D0%B5%D0%BB%D0%B5%D0%BA%D1%82%D1%80%D0%BE%D0%BD%D0%BD%D0%B8%D1%85-%D1%82%D0%B5%D0%BA%D1%81%D1%82%D0%B0%D1%85-%D1%83%D0%BA%D1%80%D0%B0%D1%97
     * https://linux.org.ua/index.php?PHPSESSID=5d41ee8e3412408b00269ca80d9f9c5b&topic=1223.300
     */
    const APOSTROPHE_MAIN = 'ʼ';

    /**
     * Available other sybols of apostrophe
     * 
     * Three apostrophes in Ukrainian: 
     * https://uk.wikipedia.org/wiki/%D0%90%D0%BF%D0%BE%D1%81%D1%82%D1%80%D0%BE%D1%84
     * ' - use this apostrophe/quote is unsafe for legacy script versions
     */
    const APOSTROPHE_POSSIBLE = [
        '’', // U+2019
        '\'', // U+0027
    ];

    /**
     * Ukrainian alphabet letters
     */
    const ALPHABET_LETTER_UA = 'абвгґдеєжзиіїйклмнопрстуфхцчшщьюяҐІЇЄ';

    /**
     * City name
     * 
     * Only en, ru or ua language
     * 
     * Example:
     * Киев, 
     * Киев-Волынский, или Ростов на Дону (один или два разделителя в виде пробела или тире), 
     * Арзамас-16
     * 
     * @var string 
     */
    private $localityName = '~' .
            '(^[' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . ']{2,30}$)|(^[' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . ']{2,20}[\s-]{1}[' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . ']{2,20}$)|(^[' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . ']{2,20}[\s-]{1}[' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . ']{1,5}[\s-]{1}[' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . ']{2,20}$)|(^[' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . ']{2,30}[\s-]{1}[\d]{1,3}$)' .
            '~ui';

    /**
     * Street name
     * 
     * Only en, ru or ua language
     * Example: 
     * Героїв Великої Вітчизняної Війни (площа) - 32 symbols
     * Авіаконструктора Ігоря Сікорського (вулиця) - 34 symbols
     * 
     * @var string 
     */
    private $streetName = '~' .
            '(^[0-9' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . '\s\-]{2,34}$)|(^[' . self::ALPHABET_LETTER_UA . ']{1}\. [0-9' . self::ALPHABET_LETTER_UA . self::APOSTROPHE_MAIN . '\s\-]{2,50}$)' .
            '~ui';

    public function getLocalityName()
    {
        return $this->localityName;
    }

    public function getStreetName()
    {
        return $this->streetName;
    }
}
