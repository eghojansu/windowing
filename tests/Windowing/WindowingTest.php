<?php

/**
 * This file is part of the eghojansu/windowing library.
 *
 * (c) Eko Kurniawan <ekokurniawanbs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fal\Windowing\Test;

use Fal\Windowing\Windowing;
use PHPUnit\Framework\TestCase;

class WindowingTest extends TestCase
{
    private $windowing;

    public function setup()
    {
        $this->windowing = new Windowing();
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Fal\\Windowing\\Windowing', Windowing::create());
    }

    public function testGetPrimeNumber()
    {
        $this->assertEquals(3, $this->windowing->getPrimeNumber());
    }

    public function testSetPrimeNumber()
    {
        $this->assertEquals(2, $this->windowing->setPrimeNumber(2)->getPrimeNumber());

        $this->expectException('LogicException');
        $this->expectExceptionMessage('Not a prime number: 4.');
        $this->windowing->setPrimeNumber(4);
    }

    public function testGetNGramValue()
    {
        $this->assertEquals(2, $this->windowing->getNGramValue());
    }

    public function testSetNGramValue()
    {
        $this->assertEquals(1, $this->windowing->setNGramValue(1)->getNGramValue());
    }

    public function testGetNWindowValue()
    {
        $this->assertEquals(4, $this->windowing->getNWindowValue());
    }

    public function testSetNWindowValue()
    {
        $this->assertEquals(1, $this->windowing->setNWindowValue(1)->getNWindowValue());
    }

    public function testCompare()
    {
        $result = $this->windowing->compare('Indonesia Raya', 'Indonesia Jaya');

        $this->assertInstanceOf('Fal\\Windowing\\Result', $result);
        $this->assertEquals(38.46, $result->getPercentage());
    }

    /**
     * @dataProvider isPrimeNumberProvider
     */
    public function testIsPrimeNumber($expected, $number)
    {
        $this->assertEquals($expected, $this->windowing->isPrimeNumber($number));
    }

    public function isPrimeNumberProvider()
    {
        return array(
            array(true, 1),
            array(true, 2),
            array(true, 3),
            array(true, 5),
            array(true, 7),
            array(true, 11),
            array(true, 13),
            array(false, 14),
        );
    }
}
