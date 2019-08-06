-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 06, 2019 at 05:14 PM
-- Server version: 5.7.27-0ubuntu0.18.04.1
-- PHP Version: 7.2.19-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attribute`
--

CREATE TABLE `attribute` (
  `attribute_id` int(11) NOT NULL,
  `attribute_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute`
--

INSERT INTO `attribute` (`attribute_id`, `attribute_name`) VALUES
(1, 'Flavor'),
(2, 'att1'),
(3, 'att2'),
(4, 'sub_brand'),
(5, 'alt_code_1'),
(6, 'alt_code_2'),
(7, 'Sub Brand'),
(8, 'color'),
(9, 'core_product_label'),
(10, 'sugar_level'),
(16, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_local_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg',
  `brand_recognition_level` enum('brand','product') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brand',
  `brand_global_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `manufacturer_id`, `brand_name`, `brand_local_name`, `brand_source`, `brand_image_location`, `brand_recognition_level`, `brand_global_code`) VALUES
(1, 1, 'General', NULL, NULL, 'images\\default\\system\\product\\default.jpg', 'brand', NULL),
(2, 2, '10 Barrel', NULL, NULL, 'images\\default\\system\\product\\default.jpg', 'brand', NULL),
(3, 4, 'Starbucks', NULL, NULL, 'images\\default\\system\\product\\default.jpg', 'brand', NULL),
(5, 29, 'Carlsberg', 'Carlsberg', 'google.com', 'images/system/projects/test_db/brand/images/Carlsberg Beer Other.jpg', 'product', '1455'),
(8, 121, 'Coconut Dreams', 'Coconut Dreams', 'google.com', 'images/system/projects/test_db/brand/images/Coconut Dream.JPG', 'brand', NULL),
(12, 125, 'Lipton', 'Lipton', 'trax.com', 'images/system/projects/test_db/brand/images/large.jpg', 'product', NULL),
(13, 127, 'LABATT 50', 'LABATT 50', 'aaaa', 'images/system/projects/test_db/brand/images/Al Capone Empty.jpg', 'product', NULL),
(14, 128, 'Wegmans', 'Wegmans', 'https://www.wegmans.com/content/dam/wegmans/images/about/logos/wegmans-logo-2008-v2.png', 'images/system/projects/test_db/brand/images/Wegmans Dairy Other.jpg', 'brand', NULL),
(15, 129, 'Dannon', 'Dannon', 'https://www.google.com/search?q=Dannon&source=lnms&tbm=isch&sa=X&ved=0ahUKEwjs7_fYh-7jAhXw7XMBHQtdCzcQ_AUIESgB&biw=1920&bih=937', 'images/system/projects/test_db/brand/images/large.jpg', 'brand', NULL),
(16, 130, 'Kroger', 'Kroger', 'Kroger', 'images/system/projects/test_db/brand/images/Kroger.JPG', 'brand', NULL),
(17, 133, 'Shurfine', 'Shurfine', 'gdf', 'images/system/projects/test_db/brand/images/Shurfine.jpg', 'brand', NULL),
(18, 132, 'Simple Truth', 'Simple Truth', '####', 'images/system/projects/test_db/brand/images/Simple Truth Other.jpg', 'brand', '###'),
(19, 132, 'Fred Meyer', 'Fred Meyer', 'aaa', 'images/system/projects/test_db/brand/images/FRED MEYER.jpg', 'product', NULL),
(20, 134, 'Nostimo', 'Nostimo', 'derhtryktulutiyyi;y', 'images/system/projects/test_db/brand/images/Nostimo.JPG', 'brand', NULL),
(21, 136, 'LUDWIG DAIRY', 'LUDWIG DAIRY', 'LUDWIG DAIRY INC', 'images/system/projects/test_db/brand/images/Annie\'s Homegrown Organic Chewy Granola Bars Chocolate Chip Cardboard Box 6 Units 5.34 oz.jpg', 'brand', 'LUDWIG DAIRY INC'),
(22, 135, 'Spartan', 'Spartan', 'sgfhg', 'images/system/projects/test_db/brand/images/Spartan.JPG', 'brand', NULL),
(23, 138, 'Foodtown', 'Foodtown', 'jnlvcnf', 'images/system/projects/test_db/brand/images/Allegiance Retail Services Llc.jpg', 'brand', NULL),
(24, 137, 'OPEN NATURE', 'OPEN NATURE', 'https://cdn-a.william-reed.com/var/wrbm_gb_food_pharma/storage/images/7/0/2/5/935207-3-eng-GB/Safeway-hit-with-false-advertising-lawsuit-over-using-synthetic-ingredients-in-100-natural-products_wrbm_large.jpg', 'images/system/projects/test_db/brand/images/OPEN NATURE.jpg', 'brand', NULL),
(25, 133, 'Stonyfield', 'Stonyfield', 'fas', 'images/system/projects/test_db/brand/images/Stonyfield Organic.jpg', 'brand', 'ss'),
(26, 1, 'Test', 'Tret', 'https://www.google.com/search?biw=1920&bih=888&tbm=isch&sa=1&ei=YlpJXavOJ5_Ez7sP2LW68Aw&q=036632032485&oq=036632032485&gs_l=img.3...995080.995080..995455...0.0..0.158.158.0j1......0....2j1..gws-wiz-img.xCd8EQvbUbQ&ved=&uact=5#imgrc=oZLlCS2QpS7NJM:', 'images/system/projects/test_db/brand/images/Fred Meyer Yogurt Peach Fruit On The Bottom Lowfat Cup 6 oz.jpg', 'product', NULL),
(27, 139, 'Tillamook', 'Tillamook', 'https://en.wikipedia.org/wiki/Tillamook_County_Creamery_Association', 'images/system/projects/test_db/brand/images/3088.jfif', 'brand', NULL),
(28, 140, 'HEB', 'HEB', 'https://services.traxretail.com/images/traxus/gmius/Brands/391/20181204073523/large', 'images/system/projects/test_db/brand/images/HEB.jpg', 'brand', NULL),
(29, 141, 'J & J', 'J & J', 'Ref', 'images/system/projects/test_db/brand/images/Nostimo Greek Yogurt Nonfat Vanilla Container Plastic 32 oz.jpg', 'product', NULL);

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
(15, 'Baby Food', 'Baby Food'),
(20, 'Soft Drinks', NULL),
(21, 'Tea', NULL),
(23, 'Dog Food', NULL),
(24, 'Diary', 'Diary'),
(25, 'Dairy', 'Dairy'),
(26, 'CUPS', 'CUPS');

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
(3, 'Bowl', 'Bowl'),
(5, 'Basin', NULL),
(6, 'Beaker', NULL),
(7, 'BEER', 'BEER'),
(8, 'Diary', 'Diary'),
(9, 'Bottle', 'Bottle'),
(10, 'CUPS', 'CUPS'),
(11, 'TUB', 'TUB');

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
(22, 'Elite', 'Elite', 'Elite', 'images/system/projects/test_db/manufacturer/images/Elite Chocolate Other.JPG'),
(23, 'Silver Queen', 'Silver Queen', 'google.com', 'images/system/projects/test_db/manufacturer/images/Silver Queen.JPG'),
(24, 'Landgarten Chocolate', 'Landgarten Chocolate', 'google.com', 'images/system/projects/test_db/manufacturer/images/Landgarten Chocolate Other.JPG'),
(25, 'Lacasitos', 'Lacasitos', 'google.com', 'images/system/projects/test_db/manufacturer/images/Lacasitos Chocolate Other.JPG'),
(29, 'Carlsberg A/s', 'Carlsberg A/S', 'google.com', 'images/system/projects/test_db/manufacturer/images/Carlsberg Group.jpg'),
(30, 'Best Yet', 'Best Yet', 'google.com', 'images/system/projects/test_db/manufacturer/images/Best Yet.jpg'),
(31, 'Best Choice', 'Best Choice', 'google.com', 'images/system/projects/test_db/manufacturer/images/Best Choice.jpg'),
(32, 'Crystal Other', 'Crystal Other', 'google.com', 'images/system/projects/test_db/manufacturer/images/Crystal Other.jpg'),
(33, 'Belfonte', 'Belfonte', 'google.com', 'images/system/projects/test_db/manufacturer/images/Belfonte.jpg'),
(34, 'Darigold', 'Darigold', 'google.com', 'images/system/projects/test_db/manufacturer/images/Darigold.JPG'),
(35, 'Driftwood', 'Driftwood', 'google.com', 'images/system/projects/test_db/manufacturer/images/Driftwood Other.JPG'),
(36, 'Alpina', 'Alpina', 'Alpina', 'images/system/projects/test_db/manufacturer/images/Alpina.jpg'),
(37, 'Abali', 'Abali', 'google.com', 'images/system/projects/test_db/manufacturer/images/Abali.jpg'),
(38, 'Hermosa Farm', 'Hermosa Farm', 'google.com', 'images/system/projects/test_db/manufacturer/images/aaaaa.JPG'),
(39, 'Agri Mark', 'Agri Mark', 'google.com', 'images/system/projects/test_db/manufacturer/images/Agri-Mark_Family_Dairy_Farms_2100x685_300_RGB-2.jpg'),
(40, 'Alta Dena', 'Alta Dena', 'google.com', 'images/system/projects/test_db/manufacturer/images/alta dena.JPG'),
(41, 'Topco', 'Topco', 'google.com', 'images/system/projects/test_db/manufacturer/images/ffff.jpg'),
(42, 'Amande', 'Amande', 'google.com', 'images/system/projects/test_db/manufacturer/images/AMANDE.jpg'),
(43, 'Producers', 'Producers', 'google.com', 'images/system/projects/test_db/manufacturer/images/11.JPG'),
(44, 'El Mexicano', 'El Mexicaon', 'google.com', 'images/system/projects/test_db/manufacturer/images/El Mexicano.jpg'),
(45, 'Latta', 'Latta', 'google.com', 'images/system/projects/test_db/manufacturer/images/1.JPG'),
(46, 'Naturlich', 'Naturlich', 'google.com', 'images/system/projects/test_db/manufacturer/images/2.jpg'),
(57, 'Deep', 'Deep', 'google.com', 'images/system/projects/test_db/manufacturer/images/DEEP.jpg'),
(58, 'Market Basket', 'Market Basket', 'yahoo', 'images/system/projects/test_db/manufacturer/images/Market-Basket-Logo-12.jpg'),
(59, 'Key Food Fresh', 'Key Food Fresh', 'bing', 'images/system/projects/test_db/manufacturer/images/KEY FOOD.jfif'),
(60, 'Red Top', 'Red Top', 'google.com', 'images/system/projects/test_db/manufacturer/images/RED TOP.jpg'),
(61, 'Lowes', 'Lowes', 'google.com', 'images/system/projects/test_db/manufacturer/images/Lowes Foods.jpg'),
(62, 'Kalona', 'Kalona', 'google.com', 'images/system/projects/test_db/manufacturer/images/KALONA SUPER NATURAL.jfif'),
(63, 'J And J', 'J And J', 'duck', 'images/system/projects/test_db/manufacturer/images/J&B.jpg'),
(64, 'Landgarten', 'Landgarten', 'yahoo.com', 'images/system/projects/test_db/manufacturer/images/Landgarten Chocolate Other.JPG'),
(119, 'Onex', 'Onex', 'google.com', 'images/system/projects/test_db/manufacturer/images/onexlogo_1024xx1258-709-0-0.jpg'),
(121, 'Full Circle', 'Full Circle', 'google.com', 'images/system/projects/test_db/manufacturer/images/Full Circle.jpg'),
(125, 'AMBEV', 'AMBEV', 'trax', 'images/system/projects/test_db/manufacturer/images/large.jpg'),
(127, 'ABI', 'ABI', 'ABI', 'images/system/projects/test_db/manufacturer/images/Bliss Empty.jpg'),
(128, 'WEGMANS FOOD MARKETS INC.', 'WEGMANS FOOD MARKETS INC.', 'https://www.google.com/search?q=wegmans+brand+logo&rlz=1C1CHBF_enLK821LK821&tbm=isch&source=lnt&tbs=isz:lt,islt:vga&sa=X&ved=0ahUKEwjRz772hu7jAhWQ7nMBHRADChwQpwUIIQ&biw=1920&bih=937&dpr=1#imgrc=vwBo8rXuRj8lIM:', 'images/system/projects/test_db/manufacturer/images/Wegmans Dairy Other.jpg'),
(129, 'DANONE WAVE', 'DANONE WAVE', 'https://www.google.com/search?q=Dannon&source=lnms&tbm=isch&sa=X&ved=0ahUKEwjs7_fYh-7jAhXw7XMBHQtdCzcQ_AUIESgB&biw=1920&bih=937', 'images/system/projects/test_db/manufacturer/images/large.jpg'),
(130, 'Kroger Co The', 'Kroger Co The', 'Kroger Co The', 'images/system/projects/test_db/manufacturer/images/Kroger.JPG'),
(131, 'TOPCO ASSOCIATES LLC', 'TOPCO ASSOCIATES LLC', 'hfh', 'images/system/projects/test_db/manufacturer/images/Shurfine.jpg'),
(132, 'KROGER CO, THE', 'KROGER CO, THE', 'ff', 'images/system/projects/test_db/manufacturer/images/KROGER CO, THE.jpg'),
(133, 'B.S.A. INTERNATIONAL S.A.', 'B.S.A. INTERNATIONAL S.A.', 'sgfh', 'images/system/projects/test_db/manufacturer/images/Shurfine.jpg'),
(134, 'TOPCO ASSOCIATES LLC.', 'TOPCO ASSOCIATES LLC.', 'https://services.traxretail.com/trax-one/gmius/products-and-brands/ma', 'images/system/projects/test_db/manufacturer/images/TOPCO ASSOCIATES LLC..JPG'),
(135, 'SPARTANNASH CO', 'SPARTANNASH CO', 'fsgr', 'images/system/projects/test_db/manufacturer/images/Shurfine.jpg'),
(136, 'LUDWIG DAIRY INC', 'LUDWIG DAIRY INC', 'LUDWIG DAIRY INC', 'images/system/projects/test_db/manufacturer/images/Annie\'s Homegrown Organic Chewy Granola Bars Chocolate Chip Cardboard Box 6 Units 5.34 oz.jpg'),
(137, 'CERBERUS CAPITAL MANAGEMENT LP', 'CERBERUS CAPITAL MANAGEMENT LP', 'http://perspectivemagazine.com/wp-content/uploads/2017/06/Cereberus.jpg', 'images/system/projects/test_db/manufacturer/images/CERBERUS CAPITAL MANAGEMENT LP.jpg'),
(138, 'Allegiance Retail Services Llc', 'Allegiance Retail Services Llc', 'fgjsgn', 'images/system/projects/test_db/manufacturer/images/Allegiance Retail Services Llc.jpg'),
(139, 'Tillamook County Creamery Association', 'Tillamook County Creamery Association', 'https://en.wikipedia.org/wiki/Tillamook_County_Creamery_Association', 'images/system/projects/test_db/manufacturer/images/3088.jfif'),
(140, 'H E BUTT GROCERY COMPANY', 'H E BUTT GROCERY COMPANY', 'https://www.google.com/search?q=H+E+BUTT+GROCERY+COMPANY&rlz=1C1CHZL_enLK766LK766&oq=H+E+BUTT+GROCERY+COMPANY&aqs=chrome..69i57.862j0j8&sourceid=chrome&ie=UTF-8', 'images/system/projects/test_db/manufacturer/images/HEB.jpg'),
(141, 'J & J Dairy Products Inc', 'J & J Dairy Products Inc', 'Ref', 'images/system/projects/test_db/manufacturer/images/J & J Yogurt Vanilla Kosher Dairy Container Plastic 32 oz.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_type` enum('empty','irrelevant','other','pos','sku') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empty',
  `product_item_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_global_code` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` int(11) NOT NULL,
  `client_category_id` int(11) NOT NULL,
  `client_sub_category_id` int(11) DEFAULT NULL,
  `product_global_status` enum('Pending Approval','Active','Inactive','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending Approval',
  `product_image_id` int(11) NOT NULL DEFAULT '1',
  `product_container_type_id` int(11) DEFAULT NULL,
  `product_short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_local_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_ean` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_smart_caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_type`, `product_item_code`, `product_global_code`, `brand_id`, `client_category_id`, `client_sub_category_id`, `product_global_status`, `product_image_id`, `product_container_type_id`, `product_short_name`, `product_local_name`, `product_ean`, `product_smart_caption`) VALUES
(1, 'General Bacon Other', 'other', NULL, NULL, 1, 4, NULL, 'Pending Approval', 1, NULL, NULL, NULL, NULL, NULL),
(2, 'General Bacon Empty', 'empty', NULL, NULL, 1, 4, NULL, 'Inactive', 1, NULL, NULL, NULL, NULL, NULL),
(3, '10 Barrel Beer Other', 'other', NULL, NULL, 2, 2, NULL, 'Active', 2, NULL, NULL, NULL, NULL, NULL),
(4, '10 Barrel Beer Apocalypse India Pale Ale Can 12 fl oz', 'sku', NULL, NULL, 2, 2, NULL, 'Active', 3, 4, NULL, NULL, NULL, NULL),
(8, '10 Barrel Beer Pub Lager Can 12 fl oz', 'sku', NULL, NULL, 2, 2, NULL, 'Active', 4, NULL, NULL, NULL, NULL, NULL),
(9, '10 Barrel Beer Cucumber Sour Crush Can 12 fl oz', 'sku', NULL, NULL, 2, 2, NULL, 'Active', 5, NULL, NULL, NULL, NULL, NULL),
(10, '10 Barrel Beer Raspberry Sour Crush Ale Can 12 fl oz', 'sku', '00014215', NULL, 2, 2, NULL, 'Active', 6, NULL, NULL, NULL, NULL, NULL),
(11, 'Starbucks Coffee Other', 'other', NULL, NULL, 3, 1, NULL, 'Active', 7, NULL, NULL, NULL, NULL, NULL),
(12, 'Starbucks Coffee Empty', 'empty', NULL, NULL, 3, 1, NULL, 'Inactive', 7, NULL, NULL, NULL, NULL, NULL),
(14, 'Lipton Iced Black Tea With Peach Bottle 1500 ml', 'sku', NULL, NULL, 12, 20, NULL, 'Pending Approval', 9, 5, NULL, NULL, NULL, NULL),
(15, 'Lipton Iced Black Tea With Lemon Bottle 1500 ml ', 'sku', NULL, NULL, 12, 20, NULL, 'Pending Approval', 10, 5, NULL, NULL, NULL, NULL),
(27, 'BEER Other', 'empty', NULL, NULL, 13, 2, NULL, 'Pending Approval', 32, NULL, NULL, NULL, NULL, NULL),
(28, 'Dannon Light And Fit Yogurt Drink Nonfat Strawberry Pack Carton 4 Pack x 7 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 33, 12, NULL, NULL, NULL, NULL),
(29, 'Dannon Light And Fit Yogurt Drink Nonfat Mixed Berry Pack Carton 4 Pack x 7 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 34, 12, NULL, NULL, NULL, NULL),
(30, 'Kroger Greek Yogurt Fat Free Vanilla Container Plastic 24 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 35, 8, NULL, NULL, NULL, NULL),
(31, 'Dannon Light And Fit Yogurt Nonfat Greek Nuts For Banana With Topping Container Plastic 5 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 36, 12, NULL, NULL, NULL, NULL),
(32, 'Kroger Yogurt Strawberry Lowfat Tetra 8 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 37, 8, NULL, NULL, NULL, NULL),
(33, 'Shurfine Yogurt Mixed Vanilla Lowfat Cup 6 oz', 'empty', NULL, NULL, 17, 24, NULL, 'Pending Approval', 38, 10, NULL, NULL, NULL, NULL),
(34, 'Wegmans Yogurt Blueberry Greek Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 14, 25, NULL, 'Pending Approval', 39, 5, NULL, NULL, NULL, NULL),
(35, 'Wegmans Yogurt Vanilla Lowfat Cup 6 oz', 'empty', NULL, NULL, 14, 25, NULL, 'Pending Approval', 40, 5, NULL, NULL, NULL, NULL),
(36, 'Wegmans Yogurt Plain Greek Nonfat Container Plastic 32 oz', 'empty', NULL, NULL, 14, 25, NULL, 'Pending Approval', 41, 4, NULL, NULL, NULL, NULL),
(37, 'Wegmans Yogurt Plain Greek Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 14, 24, NULL, 'Pending Approval', 42, 5, NULL, NULL, NULL, NULL),
(38, 'Simple Truth Organic Yogurt Black Cherry Greek Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 18, 25, NULL, 'Pending Approval', 43, 8, NULL, NULL, NULL, NULL),
(39, 'Fred Meyer Yogurt Peach Fruit On The Bottom Lowfat Cup 6 oz', 'empty', NULL, NULL, 19, 25, NULL, 'Pending Approval', 44, 7, NULL, NULL, NULL, NULL),
(40, 'Big Y Greek Yogurt Coconut Cup 5.3 oz', 'empty', NULL, NULL, 2, 25, NULL, 'Pending Approval', 45, 4, NULL, NULL, NULL, NULL),
(41, 'Kroger Blended Yogurt Vanilla Lowfat Container Plastic 32 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 46, 8, NULL, NULL, NULL, NULL),
(42, 'Simple Truth Organic Yogurt Vanilla Bean Greek Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 18, 25, NULL, 'Pending Approval', 47, 8, NULL, NULL, NULL, NULL),
(43, 'Big Y Greek Yogurt Blueberry Cup 5.3 oz', 'empty', NULL, NULL, 3, 26, NULL, 'Pending Approval', 48, 8, NULL, NULL, NULL, NULL),
(44, 'Spartan Yogurt Key Lime Original 99 Percent Fat Free Cup 6 oz', 'empty', NULL, NULL, 22, 25, NULL, 'Pending Approval', 49, 10, NULL, NULL, NULL, NULL),
(45, 'Kroger Greek Yogurt Blended Blueberry Cup 5.3 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 50, 7, NULL, NULL, NULL, NULL),
(46, 'Simple Truth Organic Yogurt Plain Greek Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 18, 25, NULL, 'Pending Approval', 51, 8, NULL, NULL, NULL, NULL),
(47, 'Kroger Greek Yogurt Plain Container Plastic 32 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 52, 8, NULL, NULL, NULL, NULL),
(48, 'Kroger Greek Yogurt Plain Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 16, 24, NULL, 'Pending Approval', 53, 13, NULL, NULL, NULL, NULL),
(49, 'Nostimo Greek Yogurt Key Lime Blended Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 54, 6, NULL, NULL, NULL, NULL),
(50, 'Kroger Greek Yogurt Blended Black Cherry Cup 5.3 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 55, 7, NULL, NULL, NULL, NULL),
(51, 'Danone Yogurt Strawberry Container Plastic 7.7 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 56, 5, NULL, NULL, NULL, NULL),
(52, 'Nostimo Greek Black Cherry Non Fat Yogurt Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 57, 13, NULL, NULL, NULL, NULL),
(53, 'Nostimo Greek Yogurt Nonfat Vanilla Blended Container Plastic 32 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 58, 10, NULL, NULL, NULL, NULL),
(54, 'Ludwig Diary Kefir Yogurt Strawberry Bottle 32 fl oz', 'empty', NULL, NULL, 21, 25, NULL, 'Pending Approval', 59, 5, NULL, NULL, NULL, NULL),
(55, 'Kroger Greek Blended Yogurt Strawberry Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 16, 24, NULL, 'Pending Approval', 60, 8, NULL, NULL, NULL, NULL),
(56, 'Kroger Greek Blended Yogurt Vanilla Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 16, 24, NULL, 'Pending Approval', 61, 8, NULL, NULL, NULL, NULL),
(57, 'Kroger Blended Greek Yogurt Raspberry Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 62, 8, NULL, NULL, NULL, NULL),
(58, 'Ludwig Diary Kefir Yogurt Cherry Container Plastic 32 fl oz', 'empty', NULL, NULL, 21, 25, NULL, 'Pending Approval', 63, 5, NULL, NULL, NULL, NULL),
(59, 'Dannon Light & Fit Yogurt Blueberry Nonfat Cup 6 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 64, 8, NULL, NULL, NULL, NULL),
(60, 'Dannon Danimals Squeezables Yogurt Blueberry Pie Low Fat Cardboard Box 4 Pack x 4 oz', 'empty', NULL, NULL, 15, 24, NULL, 'Pending Approval', 65, 10, NULL, NULL, NULL, NULL),
(61, 'Nostimo Greek Yogurt Nonfat Vanilla Container Plastic 32 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 66, 4, NULL, NULL, NULL, NULL),
(62, 'Foodtown Yogurt Pina Colada Light Fatfree Cup 6 oz', 'empty', NULL, NULL, 23, 25, NULL, 'Pending Approval', 67, 5, NULL, NULL, NULL, NULL),
(63, 'Open Nature Yogurt Greek Plain Strained Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 24, 25, NULL, 'Pending Approval', 68, 4, NULL, NULL, NULL, NULL),
(64, 'Nostimo Yogurt Strawberry Non Fat Container Plastic 32 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 69, 5, NULL, NULL, NULL, NULL),
(65, 'Kroger Greek Yogurt Ginger Snap Crunch Cup 5.3 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 70, 6, NULL, NULL, NULL, NULL),
(66, 'Nostimo Greek Vanilla Blended Non Fat Yogurt Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 71, 8, NULL, NULL, NULL, NULL),
(67, 'Nostimo Greek Yogurt Fruit On The Bottom Peach Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 72, 9, NULL, NULL, NULL, NULL),
(68, 'Nostimo Yogurt Greek Fruit On The Bottom Nonfat Strawberry Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 73, 2, NULL, NULL, NULL, NULL),
(69, 'Nostimo Greek Strawberry Non Fat Yogurt Cup 5.3 oz', 'empty', '11225122286', NULL, 20, 24, NULL, 'Pending Approval', 74, 5, NULL, NULL, NULL, NULL),
(70, 'Nostimo Greek Pomegranate Nonfat Yogurt Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 75, 13, NULL, NULL, NULL, NULL),
(71, 'Simple Truth Yogurt Vanilla Greek Cup 5.3 oz', 'empty', NULL, NULL, 18, 25, NULL, 'Pending Approval', 76, 8, NULL, NULL, NULL, NULL),
(72, 'Dannon Oikos Yogurt Drink Nonfat Vanilz Pack Carton 4 Pack x 7 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 77, 12, NULL, NULL, NULL, NULL),
(73, 'Kroger Yogurt Blended Peach Lowfat Cup 6 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 78, 8, NULL, NULL, NULL, NULL),
(74, 'Dannon Oikos Yogurt Strawberry And Mixed Berry Pack Carton 12 x 5.3 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 79, 10, NULL, NULL, NULL, NULL),
(75, 'Dannon Yogurt Oikos Blended Greek Vanilla Container Plastic 32 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 80, 6, NULL, NULL, NULL, NULL),
(76, 'Tillamook Yogurt Plain Lowfat Cup 5.3 oz', 'empty', NULL, NULL, 27, 25, NULL, 'Pending Approval', 81, 8, NULL, NULL, NULL, NULL),
(77, 'Dannon Danimals Smoothie Orange Cream Wrap Carton 6 Pack x 3.1 oz', 'empty', NULL, NULL, 15, 24, NULL, 'Pending Approval', 82, 12, NULL, NULL, NULL, NULL),
(78, 'Dannon Light & Fit Yogurt Raspberry Fat Free Cup 6 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 83, 8, NULL, NULL, NULL, NULL),
(79, 'Nostimo Yogurt Mango Non Fat Cup 5.3 oz', 'empty', NULL, NULL, 20, 24, NULL, 'Pending Approval', 84, 13, NULL, NULL, NULL, NULL),
(80, 'Foodtown Yogurt Raspberry Fatfree Cup 6 oz', 'empty', NULL, NULL, 23, 25, NULL, 'Pending Approval', 85, 5, NULL, NULL, NULL, NULL),
(81, 'HEB Yogurt Strawberry Banana Fat Free Pack Carton 4 Pack x 4 oz', 'empty', NULL, NULL, 28, 25, NULL, 'Pending Approval', 86, 12, NULL, NULL, NULL, NULL),
(82, 'Nostimo Yogurt Cocount Blended Non Fat Container Plastic 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 87, 5, NULL, NULL, NULL, NULL),
(83, 'Danone Danup Yogurt Pina Colada Container Plastic 7.7 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 88, 5, NULL, NULL, NULL, NULL),
(84, 'Kroger Greek Yogurt Raspberry Chocolate Chunk Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 16, 24, NULL, 'Pending Approval', 89, 13, NULL, NULL, NULL, NULL),
(85, 'Nostimo Greek Honey Non Fat Yogurt Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 90, 8, NULL, NULL, NULL, NULL),
(86, 'Nostimo Greek Pineapple Non Fat Yogurt Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 91, 4, NULL, NULL, NULL, NULL),
(87, 'Dannon Light & Fit Yogurt Strawberry Fat Free Cup 6 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 92, 8, NULL, NULL, NULL, NULL),
(88, 'Danone Yogurt Pecan Bottle 7 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 93, 5, NULL, NULL, NULL, NULL),
(89, 'Dannon Danimals Smoothies Cheerin Cherry Cardboard Box 6 Pack x 3.1 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 94, 10, NULL, NULL, NULL, NULL),
(90, 'Wegmans Yogurt Raspberry Blended Lowfat Cup 6 oz', 'empty', NULL, NULL, 14, 25, NULL, 'Pending Approval', 95, 4, NULL, NULL, NULL, NULL),
(91, 'Nostimo Blended Yogurt Mixed Berry Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 96, 8, NULL, NULL, NULL, NULL),
(92, 'Simple Truth Organic Yogurt Plain Greek Nonfat Container Plastic 32 oz', 'empty', NULL, NULL, 18, 25, NULL, 'Pending Approval', 97, 8, NULL, NULL, NULL, NULL),
(93, 'Ludwig Diary Kefir Yogurt Blueberry Bottle 32 fl oz', 'empty', NULL, NULL, 21, 25, NULL, 'Pending Approval', 98, 5, NULL, NULL, NULL, NULL),
(94, 'Big Y Greek Yogurt Key Lime Cup 5.3 oz', 'empty', NULL, NULL, 2, 26, NULL, 'Pending Approval', 99, 2, NULL, NULL, NULL, NULL),
(95, 'Ludwig Diary Kefir Yogurt Raspberry Bottle 32 fl oz', 'empty', NULL, NULL, 21, 24, NULL, 'Pending Approval', 100, 5, NULL, NULL, NULL, NULL),
(96, 'Tillamook Yogurt Dark Cherry Lowfat Cup 6 oz', 'empty', NULL, NULL, 27, 24, NULL, 'Pending Approval', 101, 8, NULL, NULL, NULL, NULL),
(97, 'Kroger Greek Yogurt Blueberry Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 102, 8, NULL, NULL, NULL, NULL),
(98, 'Kroger Crab Master Yogurt Pineapple Coconut Yogurt Cup 6 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 103, 8, NULL, NULL, NULL, NULL),
(99, 'Kroger Yogurt Cotton Candy And Fruit Punch Cardboard Box 8 x 2.25 oz', 'empty', NULL, NULL, 16, 25, NULL, 'Pending Approval', 104, 8, NULL, NULL, NULL, NULL),
(100, 'Dannon Oikos Crunch Yogurt Key Lime Crumble Container Plastic 5 oz', 'empty', NULL, NULL, 15, 24, NULL, 'Pending Approval', 105, 6, NULL, NULL, NULL, NULL),
(101, 'Kroger Greek Yogurt Apple & Oats Nonfat Cup 5.3 oz', 'empty', NULL, NULL, 16, 24, NULL, 'Pending Approval', 106, 13, NULL, NULL, NULL, NULL),
(102, 'J & J Yogurt Vanilla Kosher Dairy Container Plastic 32 oz', 'empty', NULL, NULL, 2, 25, NULL, 'Pending Approval', 107, 2, NULL, NULL, NULL, NULL),
(103, 'Dannon Light & Fit Yogurt Cherry Vanilla Fat Free Cup 6 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 108, 8, NULL, NULL, NULL, NULL),
(104, 'Stonyfield Organic Yogurt French Vanilla Fatfree Container Plastic 32 oz', 'empty', NULL, NULL, 25, 25, NULL, 'Pending Approval', 109, 5, NULL, NULL, NULL, NULL),
(105, 'Simple Truth Yogurt Plain Strained Greek Lowfat Container Plastic 32 oz', 'empty', NULL, NULL, 18, 25, NULL, 'Pending Approval', 110, 7, NULL, NULL, NULL, NULL),
(106, 'Danone Yogurt Strawberry Banana Bottle Wrap Plastic 8 Pack x 7 oz Value Pack', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 111, 5, NULL, NULL, NULL, NULL),
(107, 'Stonyfield Organic Yogurt Whole Milk French Vanilla Container Plastic 32 oz', 'empty', NULL, NULL, 25, 24, NULL, 'Pending Approval', 112, 5, NULL, NULL, NULL, NULL),
(108, 'Wegmans Yogurt Greek Vanilla Cup 5.3 oz', 'empty', NULL, NULL, 14, 25, NULL, 'Pending Approval', 113, 5, NULL, NULL, NULL, NULL),
(109, 'Stonyfield Organic Osoy Yogurt Cultured Soy Strawberry Fruit On The Bottom Cup 6 oz', 'empty', NULL, NULL, 25, 25, NULL, 'Pending Approval', 114, 5, NULL, NULL, NULL, NULL),
(110, 'Nostimo Greek Blueberry Non Fat Yogurt Cup 5.3 oz', 'empty', NULL, NULL, 20, 25, NULL, 'Pending Approval', 115, 3, NULL, NULL, NULL, NULL),
(111, 'Foodtown Yogurt Plain Fatfree Container Plastic 32 oz', 'empty', NULL, NULL, 23, 24, NULL, 'Pending Approval', 116, 5, NULL, NULL, NULL, NULL),
(112, 'Foodtown Yogurt Plain Whole Milk Container Plastic 32 oz', 'empty', NULL, NULL, 23, 25, NULL, 'Pending Approval', 117, 13, NULL, NULL, NULL, NULL),
(113, 'Tillamook Yogurt French Vanilla Bean Lowfat Cup 6 oz', 'empty', NULL, NULL, 27, 25, NULL, 'Pending Approval', 118, 1, NULL, NULL, NULL, NULL),
(114, 'Hiland Yogurt Black Cherry Low fat Cup 6 oz', 'empty', NULL, NULL, 15, 25, NULL, 'Pending Approval', 119, 6, NULL, NULL, NULL, NULL);

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
  `product_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg',
  `product_top_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg',
  `product_back_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg ',
  `product_bottom_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg ',
  `product_side1_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg ',
  `product_side2_image_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images\\default\\system\\product\\default.jpg '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`product_image_id`, `product_image_location`, `product_top_image_location`, `product_back_image_location`, `product_bottom_image_location`, `product_side1_image_location`, `product_side2_image_location`) VALUES
(1, 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(2, 'images\\default\\system\\product\\10 Barrel Beer Other.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(3, 'images\\default\\system\\product\\10 Barrel Beer Apocalypse India Pale Ale Can 12 fl oz.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(4, 'images\\default\\system\\product\\10 Barrel Beer Pub Lager Can 12 fl oz.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(5, 'images\\default\\system\\product\\10 Barrel Beer Cucumber Sour Crush Can 12 fl oz.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(6, 'images\\default\\system\\product\\10 Barrel Beer Raspberry Sour Crush Ale Can 12 fl oz.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(7, 'images\\default\\system\\product\\Starbucks Coffee Other.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(8, 'images/system/projects/test_db/products/images/edc3f-1536411950.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(9, 'images/system/projects/test_db/products/images/original.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(10, 'images/system/projects/test_db/products/images/tea.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(32, 'images/system/projects/test_db/products/images/7477397235d494ea2901c22.37187988.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(33, 'images/system/projects/test_db/products/images/15230053115d495b7ee06e34.06104522.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(34, 'images/system/projects/test_db/products/images/18007900235d495b919bde89.18759539.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(35, 'images/system/projects/test_db/products/images/15405464105d495b9fa0a7a3.69365244.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(36, 'images/system/projects/test_db/products/images/16289959605d495ba32b2398.29588910.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(37, 'images/system/projects/test_db/products/images/11947538625d495bac4bc852.48265823.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(38, 'images/system/projects/test_db/products/images/1955794275d495bc8269fb3.71804225.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(39, 'images/system/projects/test_db/products/images/15900782145d495bd74ae702.50742708.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(40, 'images/system/projects/test_db/products/images/1277663475d495be25fcb44.42569310.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(41, 'images/system/projects/test_db/products/images/1526379295d495be379d388.64955186.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(42, 'images/system/projects/test_db/products/images/16883673875d495be81da451.29854597.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(43, 'images/system/projects/test_db/products/images/7277685715d495bfa2fde25.04555166.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(44, 'images/system/projects/test_db/products/images/3851548195d495bfc068638.96985433.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(45, 'images/system/projects/test_db/products/images/2524446145d495c3807ba05.13721673.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(46, 'images/system/projects/test_db/products/images/17764897455d495c95c0f839.79325620.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(47, 'images/system/projects/test_db/products/images/6082199975d495cc18779f4.92698660.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(48, 'images/system/projects/test_db/products/images/5887917825d495cdc4650a5.54346821.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(49, 'images/system/projects/test_db/products/images/7194314315d495ce128b705.02371592.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(50, 'images/system/projects/test_db/products/images/19972546845d495ce94660e1.05364640.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(51, 'images/system/projects/test_db/products/images/10351286415d495d0267e9f9.09368267.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(52, 'images/system/projects/test_db/products/images/17827594345d495d25eec718.27206347.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(53, 'images/system/projects/test_db/products/images/16975677655d495d537df504.75780977.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(54, 'images/system/projects/test_db/products/images/4894415685d495d5c928dc1.78345941.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(55, 'images/system/projects/test_db/products/images/7960273385d495d724e6193.10734504.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(56, 'images/system/projects/test_db/products/images/16757809275d495d7ac47de4.96191429.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(57, 'images/system/projects/test_db/products/images/12355404955d495d96103000.61734015.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(58, 'images/system/projects/test_db/products/images/1065742685d495d9ce29e39.80175993.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(59, 'images/system/projects/test_db/products/images/5336553345d495da3e60640.77892513.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(60, 'images/system/projects/test_db/products/images/13479545835d495daf89b865.42689171.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(61, 'images/system/projects/test_db/products/images/7311237435d495db286e930.91763223.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(62, 'images/system/projects/test_db/products/images/20361205995d495dc33d6c69.88241236.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(63, 'images/system/projects/test_db/products/images/7130403495d495e26366dc8.96990623.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(64, 'images/system/projects/test_db/products/images/7897990295d495e55a33bb4.52984404.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(65, 'images/system/projects/test_db/products/images/20274217685d495e77a4b6b0.73856666.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(66, 'images/system/projects/test_db/products/images/4800267735d495e825a45d1.76082696.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(67, 'images/system/projects/test_db/products/images/7006329065d495e8e4ae191.94538111.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(68, 'images/system/projects/test_db/products/images/15940888255d495ea5e1f741.67858575.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(69, 'images/system/projects/test_db/products/images/3464439385d495ed2a28a97.41158313.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(70, 'images/system/projects/test_db/products/images/9906235795d495ee7407120.14944073.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(71, 'images/system/projects/test_db/products/images/11257118425d495efe86f3e1.34370058.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(72, 'images/system/projects/test_db/products/images/4225655885d495f03c99445.49726627.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(73, 'images/system/projects/test_db/products/images/8404564255d495f06234514.60223174.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(74, 'images/system/projects/test_db/products/images/15814108375d495f26d2bb64.73795129.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(75, 'images/system/projects/test_db/products/images/15820243285d495f38b56e39.45325710.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(76, 'images/system/projects/test_db/products/images/443679455d495f643c65a6.06517478.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(77, 'images/system/projects/test_db/products/images/19992767905d495f681f4eb0.95562004.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(78, 'images/system/projects/test_db/products/images/3528052505d495f6a0b0864.68600854.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(79, 'images/system/projects/test_db/products/images/13163948025d495f6a887e95.25255055.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(80, 'images/system/projects/test_db/products/images/8128315345d495f6c57a128.48913218.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(81, 'images/system/projects/test_db/products/images/6878298055d495f8f6b2176.14044353.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(82, 'images/system/projects/test_db/products/images/4305803235d495fa188ecb4.81346686.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(83, 'images/system/projects/test_db/products/images/8716193315d495fa82624d4.80596354.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(84, 'images/system/projects/test_db/products/images/11566898095d495fad032653.41978793.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(85, 'images/system/projects/test_db/products/images/9747538995d495fcfca4aa8.20045456.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(86, 'images/system/projects/test_db/products/images/3693890985d495fdb8bb382.94216621.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(87, 'images/system/projects/test_db/products/images/11906559405d495fe5a78618.02262078.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(88, 'images/system/projects/test_db/products/images/11106531965d496011ec4071.12778785.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(89, 'images/system/projects/test_db/products/images/14232410225d496028ce2ba1.17275714.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(90, 'images/system/projects/test_db/products/images/7332488025d4960360280a3.04483726.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(91, 'images/system/projects/test_db/products/images/3817185545d4960b274f2e0.42603606.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(92, 'images/system/projects/test_db/products/images/11292921175d4960c0cbfad5.02700333.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(93, 'images/system/projects/test_db/products/images/15498107815d4960f5427841.60866953.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(94, 'images/system/projects/test_db/products/images/4191965095d4960f5546016.77143003.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(95, 'images/system/projects/test_db/products/images/5261413985d4960f70e4c95.67602495.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(96, 'images/system/projects/test_db/products/images/18223488215d4960f80ef6b6.27667858.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(97, 'images/system/projects/test_db/products/images/4713139725d4960f9105e57.91650979.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(98, 'images/system/projects/test_db/products/images/20580121975d4960f910d257.24530693.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(99, 'images/system/projects/test_db/products/images/8505681375d4960fba9d098.50011645.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(100, 'images/system/projects/test_db/products/images/19205186925d4960fe594761.23305248.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(101, 'images/system/projects/test_db/products/images/11620182825d496102c72bc3.08614591.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(102, 'images/system/projects/test_db/products/images/3608291135d49610b2ac026.45100442.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(103, 'images/system/projects/test_db/products/images/20647857805d49610de1d886.53400418.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(104, 'images/system/projects/test_db/products/images/2386153395d49610f023904.26541602.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(105, 'images/system/projects/test_db/products/images/14881422655d496111104379.00109399.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(106, 'images/system/projects/test_db/products/images/18750885345d49612345bd45.13574320.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(107, 'images/system/projects/test_db/products/images/11731373175d496123acc2b6.09900559.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(108, 'images/system/projects/test_db/products/images/18579940625d49615b1d6d33.51676336.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(109, 'images/system/projects/test_db/products/images/20261983475d4961656803d5.69186790.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(110, 'images/system/projects/test_db/products/images/522896735d49616e8fbcd9.07813564.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(111, 'images/system/projects/test_db/products/images/1837003575d4961864e8d46.97718659.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(112, 'images/system/projects/test_db/products/images/16284874155d496189832508.63791323.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(113, 'images/system/projects/test_db/products/images/10122111875d49618d7bfc80.28757016.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(114, 'images/system/projects/test_db/products/images/12271376715d4961a9577210.92794949.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(115, 'images/system/projects/test_db/products/images/5762406865d4961accd5175.36129304.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(116, 'images/system/projects/test_db/products/images/9405379915d49621b7c2a75.36921026.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(117, 'images/system/projects/test_db/products/images/10346814655d49623ebc11a0.42813085.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(118, 'images/system/projects/test_db/products/images/11405572415d4962707ed2b4.45641493.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg '),
(119, 'images/system/projects/test_db/products/images/2008094345d4963db366b61.77750593.jpg', 'images\\default\\system\\product\\default.jpg', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ', 'images\\default\\system\\product\\default.jpg ');

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
-- Table structure for table `product_physical_attr`
--

CREATE TABLE `product_physical_attr` (
  `product_physical_attr_id` int(11) NOT NULL,
  `product_size` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_sub_packages` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_measurement_unit_id` int(11) DEFAULT NULL,
  `product_units` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_height` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_width` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_depth` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attribute`
--
ALTER TABLE `attribute`
  ADD PRIMARY KEY (`attribute_id`);

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
  ADD KEY `PRODUCT_IMAGE_ID` (`product_image_id`),
  ADD KEY `PRODUCT_CONTAINER_TYPE_ID` (`product_container_type_id`),
  ADD KEY `CLIENT_SUB_CATEGORY_ID` (`client_sub_category_id`);

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
-- Indexes for table `product_physical_attr`
--
ALTER TABLE `product_physical_attr`
  ADD PRIMARY KEY (`product_physical_attr_id`),
  ADD KEY `PRODUCT_MEASUREMENT_UNIT_ID` (`product_measurement_unit_id`);

--
-- Indexes for table `project_trax_category`
--
ALTER TABLE `project_trax_category`
  ADD PRIMARY KEY (`project_trax_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attribute`
--
ALTER TABLE `attribute`
  MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `client_category`
--
ALTER TABLE `client_category`
  MODIFY `client_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `client_sub_category`
--
ALTER TABLE `client_sub_category`
  MODIFY `client_sub_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `manufacturer`
--
ALTER TABLE `manufacturer`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;
--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;
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
  MODIFY `product_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;
--
-- AUTO_INCREMENT for table `product_measurement_unit`
--
ALTER TABLE `product_measurement_unit`
  MODIFY `product_measurement_unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `product_physical_attr`
--
ALTER TABLE `product_physical_attr`
  MODIFY `product_physical_attr_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_trax_category`
--
ALTER TABLE `project_trax_category`
  MODIFY `project_trax_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
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
  ADD CONSTRAINT `CLIENT_SUB_CATEGORY_ID` FOREIGN KEY (`client_sub_category_id`) REFERENCES `client_sub_category` (`client_sub_category_id`),
  ADD CONSTRAINT `PRODUCT_CONTAINER_TYPE_ID` FOREIGN KEY (`product_container_type_id`) REFERENCES `product_container_type` (`product_container_type_id`),
  ADD CONSTRAINT `PRODUCT_IMAGE_ID` FOREIGN KEY (`product_image_id`) REFERENCES `product_image` (`product_image_id`);

--
-- Constraints for table `product_attribute`
--
ALTER TABLE `product_attribute`
  ADD CONSTRAINT `ATTRIBUTE_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product_physical_attr`
--
ALTER TABLE `product_physical_attr`
  ADD CONSTRAINT `PRODUCT_MEASUREMENT_UNIT_ID` FOREIGN KEY (`product_measurement_unit_id`) REFERENCES `product_measurement_unit` (`product_measurement_unit_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
