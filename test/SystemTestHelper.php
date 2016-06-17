<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Error\Test;

use Es\System\System;

class SystemTestHelper extends System
{
    public static function resetSystem()
    {
        System::$instance = null;
    }
}
