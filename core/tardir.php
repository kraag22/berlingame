<?php
/**
 * Třída pro tarování adresářů a souborů zálohování souborů webové aplikace
 *
 */
Class TarDir {
    var $tar_file;
    var $gz_file;
    var $fp;
    /**
         * Odzálohuje soubory v adresáři
         * omezení: celková délka cesty k souboru nesmípřesáhnout 99 znaků.
         * Jde o omezení tar formátu bez rozšíření
         * omezení: maximální počet cest v tar-archivu se musí vejí do jednoho pole.
         * Viz fce build tree.
         *
         * @param $file_name String jméno souboru se zálohou, které bude vidět
         * uživatel (když si stahuje
         * zálohu s volbou immediate download)
         *
         * @param $dir String jméno adresářek k zabalení
         *
         * @param $working_dir String jméno adresáře, kde bude vytvo5en soubor,
         * pokud je použita volba dump_to_file
         *
         * @param  $save_file_name String jméno souboru pro dump. pokud je použita
         * kombinace
         * voleb dump_to_file a immediate_download, tak se vytvoří záloha do toho
         * souboru, ten se pak odešle uživateli, a soubor se smaže.
         * Pokud je použita volba dump_to_file a jméno není zadáno,
         * vytvoří se nějaké dost dlouhé náhodné.
         *
         * @param $download_immediate=true bool pokud je zadána tato volba,
         * je soubor ihned odeslán v hlavičce ke klientovi. Viz omezení v popisu
         * funkce.
         *
         * @param $gz_compress bool, pokud true, soubor bude zagunzipován
         *
         * @return true-false podle úspěch neúspěch
         */
    function TarDir($file_name, $dir=".", $working_dir=".", $save_file_name=null,
                                                         $download_immediate=true, $gz_compress=true) {

        if (!$save_file_name){
                        $length = 32;
                        $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
                for($i=0;$i<$length;$i++)
                {
                        $save_file_name .= $pattern{rand(0,35)};
                }
                $save_file_name .= ".tar";
                }
                if (!is_dir($dir) || !is_dir($working_dir)){
                        return false;
                }

        $this->tar_file = $working_dir . "/" . $save_file_name;

        $this->fp = fopen($this->tar_file, "wb");
        $tree = $this->build_tree($dir);
        $this->process_tree($tree);
        fputs($this->fp, pack("a512", ""));
        fclose($this->fp);

        $mime_type = "application/x-tar";
        if ($gz_compress){
                $this->gz_file = $working_dir . "/" . $save_file_name . ".gz";
                $fpgz = gzopen($this->gz_file, "wblh");
                $fptar = fopen($this->tar_file,"r");
                while ($buf = fread($fptar, 2048)){
                        fwrite($fpgz, $buf, strlen($buf));
                }
                fclose($fptar);
                gzclose($fpgz);
                $mime_type = "application/x-gz";
                if (is_file ($this->tar_file)){
                                        unlink($this->tar_file);
                        }
        }

        if ($gz_compress){
                         $file = $this->gz_file;
                }else {
                         $file = $this->tar_file;
                }

                if ($download_immediate){

                                header('Content-Type: ' . $mime_type);
                                header('Content-Disposition: attachment; filename="'.$file_name.'"');
                                header('Accept-Ranges: bytes');
                                header("Cache-control: private");
                                header('Pragma: private');


                                $fp = fopen($file, "r");
                                if (!$fp){
                                        return false;
                                }
                                header("Content-Length: ".filesize($file));

                                while ($buffer = fread($fp,1024) ){
                                        echo $buffer;
                                }
                                fclose($fp);
                                if (is_file ($this->gz_file)){
                                        unlink($this->gz_file);
                                }
                                if (is_file ($this->tar_file)){
                                        unlink($this->tar_file);
                                }

                }else {
                fs_my_copy($save_file_name, $file);
                unlink($save_file_name);
        }
                return true;
    }

    function build_tree($dir='.'){
        $output=array();
        $handle = opendir($dir);
        while(false !== ($readdir = readdir($handle))){
            if($readdir != '.' && $readdir != '..'){
                $path = $dir.'/'.$readdir;
                if (is_file($path)) {
                    $output[] = substr($path, 2, strlen($path));
                } elseif (is_dir($path)) {
                    $output[] = substr($path, 2, strlen($path)).'/';
                    $output = array_merge($output, $this->build_tree($path));
                }
            }
        }
        closedir($handle);
        return $output;
    }
    function process_tree($tree) {
        foreach( $tree as $pathfile ) {
            if (substr($pathfile, -1, 1) == '/') {
                fputs($this->fp, $this->build_header($pathfile));
            } elseif ($pathfile != $this->tar_file) {
                $filesize = filesize($pathfile);
                $block_len = 512*ceil($filesize/512)-$filesize;
                fputs($this->fp, $this->build_header($pathfile));
                fputs($this->fp, file_get_contents($pathfile));
                fputs($this->fp, pack("a".$block_len, ""));
            }
        }
        return true;
    }
    function build_header($pathfile) {
        if ( strlen($pathfile) > 99 ) die('Error');
        $info = stat($pathfile);
        if ( is_dir($pathfile) ) $info[7] = 0;
        $header = pack("a100a8a8a8a12A12a8a1a100a255",
            $pathfile,
            sprintf("%6s ", decoct($info[2])),
            sprintf("%6s ", decoct($info[4])),
            sprintf("%6s ", decoct($info[5])),
            sprintf("%11s ",decoct($info[7])),
            sprintf("%11s", decoct($info[9])),
            sprintf("%8s", " "),
            (is_dir($pathfile) ? "5" : "0"),
            "",
            ""
            );
        clearstatcache();
        $checksum = 0;
        for ($i=0; $i<512; $i++) {
            $checksum += ord(substr($header,$i,1));
        }
        $checksum_data = pack(
            "a8", sprintf("%6s ", decoct($checksum))
            );
        for ($i=0, $j=148; $i<7; $i++, $j++)
            $header[$j] = $checksum_data[$i];
        return $header;
    }
}

?>