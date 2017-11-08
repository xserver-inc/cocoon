<?php //リセットの実行
if (isset($_POST[OP_RESET_ALL_SETTINGS]) && isset($_POST[OP_CONFIRM_RESET_ALL_SETTINGS])) {
  reset_all_settings();
}