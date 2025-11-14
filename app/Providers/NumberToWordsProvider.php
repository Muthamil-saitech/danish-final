<?php

namespace App\Providers;

class NumberToWordsProvider
{
    protected string $major;
    protected string $minor;
    protected string $words = '';
    protected string $number;
    protected int $magind;
    protected array $units = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
    protected array $teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
    protected array $tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    protected array $mag = ['', 'thousand', 'million', 'billion', 'trillion'];

    public function __construct(string $major = 'rupees', string $minor = 'paise')
    {
        $this->major = $major;
        $this->minor = $minor;
    }

    public function convert(float $amount): string
    {
        $this->number = number_format($amount, 2);
        [$pounds, $pence] = explode('.', $this->number);

        $this->words = " {$this->major} ";

        if ((int)$pounds === 0) {
            $this->words = " {$this->words}";
        } else {
            $groups = explode(',', $pounds);
            $groups = array_reverse($groups);

            foreach ($groups as $this->magind => $group) {
                if (
                    $this->magind === 1 &&
                    strpos($this->words, 'hundred') === false &&
                    $groups[0] !== '000'
                ) {
                    $this->words = ' and ' . $this->words;
                }

                $this->words = $this->build($group) . $this->words;
            }
        }

        // Handle decimal (paise)
        if ((int)$pence === 0) {
            return trim($this->words);
        }

        $this->words .= ' ' .
            $this->tens[(int)substr($pence, 0, 1)] . ' ' .
            $this->units[(int)substr($pence, 1)] . " {$this->minor}";

        return trim($this->words);
    }

    protected function build(string $n): string
    {
        $res = '';
        $na = str_pad($n, 3, '0', STR_PAD_LEFT);

        if ($na === '000') {
            return '';
        }

        if ($na[0] != '0') {
            $res = ' ' . $this->units[(int)$na[0]] . ' hundred';
        }

        if ($na[1] === '0' && $na[2] === '0') {
            return $res . ' ' . $this->mag[$this->magind];
        }

        $res .= $res === '' ? '' : ' and';

        $t = (int)$na[1];
        $u = (int)$na[2];

        switch ($t) {
            case 0:
                $res .= ' ' . $this->units[$u];
                break;
            case 1:
                $res .= ' ' . $this->teens[$u];
                break;
            default:
                $res .= ' ' . $this->tens[$t] . ' ' . $this->units[$u];
                break;
        }

        $res .= ' ' . $this->mag[$this->magind];

        return $res;
    }
}
