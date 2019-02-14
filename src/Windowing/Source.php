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
 * Source to compare.
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */
class Source
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var array
     */
    private $nGram;

    /**
     * @var array
     */
    private $rollingHash;

    /**
     * @var array
     */
    private $windowTable;

    /**
     * @var array
     */
    private $fingerprints;

    /**
     * Create source instance.
     *
     * @param string $text
     *
     * @return Source
     */
    public static function create(string $text): Source
    {
        return new static($text);
    }

    /**
     * Class constructor.
     *
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->setText($text);
    }

    /**
     * Returns source text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Assign source text.
     *
     * @param string $source
     *
     * @return Source
     */
    public function setText(string $text): Source
    {
        if (empty($text)) {
            throw new \LogicException('Source text empty!');
        }

        $this->text = $text;

        return $this;
    }

    /**
     * Returns n-gram.
     *
     * @return array|null
     */
    public function getNGram(): ?array
    {
        return $this->nGram;
    }

    /**
     * Returns rolling hash.
     *
     * @return array|null
     */
    public function getRollingHash(): ?array
    {
        return $this->rollingHash;
    }

    /**
     * Returns window table.
     *
     * @return array|null
     */
    public function getWindowTable(): ?array
    {
        return $this->windowTable;
    }

    /**
     * Returns fingerprints.
     *
     * @return array|null
     */
    public function getFingerprints(): ?array
    {
        return $this->fingerprints;
    }

    /**
     * Returns true if calculation success.
     *
     * @param int $nGramValue
     * @param int $primeNumber
     * @param int $nWindowValue
     *
     * @return Source
     */
    public function calculate(int $nGramValue = 2, int $primeNumber = 3, int $nWindowValue = 4): Source
    {
        //langkah 1 : buang semua huruf yang bukan kelompok [a-z A-Z 0-9] dan ubah menjadi huruf kecil semua (lowercase)
        $text = $this->normalize($this->text);

        //langkah 2 : buat N-Gram
        $this->nGram = $this->calculateNGram($text, $nGramValue);

        //langkah 3 : rolling hash untuk masing-masing n gram
        $this->rollingHash = $this->calculateRollingHash($this->nGram, $primeNumber);

        //langkah 4 : buat windowing untuk masing-masing tabel hash
        $this->windowTable = $this->calculateWindow($this->rollingHash, $nWindowValue);

        //langkah 5 : cari nilai minimum masing-masing window table (fingerprintss)
        $this->fingerprints = $this->calculateFingerprints($this->windowTable, $nWindowValue);

        return $this;
    }

    /**
     * Normalize source text.
     *
     * @param string $text
     *
     * @return string
     */
    private function normalize(string $text): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $text));
    }

    /**
     * Calculate n-gram.
     *
     * @param string $text
     * @param int    $nGramValue
     *
     * @return array
     */
    private function calculateNGram(string $text, int $nGramValue): array
    {
        $nGram = array();
        $length = strlen($text);

        for ($i = 0; $i < $length; ++$i) {
            if ($i > ($nGramValue - 2)) {
                $ng = '';
                for ($j = $nGramValue - 1; $j >= 0; --$j) {
                    $ng .= $text[$i - $j];
                }
                $nGram[] = $ng;
            }
        }

        return $nGram;
    }

    /**
     * Calculate rolling hash for n-gram.
     *
     * @param array $ngram
     * @param int   $primeNumber
     *
     * @return array
     */
    private function calculateRollingHash(array $ngram, int $primeNumber): array
    {
        $rollingHash = array();

        foreach ($ngram as $ng) {
            $rollingHash[] = $this->hash($ng, $primeNumber);
        }

        return $rollingHash;
    }

    /**
     * Calculate window table.
     *
     * @param array $rollingHash
     * @param int   $nWindowValue
     *
     * @return array
     */
    private function calculateWindow(array $rollingHash, int $nWindowValue): array
    {
        $ngram = array();
        $length = count($rollingHash);
        $x = 0;

        for ($i = 0; $i < $length; ++$i) {
            if ($i > ($nWindowValue - 2)) {
                $ngram[$x] = array();
                $y = 0;
                for ($j = $nWindowValue - 1; $j >= 0; --$j) {
                    $ngram[$x][$y] = $rollingHash[$i - $j];
                    ++$y;
                }
                ++$x;
            }
        }

        return $ngram;
    }

    /**
     * Calculate fingerprints.
     *
     * @param array $windowTable
     * @param int   $nWindowValue
     *
     * @return array
     */
    private function calculateFingerprints(array $windowTable, int $nWindowValue): array
    {
        $fingers = array();

        for ($i = 0; $i < count($windowTable); ++$i) {
            $min = $windowTable[$i][0];
            for ($j = 1; $j < $nWindowValue; ++$j) {
                if ($min > $windowTable[$i][$j]) {
                    $min = $windowTable[$i][$j];
                }
            }
            $fingers[] = $min;
        }

        return $fingers;
    }

    /**
     * Calculate text rolling hash.
     *
     * @param string $text
     * @param int    $primeNumber
     *
     * @return int
     */
    private function hash(string $text, int $primeNumber): int
    {
        $length = strlen($text);

        if (1 == $length) {
            return ord($text);
        }

        $result = 0;

        for ($i = 0; $i < $length; ++$i) {
            $result += ord(substr($text, $i, 1)) * pow($primeNumber, $length - $i);
        }

        return $result;
    }
}
