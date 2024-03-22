<?php

namespace App\Entity\It;

use ArrayIterator;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Traversable;

class SFTPConnection implements \IteratorAggregate
{
    private $connection;
    private $sftp;

    /**
     * @throws Exception
     */
    public function __construct($host, $port=22)
    {
        $this->connection = @ssh2_connect($host, $port);
        if (! $this->connection)
            throw new Exception("Could not connect to $host on port $port.");
    }

    /**
     * @throws Exception
     */
    public function login($username, $password): void
    {
        if (! @ssh2_auth_password($this->connection, $username, $password))
            throw new Exception("Could not authenticate with username $username " .
                                "and password $password.");

        $this->sftp = @ssh2_sftp($this->connection);
        if (! $this->sftp)
            throw new Exception("Could not initialize SFTP subsystem.");
    }

    /**
     * @throws Exception
     */
    public function uploadFile($local_file, $remote_file): void
    {
        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://".intval($sftp)."$remote_file", 'r');

        if (! $stream)
            throw new Exception("Could not open file: $remote_file");

        $data_to_send = @file_get_contents($local_file);
        if ($data_to_send === false)
            throw new Exception("Could not open local file: $local_file.");

        if (@fwrite($stream, $data_to_send) === false)
            throw new Exception("Could not send data from file: $local_file.");

        @fclose($stream);
    }
    public function renameFile($remote_file, $new_remote_file): bool
    {
        $sftp = $this->sftp;
        return ssh2_sftp_rename($sftp, $remote_file, $new_remote_file);
    }
    public function getFiles($remote_dir): array
    {
        $sftp = $this->sftp;
        $files = [];
        $handle = opendir("ssh2.sftp://".$sftp.$remote_dir);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $files[] = $file;
        }
        closedir($handle);

        return $files;
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @param string $remote_dir
     * @return ArrayIterator|Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator(string $remote_dir = '/'): ArrayIterator|Traversable
    {
        $sftp = $this->sftp;
        $directory = "ssh2.sftp://".intval($sftp).$remote_dir;
        $files = [];

        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    // Ici, vous pouvez ajouter une logique supplémentaire si nécessaire,
                    // par exemple, vérifier si $file est un dossier ou un fichier, etc.
                    $files[] = $file;
                }
            }
            closedir($handle);
        } else {
            // Gérer l'erreur d'ouverture du répertoire, si nécessaire
        }

        return new ArrayIterator($files);
    }

    public function __destruct()
    {
        if ($this->connection) {
            @ssh2_exec($this->connection, 'exit');
            @ssh2_disconnect($this->connection);
        }
    }
}
