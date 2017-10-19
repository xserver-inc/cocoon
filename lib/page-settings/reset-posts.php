<?php //リセットの実行
if ($_POST[OP_RESET_ALL_SETTINGS] && $_POST[OP_CONFIRM_RESET_ALL_SETTINGS]) {
  reset_all_settings();
}