<?php
$needScan = ['./docs'];
$needUnlinks = [];
$files = scandir('./docs');
while (true) {
    if (count($needScan) == 0) {
        break;
    }
    $dir = array_pop($needScan);
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($dir . '/' . $file)) {
            $needScan[] = $dir . '/' . $file;
        }
        $needUnlinks[] = $dir . '/' . $file;
    }
}
while (true) {
    if (count($needUnlinks) == 0) {
        break;
    }
    $needUnlink = array_pop($needUnlinks);
    if (is_dir($needUnlink)) {
        rmdir($needUnlink);
    } else {
        unlink($needUnlink);
    }
}
$needScan = ['./source'];
while (true) {
    if (count($needScan) == 0) {
        break;
    }
    $dir = array_pop($needScan);
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($dir . '/' . $file)) {
            $needScan[] = $dir . '/' . $file;
            continue;
        } else {
            $doc = '# ' . str_ireplace('/', '.', substr($dir . '/' . $file, 9, strlen($dir . '/' . $file) - 14)) . "\n";
            $data = json_decode(file_get_contents($dir . '/' . $file));
            foreach ($data->params as $key => $param) {
                if (substr($key, 0, 1) == '_') {
                    continue;
                }
                $is_array = (substr($param, 0, 1) == '[]');
                if (strtolower(substr($param, 0, 1)) == substr($param, 0, 1)) {
                    $doc .= '- '.$param;
                } else {
                    $doc .= '- [' . $param;
                    if ($is_array) {
                        $doc .= '[]';
                    }
                    $doc .= '](https://github.com/eLoli-Community/GlobalBot/tree/master/docs/Event/' .
                        str_ireplace('.', '/', substr($param, 0, strlen($param))) . '.md)';
                }
                $doc .= ' ' . $key . ';';
                if (isset($data->params->{'_' . $key})) {
                    $doc .= ' //' . $data->params->{'_' . $key};
                }
                $doc .= "\n\n";
            }
            $pFiles = explode('/', './docs/' . substr($dir, 9));
            $nFile = '';
            foreach ($pFiles as $pFile) {
                $nFile .= $pFile . '/';
                if (!is_dir($nFile)) {
                    mkdir($nFile, 0755, true);
                    chmod($nFile, 0755);
                }
            }
            file_put_contents('./docs/' . substr($dir . '/' . $file, 9, strlen($dir . '/' . $file) - 14) . '.md', $doc);
        }
    }
}