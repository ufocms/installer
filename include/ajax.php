<?php ob_start(); ob_clean();

/**
 * Copyright (c) 2024 UFOCMS
 *
 * This software is licensed under the GPLv3 license.
 * See the LICENSE file for more information.
 */

$STEPS = [
    "i18n"     => function () {
        global $SLASH, $CONFIG;

        if (!file_exists(".." . $SLASH . "ufocms.zip"))
            $CONFIG["step"] = "download";
        else
            $CONFIG["step"] = "info";

        // Update i18n
        $CONFIG["i18n"] = $_POST["i18n"];
        $CONFIG["dir"]  = get_i18n($CONFIG["i18n"])['$dir'];

        status((bool) save_config(), null, [
            "reload" => true
        ]);
    },
    "download" => function () {
        global $CONFIG, $SLASH;

        $download = copy(
            "https://dl.ufocms.org/$_POST[version]/ufocms.zip",
            "..$SLASH" . "ufocms.zip"
        );

        if ($download)
            $CONFIG["step"] = "info";

        status((bool) save_config(), null, [
            "reload" => true
        ]);
    },
    "info"     => function () {
        global $CONFIG;

        $CONFIG["web_title"]  = $_POST["web_title"] ?? "";
        $CONFIG["admin_path"] = $_POST["admin_path"] ?? "";
        $CONFIG["seo"]  = !($_POST["seo"] ?? false);
        $CONFIG["step"] = "admin";

        status((bool) save_config(), null, [
            "reload" => true
        ]);
    },
    "admin"    => function () {
        global $CONFIG;

        $CONFIG["step"]  = "database";
        $CONFIG["admin"] = [];
        $CONFIG["admin"]["name"]       = $_POST["name"];
        $CONFIG["admin"]["last_name"]  = $_POST["last_name"];
        $CONFIG["admin"]["login_name"] = $_POST["login_name"];
        $CONFIG["admin"]["email"]      = $_POST["email"];
        $CONFIG["admin"]["password"]   = $_POST["password"];

        status((bool) save_config(), null, [
            "reload" => true
        ]);
    },
    "database" => function () {
        global $SLASH, $CONFIG;

        if (
            empty($_POST["hostname"]) ||
            empty($_POST["username"]) ||
            empty($_POST["database"])
        ) status(400, i18n("Please fill in the fields"));

        mysqli_report(MYSQLI_REPORT_ERROR);
        @$db = new mysqli(
            $_POST["hostname"],
            $_POST["username"],
            $_POST["password"] ?? "",
            $_POST["database"],
            !empty($_POST["port"]) ? $_POST["port"] : null
        );

        if ($db->connect_errno) {
            status(503, i18n(
                "Error (%n) : The database could not be connected. Please enter the database information carefully",
                $db->connect_error
            ));
        }

        $charset = explode("_", $_POST["collate"])[0];

        $db->set_charset($charset);

        $uri = get_uri();
        $sql = file_get_contents("assets" . $SLASH . "mysql" . $SLASH . "tables.sql");
        $sql = str_replace([
            "%prefix%",
            "%charset%",
            "%collate%",

            "%web_name%",
            "%web_url%",
            "%web_admin_url%",
            "%admin_cookie%",
            "%timezone%",
            "%i18n%",
            "%dir%",
            "%member_cookie%",
            "%ajax_key%",
            "%copyright%",
            "%task_key%",
            "%seo%"
        ], [
            $_POST["prefix"] ?? null,
            $charset,
            $_POST["collate"],

            $CONFIG["web_title"],
            sanitizeUrl($uri) . "/",
            sanitizeUrl($uri . $CONFIG["admin_path"]) . "/",
            randomHash(),
            date_default_timezone_get(),
            $CONFIG["i18n"],
            $CONFIG["dir"],
            randomHash(),
            serialize([
                "last_change" => date('Y-m-d H:i:s'),
                "key" => randomHash()
            ]),
            i18n("Proudly powered by UFOCMS"),
            randomHash(),
            $CONFIG["seo"] ? "true" : "false"
        ], $sql);

        $import = $db->multi_query($sql);

        if ($import === FALSE)
            status(FALSE, "Error importing tables");

        do {
            if ($result = $db->store_result())
                $result->free();
        } while (@$db->more_results() && @$db->next_result());

        $CONFIG["step"] = "welcome";
        $CONFIG["database"] = [
            "host"    => $_POST["hostname"],
            "user"    => $_POST["username"],
            "pass"    => $_POST["password"] ?? null,
            "name"    => $_POST["database"],
            "prefix"  => $_POST["prefix"] ?? null,
            "charset" => $charset,
            "collate" => str_replace(
                $charset, "", $_POST["collate"]
            ),
            "port"    => $_POST["port"] ?? null
        ];

        include "install.php";

        $db->close();

        status((bool) save_config(), null, [
            "reload" => true
        ]);
    }
];

$step = $_POST["step"] ?? "";

die(isset(
    $STEPS[$step]
) ? $STEPS[$step]() : "Step not found");
