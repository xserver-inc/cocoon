<?php //edump用のデバッグコード
/*** Paste this code to be able to access the edump debugger's features ***********************************************/
$YourMessageID = "dQzSkaX8lahNLqMaNdYnWOPL"; $ShowDetails = "false"; $AutoClear = "false"; $SSL = "true"; $Enable = "true";
$h="www.edump.net";$t="/sv/dist/php/include.php?id=".$YourMessageID."&sd=".$ShowDetails."&ac=".$AutoClear."&ssl="
.$SSL."&fl=".$Enable;$f=fsockopen($h, 80, $ern, $ert, 30);if(!$f){echo "$ert($ern)";}else{$o="GET ".$t." HTTP/1.1\r\n";
$o.="Host: ".$h."\r\n";$o.="Connection: Close\r\n\r\n"; $r = '';fwrite($f, $o);while(!feof($f)){$r.= fgets($f,1024);}
fclose($f);}$li=explode("###INCLUDE CODE###", $r);eval($li[1]);unset($h,$t,$ern,$ert,$f,$o,$r,$li);
/**********************************************************************************************************************/