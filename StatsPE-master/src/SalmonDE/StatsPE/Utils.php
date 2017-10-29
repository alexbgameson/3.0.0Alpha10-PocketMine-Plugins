<?php
namespace SalmonDE\StatsPE;

use SalmonDE\StatsPE\Providers\Entry;

class Utils
{

    public static function getPeriodFromSeconds(int $seconds) : string{
        $ref = new \DateTime(date('Y-m-d H:i:s', 0));
        $time = $ref->diff(new \DateTime(date('Y-m-d H:i:s', $seconds)));

        $time = ($time->y !== 0 ? $time->y.'y ' : '').($time->m !== 0 ? $time->m.'m ' : '').($time->d !== 0 ? $time->d.'d ' : '').$time->h.'h '.$time->i.'i '.$time->s.'s';

        $units = [
            Base::getInstance()->getMessage('general.onlinetime.years'),
            Base::getInstance()->getMessage('general.onlinetime.months'),
            Base::getInstance()->getMessage('general.onlinetime.days'),
            Base::getInstance()->getMessage('general.onlinetime.hours'),
            Base::getInstance()->getMessage('general.onlinetime.minutes'),
            Base::getInstance()->getMessage('general.onlinetime.seconds')
        ];
        return str_replace(['y', 'm', 'd', 'h', 'i', 's'], $units, $time);
    }

    public static function getKD(int $kills, int $deaths) : float{
        return round($kills / ($deaths !== 0 ? $deaths : 1), 2);
    }

    public static function convertValueSave(Entry $entry, $value){
        switch($entry->getExpectedType()){
            case Entry::INT:
            case Entry::FLOAT:
            case Entry::STRING:
            case Entry::MIXED:
                return $value;

            case Entry::ARRAY:
                return serialize($value);

            case Entry::BOOL:
                return (int) $value;
        }
    }

    public static function convertValueGet(Entry $entry, $value){
        switch($entry->getExpectedType()){
            case Entry::INT:
                return (int) $value;

            case Entry::FLOAT:
                return (float) $value;

            case Entry::STRING:
            case Entry::MIXED:
                return $value;

            case Entry::ARRAY:
                return unserialize($value);

            case Entry::BOOL:
                return $value === 0 ? false : true;
        }
    }

    public static function getMySQLDatatype(int $type) : string{
        switch($type){
            case Entry::INT:
                return 'BIGINT(255)';

            case Entry::FLOAT:
                return 'DECIMAL(65, 3)';

            case Entry::STRING:
                return 'VARCHAR(255)';

            case Entry::ARRAY:
                return 'VARCHAR(255)';

            case Entry::BOOL:
                return 'BIT(1)';

            case Entry::MIXED:
                return 'VARCHAR(255)';
        }
    }
}
