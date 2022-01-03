<?php

namespace App\Command;

use App\Entity\Country;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::REQUIRED, 'Number of user to generate');
    }

    static function getRandomUser()
    {
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

        for ($i = 0; $i < $input->getArgument('count'); $i++) {
            $random_user = GenerateUsers::getRandomUser();

            $country_repo = $this->entityManager->getRepository(Country::class);
            $country = $country_repo
                ->findOneBy(array('id' => rand(0, count($country_repo->findAll()))));

            $user = new User();
            $user->setEmail($random_user->email)
                ->setName($random_user->login->username)
                ->setPassword($random_user->login->md5)
                ->setRegisterDate(new DateTime($random_user->registered->date))
                ->setCountry($country);

            $output->writeln($user->__toString());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            sleep(1);
        }

        return Command::SUCCESS;
    }
}
