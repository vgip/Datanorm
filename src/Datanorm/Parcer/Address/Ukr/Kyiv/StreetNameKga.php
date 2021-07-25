<?php

namespace Vgip\Datanorm\Parcer\Address\Ukr\Kyiv;

/**
 * Get data from
 * https://kga.gov.ua/ofitsijni-dokumenti/11-ofitsijni-dokumenti/1261-reestr-vulits-mista-kieva
 * 
 * Register of streets of Kyiv
 */
class StreetNameKga
{

    /**
     * Source file path converted to CSV format with ";" separators.
     * 
     * @var string 
     */
    private $filePath = 'KyivStreet.csv';

    /**
     * File column name
     * 
     * Column serial number to key accordance
     * 
     * @var array 
     */
    private $fileColumnStructure = [];

    /**
     * Type street whitelist
     * 
     * @var array
     */
    private $typeWhitelist = [];

    /**
     * Street name pattern
     * 
     * The pattern that the street name must match
     * 
     * @var string - pattern to preg_match()
     */
    private $patternStreetName;

    /**
     * Source identifier
     * 
     * @var int
     */
    private $id;
    
    /**
     * District
     * 
     * Unique district names from file
     * 
     * @var array|null
     */
    private $districtList;

    /**
     * Street type
     * 
     * Unique street type from file
     * 
     * @var array|null
     */
    private $typeList;

    /**
     * Street name list
     * 
     * @var array
     */
    private $nameList = [];

    /**
     * File row number
     * 
     * @var int 
     */
    private $fileRowNumber = 0;

    /**
     * Counter of street types
     * 
     * @var type array|null
     */
    private $typeCounter;

    /**
     * Found some warnins
     * 
     * If present warnings, then count($this->warning) > 0 else count($this->warning) == 0
     * 
     * @var array|null 
     */
    private $warning;
    
    /**
     * Source identifier error
     * 
     * @var array|null
     */
    private $idError = [];

    /**
     * Type not found in $this->typeWhitelist
     *
     * @var array|null
     */
    private $typeNotFound = [];

    /**
     * Name double in city 
     * 
     * @var array
     */
    private $nameDouble = [];

    /**
     * Street name not valid (contain forbidden symbols)
     * 
     * Pattern valid street name: $this->patternStreetName
     * 
     * @var array - [streetname, streetname, ...]
     */
    private $nameNotValid = [];

    public function __construct()
    {
        $this->initFileColumnStructure();/** Init default file column structure */;
        $this->initTypeWhitelist();/** Init default type whitelist */;
    }

    public function getCsvAsArray()
    {
        $this->initTypeCounter();/** Set 0 (zero) to all keys of counter $this->typeCounter - ONLY AFTER initTypeCounter set! */;

        $dataCsv = null;

        $csvFileString = iconv("Windows-1251", "UTF-8", file_get_contents($this->filePath));

        $data = str_getcsv($csvFileString, "\r\n");

        foreach ($data as $rowRaw) {
            $this->fileRowNumber++;

            if ($this->fileRowNumber <= 3) {
                continue;
            }

            $csvRow = [];
            $csvRowRaw = str_getcsv($rowRaw, ";");
            foreach ($csvRowRaw AS $number => $value) {
                if (array_key_exists($number, $this->fileColumnStructure)) {
                    $key = $this->fileColumnStructure[$number];
                    $csvRow[$key] = trim($value);
                }
            }

            /** Id */
            $id = $this->processId($csvRow['id']);
            $csvRow['id'] = $id;

            /** Street type */
            $type = $this->processType($csvRow['type_name']);
            $csvRow['type_key'] = $type;

            /** City district */
            $district = $this->processDistrict($csvRow['district_string']);
            $csvRow['district_list'] = $district;

            /** Street */
            if (false !== $type) {
                $name = $this->processName($type, $csvRow['name_original']);
                $csvRow['name'] = $name;
            } else {
                $csvRow['name'] = '';
            }

            $dataCsv[] = $csvRow;
        }

        if (count($this->typeNotFound) > 0) {
            $this->warning['type_new'][0] = 'new street types found: ' . join(', ', $this->typeNotFound) . '';
        }
        if (count($this->nameNotValid) > 0) {
            $this->warning['name_not_valid'][0] = 'street names contain forbidden symbols: ' . join(', ', $this->nameNotValid) . '';
        }
        foreach ($this->idError AS $key => $value) {
            $this->warning[$key] = $value;
        }

        ksort($this->typeList);

        return $dataCsv;
    }

    public function getStreetNameHash($type, $name)
    {
        $deleteSymbolName = [' ', '\'', '’', '-'];
        $typeForHash = mb_strtolower(str_replace('\'', '', $type));
        $nameForHash = mb_strtolower(str_replace($deleteSymbolName, '', $name));

        $hash = $typeForHash . $nameForHash;

        return $hash;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getFileColumnStructure()
    {
        return $this->fileColumnStructure;
    }

    public function getTypeWhitelist()
    {
        return $this->typeWhitelist;
    }

    public function getPatternStreetName()
    {
        return $this->patternStreetName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDistrictList()
    {
        return $this->districtList;
    }

    public function getTypeList()
    {
        return $this->typeList;
    }

    public function getNameList()
    {
        return $this->nameList;
    }

    public function getFileRowNumber()
    {
        return $this->fileRowNumber;
    }

    public function getTypeCounter()
    {
        return $this->typeCounter;
    }

    public function getWarning()
    {
        return $this->warning;
    }
    
    public function getIdError()
    {
        return $this->idError;
    }

    public function getTypeNotFound()
    {
        return $this->typeNotFound;
    }

    public function getNameDouble()
    {
        return $this->nameDouble;
    }

    public function getNameNotValid()
    {
        return $this->nameNotValid;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    public function setFileColumnStructure($fileColumnStructure)
    {
        $this->fileColumnStructure = $fileColumnStructure;
    }

    public function setTypeWhitelist($typeWhitelist)
    {
        $this->typeWhitelist = $typeWhitelist;
    }

    public function setPatternStreetName($patternStreetName)
    {
        $this->patternStreetName = $patternStreetName;
    }

    private function initTypeCounter()
    {
        foreach ($this->typeWhitelist AS $key => $val) {
            $this->typeCounter[$key] = 0;
        }
    }

    private function processId($idRaw)
    {
        $idRes = null;
        $idErrNum = 0;
        
        $idInt = (int)$idRaw;
        if ((string)$idInt !== (string)$idRaw) {
            $idErrNum++;
            $this->idError['id_'.$this->fileRowNumber] = 'identifier in the string #'.$this->fileRowNumber.' "'.$idRaw.'" incorrect';
        } else {
            if (array_key_exists($idInt, $this->id)) {
                $this->idError['id_'.$this->fileRowNumber] = 'identifier "'.$idInt.'" in the string #'.$this->fileRowNumber.' is double';
            } else {
                $this->id[$idInt] = $idInt;
                $idRes = $idInt;
            }
        }
        
        return $idRes;
    }

    private function processType($typeName)
    {
        $typeKey = array_search($typeName, $this->typeWhitelist);
        if (false === $typeKey) {
            $this->idError['type_'.$this->fileRowNumber] = 'new type name "'.$typeName.'" found in the string #'.$this->fileRowNumber.'';
            $this->typeNotFound[$typeName] = '"' . $typeName . '"';
        } else {
            $this->typeCounter[$typeKey]++;
            $this->typeList[$typeKey] = $typeName;
        }

        return $typeKey;
    }

    private function processDistrict($districtRow)
    {

        $replaceList = $this->getReplaceDistrict();
        $districtRowList = explode(',', $districtRow);
        foreach ($districtRowList AS $districtNameRaw) {
            $districtNameRawNoEmpty = trim($districtNameRaw);
            if (array_key_exists($districtNameRawNoEmpty, $replaceList)) {
                $districtNameNormalized = $replaceList[$districtNameRawNoEmpty];
            } else {
                $districtNameNormalized = $districtNameRawNoEmpty;
            }
            $districtName = $districtNameNormalized;

            $this->districtList[$districtName] = $districtName;
            $district[$districtName] = $districtName;
        }

        return $district;
    }

    private function processName($type, $nameRaw)
    {
        $nameNormazlized = $this->namePrepare($nameRaw);

        $name = null;
        if (preg_match($this->patternStreetName, $nameNormazlized)) {
            $hash = $this->getStreetNameHash($type, $nameNormazlized);
            if (array_key_exists($hash, $this->nameList)) {
                if (array_key_exists($hash, $this->nameDouble)) {
                    $this->nameDouble[$hash]++;
                } else {
                    $this->nameDouble[$hash] = 2;
                }
            }
            $this->nameList[$hash] = $nameNormazlized;
            $name = $nameNormazlized;
        } else {
            $this->idError['name_'.$this->fileRowNumber] = 'street name "'.$type . ' ' . $nameNormazlized.'" has forbidden symbols in the string #'.$this->fileRowNumber.'';
            $this->nameNotValid[] = $type . ' ' . $nameNormazlized;
        }

        return $name;
    }

    private function namePrepare($nameRaw)
    {
        $name1 = str_replace('’', '\'', $nameRaw);
        $name2 = preg_replace('~(.*)( \(Озерна [0-9]{1,2}\))~ui', '$1', $name1);
        $name3 = preg_replace('~(Проектна \- [0-9]{4,6} )\(([0-9]{1,2})(-([а-я]{2})) Абрикосова\)~ui', '$2-а Абрикосова', $name2);

        $replace = $this->getReplaceName();
        if (array_key_exists($name3, $replace)) {
            $name4 = $replace[$name3];
        } else {
            $name4 = $name3;
        }

        $name = trim($name4);

        return $name;
    }

    /**
     * Default initilization type whitelist
     */
    private function initTypeWhitelist()
    {
        $this->typeWhitelist = [
            'alley' => 'алея',
            'boulevard' => 'бульвар',
            'entry' => 'в\'їзд',
            'street' => 'вулиця',
            'road' => 'дорога',
            'line' => 'лінія',
            'quarter' => 'квартал',
            'quay' => 'набережна',
            'lane' => 'провулок',
            'passage' => 'проїзд',
            'square' => 'площа',
            'avenue' => 'проспект',
            'cul-de-sac' => 'тупик',
            'descent' => 'узвіз',
            'highway' => 'шосе',
        ];
    }

    /**
     * Default initilization file column structure
     */
    private function initFileColumnStructure()
    {
        $this->fileColumnStructure = [
            0 => 'number', /** № п/п */
            1 => 'id', /** Унікальний цифровий код об'єкта */
            2 => 'name_original', /** Повне офіційне найменування об'єкта */
            3 => 'type_name', /** Категорія (тип) об'єкта */
            4 => 'district_string', /** Адміністративний район */
            5 => 'document_name', /** Документ про присвоєння найменування об'єкта */
            6 => 'document_date', /** Дата документу про присвоєння найменування об'єкта */
            7 => 'document_number', /** Номер документу про присвоєння найменування об'єкта */
            8 => 'document_title', /** Заголовок документу про присвоєння найменування об'єкта */
            9 => 'place_description', /** Місце розташування об'єкта на території міста */
            10 => 'name_old', /** Колишнє найменування об'єкта */
            11 => 'type_old', /** Колишня категорія (тип) об'єкта */
        ];
    }

    private function getReplaceDistrict()
    {
        $replace = [
            'Солом’янський' => 'Солом\'янський',
            'Дарницький (Бортничі)' => 'Дарницькій',
            'Дарницький (Бортничі)' => 'Дарницькій',
            'Оболонський (КДТ "Чорнобилець")' => 'Оболонський',
            'Оболонський (СТ "Оболонь")' => 'Оболонський',
            'Оболонський (СТ "Дніпровський-2"' => 'Оболонський',
            'СТ "Фронтовик")' => 'Оболонський',
            'Дарницький (СДТ "Стадне")' => 'Дарницький',
            'Святошинський (СТ "Нивки")' => 'Святошинський',
        ];

        return $replace;
    }

    private function getReplaceName()
    {
        $replace = [
            'Героїв Великої Вітчизняної війни' => 'Героїв Великої Вітчизняної Війни',
            'Липківського Василя Митрополита' => 'Митрополита Василя Липківського',
            'Міжозерна (Західно-Кільцева)' => 'Міжозерна',
            'Сікорського Ігоря Авіаконструктора' => 'Авіаконструктора Ігоря Сікорського',
            'Антонова Авіаконструктора' => 'Авіаконструктора Антонова',
            'Авдєєнка Генерала' => 'Генерала Авдєєнка',
            'Антоненка-Давидовича Бориса' => 'Бориса Антоненка-Давидовича',
            'Ареф\'єва Костянтина' => 'Костянтина Ареф\'єва',
            'Архипенка Олександра' => 'Олександра Архипенка',
            'Ахматової Анни' => 'Анни Ахматової',
            'Багряного Івана' => 'Івана Багряного',
            'Бажана Миколи' => 'Миколи Бажана',
            'Баха Академіка' => 'Академіка Баха',
            'Безручка Марка' => 'Марка Безручка',
            'Беретті Вікентія' => 'Вікентія Беретті',
            'Беретті Вікентія' => 'Вікентія Беретті',
            'Берлінського Максима' => 'Максима Берлінського',
            'Бестужева Олександра' => 'Олександра Бестужева',
            'Білецького Академіка' => 'Академіка Білецького',
            'Білокур Катерини' => 'Катерини Білокур',
            'Біляшівського Академіка' => 'Академіка Біляшівського',
            'Бірюзова Маршала' => 'Маршала Бірюзова',
            'Блока Олександра' => 'Олександра Блока',
            'Богомольця Академіка' => 'Академіка Богомольця',
            'Богуна Івана' => 'Івана Богуна',
            'Бойка Івана' => 'Івана Бойка',
            'Бойчука Михайла' => 'Михайла Бойчука',
            'Боровиченко Марії' => 'Марії Боровиченко',
            'Брановицького Ігоря' => 'Ігоря Брановицького',
            'Буйка Професора' => 'Професора Буйка',
            'Букіної Раїси' => 'Раїси Букіної',
            'Бутлерова Академіка' => 'Академіка Бутлерова',
            'Бучми Амвросія' => 'Амвросія Бучми',
        ];

        return $replace;
    }

}
