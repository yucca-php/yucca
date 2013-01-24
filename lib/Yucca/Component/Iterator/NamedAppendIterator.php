<?php
namespace Yucca\Component\Iterator;

class NamedAppendIterator extends \AppendIterator {
    protected $names = array();

    public function append (\Iterator $iterator, $name='') {
        parent::append($iterator);
        $this->names[] = $name;
    }

    public function name() {
        return $this->names[$this->getIteratorIndex()];
    }
}
