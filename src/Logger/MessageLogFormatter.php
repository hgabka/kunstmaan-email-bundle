<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.06.
 * Time: 11:44
 */

namespace Hgabka\KunstmaanEmailBundle\Logger;

use Monolog\Formatter\LineFormatter;

class MessageLogFormatter extends LineFormatter
{
    const SIMPLE_FORMAT = "[%datetime%] %message%\n";
}