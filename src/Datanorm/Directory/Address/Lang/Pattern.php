<?php

namespace Vgip\Datanorm\Directory\Address\Lang;

use Vgip\Datanorm\Common\Singleton;

/**
 * Description of Pattern
 */
class Pattern
{
    use Singleton;

    /**
     * Available aphostrophe
     * 
     * Three apostrophes in Ukrainian: 
     * https://uk.wikipedia.org/wiki/%D0%90%D0%BF%D0%BE%D1%81%D1%82%D1%80%D0%BE%D1%84
     * ' - use this apostrophe/quote is unsafe for legacy script versions
     */
    const APHOSTROPHE = '\'’ʼ';

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
            '(^[' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . ']{2,30}$)|(^[' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . ']{2,20}[\s-]{1}[' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . ']{2,20}$)|(^[' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . ']{2,20}[\s-]{1}[' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . ']{1,5}[\s-]{1}[' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . ']{2,20}$)|(^[' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . ']{2,30}[\s-]{1}[\d]{1,3}$)' .
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
            '(^[0-9' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . '\s\-]{2,34}$)|(^[' . self::ALPHABET_LETTER_UA . ']{1}\. [0-9' . self::ALPHABET_LETTER_UA . self::APHOSTROPHE . '\s\-]{2,50}$)' .
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
