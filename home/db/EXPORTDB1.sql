-- --------------------------------------------------------
-- Host:                         192.168.2.222
-- Server version:               11.6.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for fingerspot
CREATE DATABASE IF NOT EXISTS `fingerspot` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `fingerspot`;

-- Dumping structure for table fingerspot.access
CREATE TABLE IF NOT EXISTS `access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) DEFAULT NULL,
  `page` varchar(200) DEFAULT NULL,
  `access` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.att_log
CREATE TABLE IF NOT EXISTS `att_log` (
  `sn` varchar(30) NOT NULL DEFAULT 'manual',
  `scan_date` datetime NOT NULL,
  `pin` varchar(32) NOT NULL,
  `verifymode` int(11) NOT NULL,
  `inoutmode` int(11) NOT NULL DEFAULT 0,
  `reserved` int(11) NOT NULL DEFAULT 0,
  `work_code` int(11) NOT NULL DEFAULT 0,
  `att_id` varchar(50) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sn`,`scan_date`,`pin`),
  KEY `pin` (`pin`),
  KEY `sn` (`sn`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8151916 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.att_log_temp
CREATE TABLE IF NOT EXISTS `att_log_temp` (
  `sn` varchar(30) NOT NULL,
  `scan_date` datetime NOT NULL,
  `pin` varchar(32) NOT NULL,
  `verifymode` int(11) NOT NULL,
  `inoutmode` int(11) NOT NULL DEFAULT 0,
  `reserved` int(11) NOT NULL DEFAULT 0,
  `work_code` int(11) NOT NULL DEFAULT 0,
  `att_id` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.bank_account
CREATE TABLE IF NOT EXISTS `bank_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.billofmaterial
CREATE TABLE IF NOT EXISTS `billofmaterial` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) DEFAULT NULL,
  `id_material` int(11) DEFAULT NULL,
  `penggunaan` float DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.calender
CREATE TABLE IF NOT EXISTS `calender` (
  `date` date NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.changelog
CREATE TABLE IF NOT EXISTS `changelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_admin` varchar(200) DEFAULT NULL,
  `id_google` varchar(500) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `riwayat` varchar(1000) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.country_data
CREATE TABLE IF NOT EXISTS `country_data` (
  `id_country` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  `code1` varchar(10) NOT NULL,
  `code2` varchar(10) NOT NULL,
  `flag` varchar(20) NOT NULL,
  PRIMARY KEY (`id_country`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.currency
CREATE TABLE IF NOT EXISTS `currency` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) DEFAULT NULL,
  `nama` varchar(200) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `oldrate` float DEFAULT NULL,
  `update` datetime DEFAULT NULL,
  `olddate` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.customer
CREATE TABLE IF NOT EXISTS `customer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `id_country` int(11) DEFAULT NULL,
  `id_currency` varchar(10) DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.cuti_normatif
CREATE TABLE IF NOT EXISTS `cuti_normatif` (
  `cuti_n_id` int(11) NOT NULL DEFAULT 0,
  `cuti_n_nama` varchar(100) NOT NULL,
  `cuti_n_lama` smallint(6) NOT NULL DEFAULT 0,
  `nominal` float NOT NULL DEFAULT 0,
  `jns_bayar` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cuti_n_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.device
CREATE TABLE IF NOT EXISTS `device` (
  `sn` varchar(30) NOT NULL DEFAULT '',
  `activation_code` varchar(50) NOT NULL,
  `act_code_realtime` varchar(50) DEFAULT NULL,
  `device_name` varchar(100) DEFAULT '',
  `dev_id` smallint(6) NOT NULL DEFAULT 1 COMMENT 'no mesin',
  `comm_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: ethernet, 1: usb, 2: serial',
  `ip_address` varchar(30) DEFAULT '',
  `id_type` int(11) NOT NULL DEFAULT 0,
  `dev_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Jenis mesin = 0: ZK, 1: Hanvon, 2: Realand',
  `comm_key` varchar(50) DEFAULT '0' COMMENT 'Password koneksi mesin',
  `serial_port` varchar(30) DEFAULT '',
  `baud_rate` varchar(15) DEFAULT '',
  `ethernet_port` varchar(30) NOT NULL DEFAULT '4370',
  `layar` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: TFT, 1: BW',
  `alg_ver` tinyint(4) NOT NULL DEFAULT 10 COMMENT '9 & 10',
  `use_realtime` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'yes/no',
  `group_realtime` tinyint(4) NOT NULL DEFAULT 0,
  `last_download` date DEFAULT NULL,
  `ATTLOGStamp` varchar(50) NOT NULL DEFAULT '0',
  `OPERLOGStamp` varchar(50) NOT NULL DEFAULT '0',
  `ATTPHOTOStamp` varchar(50) NOT NULL DEFAULT '0',
  `cloud_id` varchar(100) NOT NULL DEFAULT '',
  `last_download_web` date DEFAULT NULL,
  `id_server_use` int(11) NOT NULL DEFAULT -1,
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.dev_type
CREATE TABLE IF NOT EXISTS `dev_type` (
  `dev_type` int(11) NOT NULL,
  `id_type` int(11) NOT NULL,
  `type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.effectivehours
CREATE TABLE IF NOT EXISTS `effectivehours` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `day` varchar(50) DEFAULT NULL,
  `work_start` time DEFAULT NULL,
  `work_end` time DEFAULT NULL,
  `overtime_start_1` time DEFAULT NULL,
  `overtime_end_1` time DEFAULT NULL,
  `work_break` time DEFAULT NULL,
  `work_break_end` time DEFAULT NULL,
  `overtime_break_1` time DEFAULT NULL,
  `overtime_break_end_1` time DEFAULT NULL,
  `overtime_start_2` time DEFAULT NULL,
  `overtime_start_3` time DEFAULT NULL,
  `overtime_end_2` time DEFAULT NULL,
  `overtime_end_3` time DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `overtime_break_2` time DEFAULT NULL,
  `overtime_break_3` time DEFAULT NULL,
  `overtime_break_end_2` time DEFAULT NULL,
  `overtime_break_end_3` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.employeesallarycat
CREATE TABLE IF NOT EXISTS `employeesallarycat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kode` varchar(50) DEFAULT NULL,
  `Nama` varchar(100) DEFAULT NULL,
  `Kategori` varchar(50) DEFAULT NULL,
  `Gaji_Pokok` int(11) DEFAULT NULL,
  `Gaji_Per_Jam` int(11) DEFAULT NULL,
  `Gaji_Per_Jam_Hari_Minggu` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.employee_allowance_list
CREATE TABLE IF NOT EXISTS `employee_allowance_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `allowance_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1198 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.employee_category
CREATE TABLE IF NOT EXISTS `employee_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.employee_deduction_list
CREATE TABLE IF NOT EXISTS `employee_deduction_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `deduction_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1178 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.finishing
CREATE TABLE IF NOT EXISTS `finishing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.fitting_components
CREATE TABLE IF NOT EXISTS `fitting_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `material` varchar(100) NOT NULL,
  `length_mm` int(11) NOT NULL,
  `width_mm` int(11) NOT NULL,
  `thickness` decimal(5,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `consumption` decimal(10,4) NOT NULL,
  `waste` decimal(5,2) NOT NULL,
  `total_consumption` decimal(10,4) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost_idr` decimal(15,2) NOT NULL,
  `cost_usd` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fitting_components_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jdw_d
CREATE TABLE IF NOT EXISTS `ganti_jdw_d` (
  `ganti_jdw_id` int(11) NOT NULL,
  `tgl_ganti_jdw` date NOT NULL,
  `jns_ganti_jdw` tinyint(4) NOT NULL COMMENT '0: Ganti Jadwal Kerja, 1: Ganti Jadwal Bagian, 2: Ganti Jadwal Pegawai (sesuai prioritas rendah ke tinggi)',
  `jdw_kerja_m_id` int(11) NOT NULL,
  `pegawai_id` int(11) NOT NULL DEFAULT 0 COMMENT '0: Selain Pegawai',
  PRIMARY KEY (`ganti_jdw_id`,`tgl_ganti_jdw`,`pegawai_id`,`jns_ganti_jdw`,`jdw_kerja_m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jdw_jk
CREATE TABLE IF NOT EXISTS `ganti_jdw_jk` (
  `ganti_jdw_id` int(11) NOT NULL,
  `jdw_kerja_m_id1` int(11) NOT NULL,
  `jdw_kerja_m_id2` int(11) NOT NULL,
  `tgl_awal` date NOT NULL,
  `tgl_akhir` date NOT NULL,
  `keterangan` varchar(200) NOT NULL,
  PRIMARY KEY (`ganti_jdw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jdw_pegawai
CREATE TABLE IF NOT EXISTS `ganti_jdw_pegawai` (
  `ganti_jdw_id` int(11) NOT NULL DEFAULT 0,
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `tgl_awal` date NOT NULL DEFAULT '0000-00-00',
  `tgl_akhir` date NOT NULL DEFAULT '0000-00-00',
  `jdw_kerja_m_id` int(11) NOT NULL DEFAULT 0 COMMENT 'Jadwal pengganti',
  `keterangan` varchar(200) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ganti_jdw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jdw_pembagian
CREATE TABLE IF NOT EXISTS `ganti_jdw_pembagian` (
  `ganti_jdw_id` int(11) NOT NULL DEFAULT 0,
  `pembagian1_id` int(11) NOT NULL DEFAULT 0,
  `pembagian2_id` int(11) NOT NULL DEFAULT 0,
  `pembagian3_id` int(11) NOT NULL DEFAULT 0,
  `tgl_awal` date NOT NULL DEFAULT '0000-00-00',
  `tgl_akhir` date NOT NULL DEFAULT '0000-00-00',
  `jdw_kerja_m_id` int(11) NOT NULL DEFAULT 0,
  `keterangan` varchar(200) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ganti_jdw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jk
CREATE TABLE IF NOT EXISTS `ganti_jk` (
  `ganti_jk_id` int(11) NOT NULL,
  `jk_id1` int(11) NOT NULL,
  `jk_id2` int(11) NOT NULL,
  `tgl_awal` date NOT NULL,
  `tgl_akhir` date NOT NULL,
  `keterangan` varchar(200) NOT NULL,
  PRIMARY KEY (`ganti_jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jk_d
CREATE TABLE IF NOT EXISTS `ganti_jk_d` (
  `ganti_jk_id` int(11) NOT NULL,
  `tgl_ganti_jk` date NOT NULL,
  `jns_ganti_jk` tinyint(4) NOT NULL COMMENT '0: Ganti Jam Kerja, 1: Ganti Jam Bagian, 2: Ganti Jam Pegawai (sesuai prioritas rendah ke tinggi)',
  `jk_id` int(11) NOT NULL,
  `pegawai_id` int(11) NOT NULL DEFAULT 0 COMMENT '0: Selain Pegawai',
  `libur` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ganti_jk_id`,`tgl_ganti_jk`,`pegawai_id`,`jns_ganti_jk`,`jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jk_pegawai
CREATE TABLE IF NOT EXISTS `ganti_jk_pegawai` (
  `ganti_jk_id` int(11) NOT NULL DEFAULT 0,
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `tgl_awal` date NOT NULL DEFAULT '0000-00-00',
  `tgl_akhir` date NOT NULL DEFAULT '0000-00-00',
  `jk_id` int(11) NOT NULL DEFAULT 0 COMMENT 'Jam Kerja pengganti',
  `keterangan` varchar(200) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ganti_jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.ganti_jk_pembagian
CREATE TABLE IF NOT EXISTS `ganti_jk_pembagian` (
  `ganti_jk_id` int(11) NOT NULL DEFAULT 0,
  `pembagian1_id` int(11) NOT NULL DEFAULT 0,
  `pembagian2_id` int(11) NOT NULL DEFAULT 0,
  `pembagian3_id` int(11) NOT NULL DEFAULT 0,
  `tgl_awal` date NOT NULL DEFAULT '0000-00-00',
  `tgl_akhir` date NOT NULL DEFAULT '0000-00-00',
  `jk_id` int(11) NOT NULL DEFAULT 0,
  `keterangan` varchar(200) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ganti_jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.grp_user_d
CREATE TABLE IF NOT EXISTS `grp_user_d` (
  `grp_user_id` varchar(100) NOT NULL DEFAULT '',
  `tree_id` varchar(255) NOT NULL,
  `level_tree` smallint(6) NOT NULL DEFAULT 0,
  `com_id` varchar(100) NOT NULL,
  `com_form` varchar(100) NOT NULL,
  `com_name` varchar(100) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `urutan` smallint(6) NOT NULL,
  `app_name` varchar(100) NOT NULL DEFAULT '',
  UNIQUE KEY `com_id` (`com_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.grp_user_m
CREATE TABLE IF NOT EXISTS `grp_user_m` (
  `grp_user_id` int(11) NOT NULL,
  `grp_user_name` varchar(100) NOT NULL DEFAULT '',
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(100) NOT NULL,
  `detector` text NOT NULL,
  PRIMARY KEY (`grp_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.index_ot
CREATE TABLE IF NOT EXISTS `index_ot` (
  `index_id` tinyint(4) NOT NULL,
  `type_ot` tinyint(4) NOT NULL DEFAULT 0,
  `from_ot` smallint(6) NOT NULL,
  `to_ot` smallint(6) NOT NULL,
  `multiplier` float NOT NULL,
  PRIMARY KEY (`index_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.index_type
CREATE TABLE IF NOT EXISTS `index_type` (
  `type_ot` tinyint(4) NOT NULL DEFAULT 0,
  `type_name` varchar(50) NOT NULL,
  PRIMARY KEY (`type_ot`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.informasi_pegawai
CREATE TABLE IF NOT EXISTS `informasi_pegawai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pin` varchar(50) DEFAULT NULL,
  `id_pegawai` int(11) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `masuk_kerja` date DEFAULT NULL,
  `keluar_kerja` date DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `foto` varchar(200) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jumlah_tanggungan` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `no_bpjs` varchar(50) DEFAULT NULL,
  `no_bpjstk` varchar(50) DEFAULT NULL,
  `pemilik_rekening` varchar(100) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `posisi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1176 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for procedure fingerspot.InsertDataForEachPin
DELIMITER //
CREATE PROCEDURE `InsertDataForEachPin`()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE current_pin VARCHAR(20);
    
    -- Deklarasi cursor untuk mengambil semua pin unik dengan sn = '6668601728359'
    DECLARE pin_cursor CURSOR FOR
        SELECT DISTINCT pin 
        FROM att_log 
        WHERE sn = '6668601728359';
    
    -- Deklarasi handler untuk menandai akhir dari cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    -- Buka cursor
    OPEN pin_cursor;
    
    -- Looping untuk setiap pin
    pin_loop: LOOP
        -- Ambil pin berikutnya dari cursor
        FETCH pin_cursor INTO current_pin;
        
        -- Keluar dari loop jika tidak ada data lagi
        IF done = 1 THEN
            LEAVE pin_loop;
        END IF;
        
        -- Insert data baru untuk pin saat ini
        INSERT INTO `att_log` (`sn`, `scan_date`, `pin`, `verifymode`, `inoutmode`, `reserved`, `work_code`, `att_id`)
        VALUES ('6668601728359', '2025-02-27 16:30:00', current_pin, 20, 3, 0, 0, '0');
    END LOOP;
    
    -- Tutup cursor
    CLOSE pin_cursor;
END//
DELIMITER ;

-- Dumping structure for table fingerspot.izin
CREATE TABLE IF NOT EXISTS `izin` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `izin_urutan` smallint(6) NOT NULL DEFAULT 0,
  `izin_tgl_pengajuan` date NOT NULL,
  `izin_tgl` date NOT NULL,
  `izin_jenis_id` smallint(6) NOT NULL DEFAULT 0 COMMENT 'Foreign key ke tabel jns_izin',
  `izin_catatan` varchar(255) DEFAULT NULL,
  `izin_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0:tidak di izinkan; 1: diizinkan',
  `izin_tinggal_t1` time DEFAULT NULL,
  `izin_tinggal_t2` time DEFAULT NULL,
  `cuti_n_id` int(11) DEFAULT 0,
  `izin_ket_lain` varchar(100) DEFAULT NULL,
  `izin_noscan_time` time DEFAULT NULL,
  `kat_izin_id` int(11) DEFAULT 0,
  `ket_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pegawai_id`,`izin_tgl`,`izin_jenis_id`,`izin_status`,`izin_urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.jam_kerja
CREATE TABLE IF NOT EXISTS `jam_kerja` (
  `jk_id` int(11) NOT NULL DEFAULT 0,
  `jk_name` varchar(100) NOT NULL DEFAULT '',
  `jk_kode` varchar(10) NOT NULL DEFAULT '',
  `use_set` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No',
  `jk_bcin` time NOT NULL DEFAULT '00:00:00',
  `jk_cin` smallint(6) NOT NULL DEFAULT 0,
  `jk_ecin` smallint(6) NOT NULL DEFAULT 0,
  `jk_tol_late` smallint(6) NOT NULL DEFAULT 0,
  `jk_use_ist` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No',
  `jk_ist1` time NOT NULL DEFAULT '00:00:00',
  `jk_ist2` time NOT NULL DEFAULT '00:00:00',
  `jk_tol_early` smallint(6) NOT NULL DEFAULT 0,
  `jk_bcout` smallint(6) NOT NULL DEFAULT 0,
  `jk_cout` smallint(6) NOT NULL DEFAULT 0,
  `jk_ecout` time NOT NULL DEFAULT '00:00:00',
  `use_eot` tinyint(4) NOT NULL DEFAULT 0,
  `min_eot` smallint(6) NOT NULL DEFAULT 0,
  `max_eot` smallint(6) NOT NULL DEFAULT 0,
  `reduce_eot` smallint(6) NOT NULL DEFAULT 0,
  `jk_durasi` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1: Efektif, 2: Aktual',
  `jk_countas` float NOT NULL DEFAULT 0,
  `jk_min_countas` smallint(6) NOT NULL DEFAULT 0,
  `jk_min_countas2` smallint(6) NOT NULL DEFAULT 0,
  `jk_ket` varchar(100) DEFAULT '',
  PRIMARY KEY (`jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.jam_kerja_extra
CREATE TABLE IF NOT EXISTS `jam_kerja_extra` (
  `jke_tanggal` date NOT NULL DEFAULT '0000-00-00',
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `jk_id` int(11) NOT NULL DEFAULT 0,
  `jke_libur` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'YES/NO',
  PRIMARY KEY (`jke_tanggal`,`pegawai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.jatah_cuti
CREATE TABLE IF NOT EXISTS `jatah_cuti` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `jatah_c_mulai` date NOT NULL DEFAULT '0000-00-00',
  `jatah_c_akhir` date NOT NULL DEFAULT '0000-00-00',
  `jatah_c_jml` smallint(6) DEFAULT 0,
  `jatah_c_hak_jml` smallint(6) DEFAULT 0,
  `jatah_c_ambil_jml` smallint(6) DEFAULT 0,
  `jatah_c_utang_jml` smallint(6) DEFAULT 0,
  PRIMARY KEY (`pegawai_id`,`jatah_c_mulai`,`jatah_c_akhir`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.jdw_kerja_d
CREATE TABLE IF NOT EXISTS `jdw_kerja_d` (
  `jdw_kerja_m_id` int(11) NOT NULL DEFAULT 0,
  `jdw_kerja_d_idx` smallint(6) NOT NULL DEFAULT 0 COMMENT '1:minggu; 2:senin; dst',
  `jk_id` int(11) NOT NULL DEFAULT 0,
  `jdw_kerja_d_hari` varchar(50) DEFAULT NULL,
  `jdw_kerja_d_libur` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`jdw_kerja_m_id`,`jdw_kerja_d_idx`,`jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.jdw_kerja_m
CREATE TABLE IF NOT EXISTS `jdw_kerja_m` (
  `jdw_kerja_m_id` int(11) NOT NULL DEFAULT 0,
  `jdw_kerja_m_kode` varchar(5) DEFAULT NULL,
  `jdw_kerja_m_name` varchar(100) DEFAULT NULL,
  `jdw_kerja_m_keterangan` varchar(255) DEFAULT NULL,
  `jdw_kerja_m_periode` smallint(6) DEFAULT 0,
  `jdw_kerja_m_mulai` date DEFAULT NULL,
  `jdw_kerja_m_type` tinyint(4) DEFAULT 0 COMMENT '0: Normal; 1: Pola; 2: Auto',
  `use_sama` tinyint(4) DEFAULT -1 COMMENT 'Jam kerja setiap hari sama / tidak',
  PRIMARY KEY (`jdw_kerja_m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.jdw_kerja_pegawai
CREATE TABLE IF NOT EXISTS `jdw_kerja_pegawai` (
  `pegawai_id` int(11) NOT NULL,
  `jdw_kerja_m_id` int(11) NOT NULL,
  `jdw_kerja_m_mulai` date NOT NULL,
  PRIMARY KEY (`pegawai_id`,`jdw_kerja_m_id`,`jdw_kerja_m_mulai`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.jns_izin
CREATE TABLE IF NOT EXISTS `jns_izin` (
  `izin_jenis_id` smallint(6) NOT NULL,
  `izin_jenis_name` varchar(200) NOT NULL,
  `flag` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Default, 1: Normatif',
  PRIMARY KEY (`izin_jenis_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.kategori_izin
CREATE TABLE IF NOT EXISTS `kategori_izin` (
  `kat_izin_id` int(11) NOT NULL DEFAULT 0,
  `kat_izin_nama` varchar(100) DEFAULT NULL,
  `izin_jenis_id` smallint(6) DEFAULT 0,
  PRIMARY KEY (`kat_izin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.kontrak_kerja
CREATE TABLE IF NOT EXISTS `kontrak_kerja` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `kontrak_start` date NOT NULL DEFAULT '0000-00-00',
  `kontrak_end` date NOT NULL DEFAULT '0000-00-00',
  `kontrak_status` tinyint(4) DEFAULT 0 COMMENT '0: kontrak; 1: tetap',
  `kontrak_aktif` tinyint(4) DEFAULT -1,
  PRIMARY KEY (`pegawai_id`,`kontrak_start`,`kontrak_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.labor_costs
CREATE TABLE IF NOT EXISTS `labor_costs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `material` varchar(100) NOT NULL,
  `process` varchar(255) NOT NULL,
  `time_minutes` int(11) NOT NULL,
  `wage_per_minute` decimal(10,2) NOT NULL,
  `total_cost_idr` decimal(15,2) NOT NULL,
  `cost_usd` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `labor_costs_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.lembur
CREATE TABLE IF NOT EXISTS `lembur` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `lembur_tgl` date NOT NULL DEFAULT '0000-00-00',
  `lembur_mulai` time NOT NULL DEFAULT '00:00:00',
  `lembur_selesai` time NOT NULL DEFAULT '00:00:00',
  `lembur_urut` tinyint(4) NOT NULL,
  `type_ot` tinyint(4) NOT NULL DEFAULT -1,
  `lembur_durasi_min` smallint(6) DEFAULT 0,
  `lembur_durasi_max` smallint(6) DEFAULT 0,
  `lembur_keperluan` varchar(100) DEFAULT '',
  PRIMARY KEY (`pegawai_id`,`lembur_tgl`,`lembur_mulai`,`lembur_selesai`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.libur
CREATE TABLE IF NOT EXISTS `libur` (
  `libur_tgl` date NOT NULL,
  `libur_keterangan` varchar(255) DEFAULT '',
  `libur_status` tinyint(4) DEFAULT 0 COMMENT '1: Hari Libur; 2: Cuti Bersama',
  PRIMARY KEY (`libur_tgl`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.machine_costs
CREATE TABLE IF NOT EXISTS `machine_costs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `machine` varchar(100) NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `rate_per_hour` decimal(10,2) NOT NULL,
  `total_cost_idr` decimal(15,2) NOT NULL,
  `cost_usd` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `machine_costs_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.master_penggajian
CREATE TABLE IF NOT EXISTS `master_penggajian` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_penggajian` varchar(50) DEFAULT NULL,
  `tanggal_awal_penggajian` datetime NOT NULL,
  `group` varchar(50) DEFAULT NULL,
  `creator` varchar(50) DEFAULT NULL,
  `tanggal_akhir_penggajian` datetime NOT NULL,
  `keterangan` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.master_penggajian_detail
CREATE TABLE IF NOT EXISTS `master_penggajian_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `penggajian_id` int(10) unsigned NOT NULL,
  `karyawan_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=469 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.materials
CREATE TABLE IF NOT EXISTS `materials` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `kode` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.materials_detail
CREATE TABLE IF NOT EXISTS `materials_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `satuan_id` int(11) NOT NULL,
  `kite` varchar(20) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `dimension` varchar(11) DEFAULT NULL,
  `grade` varchar(11) DEFAULT NULL,
  `color` varchar(11) DEFAULT NULL,
  `texture` varchar(11) DEFAULT NULL,
  `source` varchar(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `due` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `order_date` datetime NOT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pegawai
CREATE TABLE IF NOT EXISTS `pegawai` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `pegawai_pin` varchar(32) NOT NULL,
  `pegawai_nip` varchar(30) DEFAULT NULL,
  `pegawai_nama` varchar(255) NOT NULL,
  `pegawai_alias` varchar(50) DEFAULT NULL,
  `pegawai_pwd` varchar(10) NOT NULL DEFAULT '0',
  `pegawai_rfid` varchar(32) NOT NULL DEFAULT '0',
  `pegawai_privilege` varchar(50) NOT NULL DEFAULT '0' COMMENT '-1: Invalid, 0: User,  1: Operator, 2: Sub Admin, 3: Admin',
  `pegawai_telp` varchar(20) DEFAULT NULL,
  `pegawai_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0:Non Aktif; 1:Aktif; 2:Berhenti',
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `pembagian1_id` int(11) DEFAULT 0,
  `pembagian2_id` int(11) DEFAULT 0,
  `pembagian3_id` int(11) DEFAULT 0,
  `tgl_mulai_kerja` date DEFAULT NULL,
  `tgl_resign` date DEFAULT NULL,
  `gender` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:Laki-laki, 2:Perempuan',
  `tgl_masuk_pertama` date DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT '',
  `tmp_img` mediumtext DEFAULT NULL,
  `nama_bank` varchar(50) DEFAULT '',
  `nama_rek` varchar(100) DEFAULT '',
  `no_rek` varchar(20) DEFAULT '',
  `password_fio_desktop` varchar(6) NOT NULL DEFAULT '000000',
  `status_login_fio_desktop` tinyint(4) NOT NULL DEFAULT 0,
  `new_pegawai_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pegawai_id`),
  UNIQUE KEY `pegawai_pin` (`pegawai_pin`),
  KEY `idx_pegawai_status` (`pegawai_status`),
  KEY `idx_pegawai_nip` (`pegawai_nip`),
  KEY `idx_pegawai_nama` (`pegawai_nama`),
  KEY `idx_pegawai_id` (`pegawai_id`),
  KEY `idx_pegawai_id_order` (`pegawai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pegawai_d
CREATE TABLE IF NOT EXISTS `pegawai_d` (
  `pegawai_id` int(11) NOT NULL,
  `pend_id` int(11) NOT NULL DEFAULT 30,
  `gol_darah` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:A+, 2:B+, 3:O+, 4:AB+, 5:A-, 6:B-, 7:O-, 8:AB-',
  `stat_nikah` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:sudah menikah, 2:belum menikah, 3:duda/janda meninggal, 4:duda/janda cerai',
  `jml_anak` tinyint(4) NOT NULL DEFAULT 0,
  `alamat` varchar(200) DEFAULT NULL,
  `telp_extra` varchar(20) NOT NULL DEFAULT '',
  `hubungan` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1:Keluarga, 2:Pasangan, 3:Saudara, 4:Teman, 5:Tetangga, 6:Lainnya',
  `nama_hubungan` varchar(200) NOT NULL DEFAULT '',
  `agama` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:Islam, 2:Katolik, 3:Protestan, 4:Hindu, 5:Budha, 6:Lainnya',
  UNIQUE KEY `pegawai_id` (`pegawai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pegawai_status_pajak
CREATE TABLE IF NOT EXISTS `pegawai_status_pajak` (
  `pegawai_id` int(11) NOT NULL,
  `pegawai_status` int(11) NOT NULL,
  PRIMARY KEY (`pegawai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pembagian1
CREATE TABLE IF NOT EXISTS `pembagian1` (
  `pembagian1_id` int(11) NOT NULL DEFAULT 0,
  `pembagian1_nama` varchar(100) DEFAULT NULL,
  `pembagian1_ket` varchar(255) DEFAULT '',
  PRIMARY KEY (`pembagian1_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pembagian2
CREATE TABLE IF NOT EXISTS `pembagian2` (
  `pembagian2_id` int(11) NOT NULL DEFAULT 0,
  `pembagian2_nama` varchar(100) DEFAULT NULL,
  `pembagian2_ket` varchar(255) DEFAULT '',
  PRIMARY KEY (`pembagian2_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pembagian3
CREATE TABLE IF NOT EXISTS `pembagian3` (
  `pembagian3_id` int(11) NOT NULL DEFAULT 0,
  `pembagian3_nama` varchar(100) DEFAULT NULL,
  `pembagian3_ket` varchar(255) DEFAULT '',
  PRIMARY KEY (`pembagian3_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pembelian
CREATE TABLE IF NOT EXISTS `pembelian` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_supplier` varchar(255) NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `tanggal_nota` datetime DEFAULT NULL,
  `tanggal_jatuh_tempo` datetime DEFAULT NULL,
  `status_pembayaran` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pembelian_detail
CREATE TABLE IF NOT EXISTS `pembelian_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `jumlah` float DEFAULT NULL,
  `harga` float DEFAULT NULL,
  `status_pembayaran` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pembulatan
CREATE TABLE IF NOT EXISTS `pembulatan` (
  `from_mnt` tinyint(4) NOT NULL,
  `to_mnt` tinyint(4) NOT NULL,
  `dibulatkan` tinyint(4) NOT NULL COMMENT 'Yes/No',
  `value_mnt` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.pendidikan
CREATE TABLE IF NOT EXISTS `pendidikan` (
  `pend_id` int(11) NOT NULL,
  `pend_name` varchar(20) NOT NULL,
  PRIMARY KEY (`pend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) DEFAULT NULL,
  `id_product_cat` int(11) DEFAULT NULL,
  `nama` varchar(200) DEFAULT NULL,
  `picture` varchar(2000) DEFAULT NULL,
  `text` mediumtext DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.production
CREATE TABLE IF NOT EXISTS `production` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `design_name` varchar(255) NOT NULL,
  `material_id` int(10) unsigned NOT NULL,
  `quantity_required` int(11) NOT NULL,
  `production_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.product_category
CREATE TABLE IF NOT EXISTS `product_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.product_details
CREATE TABLE IF NOT EXISTS `product_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_date` date DEFAULT NULL,
  `dept` varchar(50) NOT NULL,
  `collection_code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `customer` varchar(50) NOT NULL,
  `markup_material` decimal(5,2) NOT NULL,
  `length_mm` int(11) NOT NULL,
  `height_mm` int(11) NOT NULL,
  `width_mm` int(11) NOT NULL,
  `nw_kg` decimal(5,2) DEFAULT NULL,
  `gw_kg` decimal(5,2) DEFAULT NULL,
  `cbm` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.product_picture
CREATE TABLE IF NOT EXISTS `product_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` varchar(50) DEFAULT NULL,
  `gambar` varchar(2000) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.proforma_invoice
CREATE TABLE IF NOT EXISTS `proforma_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `currency` varchar(10) NOT NULL,
  `payment_terms` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.proforma_invoice_details
CREATE TABLE IF NOT EXISTS `proforma_invoice_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `hs_code` varchar(20) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.purchases
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `material_id` int(10) unsigned NOT NULL,
  `quantity` float NOT NULL,
  `status` varchar(55) NOT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.p_commands
CREATE TABLE IF NOT EXISTS `p_commands` (
  `commands_sn` varchar(32) NOT NULL,
  `commands_number` int(10) unsigned NOT NULL,
  `commands_data` varchar(2600) NOT NULL,
  `commands_status` varchar(3) NOT NULL DEFAULT '7' COMMENT '7=just added,8=send,0=success,other=failed;9=resetifnousebyreboot,10=cancel',
  `commands_type` varchar(32) NOT NULL DEFAULT '7',
  `commands_dateentry` datetime NOT NULL,
  `commands_datesend` datetime NOT NULL,
  `commands_datereturn` datetime NOT NULL,
  `commands_datecancel` datetime NOT NULL COMMENT 'Tanggal perintah dibatalkan',
  PRIMARY KEY (`commands_sn`,`commands_number`),
  KEY `commands_sn` (`commands_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.p_emp
CREATE TABLE IF NOT EXISTS `p_emp` (
  `pin` varchar(32) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `priv` varchar(10) DEFAULT NULL,
  `pwd` varchar(10) DEFAULT NULL,
  `rfid` varchar(32) DEFAULT NULL,
  `grp` varchar(5) DEFAULT NULL,
  `tz` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`pin`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.p_tmp
CREATE TABLE IF NOT EXISTS `p_tmp` (
  `pin` varchar(32) NOT NULL,
  `fid` varchar(2) NOT NULL,
  `valid_str` varchar(2) NOT NULL,
  `size_str` varchar(5) NOT NULL,
  `tmp` text NOT NULL,
  PRIMARY KEY (`pin`,`fid`,`valid_str`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.raw_materials
CREATE TABLE IF NOT EXISTS `raw_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `material_type` varchar(100) NOT NULL,
  `material` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `component` varchar(100) NOT NULL,
  `dimensions` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `consumption_actual` decimal(10,4) NOT NULL,
  `waste` decimal(5,2) NOT NULL,
  `total_consumption` decimal(10,4) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost_idr` decimal(15,2) NOT NULL,
  `cost_usd` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `raw_materials_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.salarysetting
CREATE TABLE IF NOT EXISTS `salarysetting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `employee_salarycat_id` int(11) DEFAULT NULL,
  `pin` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.salary_allowance
CREATE TABLE IF NOT EXISTS `salary_allowance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kode` varchar(50) DEFAULT NULL,
  `Nama` varchar(50) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.salary_deduction
CREATE TABLE IF NOT EXISTS `salary_deduction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kode` varchar(50) DEFAULT NULL,
  `Nama` varchar(50) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.salary_pattern
CREATE TABLE IF NOT EXISTS `salary_pattern` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pattern_code` varchar(50) DEFAULT NULL,
  `employee_cat_id` int(11) DEFAULT NULL,
  `pattern_name` varchar(50) DEFAULT NULL,
  `salary_det_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.salary_pattern_employee
CREATE TABLE IF NOT EXISTS `salary_pattern_employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_salary_pattern` int(11) DEFAULT NULL,
  `id_employee` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=483 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.satuan
CREATE TABLE IF NOT EXISTS `satuan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) DEFAULT NULL,
  `nama` varchar(200) DEFAULT NULL,
  `base_unit_id` int(10) unsigned DEFAULT NULL,
  `conversion_factor` float DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.scrap
CREATE TABLE IF NOT EXISTS `scrap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `material_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `scrap_date` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.server
CREATE TABLE IF NOT EXISTS `server` (
  `id_server` int(11) NOT NULL,
  `nama_server` varchar(50) NOT NULL DEFAULT '',
  `url_server` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_server`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.server_use
CREATE TABLE IF NOT EXISTS `server_use` (
  `id_server_use` int(11) NOT NULL,
  `nama_server_use` varchar(50) NOT NULL,
  `id_server` int(11) NOT NULL,
  `apikey` varchar(50) NOT NULL,
  PRIMARY KEY (`id_server_use`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.setting
CREATE TABLE IF NOT EXISTS `setting` (
  `param_name` varchar(100) NOT NULL DEFAULT '',
  `param_value` varchar(100) DEFAULT '',
  `keterangan` varchar(100) DEFAULT '',
  PRIMARY KEY (`param_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.shift_result
CREATE TABLE IF NOT EXISTS `shift_result` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `tgl_shift` date NOT NULL DEFAULT '0000-00-00',
  `khusus_lembur` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Selain Lembur, 1: Khusus Lembur',
  `khusus_extra` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Selain Extra, 1: Khusus Extra',
  `temp_id_auto` int(11) NOT NULL DEFAULT 0 COMMENT '0: Isian default; Selain 0: Untuk shift auto',
  `jdw_kerja_m_id` int(11) NOT NULL DEFAULT 0,
  `jk_id` int(11) NOT NULL DEFAULT 0,
  `jns_dok` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Jadwal Kerja, 1: Izin, 2: Jam Kerja Extra, 3:Lembur',
  `izin_jenis_id` smallint(6) NOT NULL DEFAULT 0,
  `cuti_n_id` int(11) NOT NULL DEFAULT 0,
  `libur_umum` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'YES/NO',
  `libur_rutin` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'YES/NO',
  `jk_ot` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'YES/NO',
  `scan_in` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `att_id_in` varchar(50) NOT NULL DEFAULT '0',
  `late_permission` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'YES/NO',
  `late_minute` smallint(6) NOT NULL DEFAULT 0,
  `late` float NOT NULL DEFAULT 0,
  `break_out` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `att_id_break1` varchar(50) NOT NULL DEFAULT '0',
  `break_in` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `att_id_break2` varchar(50) NOT NULL DEFAULT '0',
  `break_minute` smallint(6) NOT NULL DEFAULT 0,
  `break` float NOT NULL DEFAULT 0,
  `break_ot_minute` smallint(6) NOT NULL DEFAULT 0,
  `break_ot` float NOT NULL DEFAULT 0,
  `early_permission` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'YES/NO',
  `early_minute` smallint(6) NOT NULL DEFAULT 0,
  `early` float NOT NULL DEFAULT 0,
  `scan_out` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `att_id_out` varchar(50) NOT NULL DEFAULT '0',
  `durasi_minute` smallint(6) NOT NULL DEFAULT 0,
  `durasi` float NOT NULL DEFAULT 0,
  `durasi_eot_minute` smallint(6) NOT NULL DEFAULT 0,
  `jk_count_as` float NOT NULL DEFAULT 0,
  `status_jk` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No',
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pegawai_id`,`tgl_shift`,`khusus_lembur`,`khusus_extra`,`temp_id_auto`),
  KEY `jdw_kerja_m_id` (`jdw_kerja_m_id`),
  KEY `jk_id` (`jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.sms_group
CREATE TABLE IF NOT EXISTS `sms_group` (
  `group_id` int(11) DEFAULT NULL,
  `group_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.sms_group_member
CREATE TABLE IF NOT EXISTS `sms_group_member` (
  `group_id` int(11) DEFAULT NULL,
  `pegawai_pin` varchar(32) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.sms_recipient
CREATE TABLE IF NOT EXISTS `sms_recipient` (
  `nama` varchar(50) DEFAULT NULL,
  `nomor_telp` varchar(20) DEFAULT NULL,
  `pegawai_pin` varchar(32) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0:penerima pribadi; 1:penerima group'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.stock
CREATE TABLE IF NOT EXISTS `stock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_material` varchar(255) NOT NULL,
  `stock_awal` float DEFAULT NULL,
  `stock_masuk` float DEFAULT NULL,
  `stock_keluar` float DEFAULT NULL,
  `price` float DEFAULT NULL,
  `id_currency` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.stock_movements
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(10) unsigned NOT NULL,
  `stock_change` int(11) NOT NULL,
  `movement_date` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.summary_costs
CREATE TABLE IF NOT EXISTS `summary_costs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `total_cost_idr` decimal(15,2) NOT NULL,
  `cost_usd` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `summary_costs_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci DEFAULT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `id_country` int(11) DEFAULT NULL,
  `id_currency` varchar(10) DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.sync_detail
CREATE TABLE IF NOT EXISTS `sync_detail` (
  `pegawai_id` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `jk_id` int(11) NOT NULL,
  `jdw_nama` varchar(100) NOT NULL,
  `jk_nama` varchar(100) NOT NULL,
  `jk_in` time NOT NULL,
  `scan_in` time NOT NULL,
  `jk_out` time NOT NULL,
  `scan_out` time NOT NULL,
  `hadir` tinyint(4) NOT NULL,
  `hadir_mnt` smallint(6) NOT NULL,
  `terlambat` tinyint(4) NOT NULL,
  `terlambat_mnt` smallint(6) NOT NULL,
  `p_awal` tinyint(4) NOT NULL,
  `p_awal_mnt` smallint(6) NOT NULL,
  `ist_mnt` smallint(6) NOT NULL,
  `ist_lebih` tinyint(4) NOT NULL,
  `ist_lebih_mnt` smallint(6) NOT NULL,
  `scan1` tinyint(4) NOT NULL,
  `lembur` tinyint(4) NOT NULL,
  `lembur_mnt` smallint(6) NOT NULL,
  `libur` tinyint(4) NOT NULL,
  `tdk_hadir` tinyint(4) NOT NULL,
  `izin_01` tinyint(4) NOT NULL,
  `izin_02` tinyint(4) NOT NULL,
  `izin_03` tinyint(4) NOT NULL,
  `izin_04` tinyint(4) NOT NULL,
  `izin_05` tinyint(4) NOT NULL,
  `izin_06` tinyint(4) NOT NULL,
  `izin_07` tinyint(4) NOT NULL,
  `izin_08` tinyint(4) NOT NULL,
  `izin_09` tinyint(4) NOT NULL,
  `izin_10` tinyint(4) NOT NULL,
  `izin_11` tinyint(4) NOT NULL,
  `izin_12` tinyint(4) NOT NULL,
  `izin_13` tinyint(4) NOT NULL,
  `izin_14` tinyint(4) NOT NULL,
  `izin_15` tinyint(4) NOT NULL,
  `izin_16` tinyint(4) NOT NULL,
  `izin_17` tinyint(4) NOT NULL,
  `izin_18` tinyint(4) NOT NULL,
  `izin_19` tinyint(4) NOT NULL,
  PRIMARY KEY (`pegawai_id`,`tgl`,`jk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.sync_rekap
CREATE TABLE IF NOT EXISTS `sync_rekap` (
  `pegawai_id` int(11) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `hadir` tinyint(4) NOT NULL,
  `hadir_mnt` smallint(6) NOT NULL,
  `terlambat` tinyint(4) NOT NULL,
  `terlambat_mnt` smallint(6) NOT NULL,
  `p_awal` tinyint(4) NOT NULL,
  `p_awal_mnt` smallint(6) NOT NULL,
  `ist_mnt` smallint(6) NOT NULL,
  `ist_lebih` tinyint(4) NOT NULL,
  `ist_lebih_mnt` smallint(6) NOT NULL,
  `scan1` tinyint(4) NOT NULL,
  `lembur` tinyint(4) NOT NULL,
  `lembur_mnt` smallint(6) NOT NULL,
  `libur` tinyint(4) NOT NULL,
  `tdk_hadir` tinyint(4) NOT NULL,
  `izin_01` tinyint(4) NOT NULL,
  `izin_02` tinyint(4) NOT NULL,
  `izin_03` tinyint(4) NOT NULL,
  `izin_04` tinyint(4) NOT NULL,
  `izin_05` tinyint(4) NOT NULL,
  `izin_06` tinyint(4) NOT NULL,
  `izin_07` tinyint(4) NOT NULL,
  `izin_08` tinyint(4) NOT NULL,
  `izin_09` tinyint(4) NOT NULL,
  `izin_10` tinyint(4) NOT NULL,
  `izin_11` tinyint(4) NOT NULL,
  `izin_12` tinyint(4) NOT NULL,
  `izin_13` tinyint(4) NOT NULL,
  `izin_14` tinyint(4) NOT NULL,
  `izin_15` tinyint(4) NOT NULL,
  `izin_16` tinyint(4) NOT NULL,
  `izin_17` tinyint(4) NOT NULL,
  `izin_18` tinyint(4) NOT NULL,
  `izin_19` tinyint(4) NOT NULL,
  PRIMARY KEY (`pegawai_id`,`bulan`,`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.s_att_log
CREATE TABLE IF NOT EXISTS `s_att_log` (
  `sn` varchar(30) NOT NULL,
  `scan_date` datetime NOT NULL,
  `pin` varchar(32) NOT NULL,
  `verifymode` int(11) NOT NULL,
  `inoutmode` int(11) NOT NULL DEFAULT 0,
  `reserved` int(11) NOT NULL DEFAULT 0,
  `work_code` int(11) NOT NULL DEFAULT 0,
  `att_id` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sn`,`scan_date`,`pin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.s_izin
CREATE TABLE IF NOT EXISTS `s_izin` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `izin_urutan` smallint(6) NOT NULL DEFAULT 0,
  `izin_tgl_pengajuan` date NOT NULL,
  `izin_tgl` date NOT NULL,
  `izin_jenis_id` smallint(6) NOT NULL DEFAULT 0 COMMENT 'Foreign key ke tabel jns_izin',
  `izin_catatan` varchar(255) DEFAULT NULL,
  `izin_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0:tidak di izinkan; 1: diizinkan',
  `izin_tinggal_t1` time DEFAULT NULL,
  `izin_tinggal_t2` time DEFAULT NULL,
  `cuti_n_id` int(11) DEFAULT 0,
  `izin_ket_lain` varchar(100) DEFAULT NULL,
  `izin_noscan_time` time DEFAULT NULL,
  `kat_izin_id` int(11) DEFAULT 0,
  `ket_status` varchar(255) DEFAULT NULL,
  `action` int(11) NOT NULL DEFAULT 1 COMMENT '1:add, 2: edit, 3: delete',
  PRIMARY KEY (`pegawai_id`,`izin_tgl`,`izin_jenis_id`,`izin_status`,`izin_urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.temp_pegawai
CREATE TABLE IF NOT EXISTS `temp_pegawai` (
  `pegawai_id` int(11) NOT NULL DEFAULT 0,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  PRIMARY KEY (`pegawai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.temp_pin
CREATE TABLE IF NOT EXISTS `temp_pin` (
  `pin` varchar(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.tmp
CREATE TABLE IF NOT EXISTS `tmp` (
  `pin` varchar(32) NOT NULL,
  `finger_idx` tinyint(4) NOT NULL,
  `alg_ver` tinyint(4) NOT NULL COMMENT 'ZK : 9&10, Realand : 19, Ebio : 29, HY : 39',
  `template1` text NOT NULL,
  PRIMARY KEY (`pin`,`finger_idx`,`alg_ver`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.tmp_uareu
CREATE TABLE IF NOT EXISTS `tmp_uareu` (
  `pin` varchar(32) NOT NULL,
  `finger_idx` tinyint(4) NOT NULL,
  `alg_ver` tinyint(4) NOT NULL,
  `template1` text NOT NULL,
  PRIMARY KEY (`pin`,`finger_idx`,`alg_ver`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.tukar_jam
CREATE TABLE IF NOT EXISTS `tukar_jam` (
  `tukar_tgl` date NOT NULL,
  `pegawai_id1` int(11) NOT NULL DEFAULT 0,
  `pegawai_id2` int(11) NOT NULL DEFAULT 0,
  `alasan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tukar_tgl`,`pegawai_id1`,`pegawai_id2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.type
CREATE TABLE IF NOT EXISTS `type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) DEFAULT NULL,
  `nama` varchar(200) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.t_iklan
CREATE TABLE IF NOT EXISTS `t_iklan` (
  `login_id` varchar(50) DEFAULT NULL,
  `tgl` date DEFAULT NULL,
  `jml` tinyint(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.t_ordering
CREATE TABLE IF NOT EXISTS `t_ordering` (
  `id_ordering` varchar(50) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `sn_device` varchar(50) NOT NULL,
  `date_ordering` datetime DEFAULT NULL,
  `link_ordering` varchar(225) DEFAULT NULL,
  `status_ordering` int(11) NOT NULL DEFAULT 0,
  `rowguid` text DEFAULT NULL,
  PRIMARY KEY (`id_ordering`,`addon_id`,`sn_device`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.t_syn
CREATE TABLE IF NOT EXISTS `t_syn` (
  `syn_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `syn_date` datetime NOT NULL,
  `syn_action` tinyint(4) NOT NULL COMMENT '0: Add, 1: Edit, 2: Delete',
  `to_table` varchar(100) NOT NULL,
  `syn_data` text NOT NULL,
  `flag` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Belum dikirim, 1: Sudah dikirim, 2: Sudah dikirim tapi error',
  `md5` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`syn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.t_syn_temp
CREATE TABLE IF NOT EXISTS `t_syn_temp` (
  `id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.uareu_device
CREATE TABLE IF NOT EXISTS `uareu_device` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT,
  `uau_device_name` varchar(100) DEFAULT NULL,
  `uau_serial_number` varchar(255) DEFAULT NULL,
  `uau_verification` varchar(255) DEFAULT NULL,
  `uau_activation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for procedure fingerspot.UpdateScan
DELIMITER //
CREATE PROCEDURE `UpdateScan`()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE current_pin VARCHAR(20);
    
    -- Deklarasi cursor untuk mengambil semua pin unik
    DECLARE pin_cursor CURSOR FOR
        SELECT DISTINCT pin FROM att_log;
    
    -- Deklarasi handler untuk menandai akhir dari cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    -- Buka cursor
    OPEN pin_cursor;
    
    -- Looping untuk setiap pin
    pin_loop: LOOP
        -- Ambil pin berikutnya dari cursor
        FETCH pin_cursor INTO current_pin;
        
        -- Keluar dari loop jika tidak ada data lagi
        IF done = 1 THEN
            LEAVE pin_loop;
        END IF;
        
        -- Update scan_date untuk pin saat ini
        UPDATE att_log
        SET scan_date = DATE_ADD(scan_date, INTERVAL 1 DAY)
        WHERE pin = current_pin
          AND sn = '6668601728359'
          AND scan_date BETWEEN '2025-02-26 00:00:00' AND '2025-02-26 08:59:59'
      LIMIT 1;
    END LOOP;
    
    -- Tutup cursor
    CLOSE pin_cursor;
END//
DELIMITER ;

-- Dumping structure for procedure fingerspot.UpdateScanDateForEachPin
DELIMITER //
CREATE PROCEDURE `UpdateScanDateForEachPin`()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE current_pin VARCHAR(20);
    
    -- Deklarasi cursor untuk mengambil semua pin unik
    DECLARE pin_cursor CURSOR FOR
        SELECT DISTINCT pin FROM att_log;
    
    -- Deklarasi handler untuk menandai akhir dari cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    -- Buka cursor
    OPEN pin_cursor;
    
    -- Looping untuk setiap pin
    pin_loop: LOOP
        -- Ambil pin berikutnya dari cursor
        FETCH pin_cursor INTO current_pin;
        
        -- Keluar dari loop jika tidak ada data lagi
        IF done = 1 THEN
            LEAVE pin_loop;
        END IF;
        
        -- Update scan_date untuk pin saat ini
        UPDATE att_log
        SET scan_date = DATE_ADD(scan_date, INTERVAL 1 DAY)
        WHERE pin = current_pin
          AND sn = '6668601728359'
          AND scan_date BETWEEN '2025-02-26 00:00:00' AND '2025-02-26 08:59:59';
    END LOOP;
    
    -- Tutup cursor
    CLOSE pin_cursor;
END//
DELIMITER ;

-- Dumping structure for procedure fingerspot.UpdateScanDateForEachPinBack
DELIMITER //
CREATE PROCEDURE `UpdateScanDateForEachPinBack`()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE current_pin VARCHAR(20);
    
    -- Deklarasi cursor untuk mengambil semua pin unik
    DECLARE pin_cursor CURSOR FOR
        SELECT DISTINCT pin FROM att_log;
    
    -- Deklarasi handler untuk menandai akhir dari cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    -- Buka cursor
    OPEN pin_cursor;
    
    -- Looping untuk setiap pin
    pin_loop: LOOP
        -- Ambil pin berikutnya dari cursor
        FETCH pin_cursor INTO current_pin;
        
        -- Keluar dari loop jika tidak ada data lagi
        IF done = 1 THEN
            LEAVE pin_loop;
        END IF;
        
        -- Update scan_date untuk pin saat ini
        UPDATE att_log
        SET scan_date = DATE_ADD(scan_date, INTERVAL 1 DAY)
        WHERE pin = current_pin
          AND sn = '6668601728359'
          AND scan_date BETWEEN '2025-02-27 00:00:00' AND '2025-02-27 08:59:59'
      LIMIT 1;
    END LOOP;
    
    -- Tutup cursor
    CLOSE pin_cursor;
END//
DELIMITER ;

-- Dumping structure for table fingerspot.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_depan` varchar(200) NOT NULL,
  `nama_belakang` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `level` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.user_log
CREATE TABLE IF NOT EXISTS `user_log` (
  `login_id` varchar(50) NOT NULL,
  `log_date` datetime NOT NULL,
  `module` int(11) NOT NULL COMMENT '0: Pengaturan, 1: Pegawai, 2: Mesin, 3: Pengecualian, 4: Laporan, 5: Proses',
  `tipe_log` tinyint(4) NOT NULL COMMENT '0: Tambah, 1: Ubah, 2: Hapus, 3: Buka Pintu',
  `nama_data` varchar(250) NOT NULL,
  `log_note` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.user_login
CREATE TABLE IF NOT EXISTS `user_login` (
  `login_id` varchar(50) NOT NULL,
  `login_pwd` varchar(32) NOT NULL,
  `grp_user_id` tinyint(4) NOT NULL DEFAULT 1,
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`login_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.versi_db
CREATE TABLE IF NOT EXISTS `versi_db` (
  `no_id` smallint(6) NOT NULL,
  `versi_db` varchar(100) NOT NULL DEFAULT '@',
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`no_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.warehouses
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.zx_bayar_kredit
CREATE TABLE IF NOT EXISTS `zx_bayar_kredit` (
  `id_bayar` int(11) NOT NULL,
  `tgl_bayar` date NOT NULL,
  `id_kredit` int(11) NOT NULL,
  `no_urut` smallint(6) NOT NULL,
  `tgl_jt` date NOT NULL,
  `debet` float NOT NULL,
  `angs_pokok` float NOT NULL,
  `bunga` float NOT NULL,
  `emp_id_auto` int(11) NOT NULL,
  `keterangan` varchar(300) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`id_bayar`),
  KEY `id_kredit` (`id_kredit`),
  KEY `tgl_jt` (`tgl_jt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.zx_jns_krd
CREATE TABLE IF NOT EXISTS `zx_jns_krd` (
  `krd_id` tinyint(4) NOT NULL,
  `krd_kode` varchar(10) NOT NULL,
  `krd_name` varchar(100) NOT NULL,
  `com_id` smallint(6) NOT NULL,
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`krd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.zx_kredit_d
CREATE TABLE IF NOT EXISTS `zx_kredit_d` (
  `id_kredit` int(11) NOT NULL,
  `no_urut` smallint(6) NOT NULL,
  `tgl_jt` date NOT NULL,
  `saldo_aw` float NOT NULL,
  `debet` float NOT NULL,
  `angs_pokok` float NOT NULL,
  `bunga` float NOT NULL,
  `saldo_akh` float NOT NULL,
  `proses_bayar` tinyint(4) NOT NULL DEFAULT 0,
  `keterangan` varchar(300) NOT NULL,
  PRIMARY KEY (`id_kredit`,`no_urut`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.zx_kredit_m
CREATE TABLE IF NOT EXISTS `zx_kredit_m` (
  `id_kredit` int(11) NOT NULL,
  `no_kredit` varchar(100) NOT NULL,
  `tgl_kredit` date NOT NULL,
  `emp_id_auto` int(11) NOT NULL,
  `krd_id` tinyint(4) NOT NULL,
  `cara_hitung` tinyint(4) NOT NULL DEFAULT 0,
  `tot_kredit` float NOT NULL,
  `saldo_aw` float NOT NULL,
  `suku_bunga` double NOT NULL,
  `periode_bulan` smallint(6) NOT NULL,
  `angs_pokok` float NOT NULL,
  `angs_pertama` date NOT NULL,
  `tot_debet` float NOT NULL,
  `tot_angs_pokok` float NOT NULL,
  `tot_bunga` float NOT NULL,
  `def_pembulatan` smallint(6) NOT NULL,
  `jumlah_piutang` float NOT NULL,
  `approv_by` varchar(200) NOT NULL,
  `keterangan` varchar(1000) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `status_lunas` tinyint(4) NOT NULL DEFAULT 0,
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`id_kredit`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_com
CREATE TABLE IF NOT EXISTS `z_pay_com` (
  `com_id` smallint(6) NOT NULL,
  `com_kode` varchar(50) NOT NULL,
  `com_name` varchar(100) NOT NULL,
  `type_com` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Tunjangan, 1: Potongan',
  `fluctuate` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No (Berubah-rubah)',
  `no_urut` smallint(6) NOT NULL,
  `param` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No (Bagian dari formula)',
  `hitung` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Periode, 1: Harian',
  `dibayar` tinyint(4) NOT NULL DEFAULT 2 COMMENT '0: Harian, 1: Mingguan, 2: Bulanan, 3: Tahunan',
  `cara_bayar` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Tunai, 1: Transfer Rekening',
  `pinjaman` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No',
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`com_id`),
  KEY `com_id` (`com_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_grp
CREATE TABLE IF NOT EXISTS `z_pay_grp` (
  `grp_id` smallint(6) NOT NULL,
  `kode_grp` varchar(50) NOT NULL,
  `grp_name` varchar(100) NOT NULL,
  `use_pengurang` tinyint(4) NOT NULL DEFAULT 0,
  `type_pengurang` tinyint(4) NOT NULL DEFAULT 0,
  `pengurang_persen` float NOT NULL DEFAULT 0,
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`grp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_grp_com
CREATE TABLE IF NOT EXISTS `z_pay_grp_com` (
  `grp_id` smallint(6) NOT NULL,
  `com_id` smallint(6) NOT NULL,
  `no_urut_ref` smallint(6) NOT NULL,
  `use_if_sum` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No - (Use kondisi)',
  `use_kode_if` tinyint(4) NOT NULL DEFAULT 0 COMMENT '-1: Tidak pakai, 0: Field laporan, 1: Komponen gaji, 2: Jenis izin, 3: Cuti normatif',
  `id_kode_if` smallint(6) NOT NULL DEFAULT 0 COMMENT 'ID kode kondisi',
  `min_if` float NOT NULL DEFAULT 0,
  `max_if` float NOT NULL DEFAULT 0,
  `use_sum` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Yes/No - (Use rumus)',
  `use_kode_sum` tinyint(4) NOT NULL DEFAULT 0 COMMENT '-1: Tidak pakai, 0: Field laporan, 1: Komponen gaji, 2: Jenis izin, 3: Cuti normatif',
  `id_kode_sum` smallint(6) NOT NULL DEFAULT 0 COMMENT 'ID kode rumus',
  `operator_sum` varchar(50) NOT NULL DEFAULT '0' COMMENT '0: *, 1: /, 2: -, 3: +, 4: Tanpa Konstanta',
  `konstanta_sum` float NOT NULL DEFAULT 0,
  `operator_sum2` varchar(50) NOT NULL DEFAULT '0' COMMENT '0: *, 1: /, 2: -, 3: +, 4: Tidak pakai',
  `nilai_rp` float NOT NULL DEFAULT 0,
  `hitung` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Periode, 1: Perhari',
  `jenis` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Normal, 1: Bertingkat, 2: Menggantikan',
  PRIMARY KEY (`com_id`,`grp_id`,`no_urut_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_grp_emp
CREATE TABLE IF NOT EXISTS `z_pay_grp_emp` (
  `grp_id` smallint(6) NOT NULL,
  `emp_id_auto` int(11) NOT NULL,
  `no_rek` varchar(50) NOT NULL,
  PRIMARY KEY (`grp_id`,`emp_id_auto`),
  KEY `grp_id` (`grp_id`),
  KEY `emp_id_auto` (`emp_id_auto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_money
CREATE TABLE IF NOT EXISTS `z_pay_money` (
  `com_id` smallint(6) NOT NULL,
  `grp_id` smallint(6) NOT NULL,
  `emp_id_auto` int(11) NOT NULL,
  `nilai_rp` float NOT NULL,
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`com_id`,`grp_id`,`emp_id_auto`),
  KEY `com_id` (`com_id`),
  KEY `emp_id_auto` (`emp_id_auto`),
  KEY `grp_id` (`grp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_process_d
CREATE TABLE IF NOT EXISTS `z_pay_process_d` (
  `process_id` int(11) NOT NULL,
  `no_urut` smallint(6) NOT NULL,
  `emp_id_auto` int(11) NOT NULL,
  `tot_payroll` float NOT NULL,
  PRIMARY KEY (`process_id`,`no_urut`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_process_m
CREATE TABLE IF NOT EXISTS `z_pay_process_m` (
  `process_id` int(11) NOT NULL,
  `process_name` varchar(250) NOT NULL,
  `date1` date NOT NULL,
  `date2` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `round_value` float NOT NULL,
  `tot_process` float NOT NULL,
  `create_by` varchar(100) DEFAULT NULL,
  `check_by` varchar(100) DEFAULT NULL,
  `approve_by` varchar(100) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `lastupdate_date` datetime NOT NULL,
  `lastupdate_user` varchar(50) NOT NULL,
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_process_sd
CREATE TABLE IF NOT EXISTS `z_pay_process_sd` (
  `process_id` int(11) NOT NULL,
  `no_urut` smallint(6) NOT NULL,
  `no_urut_ref` smallint(6) NOT NULL,
  `emp_id_auto` int(11) NOT NULL,
  `com_id` smallint(6) NOT NULL,
  `kondisi` varchar(100) NOT NULL DEFAULT '0',
  `rumus` varchar(100) NOT NULL DEFAULT '0',
  `subtot_payroll` float NOT NULL,
  `jml_faktor` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`process_id`,`no_urut`,`no_urut_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table fingerspot.z_pay_report
CREATE TABLE IF NOT EXISTS `z_pay_report` (
  `id_kode_report` tinyint(4) NOT NULL,
  `report_code` varchar(50) NOT NULL,
  `report_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id_kode_report`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
