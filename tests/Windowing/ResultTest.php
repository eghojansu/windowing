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

use Fal\Windowing\Result;
use Fal\Windowing\Source;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    private $result;

    public function setup()
    {
        $this->result = new Result(new Source('foo'), new Source('bar'), 0.0);
    }

    public function testGetSource()
    {
        $this->assertInstanceOf('Fal\\Windowing\\Source', $this->result->getSource());
    }

    public function testGetComparator()
    {
        $this->assertInstanceOf('Fal\\Windowing\\Source', $this->result->getComparator());
    }

    public function testGetCoefficient()
    {
        $this->assertEquals(0.0, $this->result->getCoefficient());
    }

    public function testGetPercentage()
    {
        $this->assertEquals(0.00, $this->result->getPercentage());
    }
}
