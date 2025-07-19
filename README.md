# PHP x SQL Forum

**PHP** x **MariaDB** Forum (using infinityFree)

**PHP** version 8.3
<br>
**MariaDB** version 10.6

This is a personal project of mine to learn PHP. It is to be expected that:

- There are bugs
- Things are not optimized
- There are missing features
- It is unsafe
- There can be massive changes at any point

This project is currently under heavy development.

# Overview

- [Pages](#Pages)
- [Config file](#config-file)
- [Database Architecture](#user-content-database-architecture)

# Pages

## Home (Categories)

This is `index.php` and displays a list of categories (only configurable via database)

## Threads

This is `topic.php` and displays a list of threads within the chosen category. These can be created by any user and deleted by moderators and admins.

## Posts

This is `thread.php` and displays all the posts within a thread. These can be created by any user and deleted by moderators and admins.

## User

This is `user.php` - Every user has a public user profile page displaying their post and thread history.

## Profile

Located in `profile/`, these are 3 pages: `settings.php`, `notifications.php` and `moderation.php`.

# Config file

The `.config.php` file is not included for obvious reasons. This file has to be manually added and shall contain all relevant information to connect to the database. Here the pattern used in my project:

```php
<?php
return array(
    "servername" => "{servername}",
    "username" => "{username}",
    "password" => "{password}",
    "dbname" => "{dbname}",
);
```

# Database Architecture

![Schematic of the database for the forum](/Forum-DB-schematic.png)

## General Structure

### General

All `deleted` columns adhere to the following logic:

| Value | Binary | Meaning                                    |
| ----- | ------ | ------------------------------------------ |
| 0     | 0000   | Not deleted                                |
| 1     | 0001   | User deleted                               |
| 2     | 0010   | Mod deleted or Auto deleted (empty thread) |
| 4     | 0100   | Thread deleted                             |
| 8     | 1000   | Ban deleted/Self account deleted           |

Which can be found on the `posts`, `threads` and `users` tables. All of these will be permenantly deleted after 60 days.

### Users

`handle` may only contain A-z 0-9 \_ . and - and must be unique and between 4 and 16 characters (inclusive)

`img_dir` (img in the schematic) contains the path to the image directory, stored in [src/images/profiles/](src/images/profiles/). Be careful to make sure include this directory when making backups, as the images are not saved in the database itself.

`clearance` level is an integer.
<br>0 = Regular user
<br>1 = Moderator <sub>(Can delete posts)</sub>
<br>2 = Moderator <sub>(The above and can delete threads)</sub>
<br>3 = Admin <sub>(The above and can ban users)</sub>
<br>4 = Admin <sub>(The above and can promote and demote all of the below)</sub>
<br>5 = Super Admin <sub>(The above and can promote and demote level 3 to level 4 admin. Can also view all deleted posts and deleted accounts and restore them (within the time limit))</sub>

### Mod_History

The `id` can be of any `post`, `thread` or `user` (indicated by the "type"). To avoid having polymorphic table queries, the `summary` column will contain a description (e.g. thread name or first 64 characters of the culprit's post) which, when clicked, shall reveal further information (dynamically generated via PHP).

The `type` column encodes for the following:

| Value | Meaning |
| ----- | ------- |
| 0     | post    |
| 1     | thread  |
| 2     | user    |

The `judgement` column encodes for the follows:

| Value | Meaning                                     |
| ----- | ------------------------------------------- |
| 0     | reported - unread                           |
| 1     | reported - read                             |
| 2     | deleted                                     |
| 3     | deleted with threads (for banned accounts)  |
| 4     | restored                                    |
| 5     | restored with threads (for banned accounts) |
| 6     | demoted                                     |
| 7     | promoted                                    |

The `reason` column encodes for the follows:

| Value | Meaning       |
| ----- | ------------- |
| 0     | Spam          |
| 1     | Inappropriate |
| 2     | Copyright     |
| 3     | Other         |
| 4     | Restored      |

### Slugs

Slugs are automatically generated for threads (for categories they need to be manually configured) and can only be edited with database access. This is to avoid SEO issues.

### Subscribed

The `subscribed` table enables users to subscribe or unsubscribe form threads. If there is no entry yet, users will be auto-subscribed if they post for the first time on a thread.

### Notifications

Notifications are created upon:

- Posting in a thread
- Mod deleted post (culprit only)
- Mod deleted thread (culprit only)
- User promotion
- User Demotion

And deleted/updated upon:

- Moderation undo action
- Self deleted post
- Expired

The `type`s are:

| Value | Meaning    |
| ----- | ---------- |
| 0     | Post       |
| 1     | Del Post   |
| 2     | Del Thread |
| 3     | Promotion  |
| 4     | Demotion   |

(TBD) <br>

`post_id` and `mod_id` are stored in the `assoc_id` column. <br>
The `deleted` makes sure that when moderators are changing the visibility of posts with undo/redo, it won't re-generate notifications but instead preserve the old one.

## Tables

(These may not be up to date and will be modified in the future)

### General

For all:

```SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
```

### Users

```SQL
CREATE TABLE `users` (
  `username` varchar(24) NOT NULL,
  `handle` varchar(16) NOT NULL,
  `image_dir` varchar(64) NOT NULL,
  `posts` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `threads` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `darkmode` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` varchar(33) NOT NULL,
  `password` text NOT NULL,
  `clearance` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `deleted_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(2) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

```SQL
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `username` (`username`),
  ADD KEY `handle` (`handle`);
COMMIT;
```

### Sessions

```SQL
CREATE TABLE `sessions` (
  `user_id` varchar(33) DEFAULT NULL,
  `ip` text DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(88) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

### Categories

```SQL
CREATE TABLE `categories` (
  `name` varchar(32) NOT NULL,
  `slug` varchar(32) NOT NULL,
  `id` varchar(33) NOT NULL,
  `description` varchar(128) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `threads` mediumint(9) UNSIGNED DEFAULT 0,
  `posts` mediumint(8) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

```SQL
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug` (`slug`);
COMMIT;
```

### Threads

```SQL
CREATE TABLE `threads` (
  `name` varchar(64) NOT NULL,
  `slug` varchar(32) NOT NULL,
  `id` varchar(33) NOT NULL,
  `user_id` varchar(33) NOT NULL,
  `category_id` varchar(33) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `deleted_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `posts` mediumint(8) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

```SQL
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `threadcategory` (`category_id`);
COMMIT;
```

### Posts

```SQL
CREATE TABLE `posts` (
  `user_id` varchar(33) NOT NULL,
  `post_id` varchar(33) NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL,
  `thread_id` varchar(33) NOT NULL,
  `edited` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `deleted_datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

```SQL
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `created` (`created`),
  ADD KEY `thread` (`thread_id`);
COMMIT;
```

### Subscribed

```SQL
CREATE TABLE `subscribed` (
  `thread_id` varchar(33) NOT NULL,
  `user_id` varchar(33) NOT NULL,
  `subscribed` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

### Mod_history

```SQL
CREATE TABLE `mod_history` (
  `mod_id` varchar(33) NOT NULL,
  `culp_id` varchar(33) NOT NULL,
  `id` varchar(33) NOT NULL,
  `summary` varchar(64) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `judgement` tinyint(1) NOT NULL DEFAULT 0,
  `sender_id` varchar(33) NOT NULL,
  `reason` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `message` text NOT NULL DEFAULT 'GENERIC',
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

```SQL
ALTER TABLE `mod_history`
  ADD PRIMARY KEY (`mod_id`),
  ADD KEY `id` (`id`),
  ADD KEY `recent` (`type`,`id`,`judgement`,`created`);
COMMIT;
```

### Notifications

```SQL
CREATE TABLE `notifications` (
  `notification_id` varchar(33) NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `sender_id` varchar(33) NOT NULL,
  `receiver_id` varchar(33) NOT NULL,
  `type` int(1) NOT NULL DEFAULT 0,
  `thread_id` varchar(33) DEFAULT '0',
  `assoc_id` varchar(33) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```
