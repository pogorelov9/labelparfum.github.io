<?php
/**
* Основные параметры WordPress.
*
* Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
* секретные ключи, язык WordPress и ABSPATH. Дополнительную информацию можно найти
* на странице {@link http://codex.wordpress.org/Editing_wp-config.php Editing
* wp-config.php} Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
*
* Этот файл используется сценарием создания wp-config.php в процессе установки.
* Необязательно использовать веб-интерфейс, можно скопировать этот файл
* с именем "wp-config.php" и заполнить значения.
*
* @package WordPress
*/

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'aek23_viet');

/** Имя пользователя MySQL */
define('DB_USER', 'aek23_viet');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'site12345');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
* Уникальные ключи и соли для аутентификации.
*
* Смените значение каждой константы на уникальную фразу.
* Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
* Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется снова авторизоваться.
*
* @since 2.6.0
*/
define('AUTH_KEY',         'D/CW+G1s6tJJ|0$f4]6qK93F|5O2+Z+IG(Iws%kf,1t:aW-}YKNdz9G +BFWw2)Z');
define('SECURE_AUTH_KEY',  ',NtVet+k#i3}kKMb8=B)oXF[9Z.}Q[D|qhC5_xdlg-d;Pr]mnRB@ QGhs r,|O@A');
define('LOGGED_IN_KEY',    'Cz=]-+asB6pT6n9,^ cWZ5gnnZO26QNZ}_qQAx|3,P8)tftnbQA(-!YuAO0]cYP7');
define('NONCE_KEY',        'KSq@ wC Xuk1zSU6~b6&O&%0W1+Vw5FpCjp7%].=/yw.;oNDh&3,[+h:|=1;u4a2');
define('AUTH_SALT',        ';Q|Zb1i:jaWq2OJ,7CF!NH)2p|C#ApKi73 <a?&<j6ao]@U:bNvd/S%QyEZiNVw%');
define('SECURE_AUTH_SALT', 'UC#-Hi#-K26InO{GdVBu|5-*Q9xdmd0vF[jj` SSb88a/s`G0<7YM|afIrbtJ1Cs');
define('LOGGED_IN_SALT',   'h`!8<0+/ 6$uGJ:L[4Q{r]a%zz~R.oK5j6-~1-u~#t^ueJxfZm{J7FZ|G5-}d|ph');
define('NONCE_SALT',       '1kQ}BhSim(EsHm5OR]*VhX>f?|R^$,Fp/?()cU DN:;gylQ[:10L1:rT!f~|kV--');

/**#@-*/

/**
* Префикс таблиц в базе данных WordPress.
*
* Можно установить несколько блогов в одну базу данных, если вы будете использовать
* разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
*/
$table_prefix  = 'wolivep_';

/**
* Язык локализации WordPress, по умолчанию английский.
*
* Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
* для выбранного языка должен быть установлен в wp-content/languages. Например,
* чтобы включить поддержку русского языка, скопируйте ru_RU.mo в wp-content/languages
* и присвойте WPLANG значение 'ru_RU'.
*/
define('WPLANG', 'ru_RU');

/**
* Для разработчиков: Режим отладки WordPress.
*
* Измените это значение на true, чтобы включить отображение уведомлений при разработке.
* Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
* в своём рабочем окружении.
*/
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
