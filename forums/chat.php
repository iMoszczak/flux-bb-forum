<?php
define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);
if ($data["action"] == 1){
     $username = $pun_user['username'];
     $id = $pun_user['id'];
     $gid = $pun_user['group_id'];
     $text = $data['text'];
    $result447 = $db->query("INSERT INTO `ajax_chat_messages` (`id`, `userID`, `userName`, `userRole`, `dateTime`, `text`) VALUES (NULL, '$id', '$username', '$gid', '18:43:29', '$text');") or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
    
}
$result447 = $db->query("SELECT * FROM `ajax_chat_messages`") or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if ($result447->num_rows > 0) {
  while($row = $result447->fetch_assoc()) {
  if ($row["userName"] == 'ChatBot') {
    $text = $row["text"];
    $usernome = $pun_user['username'];
    if (strpos($text, $usernome) !== false)
    {
      echo '<p class="rowHighlight"><span class="dateTime">'. $row["dateTime"] .'</span><a href="profile.php?id='. $row["userID"] .'" target="_blank" class="chatBot"> '. $row["userName"] .'</a>: '.pun_htmlspecialchars($row["text"]).'</p>';
    } else {
      echo '<p class="rowEven"><span class="dateTime">'. $row["dateTime"] .'</span><a href="profile.php?id='. $row["userID"] .'" target="_blank" class="chatBot"> '. $row["userName"] .'</a>: '.pun_htmlspecialchars($row["text"]).'</p>';
    }
  } else {
    $usernome = $pun_user['username'];
        if(strpos($row["text"], $usernome) !== false) {
      echo '<p class="rowHighlight"><span class="dateTime">'. $row["dateTime"] .'</span><a href="profile.php?id='. $row["userID"] .'" target="_blank" class="usergroup-'. $row["userRole"] .'"> '. $row["userName"] .'</a>: '.pun_htmlspecialchars($row["text"]).'</p>';
    } else {
      echo '<p class="rowEven"><span class="dateTime">'. $row["dateTime"] .'</span><a href="profile.php?id='. $row["userID"] .'" target="_blank" class="usergroup-'. $row["userRole"] .'"> '. $row["userName"] .'</a>: '.pun_htmlspecialchars($row["text"]).'</p>';
    }
  }
}
}


?>