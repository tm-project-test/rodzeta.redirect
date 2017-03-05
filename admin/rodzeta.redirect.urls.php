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

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

$formSaved = check_bitrix_sessid() && $request->isPost();
if ($formSaved) {
	Update($request->getPostList());
}

$currentOptions = Select(true);

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<table width="100%" class="js-table-autoappendrows">
		<tbody>
			<?php
			$i = 0;
			foreach (AppendValues($currentOptions, 5, array("", "", "")) as $url) {
				$i++;
			?>
				<tr data-idx="<?= $i ?>">
					<td>
						<input type="text" placeholder="<?=
								Loc::getMessage("RODZETA_REDIRECT_URLS_FROM") ?>"
							name="redirect_urls[<?= $i ?>][0]"
							value="<?= htmlspecialcharsex($url[0]) ?>"
							style="width:96%;">
					</td>
					<td>
						<input type="text" placeholder="<?=
								Loc::getMessage("RODZETA_REDIRECT_URLS_TO") ?>"
							name="redirect_urls[<?= $i ?>][1]"
							value="<?= htmlspecialcharsex($url[1]) ?>"
							style="width:96%;">
					</td>
					<td>
						<select name="redirect_urls[<?= $i ?>][2]"
								title="<?= Loc::getMessage("RODZETA_REDIRECT_URLS_STATUS") ?>"
								style="width:96%;">
							<option value="301" <?= $url[2] == "301"? "selected" : "" ?>>301</option>
							<option value="302" <?= $url[2] == "302"? "selected" : "" ?>>302</option>
						</select>
					</td>
					<td>
						<input name="redirect_urls[<?= $i ?>][3]" value="Y" type="checkbox"
							title="<?= Loc::getMessage("RODZETA_REDIRECT_URLS_IS_PART_URL") ?>"
							<?= $url[3] == "Y"? "checked" : "" ?>>
					</td>
				</tr>
			<?php } ?>
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
