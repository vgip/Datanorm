<?php

declare(strict_types=1);

namespace Vgip\Datanorm\Parcer\Address\Ukr\Kyiv;

use Vgip\Datanorm\Directory\Lang\Ukr\Apostrophe;

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
    private string $filePath = 'KyivStreet.csv';

    /**
     * File column name
     * 
     * Column serial number to key accordance
     * 
     * @var array 
     */
    private array $fileColumnStructure = [];

    /**
     * Type street whitelist
     * 
     * @var array
     */
    private array $typeWhitelist = [];
    
    /**
     * Kyiv district whitelist
     * 
     * @var array
     */
    private array $districtWhitelist = [];
    
    /**
     * Street name normalization
     * 
     * @var array
     */
    private array $streetNormalization = [];

    /**
     * Street name pattern
     * 
     * The pattern that the street name must match
     * 
     * @var string - pattern to preg_match()
     */
    private string $patternStreetName;

    /**
     * Source identifier
     * 
     * All unique source identifier
     * 
     * @var array
     */
    private array $id = [];
    
    /**
     * Street type
     * 
     * Unique street type from file
     * 
     * @var array|null
     */
    private ?array $typeList;

    /**
     * Street name list
     * 
     * @var array
     */
    private array $nameList = [];

    /**
     * File row number
     * 
     * @var int 
     */
    private int $fileRowNumber = 0;

    /**
     * Counter of street types
     * 
     * @var type array|null
     */
    private ?array $typeCounter;
    
    /**
     * Found some warnins
     * 
     * If present warnings, then count($this->warning) > 0 else count($this->warning) == 0
     * 
     * @var array|null 
     */
    private ?array $warning = null;
    
    /**
     * Source identifier error
     * 
     * @var array
     */
    private array $idError = [];

    /**
     * Type not found in $this->typeWhitelist
     *
     * @var array|null
     */
    private array $typeNotFound = [];
    
    /**
     * District found in file not in whitelist
     * 
     * Unique district names from file
     * 
     * @var array|null
     */
    private array $districtNotFound = [];

    /**
     * Name double in city 
     * 
     * @var array
     */
    private array $nameDouble = [];

    /**
     * Street name not valid (contain forbidden symbols)
     * 
     * Pattern valid street name: $this->patternStreetName
     * 
     * @var array - [streetname, streetname, ...]
     */
    private array $nameNotValid = [];

    public function __construct()
    {
        $this->initFileColumnStructure();/** Init default file column structure */;
    }

    public function getCsvAsArray()
    {
        $this->initTypeCounter();/** Set 0 (zero) to all keys of counter $this->typeCounter - ONLY AFTER initTypeCounter set! */;

        $dataCsv = null;

        $csvFileString = iconv("Windows-1251", "UTF-8", file_get_contents($this->filePath));
        $csvFileApostroph = Apostrophe::convertApostrophe($csvFileString);

        $data = str_getcsv($csvFileApostroph, "\r\n");

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

        $this->generateWarning();
        
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
    
    public function getDistrictWhitelist()
    {
        return $this->districtWhitelist;
    }

    public function getPatternStreetName()
    {
        return $this->patternStreetName;
    }

    public function getId()
    {
        return $this->id;
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

    public function getWarning(): ?array
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
    
    public function getDistrictNotFound()
    {
        return $this->districtNotFound;
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
    
    public function setDistrictWhitelist($districtWhitelist)
    {
        $this->districtWhitelist = $districtWhitelist;
    }
    
    public function setStreetNormalization(array $streetNormalization): void
    {
        $this->streetNormalization = $streetNormalization;
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

    /**
     * Get the districts to the street
     * 
     * One street can be located in 2 or more districts
     * 
     * @param string $districtRow
     * @return array|null
     */
    private function processDistrict($districtRow)
    {
        $district = [];
        
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

            $districtKey = array_search($districtName, $this->districtWhitelist);
            if ($districtKey) {
                $district[$districtKey] = $districtName;
            } else {
                $this->districtNotFound[$districtName] = $districtName;
            }
            
            
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

        if (array_key_exists($name3, $this->streetNormalization)) {
            $name4 = $this->streetNormalization[$name3];
        } else {
            $name4 = $name3;
        }

        $name = trim($name4);

        return $name;
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
            'Дарницький (Бортничі)' => 'Дарницький',
            'Дарницький (Бортничі)' => 'Дарницький',
            'Оболонський (КДТ "Чорнобилець")' => 'Оболонський',
            'Оболонський (СТ "Оболонь")' => 'Оболонський',
            'Оболонський (СТ "Дніпровський-2"' => 'Оболонський',
            'СТ "Фронтовик")' => 'Оболонський',
            'Дарницький (СДТ "Стадне")' => 'Дарницький',
            'Святошинський (СТ "Нивки")' => 'Святошинський',
        ];

        return $replace;
    }
    
    private function generateWarning()
    {
        if (count($this->typeNotFound) > 0) {
            $this->warning['type_new'][0] = 'new street types found: ' . join(', ', $this->typeNotFound) . '';
        }
        if (count($this->districtNotFound) > 0) {
            $this->warning['type_new'][0] = 'new district names found: ' . join(', ', $this->districtNotFound) . '';
        }
        if (count($this->nameNotValid) > 0) {
            $this->warning['name_not_valid'][0] = 'street names contain forbidden symbols: ' . join(', ', $this->nameNotValid) . '';
        }
        foreach ($this->idError AS $key => $value) {
            $this->warning[$key] = $value;
        }
    }
}
