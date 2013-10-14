<?php
#echo '<h1 style="color: #ff0000;">VIDEOMODULEN KAN VÆRE UTILGJENGELIG DELER AV ONSDAG 12.JUNI PGA FORBEREDELSER TIL FESTIVALEN</h1>';
switch($_GET['list']) {
	case 1:
		require_once('gui_forestilling.inc.php');
		break;
	case 2:
		require_once('gui_monstring.inc.php');
		break;
	case 3:
		require_once('gui_rep.inc.php');
		break;
}
?>