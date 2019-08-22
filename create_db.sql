CREATE TABLE `link`.`Link` ( 
    `link_id` INT NOT NULL AUTO_INCREMENT , 
    `link` TEXT NOT NULL , 
    `id` TEXT NOT NULL , 
    `counter` INT NOT NULL , 
    `password` TEXT NOT NULL , 
    `deleted` BOOLEAN NOT NULL , 
PRIMARY KEY (`link_id`)) ENGINE = InnoDB;