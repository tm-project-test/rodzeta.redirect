<?php
/***********************************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\String;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_REDIRECT_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_REDIRECT_MAIN_TAB_TITLE_SET"),
  ),
));

?>

<?= BeginNote() ?>
<p>
	<b>Как работает</b>
	<ul>
		<li>загрузите или создайте файл <b><a href="<?= \Rodzeta\Redirect\Utils::SRC_NAME ?>">rodzeta.redirects.csv</a></b> в папке /upload/ с помощью
			<a target="_blank" href="/bitrix/admin/fileman_file_edit.php?path=<?= urlencode(\Rodzeta\Redirect\Utils::SRC_NAME) ?>">стандартного файлового менеджера</a>;
		<li>формат файла: 2 колонки ("Откуда" "Куда"), разделитель полей - табуляция, первая строка - наименования полей;
		<li>после изменений в файле rodzeta.redirects.csv - нажмите в настройке модуля кнопку "Применить настройки";
	</ul>
</p>
<p>
	Для отключения редиректов из csv-файла нажмите "Сбросить кеш редиректов".
</p>
<?= EndNote() ?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.redirect", "redirect_www", $request->getPost("redirect_www"));
		Option::set("rodzeta.redirect", "redirect_https", $request->getPost("redirect_https"));
		Option::set("rodzeta.redirect", "redirect_slash", $request->getPost("redirect_slash"));

		\Rodzeta\Redirect\Utils::createCache();

		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_REDIRECT_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}	else if ($request->getPost("clear") != "") {
		\Rodzeta\Redirect\Utils::clearMap();

		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_REDIRECT_OPTIONS_RESETED"),
	    "TYPE" => "OK",
	  ));
	}
}



$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?> type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Использовать редирект с www на без www,<br>
				<b>www.</b>example.org -> example.org</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_www" value="Y" type="checkbox"
				<?= Option::get("rodzeta.redirect", "redirect_www") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Использовать редирект с http на https,<br>
			 <b>http</b>://example.org -> <b>https</b>://example.org</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_https" value="Y" type="checkbox"
				<?= Option::get("rodzeta.redirect", "redirect_https") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Использовать редирект со страниц без слеша на слеш,<br>
			 /catalog -> <b>/catalog/</b></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_slash" value="Y" type="checkbox"
				<?= Option::get("rodzeta.redirect", "redirect_slash") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">
  <input type="submit" name="clear" value="Сбросить кеш редиректов">

</form>

<?php

$tabControl->end();
