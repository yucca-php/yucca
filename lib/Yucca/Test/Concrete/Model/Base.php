<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Test\Concrete\Model;

class Base extends \Yucca\Model\ModelAbstract
{
    protected $fakeId;

    public function getId()
    {
        return $this->fakeId;
    }

    public function setId($id)
    {
        $this->fakeId = $id;
    }

    public function getYuccaMappingManager()
    {
        return $this->yuccaMappingManager;
    }

    public function getYuccaEntityManager()
    {
        return $this->yuccaEntityManager;
    }

    public function getYuccaIdentifier()
    {
        return $this->yuccaIdentifier;
    }

    public function getYuccaShardingKey()
    {
        return $this->yuccaShardingKey;
    }

    public function getYuccaSelectorManager()
    {
        return $this->yuccaSelectorManager;
    }
}
