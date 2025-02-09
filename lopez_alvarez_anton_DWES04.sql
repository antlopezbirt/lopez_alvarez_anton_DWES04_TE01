-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-02-2025 a las 00:41:56
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dwes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artist`
--

DROP TABLE IF EXISTS `artist`;
CREATE TABLE `artist` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `artist`
--

INSERT INTO `artist` (`id`, `name`, `country`) VALUES
(1, 'The Who', 'UK'),
(2, 'Ramones', 'USA'),
(3, 'Killing Joke', 'UK'),
(4, 'Cocteau Twins', 'UK'),
(5, 'Popol Vuh', 'Alemania'),
(6, 'Aliceffekt', 'Canadá'),
(7, 'New Order', 'UK'),
(8, 'Albert Band', 'España'),
(9, 'Midori Takada', 'Japón');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `externalid`
--

DROP TABLE IF EXISTS `externalid`;
CREATE TABLE `externalid` (
  `id` int(11) NOT NULL,
  `supplier` varchar(45) NOT NULL,
  `value` varchar(100) NOT NULL,
  `itemid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `externalid`
--

INSERT INTO `externalid` (`id`, `supplier`, `value`, `itemid`) VALUES
(1, 'Discogs', '1798436', 1),
(2, 'MusicBrainz', '4147c0d3-7939-3484-bde6-b6fbefd7bc3b', 1),
(3, 'Discogs', '10589268', 2),
(4, 'MusicBrainz', 'c5923e07-a09b-469a-af1a-bc5e67e402ec', 2),
(5, 'Discogs', '502215', 3),
(6, 'MusicBrainz', '0f6924c2-5812-36d8-8a34-df69744ce5e0', 3),
(7, 'Discogs', '10644727', 4),
(8, 'MusicBrainz', '224445b7-35ea-43c2-b8df-0e918292f5ba', 4),
(9, 'Discogs', '1085136', 5),
(10, 'MusicBrainz', 'e0e2c19a-d9a9-3b59-b69b-6afc14551599', 5),
(11, 'Discogs', '737253', 6),
(12, 'MusicBrainz', '5bcde2f7-80ac-38fa-994b-ce51df4d50e3', 6),
(13, 'Discogs', '13812171', 7),
(14, 'MusicBrainz', 'e4efe882-c8b6-45c1-a429-fb7f350225f9', 7),
(15, 'MusicBrainz', '681ad693-9074-4216-aded-d1babfede242', 8),
(16, 'Discogs', '7131107', 9),
(17, 'MusicBrainz', '48289c07-fb4c-3c7a-b185-fd800e822325', 9),
(18, 'Discogs', '121190', 10),
(19, 'MusicBrainz', 'de53b49d-be39-4daf-afe2-d650269862d7', 10),
(20, 'Discogs', '3374017', 11),
(21, 'Discogs', '17593621', 12),
(22, 'MusicBrainz', 'f018af26-cf5e-4c76-93f0-c5faa80fd371', 12),
(23, 'Discogs', '651058', 13),
(24, 'MusicBrainz', '2defbea3-6f9d-43f1-bac7-fdd196cf65a7', 13),
(25, 'Discogs', '9975883', 14),
(26, 'MusicBrainz', '76dccf5b-c8b0-44b1-93a8-fb739a066bc4', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `artistid` int(11) DEFAULT NULL,
  `format` varchar(15) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `origyear` year(4) DEFAULT NULL,
  `label` varchar(45) DEFAULT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `comment` tinytext DEFAULT NULL,
  `buyprice` decimal(6,2) DEFAULT NULL,
  `condition` varchar(2) DEFAULT NULL,
  `sellprice` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `item`
--

INSERT INTO `item` (`id`, `title`, `artistid`, `format`, `year`, `origyear`, `label`, `rating`, `comment`, `buyprice`, `condition`, `sellprice`) VALUES
(1, 'Tommy', 1, 'CD', '2005', '1969', 'Polydor', 10, 'Edición deluxe. De mis discos favoritos', 20.00, 'NM', 25.00),
(2, 'Leave Home', 2, 'CD', '2017', '1977', 'Rhino Records', 8, 'Buena reedición', 22.00, 'M', 20.00),
(3, 'Killing Joke', 3, 'CD', '2005', '1980', 'Virgin', 8, 'Reedición de este disco histórico', 17.00, 'VG', 20.00),
(4, 'Heaven or Las Vegas', 4, 'CD', '1999', '1990', '4AD', 10, 'Edición japonesa muy buscada, disco excelente', 30.00, 'NM', 35.00),
(5, 'Quadrophenia', 1, 'CD', '1996', '1973', 'Polydor', 10, 'Edición inglesa, excelente estado', 20.00, 'EX', 25.00),
(6, 'Das Hohelied Salomos', 5, 'CD', '1992', '1975', 'Spalax', 9, 'Obra maestra de krautrock, edición francesa', 25.00, 'EX', 30.00),
(7, 'Hosianna Mantra', 5, 'CD', '2019', '1972', 'BMG', 10, 'Un clásico bien remasterizado', 22.00, 'NM', 30.00),
(8, 'Short Stories in Impossible Spaces', 6, 'Digital', '2014', '2014', 'self-release', 9, 'Maravillosa banda sonora', 5.00, 'M', 25.00),
(9, 'Who’s Next', 1, 'CD', '1998', '1971', 'MCA', 10, 'Edición canadiense, obra maestra', 20.00, 'NM', 27.00),
(10, 'Bizarre Love Triangle', 7, 'Maxi', '1986', '1986', 'Factory', 8, 'Edición original que incluye el famoso remix de Shep Pettibone', 10.00, 'VG', 15.00),
(11, 'Ella tiene el cabello rubio', 8, 'Single', '1970', '1970', 'Belter', 10, 'Edición original, joya buscadísima', 40.00, 'NM', 70.00),
(12, 'Rocket to Russia', 2, 'Digital', '2017', '1977', 'Rhino', 10, 'Edicion deluxe, 77 pistas!!', 20.00, 'M', 20.00),
(13, 'Ramones Mania', 2, '2LP', '1988', '1988', 'Sire', 9, 'Otra joya recopilatoria de los Ramones', 40.00, 'NM', 40.00),
(14, 'Through the Looking Glass', 9, 'CD', '2017', '1983', 'WRWTFWW Records', 9, 'Reedición suiza con descarga digital. Muy bueno.', 20.00, 'NM', 28.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `externalid`
--
ALTER TABLE `externalid`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_EXTERNALID_TITLE_ID` (`itemid`);

--
-- Indices de la tabla `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ITEM_ARTIST_ID` (`artistid`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `artist`
--
ALTER TABLE `artist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `externalid`
--
ALTER TABLE `externalid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `item`
--
ALTER TABLE `item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `externalid`
--
ALTER TABLE `externalid`
  ADD CONSTRAINT `FK_EXTERNALID_TITLE_ID` FOREIGN KEY (`itemid`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `FK_ITEM_ARTIST_ID` FOREIGN KEY (`artistid`) REFERENCES `artist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
