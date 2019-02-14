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

namespace Fal\Windowing;

/**
 * Windowing result.
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */
class Result
{
    /**
     * @var Source
     */
    private $source;

    /**
     * @var Source
     */
    private $comparator;

    /**
     * Jaccard coefficient.
     *
     * @var float
     */
    private $coefficient;

    /**
     * Class constructor.
     *
     * @param Source $source
     * @param Source $comparator
     * @param float  $coefficient
     */
    public function __construct(Source $source, Source $comparator, float $coefficient)
    {
        $this->source = $source;
        $this->comparator = $comparator;
        $this->coefficient = $coefficient;
    }

    /**
     * Returns source.
     *
     * @return Source
     */
    public function getSource(): Source
    {
        return $this->source;
    }

    /**
     * Returns comparator.
     *
     * @return Source
     */
    public function getComparator(): Source
    {
        return $this->comparator;
    }

    /**
     * Returns coefficient.
     *
     * @return float
     */
    public function getCoefficient(): float
    {
        return $this->coefficient;
    }

    /**
     * Returns coefficient in percent.
     *
     * @param int $round
     *
     * @return float
     */
    public function getPercentage(int $round = 2): float
    {
        return round($this->coefficient * 100, $round);
    }
}
