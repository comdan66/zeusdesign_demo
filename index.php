<?php
class Tool {
  public static function directoryMap ($sourceDir, $directoryDepth = 0, $hidden = false) {
    if ($fp = @opendir ($sourceDir)) {
      $filedata = array ();
      $new_depth  = $directoryDepth - 1;
      $sourceDir = rtrim ($sourceDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

      while (false !== ($file = readdir ($fp))) {
        if (!trim ($file, '.') || (($hidden == false) && ($file[0] == '.')) || is_link ($file) || ($file == 'cmd')) continue;

        if ((($directoryDepth < 1) || ($new_depth > 0)) && @is_dir ($sourceDir . $file)) $filedata[$file] = Tool::directoryMap ($sourceDir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden);
        else array_push ($filedata, $file);
      }

      closedir ($fp);
      return $filedata;
    }

    return false;
  }
  public static function mergeArrayRecursive ($files, &$a, $k = null) {
    if (!($files && is_array ($files))) return false;
    foreach ($files as $key => $file)
      if (is_array ($file)) $key . Tool::mergeArrayRecursive ($file, $a, ($k ? rtrim ($k, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : '') . $key);
      else array_push ($a, ($k ? rtrim ($k, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : '') . $file);
  }
  public static function readFile ($file) {
    if (!file_exists ($file)) return false;
    if (function_exists ('file_get_contents')) return file_get_contents ($file);
    if (!$fp = @fopen ($file, 'rb')) return false;

    $data = '';
    flock ($fp, LOCK_SH);
    if (filesize ($file) > 0) $data =& fread ($fp, filesize ($file));
    flock ($fp, LOCK_UN);
    fclose ($fp);

    return $data;
  }
}
$dir1s = Tool::directoryMap ('demos', 1);
$dirs = array ();
foreach ($dir1s as $dir1) {
  $files = array ();
  Tool::mergeArrayRecursive (Tool::directoryMap ('demos/' . $dir1), $files, 'demos/' . $dir1);
  $dirs[$dir1] = $files;
}

?><!DOCTYPE html>
<html lang="zh-Hant">
  <head>
    <meta http-equiv="Content-Language" content="zh-tw" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui" />

    <title>宙思提案</title>

    <link href="css/public.css" rel="stylesheet" type="text/css" />
    <script src="js/public.js" language="javascript" type="text/javascript" ></script>
  </head>
  <body>
    
    <h1>請選擇專案</h1>
    <hr>
    <div id='demos'>
<?php foreach ($dirs as $name => $value) { ?>
        <a class='demo' data-val='<?php echo json_encode ($value);?>'><?php echo $name;?>(<?php echo count ($value);?>)</a>
<?php } ?>
    </div>

    <div id='img'><img src=''></div>

    <div id='menu'>
      <div><img src='demos/kyo01/01.jpg'></div>
      <div><img src='demos/kyo01/02.jpg'></div>
      <div><img src='demos/kyo01/03.jpg'></div>
    </div>
  </body>
</html>
