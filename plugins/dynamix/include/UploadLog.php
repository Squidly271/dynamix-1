<?
$url = "http://gui.us.to:8088";

function sendPaste($text, $title) {
  global $url;
  $data = array('data'     => $text,
                'language' => 'text',
                'title'    => '[unRAID] '.$title,
                'private'  => true,
                'expire'   => '2592000');
  $tmpfile = "/tmp/tmp-".mt_rand().".json";
  file_put_contents($tmpfile, json_encode($data));
  $out = shell_exec("curl -s -k -L -X POST -H 'Content-Type: application/json' -d '@$tmpfile' ${url}/api/json/create");
  unlink($tmpfile);
  $out = json_decode($out, TRUE);
  if (isset($out['result'])){
    $resp = "${url}/".$out['result']['id']."/".$out['result']['hash'];
    echo $resp;
    require_once("/usr/local/emhttp/webGui/include/Wrappers.php");
    $notify = "/usr/local/sbin/notify";
    $server = strtoupper($var['NAME']);
    exec("$notify -e '$title uploaded - [".$out['result']['id']."]' -s 'Notice [$server] - $title uploaded.' -d 'A new copy of $title has been uploaded: $resp' -i 'normal 3' -x");
  }
}
if ($_POST['pastebin']){
  $title = $_POST['title'];
  $text  = $_POST['text'];
  switch ($_POST['type']) {
    case 'file':
      sendPaste(file_get_contents($text),$title);
      break;
    case 'command':
      sendPaste(shell_exec($text." 2>&1"), $title);
      break;
    case 'text':
      sendPaste($text, $title);
      break;
  }
}
?>