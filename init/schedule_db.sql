create database schedule;

use schedule;

SELECT @@character_set_database, @@collation_database;
ALTER DATABASE COLLATE 'utf8mb4_general_ci';

DROP TABLE IF EXISTS schedules;

CREATE TABLE schedules (
    id          int(11)         NOT NULL AUTO_INCREMENT,
    begin       datetime        NOT NULL,
    end         datetime        NOT NULL,
    place       varchar(256)    NOT NULL,
    content     text            NOT NULL,
);