-- liquibase formatted sql

-- changeset AxelRubioGonzálezSof:1761199939771-1 splitStatements:false
ALTER TABLE networks ADD hide_phone_numbers_in_pws TINYINT(3) UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Boolean used to not send garage phone numbers to PWS (AGN, GV, ...)';
