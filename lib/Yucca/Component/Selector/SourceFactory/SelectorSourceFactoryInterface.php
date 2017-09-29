<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Selector\SourceFactory;

/**
 * Interface SelectorSourceFactoryInterface
 * @package Yucca\Component\Selector\SourceFactory
 */
interface SelectorSourceFactoryInterface
{
    /**
     * build factory
     * @abstract
     * @return \Yucca\Component\Selector\Source\SelectorSourceInterface
     */
    public function getSource();
}
