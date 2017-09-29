<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Selector;

/**
 * Interface SelectorInterface
 * @package Yucca\Component\Selector
 */
interface SelectorInterface extends \Countable, \Iterator
{
    /**
     * @return mixed
     */
    public function currentShardingKey();

    /**
     * @param array $criteria
     *
     * @return mixed
     */
    public function setCriteria(array $criteria);
}
