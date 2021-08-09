<?php

declare(strict_types=1);

namespace Vgip\Datanorm\Directory\Address\Country\Ukr;

use Vgip\Datanorm\Directory\Address\Country\Ukr\StreetNormalizedList;

/**
 * Check and normalize street names
 * Example: "Anysurname Адмірала" to "Адмірала Anysurname"
 */
class StreetNormalization
{
    private array $part2swap = [
        'Адмірала',
        'Архітектора',
        'Академіка',
        'Генерала',
        'Композитора',
        'Полковника',
        'Професора',
        'Маршала',
        
        'Міста',
        'Родини',
        'Сімʼї',
        
        'Андрія',
        'Алли',
        'Анатолія',
        'Адама',
        'Алішера',
        'Богдана',
        'Бориса',
        'Валерія',
        'Вадима',
        'Валентина',
        'Валі',
        'Варвари',
        'Василя',
        'Вільгельма',
        'Віталія',
        'Вікентія',
        'Віктора',
        'Владислава',
        'Володі',
        'Володимира',
        'Всеволода',
        'Вʼячеслава',
        'Дмитра',
        'Ганни',
        'Георгія',
        'Гната',
        'Григорія',
        'Данила',
        'Євгена',
        'Євгенії',
        'Єлизавети',
        'Жамбила',
        'Зої',
        'Івана',
        'Ігоря',
        'Іллі',
        'Казимира',
        'Карла',
        'Клавдії',
        'Клари',
        'Костянтина',
        'Лариси',
        'Леоніда',
        'Леоніли',
        'Лізи',
        'Луки',
        'Льва',
        'Людмили',
        'Марії',
        'Марини',
        'Максима',
        'Миколи',
        'Мирослава',
        'Михайла',
        'Наталії',
        'Натана',
        'Ованеса',
        'Олексія',
        'Олега',
        'Ольги',
        'Олекси',
        'Олени',
        'Олеся',
        'Олександра',
        'Оксани',
        'Отто',
        'Павла',
        'Петра',
        'Пилипа',
        'Платона',
        'Раїси',
        'Руслана',
        'Ріхарда',
        'Ромена',
        'Салавата',
        'Семена',
        'Сергія',
        'Симона',
        'Соломії',
        'Софії',
        'Степана',
        'Стефана',
        'Сулеймана',
        'Тараса',
        'Тетяни',
        'Устима',
        'Федора',
        'Феодори',
        'Шота',
        'Юліуса',
        'Юрія',
        'Якова',
        'Ярослава',
    ];
    
    /**
     * 
     * @param array $streetList
     * @return array
     */
    public function normalize(array $streetList): array
    {
        $res = [];
        $countt = 0;
        $streetNormalizedListObj = StreetNormalizedList::getInstance();
        $streetNormalizedList = $streetNormalizedListObj->getNormalization();
        foreach ($streetList AS $key => $name) {
            if (in_array($name, $streetNormalizedList)) {
                //$res[$key] = $name;
            } else {
                $nameExplode = explode(' ', $name);
                $nameExplodeCount = count($nameExplode);
                if ($nameExplodeCount > 1) {
                    if (2 === $nameExplodeCount) {
                        if (preg_match('~^[0-9]+$~u', $nameExplode[1])) {
                            continue;
                        }
                        if (in_array($nameExplode[1], $this->part2swap)) {
                            echo '+++'.$this->part2swap($nameExplode)."\n";
                            $res[$name] = $this->part2swap($nameExplode);
                            $countt++;
                            continue;
                        } 
                    }
                    
                    echo ''.join(' ', $nameExplode)."\n";
                }
            }
        }
        
        //echo '---'.$countt.'---';
        
        return $res;
    }
    
    private function part2swap(array $explodeName): string
    {
        $string = $explodeName[1].' '.$explodeName[0];
        
        return $string;
    }
}
