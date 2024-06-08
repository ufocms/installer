<?php ob_start(); ob_clean();

/**
 * Copyright (c) 2024 UFOCMS
 *
 * This software is licensed under the GPLv3 license.
 * See the LICENSE file for more information.
 */

require "include" . DIRECTORY_SEPARATOR . "functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
    return require "include" . $SLASH . "ajax.php";
?>
<html dir="<?= $I18n['$dir'] ?>" lang="<?= $CONFIG['i18n'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= i18n("UFOCMS installer") ?></title>

    <link rel="stylesheet" href="assets/styles/main.css">

    <script>
        const step    = "<?= $CONFIG["step"] ?>";
        const allI18n = <?= json_encode(all_i18n()) ?>;
        const i18n    = <?= json_encode($I18n, JSON_UNESCAPED_UNICODE) ?>;
        const collations = <?= file_get_contents("assets" . $SLASH . "mysql" . $SLASH . "collations.json") ?>;
        <?php if ($CONFIG["step"] == "welcome") { ?>
        const redirectTo = "<?= get_uri() . $CONFIG["admin_path"] ?>";
        <?php } ?>
    </script>

    <script src="assets/scripts/jquery.min.js"></script>
</head>
<body>
    <?php
    if (!version_compare(phpversion(), $CONFIG["requirements"]["min_php_version"], ">=")) {
        error(str_replace(
            "%n",
            $CONFIG["requirements"]["min_php_version"],
            i18n("PHP version must be above %n")
        ));
    } else {
        $result = [];
        $list_error = [];

        foreach ($CONFIG["requirements"]["extensions"] as $k => $v) {
            foreach ($v as $ik => $iv) {
                switch ($k) {
                    case "classes":
                        $result[$ik] = class_exists($ik);
                        break;
                    case "functions":
                        $result[$ik] = function_exists($ik);
                        break;
                    case "extensions":
                        $result[$ik] = extension_loaded($ik);
                        break;
                }
                if (!$result[$ik] && $iv)
                    $list_error[$ik] = "error";
                $result[$ik] = $result[$ik] ? i18n("Supported") : i18n("Not support");
            }
        }

    if (!empty($list_error)) {
        error("Your host does not support multiple php extensions");
    ?>
        <div style="display:flex;justify-content: center;align-items: center;width: 100%;margin: 50px 0 0">
            <ul style="list-style: none;padding: 0;margin: 0;">
                <?php foreach ($result ?? [] as $k => $v) { ?>
                    <li style="font-size: 20px;margin: 0 0 10px;width: 100%;display: flex;">
                        <div style="width: 200px;border-right: 3px solid gray;padding: 5px 10px"><?= $k ?></div>
                        <div style="width: 200px;margin-left:10px;padding: 5px 10px;text-align: center;<?= $v == i18n("Not support") ? "background:red;color:white" : "" ?>"><?= $v ?></div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } else {
        if ($CONFIG["step"] == "welcome") {
            if (extractUFOCMS()) { ?>
                <script src="assets/scripts/main.js"></script>
    <?php   }
        } else { ?>
        <script src="assets/scripts/main.js"></script>
    <?php }
        }
    } ?>
</body>
</html>

<?php ob_end_flush() ?>