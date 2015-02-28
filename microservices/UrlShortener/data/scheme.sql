CREATE TABLE `url` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `qrystr` varchar(255) NULL,
  `path_hash` varchar(255) NULL,
  `date_created` DATE,
  PRIMARY KEY (`id`),
  UNIQUE(`path_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;