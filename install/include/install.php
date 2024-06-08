<?php

/**
 * Copyright (c) 2024 UFOCMS
 *
 * This software is licensed under the GPLv3 license.
 * See the LICENSE file for more information.
 */

/**
 * Tables
 */
$insert_admin = "INSERT INTO `" . $CONFIG["database"]["prefix"] . "admins` (`id`, `name`, `last_name`, `login_name`, `email`, `no`, `photo`, `password`, `hash_login`, `theme`, `last_login`, `ajax_key`) VALUES (NULL, '" . $CONFIG["admin"]["name"] . "', '" . $CONFIG["admin"]["last_name"] . "', '" . $CONFIG["admin"]["login_name"] . "', '" . ($CONFIG["admin"]["email"] ?? "") . "', '', '" . $uri . "content/assets/img/unknown.png', '" . create_password($CONFIG["admin"]["password"]) . "', '0x" . hash("sha512", rand()) . "', 'light', '', '0x" . hash("sha512", rand()) . "')";
$db->query($insert_admin);