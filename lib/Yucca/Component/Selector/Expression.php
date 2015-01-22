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

class Expression
{
    /**
     * @var array
     */
    protected $texts;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param array $text
     * @param array $params
     */
    public function __construct(array $text, array $params=array())
    {
        $this->text = $text;
        $this->params = $params;
    }

    /**
     * @param $handler
     * @return mixed
     * @throws \Exception
     */
    public function toString($handler)
    {
        if(false === isset($this->text[$handler])){
            throw new \Exception('Missing handler '.$handler.' for expression : '.var_export($handler, true));
        }

        return $this->text[$handler];
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
