<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 03.01.15 - 01:09
 */

namespace ProophTest\Link\ProcessorProxy\DataType;

use Prooph\Processing\Type\AbstractDictionary;
use Prooph\Processing\Type\Description\Description;
use Prooph\Processing\Type\Description\NativeType;
use Prooph\Processing\Type\Integer;
use Prooph\Processing\Type\String;

/**
 * Class TestUser
 *
 * @package SqlConnectorTest\DataType
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class TestUser extends AbstractDictionary
{

    /**
     * @return array[propertyName => Prototype]
     */
    public static function getPropertyPrototypes()
    {
        return [
            'id' => Integer::prototype(),
            'name' => String::prototype(),
            'age' => Integer::prototype()
        ];
    }

    /**
     * @return Description
     */
    public static function buildDescription()
    {
        return new Description('TestUser', NativeType::DICTIONARY, true, 'id');
    }
}
 