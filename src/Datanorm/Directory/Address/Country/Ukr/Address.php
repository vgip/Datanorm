<?php

declare(strict_types=1);

namespace Vgip\Datanorm\Directory\Address\Country\Ukr;

use Vgip\Datanorm\Common\Singleton;

/**
 * Regions, locality and street types
 */
class Address
{
    use Singleton;
    
    private array $regionWhitelist = [
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

    private array $localityTypeWhitelist = [
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
    private array $localityTypeAbbreviationWhitelist = [
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
    private array $streetTypeWhitelist = [
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

    private array $streetTypeAbbreviationWhitelist = [
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
    
    public function getRegionWhitelist(): array
    {
        return $this->regionWhitelist;
    }

    public function getLocalityTypeWhitelist(): array
    {
        return $this->localityTypeWhitelist;
    }

    public function getLocalityTypeAbbreviationWhitelist(): array
    {
        return $this->localityTypeAbbreviationWhitelist;
    }

    public function getStreetTypeWhitelist(): array
    {
        return $this->streetTypeWhitelist;
    }

    public function getStreetTypeAbbreviationWhitelist(): array
    {
        return $this->streetTypeAbbreviationWhitelist;
    }

}
