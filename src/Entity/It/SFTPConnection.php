<?php

namespace App\Entity\It;

use Exception;
use Traversable;

class SFTPConnection implements \IteratorAggregate
{
    private $connection;
    private $sftp;

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

    public function uploadFile($local_file, $remote_file): void
    {
        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');

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
        $handle = opendir("ssh2.sftp://$sftp$remote_dir");
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
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator(string $remote_dir = '/'): Traversable
    {
        return new \ArrayIterator($this->getFiles($remote_dir));
    }
}
