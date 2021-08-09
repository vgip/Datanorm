<?php

declare(strict_types=1);

namespace Vgip\Datanorm\Transliteration\UkrEng;

/**
 * Transliteration with ukrainian to english
 * 
 * Approved resolution of the Cabinet of Ministers of Ukraine
 * of January 27, 2010 #55
 * https://www.kmu.gov.ua/npas/243262567
 */

class Cabmin2010 {

    private array $translateMain = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'h', 'ґ' => 'g', 
        'д' => 'd', 'е' => 'e', 'є' => 'ie', 'ж' => 'zh', 'з' => 'z',
        'и' => 'y', 'і' => 'i', 'ї' => 'i', 'й' => 'i', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
        'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
        'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch',
        'ю' => 'іu', 'я' => 'ia',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'H', 'Ґ' => 'G',
        'Д' => 'D', 'Е' => 'E', 'Є' => 'Ie', 'Ж' => 'Zh', 'З' => 'Z',
        'И' => 'Y', 'І' => 'I', 'Ї' => 'I', 'Й' => 'I', 'К' => 'K', 
        'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P',
        'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 
        'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch',
        'Ю' => 'Iu', 'Я' => 'Ia',
    ];
    
    private array $translateFirstSymbol = [
        'є' => 'ye', 'ї' => 'yi', 'й' => 'y', 'ю' => 'yu', 'я' => 'ya',
        'Є' => 'Ye', 'Ї' => 'Yi', 'Й' => 'Y', 'Ю' => 'Yu', 'Я' => 'Ya',
    ];

    private array $ignoreSymbol = [
        'ь', 'Ь', '\'', '’', 'ʼ',
    ];
    
    private array $zghSymbolSource = [
        'зг', 'Зг', 'зГ', 'ЗГ',
    ];
    
    private array $zghSymbolDestinaition = [
        'zgh', 'Zgh', 'zGh', 'ZGH',
    ];

    /**
     * 
     * @param string $wordRaw
     * @return string
     */
    public function transliterate(string $wordRaw): string
    {
        $translated = '';
        
        $wordSourceZghReplaced = str_replace($this->zghSymbolSource, $this->zghSymbolDestinaition, $wordRaw);
        
        $wordSourceList = $this->strSplitUnicode($wordSourceZghReplaced);
        
        foreach ($wordSourceList AS $key => $value) {
            if (in_array($value, $this->ignoreSymbol)) {
                continue;
            }
            if (0 === $key AND array_key_exists($value, $this->translateFirstSymbol)) {
                $translated .= $this->translateFirstSymbol[$value];
            } else if (array_key_exists($value, $this->translateMain)) {
                $translated .= $this->translateMain[$value];
            } else {
                $translated .= $value;
            }
        }
        
        return $translated;
    }
    
    private function strSplitUnicode(string $str, int $l = 0) 
    {
        if ($l > 0) {
            $ret = [];
            $len = mb_strlen($str);
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l);
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
