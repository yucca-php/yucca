<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yucca\Test\Component\Selector;

use Yucca\Component\Selector\Expression;

class ExpressionTest extends \PHPUnit_Framework_TestCase {

    public function test_toString(){
        $expression = new Expression(
            array('database'=>'1=1')
        );
        $this->assertEquals('1=1', $expression->toString('database'));

        try {
            $expression->toString('memcache');
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('Missing handler', $e->getMessage());
        }
    }

    public function test_getParams(){
        $params = array('param1'=>'firstValue',md5(mt_rand())=>mt_rand(0,100));
        $expression = new Expression(
            array('database'=>'1=1'),
            $params
        );
        $this->assertEquals($params, $expression->getParams());
    }

}
