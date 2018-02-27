CREATE TABLE IF NOT EXISTS `#__nochextlstesting` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`hello` text NOT NULL,
	`lang` varchar(25) NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__nochextlstesting` (`hello`, `lang`) VALUES ('Nochex TLS Testing', 'en-GB');

DROP TABLE IF EXISTS `#__nochextlstesting`