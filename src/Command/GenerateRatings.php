<?php

namespace App\Command;

use App\Entity\Rating;
use App\Entity\Series;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRatings extends Command
{
    protected static $defaultName = 'app:generate-ratings';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::REQUIRED, 'Number of ratings to generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Generating ratings',
            '============',
        ]);

        $URL = "https://goquotes-api.herokuapp.com/api/v1/random?count=" . $input->getArgument('count');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($out, false);
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $series = $this->entityManager->getRepository(Series::class)->findAll();

        foreach ($data->quotes as $q) {

            shuffle($users);
            shuffle($series);
            
            $output->writeln($users[0]);
            $output->writeln($q->text);
            $output->writeln("-------------");
            
            $rating = new Rating();
            $rating
            ->setUser($users[0])
            ->setValue(rand(3, 10))
            ->setDate(new DateTime(date('Y-m-d', strtotime( '+'.mt_rand(-1000, 0).' days'))))
            ->setComment($q->text)
            ->setSeries($series[0]);

            $this->entityManager->persist($rating);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
