CREATE TABLE 'user' (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` char(30) DEFAULT NULL,
  `last_name` char(30) DEFAULT NULL,
  `email` char(30) NOT NULL DEFAULT '',
  `password` char(30) NOT NULL DEFAULT '',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_key` char(40) DEFAULT NULL,
  `session_expire` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `flight_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `flight_number` varchar(8) CHARACTER SET ascii DEFAULT NULL,
  `date` varchar(12) CHARACTER SET ascii DEFAULT NULL,
  `dep_time` varchar(8) CHARACTER SET ascii DEFAULT NULL,
  `arr_time` varchar(8) CHARACTER SET ascii DEFAULT NULL,
  `class` varchar(8) CHARACTER SET ascii DEFAULT NULL,
  `seat` char(2) CHARACTER SET ascii DEFAULT NULL,
  `purpose` varchar(256) CHARACTER SET ascii DEFAULT NULL,
  `note` varchar(256) CHARACTER SET ascii DEFAULT NULL,
  `photourl` varchar(2083) CHARACTER SET ascii DEFAULT NULL,
  `dep_airport_id` int(11) DEFAULT NULL,
  `arr_airport_id` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `aircraft_id` int(11) DEFAULT NULL,
  `airline_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `Airline` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  `iata` char(2) CHARACTER SET ascii DEFAULT NULL,
  `icao` char(3) CHARACTER SET ascii DEFAULT NULL,
  `country` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  `active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `Aircraft` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  'aircraft_type' varchar(16) CHARACTER SET ascii DEFAULT NULL,
  'aircraft_reg' varchar(16) CHARACTER SET ascii DEFAULT NULL,
  `iata` char(2) CHARACTER SET ascii DEFAULT NULL,
  `icao` char(3) CHARACTER SET ascii DEFAULT NULL,
  `model` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  `manufacture` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `Airport` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  `city` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  `country` varchar(128) CHARACTER SET ascii DEFAULT NULL,
  `iata` char(3) CHARACTER SET ascii DEFAULT NULL,
  `icta` char(4) CHARACTER SET ascii DEFAULT NULL,
  `latitude` decimal(8,6) DEFAULT '0.000000',
  `longitude` decimal(9,6) DEFAULT '0.000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;