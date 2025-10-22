<?php
class StringAnalyzer {
    public static function analyze($value) {
        $length = strlen($value);
        $is_palindrome = strcasecmp($value, strrev($value)) === 0;
        $unique_chars = count(array_unique(str_split(strtolower($value))));
        $word_count = str_word_count($value);
        $sha256_hash = hash('sha256', $value);
        $char_freq = array_count_values(str_split($value));

        return [
            'length' => $length,
            'is_palindrome' => $is_palindrome,
            'unique_characters' => $unique_chars,
            'word_count' => $word_count,
            'sha256_hash' => $sha256_hash,
            'character_frequency_map' => $char_freq
        ];
    }
}
