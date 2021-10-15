<?php

function removeStart(string $str, string $start): string {
    return str_starts_with($str, $start) ? substr($str, strlen($start)) : $str;
}
