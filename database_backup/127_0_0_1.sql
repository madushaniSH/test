-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2019 at 02:10 PM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `query` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_type` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_length` text COLLATE utf8_bin DEFAULT NULL,
  `col_collation` varchar(64) COLLATE utf8_bin NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) COLLATE utf8_bin DEFAULT '',
  `col_default` text COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `column_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `transformation` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `transformation_options` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `input_transformation` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `settings_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

--
-- Dumping data for table `pma__designer_settings`
--

INSERT INTO `pma__designer_settings` (`username`, `settings_data`) VALUES
('root', '{\"angular_direct\":\"direct\",\"snap_to_grid\":\"off\",\"relation_lines\":\"true\",\"small_big_all\":\">\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `export_type` varchar(10) COLLATE utf8_bin NOT NULL,
  `template_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `template_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `tables` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `item_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `item_type` varchar(64) COLLATE utf8_bin NOT NULL,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `tables` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"test_db\",\"table\":\"brand\"},{\"db\":\"test_db\",\"table\":\"manufacturer\"},{\"db\":\"test_db\",\"table\":\"client_sub_category\"},{\"db\":\"test_db\",\"table\":\"client_category\"},{\"db\":\"test_db\",\"table\":\"product\"},{\"db\":\"test_db\",\"table\":\"attribute\"},{\"db\":\"test_db\",\"table\":\"product_attribute\"},{\"db\":\"test_db\",\"table\":\"product_container_type\"},{\"db\":\"test_db\",\"table\":\"product_measurement_unit\"},{\"db\":\"user_db\",\"table\":\"accounts\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `master_table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `master_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `search_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `search_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `display_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

--
-- Dumping data for table `pma__table_info`
--

INSERT INTO `pma__table_info` (`db_name`, `table_name`, `display_field`) VALUES
('test_db', 'brand', 'brand_name'),
('test_db', 'product', 'product_name'),
('test_db', 'product_image', 'product_image_location'),
('test_db', 'sub_brand', 'sub_brand_name');

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `prefs` text COLLATE utf8_bin NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text COLLATE utf8_bin NOT NULL,
  `schema_sql` text COLLATE utf8_bin DEFAULT NULL,
  `data_sql` longtext COLLATE utf8_bin DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') COLLATE utf8_bin DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2019-07-26 12:08:43', '{\"Console\\/Mode\":\"collapse\",\"ThemeDefault\":\"fallen\",\"FontSize\":\"75%\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) COLLATE utf8_bin NOT NULL,
  `tab` varchar(64) COLLATE utf8_bin NOT NULL,
  `allowed` enum('Y','N') COLLATE utf8_bin NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `usergroup` varchar(64) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `project_db`
--
CREATE DATABASE IF NOT EXISTS `project_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `project_db`;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_db_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `project_region`, `project_db_name`) VALUES
(1, 'test', 'test_region', 'test_db');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
--
-- Database: `test_db`
--
CREATE DATABASE IF NOT EXISTS `test_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `test_db`;

-- --------------------------------------------------------

--
-- Table structure for table `attribute`
--

CREATE TABLE `attribute` (
  `attribute_id` int(11) NOT NULL,
  `attribute_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `manufacturer_id`, `brand_name`, `brand_source`, `brand_image_location`) VALUES
(1, 1, 'General', NULL, 'images\\default\\system\\product\\default.jpg'),
(2, 2, '10 Barrel', NULL, 'images\\default\\system\\product\\default.jpg'),
(3, 4, 'Starbucks', NULL, 'images\\default\\system\\product\\default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `client_category`
--

CREATE TABLE `client_category` (
  `client_category_id` int(11) NOT NULL,
  `client_category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_category_local_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_category`
--

INSERT INTO `client_category` (`client_category_id`, `client_category_name`, `client_category_local_name`) VALUES
(1, 'Coffee', ''),
(2, 'Beer', ''),
(3, 'Pet Food', ''),
(4, 'Bacon', ''),
(5, 'Empty', ''),
(6, 'General', ''),
(12, 'Juice', 'Juice'),
(13, 'Energy', NULL),
(14, 'Kids', 'Kids'),
(15, 'Baby Food', 'Baby Food');

-- --------------------------------------------------------

--
-- Table structure for table `client_sub_category`
--

CREATE TABLE `client_sub_category` (
  `client_sub_category_id` int(11) NOT NULL,
  `client_sub_category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_sub_category_local_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_sub_category`
--

INSERT INTO `client_sub_category` (`client_sub_category_id`, `client_sub_category_name`, `client_sub_category_local_name`) VALUES
(1, 'Cup', NULL),
(3, 'Bowl', 'Bowl');

-- --------------------------------------------------------

--
-- Table structure for table `manufacturer`
--

CREATE TABLE `manufacturer` (
  `manufacturer_id` int(11) NOT NULL,
  `manufacturer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manufacturer_local_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manufacturer`
--

INSERT INTO `manufacturer` (`manufacturer_id`, `manufacturer_name`, `manufacturer_local_name`, `manufacturer_source`, `manufacturer_image_location`) VALUES
(1, 'General', NULL, NULL, 'images\\default\\system\\product\\default.jpg'),
(2, 'Anheuser Busch Inc', NULL, 'https://10barrel.com/', 'images\\default\\system\\product\\default.jpg'),
(4, 'Nestle Holdings Inc', NULL, NULL, 'images\\default\\system\\product\\default.jpg'),
(19, 'Elite', 'Elite', 'google.com', 'images/system/projects/test_db/manufacturer/images/Elite Chocolate Other.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_item_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` int(11) NOT NULL,
  `client_category_id` int(11) NOT NULL,
  `product_is_sub_brand` tinyint(1) NOT NULL DEFAULT 0,
  `product_global_status` enum('Pending Approval','Active','Inactive','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending Approval',
  `product_image_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_item_code`, `brand_id`, `client_category_id`, `product_is_sub_brand`, `product_global_status`, `product_image_id`) VALUES
(1, 'General Bacon Other', NULL, 1, 4, 0, 'Pending Approval', 1),
(2, 'General Bacon Empty', NULL, 1, 4, 0, 'Inactive', 1),
(3, '10 Barrel Beer Other', NULL, 2, 2, 0, 'Active', 2),
(4, '10 Barrel Beer Apocalypse India Pale Ale Can 12 fl oz', NULL, 2, 2, 0, 'Active', 3),
(8, '10 Barrel Beer Pub Lager Can 12 fl oz', NULL, 2, 2, 0, 'Active', 4),
(9, '10 Barrel Beer Cucumber Sour Crush Can 12 fl oz', NULL, 2, 2, 0, 'Active', 5),
(10, '10 Barrel Beer Raspberry Sour Crush Ale Can 12 fl oz', '00014215', 2, 2, 0, 'Active', 6),
(11, 'Starbucks Coffee Other', NULL, 3, 1, 0, 'Active', 7),
(12, 'Starbucks Coffee Empty', NULL, 3, 1, 0, 'Inactive', 7);

-- --------------------------------------------------------

--
-- Table structure for table `product_attribute`
--

CREATE TABLE `product_attribute` (
  `product_attribute_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_container_type`
--

CREATE TABLE `product_container_type` (
  `product_container_type_id` int(11) NOT NULL,
  `product_container_type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_container_type`
--

INSERT INTO `product_container_type` (`product_container_type_id`, `product_container_type_name`) VALUES
(1, 'VAP'),
(2, 'aluminum'),
(3, 'aluminum keg'),
(4, 'bag plastic'),
(5, 'bottle'),
(6, 'bottle can'),
(7, 'box round'),
(8, 'can'),
(9, 'capsule'),
(10, 'cardboard box'),
(11, 'carton'),
(12, 'carton pack'),
(13, 'case'),
(14, 'coffee bag');

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

CREATE TABLE `product_image` (
  `product_image_id` int(11) NOT NULL,
  `product_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`product_image_id`, `product_image_location`) VALUES
(1, 'images\\default\\system\\product\\default.jpg'),
(2, 'images\\default\\system\\product\\10 Barrel Beer Other.jpg'),
(3, 'images\\default\\system\\product\\10 Barrel Beer Apocalypse India Pale Ale Can 12 fl oz.jpg'),
(4, 'images\\default\\system\\product\\10 Barrel Beer Pub Lager Can 12 fl oz.jpg'),
(5, 'images\\default\\system\\product\\10 Barrel Beer Cucumber Sour Crush Can 12 fl oz.jpg'),
(6, 'images\\default\\system\\product\\10 Barrel Beer Raspberry Sour Crush Ale Can 12 fl oz.jpg'),
(7, 'images\\default\\system\\product\\Starbucks Coffee Other.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_measurement_unit`
--

CREATE TABLE `product_measurement_unit` (
  `product_measurement_unit_id` int(11) NOT NULL,
  `product_measurement_unit_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_measurement_unit`
--

INSERT INTO `product_measurement_unit` (`product_measurement_unit_id`, `product_measurement_unit_name`) VALUES
(1, 'cl'),
(2, 'fl oz'),
(3, 'g'),
(4, 'gal'),
(5, 'kg'),
(6, 'l'),
(7, 'lb'),
(8, 'm'),
(9, 'mg'),
(10, 'ml'),
(11, 'oz');

-- --------------------------------------------------------

--
-- Table structure for table `project_trax_category`
--

CREATE TABLE `project_trax_category` (
  `project_trax_category_id` int(11) NOT NULL,
  `project_trax_category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_trax_category`
--

INSERT INTO `project_trax_category` (`project_trax_category_id`, `project_trax_category_name`) VALUES
(1, 'APDO'),
(2, 'Adult Diaper'),
(3, 'Air Fragrances Sticks'),
(4, 'Baby Care'),
(5, 'Baby Wipes'),
(6, 'Beer & Cider'),
(7, 'Beer Taps'),
(8, 'Cat Food'),
(9, 'Cheese'),
(10, 'Dairy'),
(11, 'Dog Food'),
(12, 'Face Care'),
(13, 'Fiber'),
(14, 'General'),
(15, 'Juice'),
(16, 'Milk Powder'),
(17, 'Mobile phones'),
(18, 'Tea'),
(19, 'Toothbrush'),
(20, 'Water'),
(21, 'Wine');

-- --------------------------------------------------------

--
-- Table structure for table `sub_brand`
--

CREATE TABLE `sub_brand` (
  `sub_brand_id` int(11) NOT NULL,
  `sub_brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_brand_source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_brand_image_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'images\\default\\system\\product\\default.jpg',
  `brand_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attribute`
--
ALTER TABLE `attribute`
  ADD PRIMARY KEY (`attribute_id`),
  ADD UNIQUE KEY `attribute_name` (`attribute_name`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD KEY `MANUFACTURER_ID` (`manufacturer_id`);

--
-- Indexes for table `client_category`
--
ALTER TABLE `client_category`
  ADD PRIMARY KEY (`client_category_id`);

--
-- Indexes for table `client_sub_category`
--
ALTER TABLE `client_sub_category`
  ADD PRIMARY KEY (`client_sub_category_id`);

--
-- Indexes for table `manufacturer`
--
ALTER TABLE `manufacturer`
  ADD PRIMARY KEY (`manufacturer_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_name` (`product_name`),
  ADD UNIQUE KEY `product_item_code` (`product_item_code`),
  ADD KEY `BRAND_ID` (`brand_id`),
  ADD KEY `CLIENT_CATEGORY_ID` (`client_category_id`),
  ADD KEY `PRODUCT_IMAGE_ID` (`product_image_id`);

--
-- Indexes for table `product_attribute`
--
ALTER TABLE `product_attribute`
  ADD PRIMARY KEY (`product_attribute_id`),
  ADD KEY `ATTRIBUTE_ID` (`attribute_id`),
  ADD KEY `ATTRIBUTE_PRODUCT_ID` (`product_id`);

--
-- Indexes for table `product_container_type`
--
ALTER TABLE `product_container_type`
  ADD PRIMARY KEY (`product_container_type_id`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`product_image_id`);

--
-- Indexes for table `product_measurement_unit`
--
ALTER TABLE `product_measurement_unit`
  ADD PRIMARY KEY (`product_measurement_unit_id`);

--
-- Indexes for table `project_trax_category`
--
ALTER TABLE `project_trax_category`
  ADD PRIMARY KEY (`project_trax_category_id`);

--
-- Indexes for table `sub_brand`
--
ALTER TABLE `sub_brand`
  ADD PRIMARY KEY (`sub_brand_id`),
  ADD KEY `BRAND_ID_FOR_SUB_BRAND` (`brand_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `client_category`
--
ALTER TABLE `client_category`
  MODIFY `client_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `client_sub_category`
--
ALTER TABLE `client_sub_category`
  MODIFY `client_sub_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `manufacturer`
--
ALTER TABLE `manufacturer`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_attribute`
--
ALTER TABLE `product_attribute`
  MODIFY `product_attribute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_container_type`
--
ALTER TABLE `product_container_type`
  MODIFY `product_container_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
  MODIFY `product_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_measurement_unit`
--
ALTER TABLE `product_measurement_unit`
  MODIFY `product_measurement_unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `project_trax_category`
--
ALTER TABLE `project_trax_category`
  MODIFY `project_trax_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sub_brand`
--
ALTER TABLE `sub_brand`
  MODIFY `sub_brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brand`
--
ALTER TABLE `brand`
  ADD CONSTRAINT `MANUFACTURER_ID` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `BRAND_ID` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`),
  ADD CONSTRAINT `CLIENT_CATEGORY_ID` FOREIGN KEY (`client_category_id`) REFERENCES `client_category` (`client_category_id`),
  ADD CONSTRAINT `PRODUCT_IMAGE_ID` FOREIGN KEY (`product_image_id`) REFERENCES `product_image` (`product_image_id`);

--
-- Constraints for table `product_attribute`
--
ALTER TABLE `product_attribute`
  ADD CONSTRAINT `ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`attribute_id`),
  ADD CONSTRAINT `ATTRIBUTE_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `sub_brand`
--
ALTER TABLE `sub_brand`
  ADD CONSTRAINT `BRAND_ID_FOR_SUB_BRAND` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`);
--
-- Database: `user_db`
--
CREATE DATABASE IF NOT EXISTS `user_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `user_db`;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `account_gid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_nic` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_latest_login_date_time` datetime NOT NULL,
  `account_current_active` tinyint(1) NOT NULL DEFAULT 0,
  `account_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_password_reset_request` tinyint(1) NOT NULL DEFAULT 0,
  `account_password_reset_identity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_dob` date DEFAULT NULL,
  `account_user_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_home_contact_no` int(15) DEFAULT NULL,
  `account_contact_no` int(15) DEFAULT NULL,
  `account_join_date` date NOT NULL DEFAULT current_timestamp(),
  `account_personal_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_profile_picture_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\avatar\\default-avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_gid`, `account_nic`, `account_email`, `account_latest_login_date_time`, `account_current_active`, `account_password`, `account_password_reset_request`, `account_password_reset_identity`, `account_first_name`, `account_last_name`, `account_dob`, `account_user_address`, `account_permanent_address`, `account_home_contact_no`, `account_contact_no`, `account_join_date`, `account_personal_email`, `account_profile_picture_location`) VALUES
(1, 'G1485', 'N1485', 'g14863.malika@gmail.com', '2019-07-26 12:53:18', 0, '$2y$10$1oASi9Gdgns.3SMQPK7S7ehhIIVRt1eg3Nbix2H3cBVs42c6rNTxK', 0, NULL, 'Mason', 'Jason', '1999-08-30', 'No 54 / 5 Streety Street, Towny Town', NULL, 112254512, 0, '2019-07-17', NULL, 'images/user/1/uploads/profile/c4d.gif'),
(2, 'G1234', 'N1234', 'g1234.lana@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$u2BrR2YAKyJ6iVrSGUEjcuBKi9LN/gAtBnsdAH5p1TKLUBg8MRyI2', 0, NULL, 'Lana', 'Banana', '1999-09-29', 'No 1234 Red Street, Blue Town', NULL, 10012151, 0, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(3, 'G4567', 'N4567', 'g4567.karl@gmail.com', '2019-07-26 12:53:45', 1, '$2y$10$5Fyos2jbesiltKrjfMsT8exsVz.e/fTZOlGEnD8t8S1ie41l9eC/m', 0, NULL, 'Karl', 'Nein', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/3/uploads/profile/104319.jpg'),
(4, 'G9999', 'N9999', 'g999.flora@gmail.com', '2019-07-25 20:07:19', 0, '$2y$10$ewg16ZSYdtp3ZHsHiwH.C.bAJJgGFhuoI9yaMcdjeqVTFs/vqKPQm', 0, NULL, 'Flora', 'Fauna', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/4/uploads/profile/Flora_content_img.jpg'),
(5, 'G9809', 'N9809', 'g9809@gmail.com', '2019-07-19 18:15:44', 0, '$2y$10$rVYx7G7MZUYf.25bI/z0TeBp81z/1Tw8E1T.Y6WCsRLdUI0MWvv2a', 0, NULL, 'Trebl', 'Bass', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images/user/5/uploads/profile/61wcEoXVcuL._SX425_.jpg'),
(6, 'G2312', 'N2312', 'g2312.mary@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$cpWkESBcOFk8KE41UdCxI.Dr4NRChat1AN5STdbCBM4gOcmbeQOiS', 0, NULL, 'Mary', 'Sad', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(7, 'G0001', 'N0001', 'g0001.ping@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$KWpkrGfQsN62VxoVmDTLeO2Ja028fjD./nF.vcvhwhVJFqm8ymdCK', 0, NULL, 'Ping', 'Pong', NULL, NULL, NULL, NULL, NULL, '2019-07-17', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(8, 'G7891', 'N7891', 'g7891.thunder@gmail.com', '0000-00-00 00:00:00', 0, '$2y$10$6T9EQnrdlKZqpRz/svXtA.XjaA.ud9TMsx.wdtLUDTm24HFmmQrle', 0, NULL, 'Thunder', 'Cracker', NULL, NULL, NULL, NULL, NULL, '2019-07-18', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(9, 'G5555', 'N5555', 'g5555.proper@gmail.com', '2019-07-18 16:14:12', 0, '$2y$10$4mluEnkPFjuTnV7M5WBCAuQJguH5z4uJfQ2VfmGmlepk8WfXbwO5e', 0, NULL, 'Proper', 'Test', NULL, NULL, NULL, NULL, NULL, '2019-07-18', NULL, 'images\\default\\system\\avatar\\default-avatar.jpg'),
(10, 'G8090', 'N8090', 'g8090.ginger@gmail.com', '2019-07-19 19:31:18', 0, '$2y$10$uoM.QzuKXQQAbPdCXs4vyurrZ4WuArRL46r9Yb2zR6xKWuyaUJ6kW', 0, NULL, 'Ginger', 'Barl', NULL, NULL, NULL, NULL, NULL, '2019-07-19', NULL, 'images/user/10/uploads/profile/giphy (1).gif');

-- --------------------------------------------------------

--
-- Table structure for table `account_designations`
--

CREATE TABLE `account_designations` (
  `account_designation_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_designations`
--

INSERT INTO `account_designations` (`account_designation_id`, `account_id`, `designation_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 4),
(7, 7, 3),
(8, 8, 3),
(9, 9, 5),
(10, 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `designation_id` int(11) NOT NULL,
  `designation_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`designation_id`, `designation_name`) VALUES
(1, 'Admin'),
(2, 'Supervisor'),
(3, 'SRT'),
(4, 'SRT Analyst'),
(5, 'ODA');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `account_designations`
--
ALTER TABLE `account_designations`
  ADD PRIMARY KEY (`account_designation_id`),
  ADD KEY `ACCOUNT_ID` (`account_id`),
  ADD KEY `DESIGNATION_ID` (`designation_id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`designation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `account_designations`
--
ALTER TABLE `account_designations`
  MODIFY `account_designation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `designation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_designations`
--
ALTER TABLE `account_designations`
  ADD CONSTRAINT `ACCOUNT_ID` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  ADD CONSTRAINT `DESIGNATION_ID` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`designation_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
