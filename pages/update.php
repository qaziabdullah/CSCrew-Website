<?php

if(!function_exists("Path")) {
    return;
}

if(!isset($_SESSION['steamid'])) {
    echo 'steamid';
    exit;
}

/************/
/* Database */
/************/

$conn = require_once('imports/database.php');
if($conn != 1) {
    echo 'database';
    exit;
}

/***************/
/* Update skin */
/***************/

// team
// type
// index
// paint
// wear
// seed
// nametag
// stattrak
// stickers
// keychains

$_POST['nametag']=='false'?$_POST['nametag'] = NULL:false;

try {
switch($_POST['type']) {
    case 'gloves':
        if($_POST['paint'] == 'ct') {
            $state = $pdo->prepare("DELETE FROM `wp_player_gloves` WHERE `steamid` = ? AND `weapon_team` = 3");
            $state->execute([$_SESSION['steamid']]);
            return;
        }else if($_POST['paint'] == 't') {
            $state = $pdo->prepare("DELETE FROM `wp_player_gloves` WHERE `steamid` = ? AND `weapon_team` = 2");
            $state->execute([$_SESSION['steamid']]);
            return;
        }

        $state = $pdo->prepare("SELECT * FROM `wp_player_gloves` WHERE `steamid` = ? AND `weapon_team` = ?");
        $state->execute([$_SESSION['steamid'], $_POST['team']]);
        $exists = $state->fetch();

        if($exists) {
            $state = $pdo->prepare("UPDATE `wp_player_gloves` SET  `weapon_defindex` = ? WHERE `steamid` = ? AND `weapon_team` = ?");
            $state->execute([$_POST['index'], $_SESSION['steamid'], $_POST['team']]);
        }else {
            $state = $pdo->prepare("INSERT INTO `wp_player_gloves`(`steamid`, `weapon_team`, `weapon_defindex`) VALUES(?,?,?)");
            $state->execute([$_SESSION['steamid'], $_POST['team'], $_POST['index']]);
        }

        $state = $pdo->prepare("DELETE FROM `wp_player_skins` WHERE `steamid` = ? AND `weapon_team` = ? AND `weapon_defindex` = ?");
        $state->execute([$_SESSION['steamid'], $_POST['team'], $_POST['index']]);

        $state = $pdo->prepare("INSERT INTO `wp_player_skins` VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $state->execute([
            $_SESSION['steamid'], $_POST['team'], $_POST['index'], $_POST['paint'],
            $_POST['wear'], $_POST['seed'], $_POST['nametag'], $_POST['stattrak'],
            0, "0;0;0;0;0;0;0", "0;0;0;0;0;0;0", "0;0;0;0;0;0;0",
            "0;0;0;0;0;0;0", "0;0;0;0;0;0;0", "0;0;0;0;0"
        ]);
        break;
    case 'agents':
        $state = $pdo->prepare("SELECT * FROM `wp_player_agents` WHERE `steamid` = ?");
        $state->execute([$_SESSION['steamid']]);
        $exists = $state->fetch();

        if($_POST['index'] == 'default') {
            if($_POST['team'] == 2 && !isset($exists['agent_ct']) || $_POST['team'] == 3 && !isset($exists['agent_t'])) {
                $state = $pdo->prepare("DELETE FROM `wp_player_agents` WHERE `steamid` = ?");
            }else if($_POST['team'] == 2) {
                $state = $pdo->prepare("UPDATE `wp_player_agents` SET `agent_t` = NULL WHERE `steamid` = ?");
            }else if($_POST['team'] == 3) {
                $state = $pdo->prepare("UPDATE `wp_player_agents` SET `agent_ct` = NULL WHERE `steamid` = ?");
            }

            $state->execute([$_SESSION['steamid']]);
            return;
        }

        if($exists) {
            if($_POST['team'] == 2) {
                $state = $pdo->prepare("UPDATE `wp_player_agents` SET `agent_t` = ? WHERE `steamid` = ?");
            }else {
                $state = $pdo->prepare("UPDATE `wp_player_agents` SET `agent_ct` = ? WHERE `steamid` = ?");
            }
            $state->execute([$_POST['index'], $_SESSION['steamid']]);
        }else {
            if($_POST['team'] == 2) {
                $state = $pdo->prepare("INSERT INTO `wp_player_agents`(`steamid`, `agent_t`) VALUES(?,?)");
            }else {
                $state = $pdo->prepare("INSERT INTO `wp_player_agents`(`steamid`, `agent_ct`) VALUES(?,?)");
            }
            $state->execute([$_SESSION['steamid'], $_POST['index']]);
        }
        break;
    case 'mvp':
        $state = $pdo->prepare("SELECT * FROM `wp_player_music` WHERE `steamid` = ? AND `weapon_team` = ?");
        $state->execute([$_SESSION['steamid'], $_POST['team']]);
        $exists = $state->fetch();

        if($exists) {
            $state = $pdo->prepare("UPDATE `wp_player_music` SET  `music_id` = ? WHERE `steamid` = ? AND `weapon_team` = ?");
            $state->execute([$_POST['index'], $_SESSION['steamid'], $_POST['team']]);
        }else {
            $state = $pdo->prepare("INSERT INTO `wp_player_music`(`steamid`, `weapon_team`, `music_id`) VALUES(?,?,?)");
            $state->execute([$_SESSION['steamid'], $_POST['team'], $_POST['index']]);
        }
        break;
	case 'custom_mvp':
		$state = $pdo->prepare("SELECT * FROM `PersonData` WHERE `PlayerSteamID` = ?");
		$state->execute([$_SESSION['steamid']]);
		$exists = $state->fetch();

		if($exists) {
			$state = $pdo->prepare("UPDATE `PersonData` SET `MusicKit` = ? WHERE `PlayerSteamID` = ?");
			$state->execute(['MVP_' . $_POST['index'], $_SESSION['steamid']]);
		} else {
			$state = $pdo->prepare("INSERT INTO `PersonData`(`PlayerSteamID`, `MusicKit`) VALUES(?, ?)");
			$state->execute([$_SESSION['steamid'], 'MVP_' . $_POST['index']]);
		}
		break;
    default:
        if($_POST['type'] == 'knifes') {
            $state = $pdo->prepare("SELECT * FROM `wp_player_knife` WHERE `steamid` = ? AND `weapon_team` = ?");
            $state->execute([$_SESSION['steamid'], $_POST['team']]);
            $exists = $state->fetch();

            if($exists) {
                $state = $pdo->prepare("UPDATE `wp_player_knife` SET `knife` = ? WHERE `steamid` = ? AND `weapon_team` = ?");
                $state->execute([$_POST['name'], $_SESSION['steamid'], $_POST['team']]);
            }else {
                $state = $pdo->prepare("INSERT INTO `wp_player_knife` VALUES(?,?,?)");
                $state->execute([$_SESSION['steamid'], $_POST['team'], $_POST['name']]);
            }
        }

        $state = $pdo->prepare("SELECT * FROM `wp_player_skins` WHERE `steamid` = ? AND `weapon_team` = ? AND `weapon_defindex` = ?");
        $state->execute([$_SESSION['steamid'], $_POST['team'], $_POST['index']]);
        $exists = $state->fetch();

        $stickersval = json_decode($_POST['stickers']);
        $keychainsval = json_decode($_POST['keychains']);

        if($exists) {
            $state = $pdo->prepare("UPDATE `wp_player_skins` SET `weapon_paint_id`=?,`weapon_wear`=?,`weapon_seed`=?,`weapon_nametag`=?,`weapon_stattrak`=?,`weapon_sticker_0`=?,`weapon_sticker_1`=?,`weapon_sticker_2`=?,`weapon_sticker_3`=?,`weapon_sticker_4`=?,`weapon_keychain`=? WHERE `steamid`=? AND `weapon_team`=? AND `weapon_defindex`=?");
            $state->execute([
                $_POST['paint'], $_POST['wear'], $_POST['seed'], $_POST['nametag'], $_POST['stattrak'],
                $stickersval[0], $stickersval[1], $stickersval[2],
                $stickersval[3], $stickersval[4], $keychainsval[0],
                $_SESSION['steamid'], $_POST['team'], $_POST['index']
            ]);
        }else {
            $state = $pdo->prepare("INSERT INTO `wp_player_skins` VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $state->execute([
                $_SESSION['steamid'], $_POST['team'], $_POST['index'], $_POST['paint'],
                $_POST['wear'], $_POST['seed'], $_POST['nametag'], $_POST['stattrak'],
                0, $stickersval[0], $stickersval[1], $stickersval[2],
                $stickersval[3], $stickersval[4], $keychainsval[0]
            ]);
        }
        break;
}
}catch(Exception $err) {
    echo $err->getMessage();
}