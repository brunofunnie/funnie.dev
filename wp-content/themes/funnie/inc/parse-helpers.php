<?php
if (!defined('ABSPATH')) exit;

/**
 * Parse a pipe-delimited textarea into a list of associative rows.
 *
 * Input:  "2023 — Present | Senior Web Developer · FunnieTech\n2020 — 2023 | Frontend Engineer"
 * Keys:   ['period', 'title']
 * Output: [ ['period' => '2023 — Present', 'title' => 'Senior Web Developer · FunnieTech'], ... ]
 *
 * Blank lines and anything starting with '#' are skipped.
 */
function funnie_parse_rows($text, array $keys): array {
    $out = [];
    $lines = preg_split('/\r?\n/', (string) $text);
    if (!$lines) return $out;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $parts = array_map('trim', explode('|', $line));
        $row = [];
        foreach ($keys as $i => $key) $row[$key] = $parts[$i] ?? '';
        $out[] = $row;
    }
    return $out;
}

function funnie_format_rows(array $rows, array $keys): string {
    $lines = [];
    foreach ($rows as $r) {
        $cols = [];
        foreach ($keys as $k) $cols[] = (string) ($r[$k] ?? '');
        $lines[] = implode(' | ', $cols);
    }
    return implode("\n", $lines);
}
