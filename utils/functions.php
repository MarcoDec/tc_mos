<?php

function num_fr_format(float $number, int $decimals = 2): string {
    return number_format($number, $decimals, ',', ' ');
}

function removeEnd(string $str, string $end): string {
    return str_ends_with($str, $end) ? substr($str, 0, -strlen($end)) : $str;
}

function removeStart(string $str, string $start): string {
    return str_starts_with($str, $start) ? substr($str, strlen($start)) : $str;
}
