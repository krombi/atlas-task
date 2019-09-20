-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Сен 20 2019 г., 18:51
-- Версия сервера: 10.1.38-MariaDB-0+deb9u1
-- Версия PHP: 7.3.8-1+0~20190807.43+debian9~1.gbp7731bf

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `admin_atlas`
--

-- --------------------------------------------------------

--
-- Структура таблицы `at_fields`
--

CREATE TABLE `at_fields` (
  `id` int(11) NOT NULL,
  `ident` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'text',
  `label` varchar(150) NOT NULL,
  `alias` int(1) NOT NULL DEFAULT '0',
  `validator` varchar(50) NOT NULL,
  `features` text,
  `sort` int(11) NOT NULL DEFAULT '1000000',
  `active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `at_fields`
--

INSERT INTO `at_fields` (`id`, `ident`, `type`, `label`, `alias`, `validator`, `features`, `sort`, `active`) VALUES
(1, 'fio', 'text', 'ФИО', 1, 'text', '{\"required\":true,\"input\":{\"placeholder\":\"Иванов Иван Иванович\",\"match\":\"%5E%5Ba-zA-Z%D0%B0-%D1%8F%D0%90-%D0%AF%D1%91%D0%81%5Cs%5D%2A%24\",\"length\":150},\"errors\":{\"required\":\"ФИО должно быть заполнено!\",\"match\":\"Неверный формат ФИО!\",\"length\":\"ФИО не может быть больше 150 символов!\"}}', 1, 1),
(2, 'email', 'email', 'Электронная почта', 0, 'email', '{\"required\":true,\"input\":{\"placeholder\":\"ivan@ivanov.com\",\"length\":100,\"match\":\"%5E%5Ba-z%5C-%5C_%5C.%5Cd%5D%2A%40%5Ba-z%5C-%5Cd%5D%2A.%5Ba-z%5D%7B2%2C%7D%24\",\"compare\":\"email\"},\"errors\":{\"required\":\"Электронная почта должна быть указана!\",\"match\":\"Неверный формат адреса электронной почты!\",\"length\":\"Длина электронной почты не должна превышать 100 символов!\"}}', 2, 1),
(3, 'dob', 'text', 'Дата рождения', 0, 'text', '{\"input\":{\"attributes\":{\"class\":[\"datepicker-here\"]},\"placeholder\":\"18.09.1988\",\"mask\":\"9{2}.9{2}.9{4}\",\"match\":\"%5E%5B%5Cd%5D%7B2%7D.%5B%5Cd%5D%7B2%7D.%5B%5Cd%5D%7B4%7D%24\"},\"errors\":{\"match\":\"Неверный формат даты!\"}}', 3, 1),
(4, 'sex', 'radio', 'Пол', 0, 'choose', '{\"class\":[\"list-inline\"],\"errors\":{\"possible\":\"Неизвестный пол!\"}}', 4, 1),
(5, 'document', 'select', 'Тип документа', 0, 'choose', '{\"select\":{\"default\":\"Выбрать\"},\"errors\":{\"possible\":\"Неизвестный тип документа!\"}}', 5, 1),
(6, 'docnumber', 'text', 'Номер документа', 0, 'text', '{\"input\":{\"placeholder\":\"Выберите тип документа\",\"match\":\"%5E%5Ba-zA-Z%D0%B0-%D1%8F%D0%90-%D0%AF%D1%91%D0%81%5Cs%5Cd%5D%2A%24\",\"disabled\":true},\"errors\":{\"match\":\"Введенные данные не соответствуют стандарту!\"},\"depends\":{\"document\":{\"rb-pasport\":{\"input\":{\"placeholder\":\"AA 123456\",\"mask\":\"a{2} 9{6}\",\"match\":\"%5E%5Ba-zA-Z%D0%B0-%D1%8F%D0%90-%D0%AF%D1%91%D0%81%5D%7B2%7D%20%5B0-9%5D%7B6%7D%24\",\"disabled\":false}},\"rf-pasport\":{\"input\":{\"placeholder\":\"1234 567890\",\"mask\":\"9{4} 9{6}\",\"match\":\"%5E%5B%5Cd%5D%7B4%7D%20%5B%5Cd%5D%7B6%7D%24\",\"disabled\":false}},\"driver-license\":{\"input\":{\"placeholder\":\"12 34 567890\",\"mask\":\"9{2} 9{2} 9{6}\",\"match\":\"%5E%5B%5Cd%5D%7B2%7D%20%5B%5Cd%5D%7B2%7D%20%5B0-9%5D%7B6%7D%24\",\"disabled\":false}},\"birth-certificate\":{\"input\":{\"placeholder\":\"1 AA №123456\",\"mask\":\"9{1} a{2} №9{6}\",\"match\":\"%5E%5B%5Cd%5D%7B1%7D%20%5Ba-zA-Z%D0%B0-%D1%8F%D0%90-%D0%AF%D1%91%D0%81%5D%7B2%7D%20%E2%84%96%5B%5Cd%5D%7B6%7D%24\",\"disabled\":false}}}}}', 6, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `at_field_values`
--

CREATE TABLE `at_field_values` (
  `id` int(11) NOT NULL,
  `field` int(11) NOT NULL,
  `ident` varchar(50) NOT NULL,
  `selected` int(1) NOT NULL DEFAULT '0',
  `label` varchar(150) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '1000000',
  `active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `at_field_values`
--

INSERT INTO `at_field_values` (`id`, `field`, `ident`, `selected`, `label`, `sort`, `active`) VALUES
(1, 4, 'man', 0, 'Мужчина', 1, 1),
(2, 4, 'women', 0, 'Женщина', 2, 1),
(3, 5, 'rb-pasport', 0, 'Паспорт РБ', 1, 1),
(4, 5, 'rf-pasport', 0, 'Паспорт РФ', 2, 1),
(5, 5, 'driver-license', 0, 'Водительское удостоверение', 3, 1),
(6, 5, 'birth-certificate', 0, 'Свидетельство о рождении', 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `at_makers`
--

CREATE TABLE `at_makers` (
  `id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `agent` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `at_makers`
--

INSERT INTO `at_makers` (`id`, `hash`, `ip`, `agent`) VALUES
(1, 'c203aff4b19faca21a30c97e1ac6ee2909cd479a', '83.149.47.203', '{\"platform\":\"Macintosh\",\"browser\":\"Chrome\",\"version\":\"76.0.3809.132\"}');

-- --------------------------------------------------------

--
-- Структура таблицы `at_rights`
--

CREATE TABLE `at_rights` (
  `id` int(11) NOT NULL,
  `ident` varchar(50) NOT NULL,
  `label` varchar(150) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '1000000',
  `active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `at_rights`
--

INSERT INTO `at_rights` (`id`, `ident`, `label`, `sort`, `active`) VALUES
(1, 'catalog-access', 'Доступ к разделу \"Каталог\"', 1, 1),
(2, 'users-access', 'Доступ к разделу \"Пользователи\"', 2, 1),
(3, 'orders-access', 'Доступ к разделу \"Заказы\"', 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `at_users`
--

CREATE TABLE `at_users` (
  `id` int(11) NOT NULL,
  `created` bigint(20) NOT NULL,
  `whose` int(11) NOT NULL,
  `alias` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `at_user_fields`
--

CREATE TABLE `at_user_fields` (
  `id` int(11) NOT NULL,
  `created` bigint(20) NOT NULL,
  `updated` bigint(20) DEFAULT NULL,
  `user` int(11) NOT NULL,
  `field` int(11) NOT NULL,
  `content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `at_user_rights`
--

CREATE TABLE `at_user_rights` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `permission` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `at_fields`
--
ALTER TABLE `at_fields`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `at_field_values`
--
ALTER TABLE `at_field_values`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `at_makers`
--
ALTER TABLE `at_makers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `at_rights`
--
ALTER TABLE `at_rights`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `at_users`
--
ALTER TABLE `at_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `at_user_fields`
--
ALTER TABLE `at_user_fields`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `at_user_rights`
--
ALTER TABLE `at_user_rights`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `at_fields`
--
ALTER TABLE `at_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `at_field_values`
--
ALTER TABLE `at_field_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT для таблицы `at_makers`
--
ALTER TABLE `at_makers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `at_rights`
--
ALTER TABLE `at_rights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `at_users`
--
ALTER TABLE `at_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `at_user_fields`
--
ALTER TABLE `at_user_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `at_user_rights`
--
ALTER TABLE `at_user_rights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
