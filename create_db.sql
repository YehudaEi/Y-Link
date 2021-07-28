CREATE TABLE `mainTable` (
  `id` int NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  `link` text NOT NULL,
  `password` text NOT NULL,
  `ip` text,
  `create_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `clicks` (
 `id` int NOT NULL AUTO_INCREMENT,
 `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
 `ip` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
 `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
 `language` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
 `referrer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
 `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
