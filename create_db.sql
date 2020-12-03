CREATE TABLE `mainTable` ( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `path` TEXT NOT NULL , 
    `link` TEXT NOT NULL , 
    `password` TEXT NOT NULL , 
    `deleted` BOOLEAN NOT NULL , 
PRIMARY KEY (`id`)) ENGINE = InnoDB;

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
