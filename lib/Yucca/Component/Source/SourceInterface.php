<?php
namespace Yucca\Component\Source;

interface SourceInterface
{
    public function canHandle($field);
    public function isIdentifier($field);
    public function load(array $identifier);
    public function remove(array $identifier);
    public function save($datas, array $identifier=array(), &$affectedRows=null);
    public function saveAfterLoading($datas, array $identifier=array(), &$affectedRows=null);
}
