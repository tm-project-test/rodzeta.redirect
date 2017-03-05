<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

// TODO use rights "can edit content"
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

$formSaved = check_bitrix_sessid() && $request->isPost();
if ($formSaved) {
	$data = $request->getPostList();
	OptionsUpdate($request->getPostList());
}

$currentOptions = Options();

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<div class="adm-detail-title">
		<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_TITLE") ?>
	</div>

	<table width="100%">
		<tbody>
			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_WWW_TITLE") ?>
					</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_www" value="Y" type="checkbox"
						<?= $currentOptions["redirect_www"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_TITLE") ?>
					</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<label>
						<input name="redirect_https" value="" type="radio"
							<?= $currentOptions["redirect_https"] == ""? "checked" : "" ?>>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_NO") ?>
					</label>
					<br>
					<label>
						<input name="redirect_https" value="to_https" type="radio"
							<?= $currentOptions["redirect_https"] == "to_https"? "checked" : "" ?>>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_HTTPS") ?>
					</label>
					<br>
					<label>
						<input name="redirect_https" value="to_http" type="radio"
							<?= $currentOptions["redirect_https"] == "to_http"? "checked" : "" ?>>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_HTTP") ?>
					</label>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_SLASH_TITLE") ?>
					</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_slash" value="Y" type="checkbox"
						<?= $currentOptions["redirect_slash"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_INDEX_TITLE") ?>
					</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_index" value="Y" type="checkbox"
						<?= $currentOptions["redirect_index"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>
						<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_MULTISLASH_TITLE") ?>
					</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_multislash" value="Y" type="checkbox"
						<?= $currentOptions["redirect_multislash"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>
						<?= Loc::getMessage("RODZETA_REDIRECT_IGNORE_QUERY") ?>
					</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="ignore_query" value="Y" type="checkbox"
						<?= $currentOptions["ignore_query"] == "Y"? "checked" : "" ?>>
				</td>
			</tr>

			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<label>
						<?= Loc::getMessage("RODZETA_REDIRECT_URLS_TITLE") ?>
					</label>
				</td>
				<td class="adm-detail-content-cell-r" width="50%">
					<input name="redirect_urls" value="Y" type="checkbox"
						<?= $currentOptions["redirect_urls"] == "Y"? "checked" : "" ?>>
					<?= str_replace($_SERVER["DOCUMENT_ROOT"], "", FILE_REDIRECTS) ?>
				</td>
			</tr>
		</tbody>
	</table>

</form>

<?php if ($formSaved) { ?>

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
