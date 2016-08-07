
# Скрипт редиректа с указанного урл (старого) на новый

- cписок редиректов в файле config.php
- /api/redirect/add/ добавление списка из redirects.csv (1 строка как заголовок - пропускается) в config.php

## Способы настройки

1) прописать в главном файле или в общем подключаемом файле скрипта/cms/etc.

    include $_SERVER["DOCUMENT_ROOT"] . "/api/redirect/init.php";

2) php.ini

    auto_prepend_file = "/path/to/api/redirect/init.php"

3) в конфиге .htaccess

    php_value auto_prepend_file /path/to/api/redirect/init.php
