<?php

if (!function_exists('extract_domain')) {
    function extract_domain(string $email): ?string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return substr(strrchr($email, '@'), 1);
    }
}
