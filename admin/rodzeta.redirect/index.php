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

/*
$formSaved = check_bitrix_sessid() && $request->isPost();
if ($formSaved) {
	$data = $request->getPostList();
	Options\Update($request->getPostList());
}

$currentOptions = Options\Select();
$currentOptions["fields"] = array_merge(
	[
		"AUTHOR" => ["AUTHOR", "Ваше имя"],
		"AUTHOR_EMAIL" => ["AUTHOR_EMAIL", "Ваш e-mail"],
		"TEXT" => ["TEXT", "Ваше сообщение"],
		//
		"USER_REGION" => ["USER_REGION", "Регион"],
		"USER_PHONE" => ["USER_PHONE", "Телефон"],
		"USER_SITE" => ["USER_SITE", "Сайт"],
	],
	$currentOptions["fields"]
);

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<div class="adm-detail-title">Список кодов для дополнительных полей</div>

	<table width="100%" class="js-table-autoappendrows">
		<tbody>
			<?php
				$i = 0;
				foreach (AppendValues($currentOptions["fields"], 5, ["", ""]) as $i => $field) { $i++;
					$readonly = ($field[0] == "AUTHOR"
						|| $field[0] == "AUTHOR_EMAIL"
						|| $field[0] == "TEXT")? "readonly" : "";
			?>
				<tr data-idx="<?= $i ?>">
					<td>
						<input type="text" placeholder="Код поля"
							name="fields[<?= $i ?>][0]"
							value="<?= htmlspecialcharsex($field[0]) ?>"
							<?= $readonly ?> style="width:96%;">
					</td>
					<td>
						<input type="text" placeholder="Название поля"
							name="fields[<?= $i ?>][1]"
							value="<?= htmlspecialcharsex($field[1]) ?>"
							style="width:96%;">
					</td>
					<td>
						<input type="checkbox" style="margin-top:6px;"
							title="Добавлять в CSV"
							name="fields[<?= $i ?>][2]"
							value="Y" <?= $field[2] == "Y"? "checked" : "" ?>>
					</td>
					<td>
						<select name="fields[<?= $i ?>][3]" style="width:96%;">
							<option value="">Код поля для лида Bitrix24</option>
							<?php foreach ([
										"COMPANY_TITLE",
										"NAME",
										"LAST_NAME",
										"SECOND_NAME",
										"POST",
										"ADDRESS",
										"COMMENTS",
										"SOURCE_DESCRIPTION",
										"STATUS_DESCRIPTION",
										"OPPORTINUTY",
										"CURRENCY_ID",
										"PRODUCT_ID",
										"SOURCE_ID",
										"STATUS_ID",
										"ASSIGNED_BY_ID",
										"PHONE_WORK",
										"PHONE_MOBILE",
										"PHONE_FAX",
										"PHONE_HOME",
										"PHONE_PAGER",
										"PHONE_OTHER",
										"WEB_WORK",
										"WEB_HOME",
										"WEB_FACEBOOK",
										"WEB_LIVEJOURNAL",
										"WEB_TWITTER",
										"WEB_OTHER",
										"EMAIL_WORK",
										"EMAIL_HOME",
										"EMAIL_OTHER",
										"IM_SKYPE",
										"IM_ICQ",
										"IM_MSN",
										"IM_JABBER",
										"IM_OTHER",
									] as $lidFieldCode) { ?>
								<option value="<?= $lidFieldCode ?>"
									<?= $lidFieldCode == $field[3]?
										"selected" : "" ?>><?= $lidFieldCode ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<p>
		&nbsp;&nbsp;&nbsp;Путь к CSV файлу:
		<?= str_replace($_SERVER["DOCUMENT_ROOT"], "", FILE_FORMS) ?>
	</p>

	<br>
	<div class="adm-detail-title">Данные аккаунта Bitrix24</div>

	<table width="100%">
		<tr>
			<td>
				<input type="text" size="30" name="bitrix24_portal_url"
					value="<?= htmlspecialcharsex($currentOptions["bitrix24"]["portal_url"]) ?>"
					style="width:96%;"
					placeholder="Адрес портала Bitrix24">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="text" size="30" name="bitrix24_login"
					value="<?= htmlspecialcharsex($currentOptions["bitrix24"]["login"]) ?>"
					style="width:96%;"
					placeholder='LOGIN пользователя-"лидогенератора"'>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input name="bitrix24_password" size="30" type="password"
					style="width:96%;"
					readonly
	    		onfocus="this.removeAttribute('readonly')"
	    		value="<?= htmlspecialcharsex($currentOptions["bitrix24"]["password"]) ?>"
	    		placeholder='PASSWORD пользователя-"лидогенератора"'>
			</td>
		</tr>
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

<script>

BX.ready(function () {
	"use strict";

	// init options
	//...

	// autoappend rows
	function makeAutoAppend($table) {
		function bindEvents($row) {
			for (let $input of $row.querySelectorAll('input[type="text"]')) {
				$input.addEventListener("change", function (event) {
					let $tr = event.target.closest("tr");
					let $trLast = $table.rows[$table.rows.length - 1];
					if ($tr != $trLast) {
						return;
					}
					$table.insertRow(-1);
					$trLast = $table.rows[$table.rows.length - 1];
					$trLast.innerHTML = $tr.innerHTML;
					let idx = parseInt($tr.getAttribute("data-idx")) + 1;
					$trLast.setAttribute("data-idx", idx);
					for (let $input of $trLast.querySelectorAll("input,select")) {
						let name = $input.getAttribute("name");
						if (name) {
							$input.setAttribute("name", name.replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
						}
					}
					bindEvents($trLast);
				});
			}
		}
		for (let $row of document.querySelectorAll(".js-table-autoappendrows tr")) {
			bindEvents($row);
		}
	}
	for (let $table of document.querySelectorAll(".js-table-autoappendrows")) {
		makeAutoAppend($table);
	}

});

</script>

<?php */
