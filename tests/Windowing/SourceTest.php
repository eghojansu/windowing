<?php

/**
 * This file is part of the eghojansu/windowing library.
 *
 * (c) Eko Kurniawan <ekokurniawanbs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Created at Feb 14, 2019 15:59
 */

declare(strict_types=1);

namespace Fal\Windowing\Test;

use Fal\Windowing\Source;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    private $source;

    public function setup()
    {
        $this->source = new Source('foobar');
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Fal\\Windowing\\Source', Source::create('foo'));
    }

    public function testGetText()
    {
        $this->assertEquals('foobar', $this->source->getText());
    }

    public function testSetText()
    {
        $this->assertEquals('bar', $this->source->setText('bar')->getText());

        $this->expectException('LogicException');
        $this->expectExceptionMessage('Source text empty!');
        $this->source->setText('');
    }

    public function testGetNGram()
    {
        $this->assertNull($this->source->getNGram());
    }

    public function testGetRollingHash()
    {
        $this->assertNull($this->source->getRollingHash());
    }

    public function testGetWindowTable()
    {
        $this->assertNull($this->source->getWindowTable());
    }

    public function testGetFingerprints()
    {
        $this->assertNull($this->source->getFingerprints());
    }

    public function testCalculate()
    {
        $this->source->calculate();

        $nGram = array('fo', 'oo', 'ob', 'ba', 'ar');
        $rollingHash = array(1251, 1332, 1293, 1173, 1215);
        $windowTable = array(array(1251, 1332, 1293, 1173), array(1332, 1293, 1173, 1215));
        $fingerprints = array(1173, 1173);

        $this->assertEquals($nGram, $this->source->getNGram());
        $this->assertEquals($rollingHash, $this->source->getRollingHash());
        $this->assertEquals($windowTable, $this->source->getWindowTable());
        $this->assertEquals($fingerprints, $this->source->getFingerprints());
    }

    public function testCalculateHashOne()
    {
        $this->source->calculate(1);

        $nGram = array('f', 'o', 'o', 'b', 'a', 'r');
        $rollingHash = array(102, 111, 111, 98, 97, 114);
        $windowTable = array(
          array(102, 111, 111, 98),
          array(111, 111, 98, 97),
          array(111, 98, 97, 114),
        );
        $fingerprints = array(98, 97, 97);

        $this->assertEquals($nGram, $this->source->getNGram());
        $this->assertEquals($rollingHash, $this->source->getRollingHash());
        $this->assertEquals($windowTable, $this->source->getWindowTable());
        $this->assertEquals($fingerprints, $this->source->getFingerprints());
    }
}
