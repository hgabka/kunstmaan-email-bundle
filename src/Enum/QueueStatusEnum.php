<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.05.
 * Time: 12:37
 */

namespace Hgabka\KunstmaanEmailBundle\Enum;

abstract class QueueStatusEnum
{
    const TYPE_INIT = "init";
    const TYPE_ELKULDVE = "elkuldve";
    const TYPE_HIBA = "hiba";
    const TYPE_SIKERTELEN = "sikertelen";

    /** @var array user friendly named type */
    protected static $typeName = [
        self::TYPE_INIT    => 'Létrehozva',
        self::TYPE_ELKULDVE => 'Elküldve',
        self::TYPE_HIBA => 'Hiba',
        self::TYPE_SIKERTELEN  => 'Sikertelen',
    ];

    /**
     * @param  string $typeShortName
     * @return string
     */
    public static function getStatusName($typeShortName)
    {
        if (!isset(static::$typeName[$typeShortName])) {
            return "Unknown type ($typeShortName)";
        }

        return static::$typeName[$typeShortName];
    }

    /**
     * @return array<string>
     */
    public static function getAvailableStatuses()
    {
        return [
            self::TYPE_INIT,
            self::TYPE_ELKULDVE,
            self::TYPE_HIBA,
            self::TYPE_SIKERTELEN
        ];
    }
}