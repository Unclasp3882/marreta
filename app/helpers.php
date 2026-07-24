<?php

declare(strict_types=1);

if (! function_exists('is_assoc_array')) {
    function is_assoc_array(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
