<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanEmailBundle\Enum;

abstract class MessageStatusEnum
{
    const STATUS_INIT = 'init';
    const STATUS_KULDENDO = 'kuldendo';
    const STATUS_FOLYAMATBAN = 'folyamatban';
    const STATUS_ELKULDVE = 'elkuldve';

    /** @var array user friendly named type */
    protected static $statusName = [
        self::STATUS_INIT => 'Létrehozva',
        self::STATUS_KULDENDO => 'Küldésre megjelölve',
        self::STATUS_FOLYAMATBAN => 'Küldés alatt',
        self::STATUS_ELKULDVE => 'Elküldve',
    ];

    /**
     * @param string $statusShortName
     *
     * @return string
     */
    public static function getStatusName($statusShortName)
    {
        if (!isset(static::$statusName[$statusShortName])) {
            return "Unknown type ($statusShortName)";
        }

        return static::$statusName[$statusShortName];
    }

    /**
     * @return array<string>
     */
    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_INIT,
            self::STATUS_KULDENDO,
            self::STATUS_FOLYAMATBAN,
            self::STATUS_ELKULDVE,
        ];
    }
}
