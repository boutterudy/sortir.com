<?php

namespace App\Commands;

use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\CharsetConverter;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class CsvImport extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordHasherInterface
     */
    private $hasher;

    /**
     * @var CampusRepository
     */
    private $campusRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param EntityManagerInterface $em
     * @param CampusRepository $campusRepository
     * @throws LogicException
     */
    public function __construct(EntityManagerInterface $em, CampusRepository $campusRepository, UserPasswordHasherInterface $hasher, UserRepository $userRepository)
    {
        parent::__construct();
        $this->em = $em;
        $this->campusRepository = $campusRepository;
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('app:csv:import')
            ->setDescription('Importer un fichier CSV');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Tentative d\'import des données');
        $reader = Reader::createFromPath('uploads/csv/datas.csv');
        $encoder = (new CharsetConverter())->inputEncoding('windows-1252');

        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);
        $records = $encoder->convert($reader->getRecords(['nickName', 'password', 'lastName', 'firstName', 'phone', 'email', 'isAdmin', 'isActive', 'Campus']));
        foreach ($records as $offset => $record){
            $user = $this->userRepository->findOneBy(['nickName'=>$record['nickName']]);
            if($user == null){
                $user = new User();
                $user->setNickName($record['nickName'])
                    ->setPassword($this->hasher->hashPassword($user, $record['password']))
                    ->setLastName($record['lastName'])
                    ->setFirstName($record['firstName'])
                    ->setPhoneNumber($record['phone'])
                    ->setEmail($record['email'])
                    ->setIsAdmin(($record['isAdmin'] == 'Oui'))
                    ->setIsActive($record['isActive'] == 'Oui')
                    ->setCampus($this->campusRepository->findOneBy(['name'=>$record['Campus']]));
                $this->em->persist($user);
            }
        }
        $this->em->flush();
        $io->success('La commande s\'est bien exécutée');

        $fsObject = new Filesystem();
        $current_dir_path = getcwd();
        $directory = [ $current_dir_path . '\uploads\csv'];
        $fsObject->remove($directory);

        return Command::SUCCESS;
    }
}