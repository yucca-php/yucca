<?php
namespace Yucca\Concrete\Model;

class Base extends \Yucca\Model\ModelAbstract {
    protected $fakeId;

    public function getId() {
        return $this->fakeId;
    }

    public function setId($id) {
        $this->fakeId = $id;
    }

    public function getYuccaMappingManager() {
        return $this->yuccaMappingManager;
    }

    public function getYuccaEntityManager() {
        return $this->yuccaEntityManager;
    }

    public function getYuccaIdentifier(){
        return $this->yuccaIdentifier;
    }

    public function getYuccaSelectorManager(){
        return $this->yuccaSelectorManager;
    }
}