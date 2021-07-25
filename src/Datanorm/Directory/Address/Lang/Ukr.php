<?php

namespace Vgip\Datanorm\Directory\Address\Lang;

/**
 * Regions, locality and street types
 */
class Ukr
{
    private $regionWhitelist = [
        'vinnytska' => 'Вінницька область',
        'volynska' => 'Волинська область',
        'dnipropetrovska' => 'Дніпропетровська область',
        'donetska' => 'Донецька область',
        'zhytomyrska' => 'Житомирська область',
        'zakarpatska' => 'Закарпатська область',
        'zaporizka' => 'Запорізька область',
        'ivanofrankivska' => 'Івано-Франківська область',
        'kyiv' =>'Київ',
        'kyivska' => 'Київська область',
        'kirovohradska'  => 'Кіровоградська область',
        'krym' => 'Автономна Республіка Крим',
        'luhanska'  => 'Луганська область',
        'lvivska'  => 'Львівська область',
        'mykolaivska'  => 'Миколаївська область',
        'odeska'  => 'Одеська область',
        'poltavska' => 'Полтавська область',
        'rivnenska' => 'Рівненська область',
        'sevastopol' => 'Севастополь',
        'sumska' => 'Сумська область',
        'ternopilska' => 'Тернопільська область',
        'kharkivska' => 'Харківська область',
        'khersonska' => 'Херсонська область',
        'khmelnytska' => 'Хмельницька область',
        'cherkaska' => 'Черкаська область',
        'chernivetska' => 'Чернівецька область',
        'chernihivska' => 'Чернігівська область',
    ];

    private $localityTypeWhitelist = [
        'city'                  => 'місто',
        'uts'                   => 'смт',
        'settlement'            => 'селище',
        'vilage'                => 'село',
    ];
    
    /** 
     * Locality type abbreviation whitelist UA
     * 
     * See Spaces after name 
     * Place the abbreviation close to the name of the locality without spaces
     * https://zakon.rada.gov.ua/laws/show/v0048359-09#Text 6.6.4
     * 
     * @var string
     */
    private $localityTypeAbbreviationWhitelist = [
        'city'                  => 'м.',
        'uts'                   => 'смт ',
        'settlement'            => 'с-ще ',
        'vilage'                => 'с.',
    ];
    
    /**
     * Street type wheitelist RU
     * 
     * @var string
     */
    private $streetTypeWhitelist = [
        'alley'                 => 'алея',
        'boulevard'             => 'бульвар',
        'entry'                 => 'в\'їзд',
        'street'                => 'вулиця',
        'road'                  => 'дорога',
        'line'                  => 'лінія',
        'quarter'               => 'квартал',
        'quay'                  => 'набережна',
        'maydan'                => 'майдан',
        'lane'                  => 'провулок',
        'passage'               => 'проїзд',
        'square'                => 'площа',
        'avenue'                => 'проспект',
        'cul-de-sac'            => 'тупик',
        'descent'               => 'узвіз',
        'highway'               => 'шосе',
    ];

    private $streetTypeAbbreviationWhitelist = [
        'alley'                 => 'алея',
        'boulevard'             => 'бульв.',
        'entry'                 => 'в\'їзд',
        'street'                => 'вул.',
        'road'                  => 'дорога',
        'line'                  => 'лінія',
        'quarter'               => 'квартал',
        'quay'                  => 'набережна',
        'maydan'                => 'майдан',
        'lane'                  => 'пров.',
        'passage'               => 'проїзд',
        'square'                => 'пл',
        'avenue'                => 'просп.',
        'cul-de-sac'            => 'тупик',
        'descent'               => 'узвіз',
        'highway'               => 'шосе',
    ];
    
    public function getRegionWhitelist()
    {
        return $this->regionWhitelist;
    }

    public function getLocalityTypeWhitelist()
    {
        return $this->localityTypeWhitelist;
    }

    public function getLocalityTypeAbbreviationWhitelist()
    {
        return $this->localityTypeAbbreviationWhitelist;
    }

    public function getStreetTypeWhitelist()
    {
        return $this->streetTypeWhitelist;
    }

    public function getStreetTypeAbbreviationWhitelist()
    {
        return $this->streetTypeAbbreviationWhitelist;
    }

}
