<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\SourceFactory;

/**
 * Interface SourceFactoryInterface
 * @package Yucca\Component\SourceFactory
 */
interface SourceFactoryInterface
{
    /**
     * build factory
     * @abstract
     * @param string $sourceName
     * @param array  $params
     * @return mixed
     */
    public function getSource($sourceName, array $params = array());
}
