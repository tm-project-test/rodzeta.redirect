<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

use Bitrix\Main\{Application, Localization\Loc};

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

// TODO заменить на определение доступа к редактированию контента
// 	if (!$USER->CanDoOperation("rodzeta.siteoptions"))
if (!$GLOBALS["USER"]->IsAdmin()) {
	//$APPLICATION->authForm("ACCESS DENIED");
  return;
}

Loc::loadMessages(__FILE__);
//Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . ID . "/admin/" . ID . "/index.php");

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

StorageInit();

$formSaved = check_bitrix_sessid() && $request->isPost();
if ($formSaved) {
	$data = $request->getPostList();
	Options\Update($request->getPostList());
}

$currentOptions = Options\Select();

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<div class="adm-detail-title">Настройка типовых редиректов</div>

	<table width="100%">
		<tbody>
			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>Редирект с www на без www,<br>
						<b>www.</b>example.org -> example.org</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_www" value="Y" type="checkbox"
						<?= $currentOptions["redirect_www"] == "Y"? "checked" : "" ?>>
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
							<?= $currentOptions["redirect_https"] == ""? "checked" : "" ?>> не использовать
					</label>
					<br>
					<label>
						<input name="redirect_https" value="to_https" type="radio"
							<?= $currentOptions["redirect_https"] == "to_https"? "checked" : "" ?>> на https://*
					</label>
					<br>
					<label>
						<input name="redirect_https" value="to_http" type="radio"
							<?= $currentOptions["redirect_https"] == "to_http"? "checked" : "" ?>> на http://*
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
						<?= $currentOptions["redirect_slash"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>Редирект со страниц <b>*/index.php</b> на <b>*/</b>,<br>
					 /about/index.php -> <b>/about/</b></label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_index" value="Y" type="checkbox"
						<?= $currentOptions["redirect_index"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>Редирект с удалением множественных слешей,<br>
					 //news///index.php -> <b>/news/</b></label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_multislash" value="Y" type="checkbox"
						<?= $currentOptions["redirect_multislash"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>
		</tbody>
	</table>

	<br>
	<p>
		&nbsp;&nbsp;&nbsp;Путь к CSV файлу:
		<?= str_replace($_SERVER["DOCUMENT_ROOT"], "", FILE_REDIRECTS) ?>
	</p>

</form>

<?php if (0 && $formSaved) { ?>

	<script>
		// close after submit
		top.BX.WindowManager.Get().AllowClose();
		top.BX.WindowManager.Get().Close();
	</script>

<?php } else { ?>

	<script>
		// add buttons for current windows
		BX.WindowManager.Get().SetButtons([
			BX.CDialog.prototype.btnSave,
			BX.CDialog.prototype.btnCancel
			//,BX.CDialog.prototype.btnClose
		]);
	</script>

<?php } ?>
