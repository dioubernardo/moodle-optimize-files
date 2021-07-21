<?php

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

$tmpFolder = sys_get_temp_dir();

/* Impedir a execução simultanea */
$lockfile =  $tmpFolder. '/optimizer_files.lock';
$pid = file_get_contents($lockfile);
if (empty($pid) or posix_getsid($pid) === false) {
   file_put_contents($lockfile, getmypid());
} else {
   exit;
}

/* Optimize files */
$record = $DB->get_record_sql('
    select f.mimetype, of.contenthash
    from {optimizer_files} of
    left join {files} f ON f.contenthash = of.contenthash
    where of.otimized = 0
    limit 1
');
if ($record){

    $originalFile = $CFG->dataroot . '/filedir/' . substr($record->contenthash, 0, 2) . '/' . substr($record->contenthash, 2, 2) . '/' . $record->contenthash;
    $tmpFile = $tmpFolder.'/of-'.$record->contenthash;

    /**
     * Should be used $record->mimetype, but...
     * See https://tracker.moodle.org/browse/MDL-70939
     */
    $correctMimetype = mime_content_type($originalFile);

    switch($correctMimetype){
        case 'video/mp4':
            $tmpFile .= '.mp4';
            $sucesso = executar("/usr/bin/ffmpeg -hide_banner -loglevel error -vcodec h264 -acodec aac -strict -2 -nostdin -i ".escapeshellarg($originalFile)." ".escapeshellarg($tmpFile));
            break;
        case 'application/pdf':
            $sucesso = executar("/usr/bin/gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/printer -dDetectDuplicateImages=true -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".escapeshellarg($tmpFile)." ".escapeshellarg($originalFile));
            break;
        case 'image/png':
            $sucesso = executar("/usr/bin/optipng -quiet ".escapeshellarg($originalFile)." -out ".escapeshellarg($tmpFile));
            break;
        case 'image/jpeg':
            $sucesso = executar("/usr/bin/jpegtran -outfile ".escapeshellarg($tmpFile)." ".escapeshellarg($originalFile));
            break;
        default:
            $sucesso = false;
    }

    if ($sucesso){
        $finalsize = filesize($tmpFile);
        if ($finalsize > 0 and $finalsize < filesize($originalFile)){
            $perms = fileperms($originalFile);
            if (rename($tmpFile, $originalFile)){
                chown($originalFile, $perms);

                $DB->execute('update {files} set filesize=:filesize where contenthash=:contenthash', [
                    'contenthash' => $record->contenthash,
                    'filesize' => $finalsize
                ]);
            }
        }
    }

    if (file_exists($tmpFile))
        unlink($tmpFile);

    $DB->execute('update {optimizer_files} set otimized=1 where contenthash=:contenthash', [
        'contenthash' => $record->contenthash
    ]);

}

function executar($cmd){
    $pipes = [];
    $fp = proc_open($cmd, array(
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w')
    ), $pipes);
    $stdout = '';
    while (!feof($pipes[1])) {
        $stdout .= fgets($pipes[1], 4096);
    }
    $stdout = trim($stdout);
    fclose($pipes[1]);
    $stderr = '';
    while (!feof($pipes[2])) {
        $stderr .= fgets($pipes[2], 4096);
    }
    $stderr = trim($stderr);
    fclose($pipes[2]);
    $status = proc_get_status($fp);
    $exitcode = $status['exitcode'];
    proc_close($fp);

    if ($exitcode != 0 or !empty($stdout) or !empty($stderr)){
        echo "Erro in {$cmd}\n exitcode: {$exitcode}\n";
        if (!empty($stdout))
            echo " stdout: {$stdout}\n";
        if (!empty($stderr))
            echo " stderr: {$stderr}\n";
        return false;
    }
    return true;
}
