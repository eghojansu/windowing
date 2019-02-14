<?php

/**
 * This file is part of the eghojansu/windowing library.
 *
 * (c) Eko Kurniawan <setiawanegho@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fal\Windowing;

/**
 * Windowing main class.
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */
class Windowing
{
    /**
     * @var int
     */
    private $primeNumber = 3;

    /**
     * @var int
     */
    private $nGramValue = 2;

    /**
     * @var int
     */
    private $nWindowValue = 4;

    /**
     * Class instance creation.
     *
     * @param int $nGramValue
     * @param int $primeNumber
     * @param int $nWindowValue
     *
     * @return Windowing
     */
    public static function create(int $nGramValue = 2, int $primeNumber = 3, int $nWindowValue = 4): Windowing
    {
        return new static($nGramValue, $primeNumber, $nWindowValue);
    }

    /**
     * Class constructor.
     *
     * @param int $nGramValue
     * @param int $primeNumber
     * @param int $nWindowValue
     */
    public function __construct(int $nGramValue = 2, int $primeNumber = 3, int $nWindowValue = 4)
    {
        $this->setNGramValue($nGramValue);
        $this->setPrimeNumber($primeNumber);
        $this->setNWindowValue($nWindowValue);
    }

    /**
     * Returns prime number.
     *
     * @return int
     */
    public function getPrimeNumber(): int
    {
        return $this->primeNumber;
    }

    /**
     * Assign prime number.
     *
     * @param int $primeNumber
     *
     * @return Windowing
     */
    public function setPrimeNumber(int $primeNumber): Windowing
    {
        if (!$this->isPrimeNumber($primeNumber)) {
            throw new \LogicException(sprintf('Not a prime number: %s.', $primeNumber));
        }

        $this->primeNumber = $primeNumber;

        return $this;
    }

    /**
     * Returns n-gram value.
     *
     * @return int
     */
    public function getNGramValue(): int
    {
        return $this->nGramValue;
    }

    /**
     * Assign n-gram value.
     *
     * @param int $nGramValue
     *
     * @return Windowing
     */
    public function setNGramValue(int $nGramValue): Windowing
    {
        $this->nGramValue = $nGramValue;

        return $this;
    }

    /**
     * Returns n-window value.
     *
     * @return int
     */
    public function getNWindowValue(): int
    {
        return $this->nWindowValue;
    }

    /**
     * Assign n-window value.
     *
     * @param int $nWindowValue
     *
     * @return Windowing
     */
    public function setNWindowValue(int $nWindowValue): Windowing
    {
        $this->nWindowValue = $nWindowValue;

        return $this;
    }

    /**
     * Compare text.
     *
     * @param string $sourceText
     * @param string $comparatorText
     *
     * @return Result
     */
    public function compare(string $comparatorText, string $sourceText): Result
    {
        $source = Source::create($sourceText)->calculate($this->nGramValue, $this->primeNumber, $this->nWindowValue);
        $comparator = Source::create($comparatorText)->calculate($this->nGramValue, $this->primeNumber, $this->nWindowValue);
        $coefficient = $this->jaccardCoeficient($source->getFingerprints(), $comparator->getFingerprints());

        return new Result($source, $comparator, $coefficient);
    }

    /**
     * Returns true if number is a prime.
     *
     * @param int $number
     *
     * @return bool
     */
    public function isPrimeNumber(int $number): bool
    {
        if (1 === $number) {
            return true;
        }

        for ($i = 2, $max = $number / 2; $i <= $max; ++$i) {
            if (0 === $number % $i) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns jaccard coefficient.
     *
     * @param array $fingerprint1
     * @param array $fingerprint2
     *
     * @return float
     */
    private function jaccardCoeficient(array $fingerprint1, array $fingerprint2): float
    {
        $intersection = array_intersect($fingerprint1, $fingerprint2);
        $unions = array_merge($fingerprint1, $fingerprint2);

        $intersectionCount = count($intersection);
        $unionsCount = count($unions);

        $divisor = $unionsCount - $intersectionCount;
        $coefficient = $divisor > 0 ? $intersectionCount / $divisor : 0;

        return $coefficient;
    }
}
