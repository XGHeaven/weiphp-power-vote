DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='power_vote' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='power_vote' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_power_vote`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='power_vote_option' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='power_vote_option' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_power_vote_option`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='power_vote_log' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='power_vote_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_power_vote_log`;