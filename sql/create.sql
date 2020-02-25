CREATE TABLE `pad_coder_repo` (
 `ID` varchar(32) NOT NULL,
 `name` varchar(64) NOT NULL,
 `type` varchar(32) NOT NULL,
 `size` int(11) NOT NULL,
 `status` tinyint(1) NOT NULL,
 `collection` varchar(32) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
