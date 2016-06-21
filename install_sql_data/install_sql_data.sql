-- --------------------------------------------------------

--
-- Структура таблицы `demosite__blocks`
--

CREATE TABLE IF NOT EXISTS `demosite__blocks` (
  `blockname` varchar(255) CHARACTER SET utf8 NOT NULL,
  `blockdescription` text CHARACTER SET utf8 NOT NULL,
  `blockview` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`blockname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `demosite__blocks`
--

INSERT INTO `demosite__blocks` (`blockname`, `blockdescription`, `blockview`) VALUES
('BLOCK1', 'Тестовый блок', '<h2>Блок</h2>\r\n\r\nЭто глобальный блок 1\r\n\r\nЗдесь вы можете разместить контактные данные');

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__categories`
--

CREATE TABLE IF NOT EXISTS `demosite__categories` (
  `category` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cat_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `template` varchar(600) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `demosite__categories`
--

INSERT INTO `demosite__categories` (`category`, `cat_name`, `parent`, `template`) VALUES
('inform', 'Основной раздел', 'root', '/templates/readers/bootstrap.tpl');

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__comments`
--

CREATE TABLE IF NOT EXISTS `demosite__comments` (
  `id_comment` int(11) NOT NULL,
  `id_page` text COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `rating` int(11) NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id_comment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__options`
--

CREATE TABLE IF NOT EXISTS `demosite__options` (
  `optname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `optvalue` text COLLATE utf8_unicode_ci NOT NULL,
  `optnote` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`optname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `demosite__options`
--

INSERT INTO `demosite__options` (`optname`, `optvalue`, `optnote`) VALUES
('CACHE', 'OFF', '<b>Включен ли кэш</b>\r\nOFF - выключен<br/>\r\nON - включен<br/>'),
('META_KEYWORDS', 'Ключевые слова', 'Основные ключевые слова для поисковиков'),
('META_DESCRIPTION', 'Описание проекта', 'Основное описание для поисковиков'),
('EMAIL_ADMIN', 'test@test.mars', 'Электропочта администратора'),
('SITE_NAME', 'Мой сайт', 'Название сайта'),
('CLOSED', 'OFF', '<b>Выключен ли сайт</b>\r\nOFF - ВКЛЮЧЕН\r\nON - ВЫКЛЮЧЕН'),
('CLOSED_MESSAGE', 'Сайт закрыт на обновление, ведутся технические работы\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n', 'Сообщение о закрытии ресурса'),
('MAIN_TEMPLATE', '/templates/readers/bootstrap.tpl', 'Основной шаблон'),
('SHOP_ITEMSPERPAGE', '24', 'Витрина: Число элементов в выдаче'),
('SKLAD_ITEMSPERPAGE', '15', 'Склад: Число элементов в выдаче '),
('SHOP_MAXCOL', '4', 'Витрина: Число столбцов на странице на 1 меньше'),
('SHOP_ITEMSPRICESEARCH', '12', 'Витрина: Число элементов в выдаче при поиске по цене'),
('SHOP_COLSPRICESEARCH', '4', 'Витрина: Число колонок в выдаче при поиске по цене'),
('SHOP_ITEMSTAGSEARCH', '12', 'Витрина: Число элементов в выдаче при поиске по тегам'),
('SHOP_COLSSTAGSEARCH', '4', 'Витрина: Число колонок в выдаче при поиске по тегам'),
('ARTICLES', '8', 'Число страниц в выдаче новостей'),
('COLUMNS', '3', 'Число колонок в выдаче новостей'),
('GET_ARTICLES_FROM', 'inform', 'Категория для информера обновленных материалов');

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__pages`
--

CREATE TABLE IF NOT EXISTS `demosite__pages` (
  `id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'public',
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `visitors` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `demosite__pages`
--

INSERT INTO `demosite__pages` (`id`, `title`, `body`, `category`, `status`, `username`, `created`, `visitors`) VALUES
('mainpage', 'Potto CMS', '<p>Поздравляю, вы установили Potto CMS.</p>\r\n\r\n<p>Ваш логин и пароль: admin / admin</p>', 'inform', 'public', 'installer', '2012-09-01 00:00:00', 71);

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__permissions`
--

CREATE TABLE IF NOT EXISTS `demosite__permissions` (
  `module` varchar(40) NOT NULL,
  `action` varchar(40) NOT NULL,
  `roles` text NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`module`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__pm`
--

CREATE TABLE IF NOT EXISTS `demosite__pm` (
  `id_pm` int(11) unsigned NOT NULL,
  `usr1` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `usr2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `readed` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id_pm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__profiles`
--

CREATE TABLE IF NOT EXISTS `demosite__profiles` (
  `username` varchar(30) NOT NULL,
  `registration_at` datetime NOT NULL,
  `login_at` datetime NOT NULL,
  `registration_ip` varchar(255) NOT NULL,
  `activity_at` datetime NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__roles`
--

CREATE TABLE IF NOT EXISTS `demosite__roles` (
  `role` varchar(20) NOT NULL,
  `rolename` varchar(255) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__styles`
--

CREATE TABLE IF NOT EXISTS `demosite__styles` (
  `stylename` varchar(255) CHARACTER SET utf8 NOT NULL,
  `styledescription` text CHARACTER SET utf8 NOT NULL,
  `styleview` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__trade_operations`
--

CREATE TABLE IF NOT EXISTS `demosite__trade_operations` (
  `operation` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `dtype` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`operation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__trade_operations_details`
--

CREATE TABLE IF NOT EXISTS `demosite__trade_operations_details` (
  `operation` varchar(255) NOT NULL,
  `artikul` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`operation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__trade_sales`
--

CREATE TABLE IF NOT EXISTS `demosite__trade_sales` (
  `date` datetime NOT NULL,
  `username` varchar(255) NOT NULL,
  `artikul` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `count` int(11) NOT NULL,
  `buyer` text NOT NULL,
  `totally` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__trade_sklad`
--

CREATE TABLE IF NOT EXISTS `demosite__trade_sklad` (
  `artikul` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `captiontxt` text NOT NULL,
  `description` text NOT NULL,
  `note` text NOT NULL,
  `count` int(11) NOT NULL,
  `price` float NOT NULL,
  `photo` varchar(255) NOT NULL,
  `visitors` varchar(11) NOT NULL DEFAULT '0',
  `tags` text NOT NULL,
  `see_also` varchar(255) NOT NULL,
  PRIMARY KEY (`artikul`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `demosite__trade_sklad`
--

INSERT INTO `demosite__trade_sklad` (`artikul`, `type`, `captiontxt`, `description`, `note`, `count`, `price`, `photo`, `visitors`, `tags`, `see_also`) VALUES
('verona03_v', 'shpon', 'Дверное полотно Верона 03 (стекло матовое) . Цвет: Венге', '<p style="text-align: justify; ">\r\n	<span style="font-family: georgia, serif; font-size: 18px; text-align: justify; ">Двери этой серии &quot;Verona&quot;- комбинированные, т.е. в каждой модели присутствуют стекла и филенки. Конструкционной особенностью полотен является отсутствие поперечных и вертикальных соединительных элементов. Стекла и филенки образуют единую плоскость. Верхние и нижние перегородки дверей могут быть в прямоугольном или конусном исполнениях.</span></p>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Покрытие: </em>Экошпон</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Тип профиля:</em> Прямой, конусный</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Размеры полотен:</em> 2000х600, 2000х700, 2000х800, 2000х900 мм&nbsp;</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Толщина полотна: </em>44 мм</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Остекление:</em> матированное белое</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Упаковка:</em>стрейч пленка, гофрокартон&nbsp;</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Рекомендуемая комплектация:</em> Телескопические погонажные изделия</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Цветовые решения:</em> венге, беленый дуб</span></span></div>\r\n', 'Для заметок...', 9999, 6300, '/fotos/verona03_v.jpg', '53', 'Венге, остекленная, Verda, экошпон', 'verona03_b'),
('verona04_b', 'shpon', 'Дверное полотно Верона 04 (стекло матовое) . Цвет: Беленый дуб', '<p>\r\n	<span style="font-family: georgia, serif; font-size: 18px; text-align: justify; ">Двери этой серии &quot;Verona&quot;- комбинированные, т.е. в каждой модели присутствуют стекла и филенки. Конструкционной особенностью полотен является отсутствие поперечных и вертикальных соединительных элементов. Стекла и филенки образуют единую плоскость. Верхние и нижние перегородки дверей могут быть в прямоугольном или конусном исполнениях.</span></p>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Покрытие:</em> Экошпон</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Тип профиля:</em> Прямой, конусный</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Размеры полотен:</em>2000х600, 2000х700, 2000х800, 2000х900 мм&nbsp;</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Толщина полотна:</em> 44 мм</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Остекление: </em>матированное белое</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Упаковка</em>:стрейч пленка, гофрокартон&nbsp;</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Рекомендуемая комплектация</em>: Телескопические погонажные изделия</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Цветовые решения:</em> венге, беленый дуб</span></span></div>\r\n', '', 9999, 6300, '/fotos/verona04_b.jpg', '0', 'Беленый дуб, остекленная, Verda, экошпон', 'verona04_v'),
('verona03_b', 'shpon', 'Дверное полотно Верона 03 (стекло матовое) . Цвет: Беленый дуб', '<p>\r\n	&nbsp;</p>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;">Двери этой серии &quot;Verona&quot;- комбинированные, т.е. в каждой модели присутствуют стекла и филенки. Конструкционной особенностью полотен является отсутствие поперечных и вертикальных соединительных элементов. Стекла и филенки образуют единую плоскость. Верхние и нижние перегородки дверей могут быть в прямоугольном или конусном исполнениях.</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Покрытие:</em> Экошпон</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Тип профиля: </em>Прямой, конусный</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Размеры полотен:</em>2000х600, 2000х700, 2000х800, 2000х900 мм&nbsp;</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Толщина полотна:</em> 44 мм</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Остекление:</em> матированное белое</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Упаковка:</em>стрейч пленка, гофрокартон&nbsp;</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Рекомендуемая комплектация</em>: Телескопические погонажные изделия</span></span></div>\r\n<div style="text-align: justify; ">\r\n	&nbsp;</div>\r\n<div style="text-align: justify; ">\r\n	<span style="font-size:18px;"><span style="font-family:georgia,serif;"><em>Цветовые решения</em>: венге, беленый дуб</span></span></div>\r\n', '', 0, 6300, '/fotos/verona03_b.jpg', '8', 'Беленый дуб, остекленная, Verda, экошпон', 'verona03_v');

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__trade_sklad_calls`
--

CREATE TABLE IF NOT EXISTS `demosite__trade_sklad_calls` (
  `callid` int(11) NOT NULL,
  `callfrom` text CHARACTER SET utf8 NOT NULL,
  `callto` text CHARACTER SET utf8 NOT NULL,
  `callmsg` text CHARACTER SET utf8 NOT NULL,
  `calldt` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`callid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__trade_structure`
--

CREATE TABLE IF NOT EXISTS `demosite__trade_structure` (
  `category` varchar(255) NOT NULL,
  `catname` varchar(255) NOT NULL,
  `parent` varchar(255) NOT NULL,
  PRIMARY KEY (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `demosite__trade_structure`
--

INSERT INTO `demosite__trade_structure` (`category`, `catname`, `parent`) VALUES
('shpon', 'Шпон', '');

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__useroptions`
--

CREATE TABLE IF NOT EXISTS `demosite__useroptions` (
  `username` varchar(32) NOT NULL,
  `optname` varchar(34) NOT NULL,
  `optvalue` text NOT NULL,
  PRIMARY KEY (`username`,`optname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `demosite__users`
--

CREATE TABLE IF NOT EXISTS `demosite__users` (
  `user` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ukey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `online` int(11) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `demosite__users`
--

INSERT INTO `demosite__users` (`user`, `ukey`, `role`, `online`) VALUES
('admin', 'd7ace43aa761e31d0fe19ae0cd7dea68', 'admin', 0);

CREATE TABLE IF NOT EXISTS `demosite__translations` (
  `id` varchar(32) NOT NULL,
  `lang` varchar(34) NOT NULL,
  `caption` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `demosite__voc` (
  `lang` varchar(255) NOT NULL,
  `orig` varchar(255) NOT NULL,
  `wrd` varchar(255) NOT NULL,
  PRIMARY KEY (`orig`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

