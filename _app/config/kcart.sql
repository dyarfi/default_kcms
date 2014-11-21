-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 29, 2012 at 12:04 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cignite_cart`
--

-- --------------------------------------------------------

--
-- Table structure for table `kcart_categories`
--

CREATE TABLE IF NOT EXISTS `kcart_categories` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `shortdesc` varchar(255) NOT NULL,
  `longdesc` text NOT NULL,
  `parentid` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `kcart_categories`
--

INSERT INTO `kcart_categories` (`id`, `name`, `shortdesc`, `longdesc`, `parentid`, `status`, `created`, `modified`) VALUES
(1, 'Shoes', 'Shoes Apparel', '', 0, 'active', '2011-04-14 09:52:37', '0000-00-00 00:00:00'),
(2, 'Shirt', 'Shirt Apparel', '', 0, 'active', '2011-04-14 09:52:37', '0000-00-00 00:00:00'),
(3, 'Pants', 'Pants Apparel', '', 0, 'active', '2011-04-14 09:52:37', '0000-00-00 00:00:00'),
(4, 'Kids Shoes', 'Kids Shoes Apparel', '', 1, 'active', '2011-04-14 09:52:37', '0000-00-00 00:00:00'),
(5, 'Kids Shirt', 'Kids Shirt Apparel', '', 2, 'active', '2011-04-14 09:52:37', '0000-00-00 00:00:00'),
(6, 'Kids Pants', 'Kids Pants Apparel', '', 3, 'active', '2011-04-14 09:52:37', '0000-00-00 00:00:00'),
(7, 'Girls Apparel', 'Anything for modern girl', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur quam justo, dictum in, imperdiet in, dictum cursus, leo. Aliquam est. Nunc auctor tortor non elit. Quisque diam.', 0, 'active', '2011-08-31 19:33:18', '0000-00-00 00:00:00'),
(8, 'Sports Wear', 'For sporting events', 'For sporting events and everyday activities.', 2, 'active', '2011-08-31 18:53:52', '0000-00-00 00:00:00'),
(9, 'Accessories', 'Women Accessories', 'For your best and latest accessories collections.', 7, 'active', '0000-00-00 00:00:00', '2011-09-01 20:00:05');

-- --------------------------------------------------------

--
-- Table structure for table `kcart_colors`
--

CREATE TABLE IF NOT EXISTS `kcart_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `kcart_colors`
--

INSERT INTO `kcart_colors` (`id`, `name`, `status`) VALUES
(1, 'Blue', 'active'),
(2, 'Red', 'active'),
(3, 'Green', 'active'),
(4, 'Black', 'active'),
(5, 'White', 'active'),
(6, 'Magenta', 'active'),
(7, 'Yellow', 'active'),
(8, 'Maroon', 'active'),
(9, 'Cyan', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `kcart_orders`
--

CREATE TABLE IF NOT EXISTS `kcart_orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kcart_orders`
--


-- --------------------------------------------------------

--
-- Table structure for table `kcart_order_items`
--

CREATE TABLE IF NOT EXISTS `kcart_order_items` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `product_qty` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kcart_order_items`
--

--
-- Table structure for table `kcart_products`
--

CREATE TABLE IF NOT EXISTS `kcart_products` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `shortdesc` varchar(255) NOT NULL,
  `longdesc` text NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `grouping` varchar(16) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL,
  `category_id` int(11) NOT NULL,
  `featured` enum('true','false') NOT NULL,
  `price` float(6,2) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `kcart_products`
--

INSERT INTO `kcart_products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`, `modified`, `created`) VALUES
(12, 'Adidas Predator', 'Futsal shoes', 'Futsal Sport Shoes', '234px234px_adidas_predator.jpg', 'adidas_predator.jpg', 'Hot Sale', 'active', 8, 'true', 1230.00, '2011-09-02 03:31:48', '2011-09-02 02:45:18'),
(13, 'Adidas Samba', 'Causal Shoes', 'Causal Shoes for active people', '234px234px_adidas_samba.jpg', 'adidas_samba.jpg', 'Hot Sale', 'active', 8, 'true', 1234.00, '2011-09-02 03:31:48', '2011-09-02 02:46:41'),
(14, 'Man UTD Futbol Jersey', 'Jersey for sport', 'Man UTD Futbol Jersey for women.', '234px234px_manutd_futbol.jpg', 'manutd_futbol.jpg', 'Hot Sale', 'active', 8, 'true', 1234.00, '2011-09-02 03:31:48', '2011-09-02 03:31:31'),
(15, 'Liverpool Soccer Jersey', 'Jersey for sport', 'Liverpool Soccer Jersey for Adults.', '234px234px_liverpool_futbol.jpg', 'liverpool_futbol.jpg', 'Hot Sale', 'active', 8, 'true', 2346.00, '2011-09-02 03:31:48', '2011-09-02 02:51:57'),
(16, 'Man UTD Futbol Jersey - Kids', 'Jersey for sport', 'Jersey for MANUTD Fans.', '234px234px_manutd_futbol.jpg', 'manutd_futbol.jpg', 'Hot Sale', 'active', 8, 'true', 1234.00, '2011-09-02 03:31:48', '2011-09-02 03:23:50'),
(17, 'Liverpool Soccer Jersey - Kids', 'Jersey for sport', 'Soccer Jersey for Liverpool FC', '234px234px_liverpool_futbol.jpg', 'liverpool_futbol.jpg', 'Hot Sale', 'active', 8, 'true', 2346.00, '2011-09-02 03:31:48', '2011-09-02 02:52:07'),
(18, 'Chelsea FC Futbol Jersey - Kids', 'Jersey for sport', '', '234px234px_chelsea_jersey.jpg', 'chelsea_jersey.jpg', '', 'active', 3, 'true', 2346.00, '2011-04-14 09:38:34', '0000-00-00 00:00:00'),
(19, 'Arsenal FC Futbol Jersey - Kids', 'Jersey for sport', '', '234px234px_arsenal_jersey.jpg', 'arsenal_jersey.jpg', '', 'active', 3, 'true', 2346.00, '2011-04-14 09:38:34', '0000-00-00 00:00:00'),
(20, 'Man UTD Futbol Jersey - Kids', 'Jersey for sport', '', '234px234px_liverpool_futbol.jpg', 'liverpool_futbol.jpg', '', 'active', 4, 'true', 2346.00, '2011-04-14 09:38:34', '0000-00-00 00:00:00'),
(21, 'Chelsea FC Futbol Jersey - Kids', 'Jersey for sport', '', '234px234px_chelsea_jersey.jpg', 'chelsea_jersey.jpg', '', 'active', 5, 'true', 2346.00, '2011-04-14 09:38:34', '0000-00-00 00:00:00'),
(22, 'Arsenal FC Futbol Jersey - Kids', 'Jersey for sport', '', '234px234px_arsenal_jersey.jpg', 'arsenal_jersey.jpg', '', 'active', 6, 'true', 2346.00, '2011-04-14 09:38:34', '0000-00-00 00:00:00'),
(23, 'Dynomo', 'A heritage', 'Aliquam est. Nunc auctor tortor non elit. Quisque diam. Aliquam', '', '', '', 'active', 1, 'true', 2323.00, '2011-06-29 21:33:59', '0000-00-00 00:00:00'),
(24, 'Vandalism', 'The vandals', 'Punk rock band', 'imgres7.jpg', 'imgres7.jpg', '', 'active', 1, 'true', 9999.99, '2011-09-02 21:34:49', '2011-06-29 21:41:42'),
(25, 'Girls Shorts', 'Trendy Shorts', 'For a young, energetic busy and ladies.', 'imgres1.jpg', 'imgres2.jpg', '', 'active', 7, 'true', 1200.00, '2011-08-30 23:25:57', '2011-08-30 21:22:14'),
(26, 'Casual Thanktop', 'Casual Thanktop', 'Casual Thanktop', 'imgres3.jpg', 'imgres3.jpg', '', 'active', 1, 'true', 1234.00, '2011-08-30 23:25:43', '2011-08-30 23:25:37'),
(27, 'Headband', 'head band', 'head band for accessories.', 'imgres2.jpg', 'imgres2.jpg', 'Stock Sale', 'active', 9, 'true', 9999.99, '2011-09-02 03:34:02', '2011-09-02 03:34:26'),
(28, 'Active Wrist Watch', 'Wrist Watch', 'Wrist Watch for trendy girls', 'imgres4.jpg', 'imgres4.jpg', '', 'active', 8, 'true', 1234.00, '2011-09-01 19:06:21', '2011-09-01 06:15:16'),
(29, 'Active Wrist Watch', 'Wrist Watch', 'Wrist Watch for trendy girls', 'imgres5.jpg', 'imgres5.jpg', '', 'active', 9, 'true', 1234.00, '2011-09-02 03:21:52', '2011-09-02 03:22:16'),
(30, 'Short Socks', 'Cute Short Socks', 'Cute Short Socks for any conditions', 'imgres6.jpg', 'imgres6.jpg', '', 'active', 9, 'true', 1234.00, '2011-09-01 20:01:16', '2011-09-01 20:01:40');

-- --------------------------------------------------------

--
-- Table structure for table `kcart_products_colors`
--

CREATE TABLE IF NOT EXISTS `kcart_products_colors` (
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kcart_products_colors`
--

INSERT INTO `kcart_products_colors` (`product_id`, `color_id`) VALUES
(16, 2),
(29, 6),
(29, 5),
(29, 4),
(27, 9),
(13, 2),
(28, 4),
(28, 3),
(12, 6),
(12, 5),
(12, 4),
(17, 5),
(17, 4),
(17, 2),
(27, 5),
(27, 3),
(27, 2),
(28, 9),
(28, 2),
(28, 1),
(30, 9),
(30, 4),
(30, 3),
(13, 3),
(13, 4),
(15, 4),
(15, 2),
(16, 4),
(16, 5),
(14, 2),
(14, 4),
(14, 5),
(27, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kcart_products_sizes`
--

CREATE TABLE IF NOT EXISTS `kcart_products_sizes` (
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kcart_products_sizes`
--

INSERT INTO `kcart_products_sizes` (`product_id`, `size_id`) VALUES
(28, 2),
(28, 1),
(12, 3),
(27, 5),
(29, 5),
(29, 4),
(12, 2),
(17, 5),
(17, 4),
(17, 3),
(17, 2),
(17, 1),
(27, 4),
(27, 3),
(28, 5),
(28, 4),
(28, 3),
(30, 5),
(30, 4),
(30, 3),
(30, 1),
(12, 4),
(13, 2),
(13, 3),
(15, 5),
(15, 4),
(15, 3),
(15, 2),
(16, 2),
(16, 3),
(16, 4),
(14, 2),
(14, 3),
(14, 4);

-- --------------------------------------------------------

--
-- Table structure for table `kcart_product_files`
--

CREATE TABLE IF NOT EXISTS `kcart_product_files` (
  `product_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kcart_product_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `kcart_sizes`
--

CREATE TABLE IF NOT EXISTS `kcart_sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `kcart_sizes`
--

INSERT INTO `kcart_sizes` (`id`, `name`, `status`) VALUES
(1, 'S', 'active'),
(2, 'M', 'active'),
(3, 'L', 'active'),
(4, 'XL', 'active'),
(5, 'XXL', 'active');

