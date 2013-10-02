<?php
/**
 * @file user_utils.php
 * @brief nástroje pro přihlašování uživatelů apod...
 * @author Michal Podzimek
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_users Uživatelé
 * @{
 *
 *
 */


/**
 * @brief Generuje náhodné heslo
 *
 * @param $password_length délka hesla
 */
function generate_password($password_length) {
        /* znaky, které se použijí při generování hesla */
        $chars = 'abcdefghijklmnopqrstuvwxyz'.
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
                '0123456789';

        $password = "";

        while (strlen($password) < $password_length) {
                $password .= substr($chars, rand(0, strlen($chars) - 1), 1);
        }
        return($password);
}

/**
 * @brief Vrať pole se jménem souboru (url) s ikonkou a rozměry (3).
 *
 * @param db spoj. s db
 * @param id id uživatele
 */
function get_icon_info($db, $id, $default) {

	global $ICON_PATH,$APP_ROOT,$DIRECTORY;
	$exts = array('jpg','png');

	foreach ($exts as $ext) {
		$file = "${DIRECTORY}$ICON_PATH/$id.jpg";
		if (file_exists($file)) {
			$img = getimagesize($file);
			$img['url'] = "$APP_ROOT/$ICON_PATH/$id.jpg";
			$img['attr'] = $img[3];
			return $img;
		}
	}

	$file = "${DIRECTORY}skins/default/icons/${default}.png";
	$img = getimagesize($file);
	$img['url'] = "$APP_ROOT/skins/default/icons/${default}.png";
	$img['attr'] = $img[3];
	return $img;
}

/**
 * @}
 * @}
 */

?>