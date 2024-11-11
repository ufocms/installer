--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `%prefix%admins` (
    `id`           bigint(20) NOT NULL AUTO_INCREMENT,
    `name`         text       NOT NULL,
    `last_name`    text       NOT NULL,
    `login_name`   text       NOT NULL,
    `email`        text       NOT NULL,
    `no`           text       NOT NULL,
    `photo`        text       NOT NULL,
    `password`     text       NOT NULL,
    `hash_login`   text       NOT NULL,
    `theme`        text       NOT NULL,
    `last_login`   text       NOT NULL,
    `ajax_key`     text       NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=%charset% COLLATE=%collate%;

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `%prefix%category` (
    `id`          bigint(20) NOT NULL AUTO_INCREMENT,
    `title`       text       NOT NULL,
    `photo`       text       NOT NULL,
    `description` text       NOT NULL,
    `link`        text       NOT NULL,
    `_from`       text       NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=%charset% COLLATE=%collate%;

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `%prefix%comments` (
    `id`       bigint(20) NOT NULL AUTO_INCREMENT,
    `mid`      bigint(20) NOT NULL,
    `aid`      bigint(20) NOT NULL,
    `pid`      bigint(20) NOT NULL,
    `guest`    text DEFAULT NULL,
    `comment`  text       NOT NULL,
    `dateTime` text       NOT NULL,
    `rate`     int(11)    NOT NULL,
    `more`     text DEFAULT NULL,
    `_for`     text       NOT NULL,
    `_reply`   bigint(20) NOT NULL,
    `accept`   int(11)    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=%charset% COLLATE=%collate%;

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `%prefix%members` (
    `uid`        bigint(20) NOT NULL AUTO_INCREMENT,
    `name`       text       NOT NULL,
    `last_name`  text       NOT NULL,
    `username`   text       NOT NULL,
    `email`      text       NOT NULL,
    `no`         text       NOT NULL,
    `password`   text       NOT NULL,
    `photo`      text       NOT NULL,
    `hash`       text       NOT NULL,
    `last_login` text       NOT NULL,
    `last_ip`    text       NOT NULL,
    `verify`     text       NOT NULL,
    `dateTime`   text       NOT NULL,
    `roles`      text       NOT NULL,
    `more`       text       NOT NULL,
    PRIMARY KEY (`uid`)
) ENGINE = InnoDB DEFAULT CHARSET=%charset% COLLATE=%collate%;

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `%prefix%menu` (
    `id`            bigint(20) NOT NULL AUTO_INCREMENT,
    `title`         text       NOT NULL,
    `icon`          text       NOT NULL,
    `link`          text       NOT NULL,
    `sub`           bigint(20) NOT NULL,
    `position`      text DEFAULT NULL,
    `display_order` bigint(20) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=%charset% COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `%prefix%meta` (
    `id`     bigint(20) NOT NULL AUTO_INCREMENT,
    `_key`   text       NOT NULL,
    `_value` text       NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=%charset% COLLATE=%collate%;

--
-- Dumping data for table `meta`
--

INSERT INTO `%prefix%meta` (`id`, `_key`, `_value`)
VALUES (1, 'web_name', '%web_name%'),
       (2, 'web_url', '%web_url%'),
       (3, 'web_admin_url', '%web_admin_url%'),
       (4, 'admin_cookie', '%admin_cookie%'),
       (5, 'table_rows', '25'),
       (6, 'timezone', '%timezone%'),
       (7, 'admin_header', '1'),
       (8, 'web_icon', '%web_url%content/assets/img/logo.png'),
       (9, 'lang', '%i18n%'),
       (10, 'dir', '%dir%'),
       (11, 'theme', ''),
       (12, 'banner', ''),
       (13, 'member_cookie', '%member_cookie%'),
       (14, 'debug', 'false'),
       (15, 'smtp', 'a:4:{s:4:"host";s:0:"";s:4:"auth";s:4:"true";s:6:"secure";s:3:"ssl";s:4:"port";s:0:"";}'),
       (16, 'mail', 'a:4:{s:8:"username";s:0:"";s:8:"password";s:0:"";s:9:"from_mail";s:0:"";s:9:"from_name";s:0:"";}'),
       (17, 'unknown_photo', '%web_url%content/assets/img/unknown.png'),
       (18, 'error_photo', '%web_url%content/assets/img/img.png'),
       (19, 'ajax_key', '%ajax_key%'),
       (20, 'type_time', 'gregorian'),
       (21, 'copyright', '%copyright%'),
       (22, 'accept_comment', 'false'),
       (23, 'listing_files', 'on'),
       (24, 'task_key', '%task_key%'),
       (25, 'tasks', 'on'),
       (26, 'protocol', 'http'),
       (27, 'seo', '%seo%'),
       (28, 'new_version', ''),
       (29, 'step_update', '0'),
       (30, 'status', '0'),
       (31, 'accept-member', 'false'),
       (32, 'slug_blog', 'blog'),
       (33, 'minify_html', 'false'),
       (34, 'structure_datetime', 'Y-m-d H:i:s'),
       (35, 'slug_category', 'category'),
       (36, 'charset', 'UTF-8'),
       (37, 'slug_signup', 'signup'),
       (38, 'slug_login', 'login'),
       (39, 'slug_forgot_password', 'forgot-password'),
       (40, 'slug_account', 'account'),
       (41, 'slug_verify', 'verify'),
       (42, 'verify_timeout', '120'),
       (43, 'verify_code', '{\"numbers\":2,\"alphabets\":5}'),
       (44, 'account_upload_photo', '{\"active\":true,\"types\":[\"png\",\"jpg\",\"jpeg\"],\"size\":5,\"folder\":\"profiles\"}');

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `%prefix%pages`
(
    `id`         bigint(20) NOT NULL AUTO_INCREMENT,
    `title`      text       NOT NULL,
    `content`    longtext   NOT NULL,
    `short_desc` text       NOT NULL,
    `photo`      text       NOT NULL,
    `link`       text       NOT NULL,
    `category`   text       NOT NULL,
    `tags`       text       NOT NULL,
    `status`     int(11)    NOT NULL,
    `type`       text       NOT NULL,
    `author`     text       NOT NULL,
    `password`   text       NOT NULL,
    `dateTime`   text       NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=%charset% COLLATE=%collate%;
