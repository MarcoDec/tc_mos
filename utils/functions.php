<?php

function removeStart(string $string, string $start): string {
    return str_starts_with($string, $start) ? substr($string, strlen($start)) : $string;
}
