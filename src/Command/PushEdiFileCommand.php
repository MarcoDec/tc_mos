<?php
namespace App\Command;

use App\Entity\It\SFTPConnection;
use App\Service\NewFileDetector;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PushEdiFileCommand extends Command
{
    public function __construct(private SFTPConnection $sftp)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Push an edi file into a folder.')
            ->addArgument('localFile', InputArgument::REQUIRED, 'The path to the local file')
            ->addArgument('remoteDirectory', InputArgument::REQUIRED, 'The remote directory where the file will be pushed')
            ->addArgument('remoteFile', InputArgument::REQUIRED, 'The remote file name');
    }

    /**
     * @throws Exception
     * Example de commande pour test
     *    php bin/console app:push-edi-file edi/test_delfor/ORDERS_AN20231027101146-805.json /test/delfor ORDERS_AN20231027101146-805.json
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $localFile = $input->getArgument('localFile');
        $remoteDirectory = $input->getArgument('remoteDirectory');
        $remoteFile = $input->getArgument('remoteFile');
        //On affiche le chemin absolu courant
        $output->writeln('Chemin absolu courant: ' . getcwd());
        //On commence par tester l'existance du fichier local
        if (!file_exists($localFile)) {
            $output->writeln("Le fichier local '$localFile' n'existe pas");
            return 1;
        }

        $output->writeln('Connexion au serveur SFTP');
        try {
            $this->sftp->login($_ENV['SFTP_USER'], $_ENV['SFTP_PASSWORD']);
        } catch (Exception $e) {
            $output->writeln('Erreur de connexion');
            return 1;
        }
        $output->writeln('Connexion réussie');
        try {
            $this->sftp->pushFile($remoteDirectory, $localFile, $remoteFile);
        } catch (Exception $e) {
            $output->writeln('Erreur de transfert');
            return 1;
        }
        $output->writeln('File pushed.');
        return 0;
    }
    function getName(): string
    {
        return 'app:push-edi-file';
    }
}
