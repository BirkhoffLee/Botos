<?php
/*
 * Check if update is available
*/
global $config;
global $root;
$updateServer = 'http://botos.irkhoff.com';
$updateCheck = $updateServer . '/update/latest.txt';
$updateLog = $updateServer . '/update/latest_update_log.txt';

$latestVersion = file_get_contents($updateCheck);
$latestVer = explode(" ", $latestVersion);
$latestVer = $latestVer[0];
$downloadName = $latestVer . '.zip';
$updateDownload = $updateServer . '/update/files/' . $downloadName;
$updateTemp = $root . '/cmd/extra/updateTemp';
$filePath = $updateTemp . '/zip/' . $downloadName;

@mkdir($updateTemp, 0777);
@mkdir($updateTemp . '/zip', 0777);
@mkdir($updateTemp . '/files', 0777);

$this->Submitlog('<strong>[UPDATE] Now version is: ' . $config['version'] . '</strong>');
$this->Submitlog('<strong>[UPDATE] The latest version is: ' . $latestVersion . '</strong>');

if($config['version'] != $latestVersion){
    /*
     * New version is available
    */
    $this->Submitlog('<strong>[UPDATE] Downloading the latest version..</strong>');
    $fp_input = fopen($updateDownload, 'r');
    file_put_contents($filePath, $fp_input);

    $this->Submitlog('<strong>[UPDATE] Downloaded the latest version file!</strong>');

    require_once('pclzip.lib.php');
    $archive = new PclZip($filePath);
    $archive->extract(PCLZIP_OPT_PATH, $updateTemp . '/files');
    $this->Submitlog('<strong>[UPDATE] Extracted the latest version files!</strong>');

    $source = $updateTemp . '/files/';
    $distination = $root;
    $failedCopyFile = false;

    if ($dh = opendir($source)){
        while (($sf = readdir($dh)) !== false){
            if ($sf == '.' || $sf == '..'){
                continue;
            }

            $sourceFile = $source . $sf;
            $distinationFile = $distination . $sf;

            if (!copy($sourceFile, $distinationFile)){
                $failedCopyFile = true;
                $this->Submitlog('<strong><span style="color:red">[UPDATE] Failed to copy new file!</span></strong>');
            }
        }
    }
    if(!$failedCopyFile){
        $this->Submitlog('<strong>[UPDATE] Copied the latest version files to root dir!</strong>');

        $this->Submitlog('<strong>[UPDATE LOG] 更新日誌 &nbsp;&nbsp;&nbsp;' . $latestVersion . '</strong>');
        $this->Submitlog('<strong>[UPDATE LOG] ===============================</strong>');
        $fp = fopen($updateLog,'r');
        while(!feof($fp)){
            $buffer = iconv('big5', 'utf-8', fgets($fp, 4096));
            $this->Submitlog('<strong>[UPDATE LOG] ' . $buffer . '</strong>');
        }
        fclose($fp);
        $this->Submitlog('<strong>[UPDATE LOG] ===============================</strong>');

        $this->Submitlog('<strong>[UPDATE] Update complete. Botos will restart in 5 seconds. Enjoy!</strong>');
        $this->send_data('QUIT Update', false);
        echo "<meta http-equiv=\"refresh\" content=\"5\">";
        exit;
    } else {
        $this->Submitlog('<strong>[UPDATE] There are some problems with the update. Please update Botos manually.</strong>');
    }
} else {
    $this->Submitlog('<strong>[UPDATE] This is the latest version, no need to update.</strong>');
}