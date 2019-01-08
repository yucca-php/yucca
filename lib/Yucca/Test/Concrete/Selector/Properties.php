<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Test\Concrete\Selector;

/**
 * Class Properties
 * @package Yucca\Test\Concrete\Selector
 */
class Properties extends Base
{
    /**
     * @param string $criteria
     */
    public function addFirstCriteria($criteria)
    {
        $this->criterias['first'] = $criteria;
    }
}
