ALTER TABLE `nsm_user` CHANGE COLUMN `user_email` `user_email` VARCHAR(254) NOT NULL;
ALTER TABLE nsm_user ADD UNIQUE INDEX `user_email_unique_idx` (`user_email` ASC);