<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new \CAdminTabControl("tabControl", [
  [
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_REDIRECT_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_REDIRECT_MAIN_TAB_TITLE_SET"),
  ],
  [
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_REDIRECT_URLS_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_REDIRECT_URLS_TAB_TITLE_SET", [
			"#FILE#" => Utils::SRC_NAME
		]),
  ],
]);

?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.redirect", "redirect_www", $request->getPost("redirect_www"));
		Option::set("rodzeta.redirect", "redirect_https", $request->getPost("redirect_https"));
		Option::set("rodzeta.redirect", "redirect_slash", $request->getPost("redirect_slash"));
		Option::set("rodzeta.redirect", "redirect_index", $request->getPost("redirect_index"));
		Option::set("rodzeta.redirect", "redirect_multislash", $request->getPost("redirect_multislash"));

		Utils::saveToCsv($request->getPost("redirect_urls"));
		Utils::createCache();

		\CAdminMessage::showMessage([
	    "MESSAGE" => Loc::getMessage("RODZETA_REDIRECT_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ]);
	}	else if ($request->getPost("clear") != "") {
		Utils::clearMap();

		\CAdminMessage::showMessage([
	    "MESSAGE" => Loc::getMessage("RODZETA_REDIRECT_OPTIONS_RESETED"),
	    "TYPE" => "OK",
	  ]);
	}
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?> type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Редирект с www на без www,<br>
				<b>www.</b>example.org -> example.org</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_www" value="Y" type="checkbox"
				<?= Option::get("rodzeta.redirect", "redirect_www") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Редирект http <-> https,<br>
			 <b>http</b>://example.org <-> <b>https</b>://example.org</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<label>
				<input name="redirect_https" value="" type="radio"
					<?= Option::get("rodzeta.redirect", "redirect_https") == ""? "checked" : "" ?>> не использовать
			</label>
			<br>
			<label>
				<input name="redirect_https" value="to_https" type="radio"
					<?= Option::get("rodzeta.redirect", "redirect_https") == "to_https"? "checked" : "" ?>> на https://*
			</label>
			<br>
			<label>
				<input name="redirect_https" value="to_http" type="radio"
					<?= Option::get("rodzeta.redirect", "redirect_https") == "to_http"? "checked" : "" ?>> на http://*
			</label>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Редирект со страниц без слеша на слеш,<br>
			 /catalog -> <b>/catalog/</b></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_slash" value="Y" type="checkbox"
				<?= Option::get("rodzeta.redirect", "redirect_slash") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Редирект со страниц <b>*/index.php</b> на <b>*/</b>,<br>
			 /about/index.php -> <b>/about/</b></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_index" value="Y" type="checkbox"
				<?= Option::get("rodzeta.redirect", "redirect_index") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Редирект с удалением множественных слешей,<br>
			 //news///index.php -> <b>/news/</b></label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_multislash" value="Y" type="checkbox"
				<?= Option::get("rodzeta.redirect", "redirect_multislash") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">

			<table width="100%">
				<tbody>

					<?php
					$i = 0;
					foreach (Utils::getMapFromCsv() as $urlFrom => $urlTo) {
						$i++;
					?>
						<tr>
							<td>
								<input type="text" placeholder="Откуда"
									name="redirect_urls[<?= $i ?>][0]"
									value="<?= htmlspecialcharsex($urlFrom) ?>"
									style="width:96%;">
							</td>
							<td>
								<input type="text" placeholder="Куда"
									name="redirect_urls[<?= $i ?>][1]"
									value="<?= htmlspecialcharsex($urlTo) ?>"
									style="width:96%;">
							</td>
						</tr>
					<?php } ?>

					<?php foreach (range(1, 20) as $n) {
						$i++;
					?>
						<tr>
							<td>
								<input type="text" placeholder="Откуда"
									name="redirect_urls[<?= $i ?>][0]"
									value=""
									style="width:96%;">
							</td>
							<td>
								<input type="text" placeholder="Куда"
									name="redirect_urls[<?= $i ?>][1]"
									value=""
									style="width:96%;">
							</td>
						</tr>
					<?php } ?>


				</tbody>
			</table>

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
