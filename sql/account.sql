-- --------------------------------------------------------
-- 主機:                           192.168.195.53
-- 伺服器版本:                        10.6.8-MariaDB - mariadb.org binary distribution
-- 伺服器作業系統:                      Win64
-- HeidiSQL 版本:                  11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- 傾印 account 的資料庫結構
CREATE DATABASE IF NOT EXISTS `account` /*!40100 DEFAULT CHARACTER SET utf8mb3 */;
USE `account`;

-- 傾印  資料表 account.0 結構
CREATE TABLE IF NOT EXISTS `0` (
  `UUID` char(32) NOT NULL,
  `AccountName` char(50) NOT NULL,
  `Password` text DEFAULT NULL,
  `DisplayName` char(50) DEFAULT NULL,
  `SubID` smallint(6) unsigned DEFAULT NULL,
  `Operator` tinyint(4) DEFAULT NULL,
  `CreateTime` datetime DEFAULT NULL,
  `Email` text DEFAULT NULL,
  `PhoneNumber` text DEFAULT NULL,
  `DisplayImage` mediumblob DEFAULT NULL,
  `Lang` char(50) DEFAULT NULL,
  `LastPlay` int(11) unsigned DEFAULT NULL,
  `token` char(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- 取消選取資料匯出。

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
