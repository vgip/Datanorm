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
     * @var array - ['street name as in file' => 'normalized name']
     */
    private array $streetNormalization = [
        'Міжозерна (Західно-Кільцева)' => 'Міжозерна',
    ];

    /**
     * Street name pattern
     * 
     * The pattern that the street name must match
     * 
     * @var string - pattern to preg_match()
     */
    private string $patternStreetName = '~[0-9абвгґдеєжзиіїйклмнопрстуфхцчшщьюяҐІЇЄ\s\-\.]*~ui';
    
    /**
     * File column name
     * 
     * Column serial number to key accordance
     * 
     * @var array 
     */
    private array $fileColumnStructure = [];

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
    private ?array $typeList = null;

    /**
     * Street name list
     * 
     * This data save after csv file processing
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
     * Quantity of all street types in Kyiv
     * 
     * @var type array|null - [street] => 2089, [road] => 5, ...
     */
    private ?array $typeCounter = null;
    
    /**
     * Name double in city 
     * 
     * @var array
     */
    private array $nameDouble = [];
    
    /**
     * Found some warnings
     * 
     * If present warnings, then count($this->warning) > 0 else count($this->warning) == 0
     * 
     * @var array|null 
     */
    private ?array $warning = null;
    
    /**
     * Source values error
     * 
     * @var array
     */
    private array $warningValue = [];

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
     * Street name not valid (contain forbidden symbols)
     * 
     * Pattern valid street name: $this->patternStreetName
     * 
     * @var array - [streetname, streetname, ...]
     */
    private array $nameNotValid = [];

    public function __construct()
    {
        $this->initFileColumnStructure(); /** Init default file column structure */
    }

    /**
     * Get array with normalized data from CSV file
     * 
     * Check and normalized street name data.
     * - Convert form a file charset Windows-1251 to UTF-8.
     * - Convert possible apostrophe symbols to one symbol (ʼ - 02BC).
     * - Check id (forbidden symbols, double). If error see to $this->warning. 
     * - Check street type by whitelist. 
     *   New type save to $this->warning and this->typeNotFound.
     * - Check Kyiv district name by whitelist.
     *   New Kyiv district name save to $this->warning and this->districtNotFound.
     * - Check the street names and normalized street names .
     *   (if data saved to $this->streetNormalization array)
     * - Generate $this->nameDouble array - save 2 or more double street name.
     * - Generate $this->nameList - all unique street names.
     * - Generate $this->typeCounter - quantity of all street types in Kyiv.
     * 
     * @param string|null $filePath
     * @return array|null
     */
    public function getCsvAsArray(?string $filePath = null): ?array
    {
        /** Set 0 (zero) to all keys of counter $this->typeCounter - ONLY AFTER initTypeCounter set! */;
        $this->initTypeCounter();
        
        /** Set $this->filePath file path if not null */
        $this->setFilePath($filePath);

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
            if (null === $type) {
                $csvRow['name'] = '';
            } else {
                $name = $this->processName($type, $csvRow['name_original']);
                $csvRow['name'] = $name;
            }

            $dataCsv[] = $csvRow;
        }

        $this->generateWarning();
        
        return $dataCsv;
    }

    /**
     * Get hash for street (type + street name)
     * 
     * @param string $type
     * @param string $name
     * @return string
     */
    public function getStreetNameHash(string $type, string $name): string
    {
        $deleteSymbolName = [' ', '\'', '’', '-'];
        $typeForHash = mb_strtolower(str_replace('\'', '', $type));
        $nameForHash = mb_strtolower(str_replace($deleteSymbolName, '', $name));

        $hash = $typeForHash . $nameForHash;

        return $hash;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getTypeWhitelist(): array
    {
        return $this->typeWhitelist;
    }
    
    public function getDistrictWhitelist(): array
    {
        return $this->districtWhitelist;
    }

    public function getPatternStreetName(): string
    {
        return $this->patternStreetName;
    }

    public function getId(): array
    {
        return $this->id;
    }

    public function getTypeList(): ?array
    {
        return $this->typeList;
    }

    public function getNameList(): array
    {
        return $this->nameList;
    }

    public function getFileRowNumber(): int
    {
        return $this->fileRowNumber;
    }

    public function getTypeCounter(): ?array
    {
        return $this->typeCounter;
    }
    
    public function getNameDouble(): array
    {
        return $this->nameDouble;
    }

    public function getWarning(): ?array
    {
        return $this->warning;
    }
    
    public function getWarningValue(): array
    {
        return $this->warningValue;
    }

    public function getTypeNotFound(): array
    {
        return $this->typeNotFound;
    }
    
    public function getDistrictNotFound(): array
    {
        return $this->districtNotFound;
    }

    public function getNameNotValid(): array
    {
        return $this->nameNotValid;
    }
    
    public function setFilePath(?string $filePathRaw): void
    {
        if (null !== $filePathRaw) {
            $this->filePath = $filePathRaw;
        }
    }

    public function setTypeWhitelist(array $typeWhitelist): void
    {
        $this->typeWhitelist = $typeWhitelist;
    }
    
    public function setDistrictWhitelist(array $districtWhitelist): void
    {
        $this->districtWhitelist = $districtWhitelist;
    }
    
    public function setStreetNormalization(array $streetNormalization): void
    {
        $this->streetNormalization = $streetNormalization;
    }

    public function setPatternStreetName(string $patternStreetName): void
    {
        $this->patternStreetName = $patternStreetName;
    }

    private function initTypeCounter(): void
    {
        foreach ($this->typeWhitelist AS $key => $val) {
            $this->typeCounter[$key] = 0;
        }
    }

    private function processId(?string $idRaw)
    {
        $idRes = null;
        $idErrNum = 0;
        
        $idInt = (int)$idRaw;
        if ((string)$idInt !== (string)$idRaw) {
            $idErrNum++;
            $this->warningValue['id_'.$this->fileRowNumber] = 'identifier in the string #'.$this->fileRowNumber.' "'.$idRaw.'" incorrect';
        } else {
            if (array_key_exists($idInt, $this->id)) {
                $this->warningValue['id_'.$this->fileRowNumber] = 'identifier "'.$idInt.'" in the string #'.$this->fileRowNumber.' is double';
            } else {
                $this->id[$idInt] = $idInt;
                $idRes = $idInt;
            }
        }
        
        return $idRes;
    }

    private function processType(string $typeName): ?string
    {
        $typeKey = array_search($typeName, $this->typeWhitelist);
        if (false === $typeKey) {
            $this->warningValue['type_'.$this->fileRowNumber] = 'new type name "'.$typeName.'" found in the string #'.$this->fileRowNumber.'';
            $this->typeNotFound[$typeName] = '"' . $typeName . '"';
            $typeRes = null;
        } else {
            $this->typeCounter[$typeKey]++;
            $this->typeList[$typeKey] = $typeName;
            $typeRes = (string)$typeKey;
        }

        return $typeRes;
    }

    /**
     * Get the districts to the street
     * 
     * One street can be located in 2 or more districts
     * 
     * @param string $districtRow
     * @return array|null - list of street districts
     */
    private function processDistrict(string $districtRow): array
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

    private function processName(string $type, string $nameRaw): ?string
    {
        $nameNormalized = $this->namePrepare($nameRaw);

        $name = null;
        if (preg_match($this->patternStreetName, $nameNormalized)) {
            $hash = $this->getStreetNameHash($type, $nameNormalized);
            if (array_key_exists($hash, $this->nameList)) {
                if (array_key_exists($hash, $this->nameDouble)) {
                    $this->nameDouble[$hash]++;
                } else {
                    $this->nameDouble[$hash] = 2;
                }
            }
            $this->nameList[$hash] = $nameNormalized;
            $name = $nameNormalized;
        } else {
            $this->warningValue['name_'.$this->fileRowNumber] = 'street name "'.$type . ' ' . $nameNormalized.'" has forbidden symbols in the string #'.$this->fileRowNumber.'';
            $this->nameNotValid[] = $type . ' ' . $nameNormalized;
        }

        return $name;
    }

    private function namePrepare(string $nameRaw): string
    {
        $name = [];
        $name['raw'] = str_replace('’', '\'', $nameRaw);
        /** Example: Берізоньки (Озерна 14) -> Берізоньки */
        $name['raw'] = preg_replace('~(.*)( \(Озерна [0-9]{1,2}\))~ui', '$1', $name['raw']);
        /** Example: Проектна - 13125 (1-ша Абрикосова) -> 1-ша Абрикосова */
        $name['raw'] = preg_replace('~(Проектна \- [0-9]{4,6} )\(([0-9]{1,2}-[а-я]{2}) Абрикосова\)~ui', '$2 Абрикосова', $name['raw']);

        if (array_key_exists($name['raw'], $this->streetNormalization)) {
            $name['raw'] = $this->streetNormalization[$name['raw']];
        }

        $name['raw'] = trim($name['raw']);

        return $name['raw'];
    }

    /**
     * Default initilization file column structure
     */
    private function initFileColumnStructure(): void
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

    private function getReplaceDistrict(): array
    {
        $replace = [
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
    
    private function generateWarning(): void
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
        
        foreach ($this->warningValue AS $key => $value) {
            $this->warning[$key] = $value;
        }
    }
}
