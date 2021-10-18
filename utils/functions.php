<?php

function num_fr_format(float $number, int $decimals = 2): string {
    return number_format($number, $decimals, ',', ' ');
}

function removeStart(string $str, string $start): string {
    return str_starts_with($str, $start) ? substr($str, strlen($start)) : $str;
}
