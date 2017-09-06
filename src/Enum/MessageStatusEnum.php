<?php

namespace Hgabka\KunstmaanEmailBundle\Enum;

abstract class MessageStatusEnum
{
    const TYPE_INIT = "init";
    const TYPE_KULDENDO = "kuldendo";
    const TYPE_FOLYAMATBAN = "folyamatban";
    const TYPE_ELKULDVE = "elkuldve";

    /** @var array user friendly named type */
    protected static $typeName = [
        self::TYPE_INIT    => 'Létrehozva',
        self::TYPE_KULDENDO => 'Küldésre megjelölve',
        self::TYPE_FOLYAMATBAN => 'Küldés alatt',
        self::TYPE_ELKULDVE  => 'Elküldve',
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
            self::TYPE_KULDENDO,
            self::TYPE_FOLYAMATBAN,
            self::TYPE_ELKULDVE
        ];
    }
}