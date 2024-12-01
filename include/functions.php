<?php

/**
 * Copyright (c) 2024 UFOCMS
 *
 * This software is licensed under the GPLv3 license.
 * See the LICENSE file for more information.
 */

$SLASH  = DIRECTORY_SEPARATOR;
$CONFIG = json_decode(file_get_contents(
    "config.json"
), true);
$I18n   = json_decode(file_get_contents(
    "i18n$SLASH$CONFIG[i18n].json"
), true);

function save_config () {
    global $CONFIG;
    return file_put_contents("config.json", json_encode(
        $CONFIG, JSON_UNESCAPED_UNICODE
    ));
}

function all_i18n (): array {
    global $SLASH;

    $scan = scandir("i18n");

    unset($scan[array_search('.', $scan, true)]);
    unset($scan[array_search('..', $scan, true)]);

    $list = [];

    foreach ($scan as $item) {
        $i18n = json_decode(
            file_get_contents("i18n$SLASH$item"),
            true
        );
        $list[$i18n['$title']] = $item;
    }

    return $list;
}

function get_i18n ($lang): array {
    global $SLASH;
    return json_decode(file_get_contents("i18n$SLASH$lang.json"), true);
}

function i18n (string $string, ...$values) {
    global $I18n;

    if (isset(func_get_args()[1])) {
        $explode = explode("%n", i18n($string));
        $newText = "";

        for ($i = 0; $i < count($explode); $i++)
            $newText .= $explode[$i] . ($values[$i] ?? "");

        return $newText;
    }

    return $I18n[$string] ?? $string;
}

function status ($status, $message = null, $data = []) {
    if (is_bool($status)) {
        $message = $status ? "Done successfully" : "System error";
        $status  = $status ? 200 : 503;
    }

    die(json_encode([
        "status"  => $status,
        "message" => i18n($message)
    ] + $data, JSON_UNESCAPED_UNICODE));
}

function randomHash (int $length = 50): string {
    $hash = "";
    $possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for ($i = 0; $i < $length; $i++)
        $hash .= $possible[rand(0, strlen($possible) - 1)];

    return "0x$hash";
}

function create_password ($pass): string {
    $md5    = md5($pass);
    $sha256 = hash("sha256", $md5);
    $sha512 = hash("sha512", $sha256);
    return md5($sha512);
}

function get_uri (): string {
    $location = (
        isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http"
    ) . "://$_SERVER[HTTP_HOST]/";
    $uri = ltrim($_SERVER["REQUEST_URI"], "/");
    $location = $location . pathinfo($uri)["dirname"] . "/";
    return rtrim($location, "//") . "/";
}

function sanitizeUrl ($url) {
    // Check if URL is empty
    if (empty($url)) return null;

    // Determine the default protocol (HTTP or HTTPS)
    if (strpos($url, 'https://') === 0)
        $defaultProtocol = 'https://'; // Use HTTPS if URL has it
    else
        $defaultProtocol = 'http://'; // Default protocol

    // Remove the protocol from the URL for sanitization
    $urlWithoutProtocol = preg_replace('/^https?:\/\//', '', $url);

    // Get the domain part
    $domain = strtok($urlWithoutProtocol, '/'); // Get the first part as domain
    $path = substr($urlWithoutProtocol, strlen($domain)); // Get the rest as path

    // Handle '.' and '..' in the path
    $parts = explode('/', $path);
    $sanitizedParts = [];

    foreach ($parts as $part) {
        if ($part === '' || $part === '.') {
            continue; // Skip empty parts and '.'
        } elseif ($part === '..') {
            array_pop($sanitizedParts); // Remove last element if exists
        } else {
            $sanitizedParts[] = $part; // Add valid part
        }
    }

    // Reconstruct the URL with the appropriate protocol and domain
    $sanitizedUrl = $defaultProtocol . $domain . '/' . implode('/', $sanitizedParts);

    return rtrim($sanitizedUrl, '/'); // Remove trailing slash if any
}

function error (string $string) {
    global $I18n;
    echo "<div style='display:flex;justify-content: center;margin-top: 10px'><div style='width: 80%;height: 80px;background: whitesmoke;border-radius: 0 8px 8px 0;font-family: system-ui;font-weight: bolder;display: flex;align-items: center;padding: 0 10px;box-sizing: border-box;border-" . ($I18n['$dir'] == "ltr" ? "left: 5px solid red;" : "right: 5px solid red;") . "font-size: 18px'>" . i18n($string) . "</div></div>";
}

/**
 * Extract ufocms.zip
 * Delete UFOCMS Installer
 */
function extractUFOCMS (): bool {
    global $SLASH;

    $destination = __DIR__ . "$SLASH..$SLASH..$SLASH";
    $ufocmsZip = '..' . $SLASH . 'ufocms.zip';

    if (!file_exists($ufocmsZip))
        return configureUFOCMS($destination);

    $zip = new ZipArchive;

    if ($zip->open($ufocmsZip) === TRUE) {
        try {
            $extract = $zip->extractTo($destination);
            $zip->close();

            if ($extract) {
                # Configure UFOCMS
                configureUFOCMS($destination);

                # Clean
                unlink($ufocmsZip);

                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    error("ufocms.zip file could not be extracted");

    return false;
}

function configureUFOCMS (string $directory): bool {
    global $SLASH, $CONFIG;

    $directory = $directory . "cms$SLASH";
    $database  = $CONFIG['database'];
    $completes = [];

    # Update include/config.php
    $config = $directory . 'include' . $SLASH . 'config.php';
    $configData  = file_get_contents($config);
    $completes[] = file_put_contents(
        $config, str_replace([
            '%db_host%',
            '%db_pass%',
            '%db_user%',
            '%db_name%',
            '"%db_port%"',
            '%db_prefix%',
            '%db_charset%',
            '%db_collate%'
        ], [
            $database['host'],
            $database['pass'],
            $database['user'],
            $database['name'],
            !empty($database['port']) ? (
                (int) $database['port']
            ) : 'null',
            $database['prefix'],
            $database['charset'],
            $database['collate']
        ], $configData)
    );

    # Rename cms folder
    if (is_dir($directory))
        $completes[] = rename($directory, "$directory..$SLASH$CONFIG[admin_path]");

    # Update private/package.json
    $package = "$directory..$SLASH" . "content" . $SLASH . "private$SLASH" . "package.json";
    $packageData = json_decode(
        file_get_contents($package), true
    );
    $packageData['admin_path'] = "$CONFIG[admin_path]$SLASH";
    $completes[] = file_put_contents($package, json_encode(
        $packageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ));

    foreach ($completes as $complete) {
        if (!$complete)
            $completes = false;
    }

    return is_array($completes);
}
