CREATE TABLE `mainTable` ( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `path` TEXT NOT NULL , 
    `link` TEXT NOT NULL , 
    `password` TEXT NOT NULL , 
    `deleted` BOOLEAN NOT NULL , 
PRIMARY KEY (`id`)) ENGINE = InnoDB;