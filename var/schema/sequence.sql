CREATE TABLE `sequence` (
	`scope` VARCHAR(64) NOT NULL,
	`value` BIGINT NOT NULL DEFAULT 0,
	PRIMARY KEY (`scope`)
)
ENGINE=InnoDB;