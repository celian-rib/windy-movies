<?php

namespace App\Command;

use App\Entity\Country;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateUsers extends Command
{
    protected static $defaultName = 'app:generate-user';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    static function getRandomUser() {
        $URL = "https://randomuser.me/api/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($out, false);
        return $data->results[0];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln([
            'Generating user',
            '============',
        ]);

        $random_user = GenerateUsers::getRandomUser();

        $country = $this->entityManager
            ->getRepository(Country::class)
            ->findOneBy(array('id' => 1));

        $output->writeln($country->getName());

        $user = new User();
        $user->setEmail($random_user->email)
            ->setName($random_user->login->username)
            ->setPassword($random_user->login->md5)
            ->setRegisterDate(new DateTime($random_user->registered->date))
            ->setCountry($country);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}
