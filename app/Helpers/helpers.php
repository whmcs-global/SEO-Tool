<?php

if (!function_exists('jsencode_userdata')) {
    function jsencode_userdata(string $data, string $encryptionMethod = null, string $secret = null)
    {
        $encryptionMethod = config('app.encryptionMethod');
        $secret = config('app.secrect');
        try {
            $iv = substr($secret, 0, 16);
            $jsencodeUserdata = str_replace('/', '!', openssl_encrypt($data, $encryptionMethod, $secret, 0, $iv));
            $jsencodeUserdata = str_replace('+', '~', $jsencodeUserdata);
            return $jsencodeUserdata;
        } catch (\Exception $e) {
            return null;
        }
    }
}
if (!function_exists('jsdecode_userdata')) {
    function jsdecode_userdata(string $data, string $encryptionMethod = null, string $secret = null)
    {
        $encryptionMethod = config('app.encryptionMethod');
        $secret = config('app.secrect');
        try {
            $iv = substr($secret, 0, 16);
            $data = str_replace('!', '/', $data);
            $data = str_replace('~', '+', $data);
            $jsencodeUserdata = openssl_decrypt($data, $encryptionMethod, $secret, 0, $iv);
            return $jsencodeUserdata;
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('getRootDomain')) {
    function getRootDomain($url) {
        $host = parse_url('http://' . str_replace(['http://', 'https://'], '', $url), PHP_URL_HOST);
        $hostParts = explode('.', $host);
        if (count($hostParts) < 2) {
            return $host;
        }

        $domain = $hostParts[count($hostParts) - 2] . '.' . $hostParts[count($hostParts) - 1];
        return $domain;
    }
}
