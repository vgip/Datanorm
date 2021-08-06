<?php

namespace Vgip\Datanorm\Directory\Address\Country\Ukr\City;

use Vgip\Datanorm\Common\Singleton;

class Kyiv
{
    use Singleton;
    
    private $districtWhitelist = [
        'holosiivskyi' => 'Голосіївський',
        'darnytskyi' => 'Дарницький',
        'desnianskyi' => 'Деснянський',
        'dniprovskyi' => 'Дніпровський',
        'obolonskyi' => 'Оболонський',
        'pecherskyi' => 'Печерський',
        'podilskyi' => 'Подільський',
        'sviatoshynskyi' => 'Святошинський',
        'solomianskyi' => 'Соломʼянський',
        'shevchenkivskyi' => 'Шевченківський',
    ];
    
    /**
     * Get the list of all districts of Kyiv
     * 
     * @return array
     */
    public function getDistrictWhitelist()
    {
        return $this->districtWhitelist;
    }
    
    /**
     * Is exists district name
     * 
     * @param string $name
     * @return boolean
     */
    public function isDistrictNameExists($name) 
    {
        $res = false;
        if (in_array($name, $this->districtWhitelist)) {
            $res = true;
        }
        
        return $res;
    }
}
