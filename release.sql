ALTER TABLE `residents` ADD `ukphone` VARCHAR(20) NOT NULL ; 

ALTER TABLE `residents` 
ADD `how_can_contribute` TEXT CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL , 
ADD `how_benefit_you` TEXT CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL ;