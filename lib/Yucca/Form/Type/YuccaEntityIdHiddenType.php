<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class YuccaEntityIdHiddenType
 * @package Yucca\Form\Type
 */
class YuccaEntityIdHiddenType extends YuccaEntityIdType
{
    /**
     * @return mixed
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
