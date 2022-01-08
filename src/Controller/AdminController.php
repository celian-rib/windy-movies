<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Country;
use App\Entity\Episode;
use App\Entity\ExternalRating;
use App\Entity\ExternalRatingSource;
use App\Entity\Genre;
use App\Entity\Rating;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Series;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

function fetchImdbAPI($imdb_id, $season_id = null, $episode_id = null)
{
    $API_KEY = "38a1fd74";
    $BASE_URL = "http://www.omdbapi.com/?i=" . $imdb_id . "&apikey=" . $API_KEY;

    if ($season_id != null)
        $BASE_URL = $BASE_URL . "&season=" . $season_id;
    if ($episode_id != null)
        $BASE_URL = $BASE_URL . "&episode=" . $episode_id;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $BASE_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $out = curl_exec($ch);
    curl_close($ch);
    return json_decode($out, false);
}

function explodeAndInstanciate(EntityManagerInterface $entityManager, $separator, $source, $entityClass, $field = 'name')
{
    $entities = [];
    foreach (explode($separator, $source) as $item_name)
        $entities[] = $entityManager
            ->getRepository($entityClass)
            ->findOneBy([$field => $item_name]) ?? new $entityClass($item_name);
    foreach ($entities as $e)
        $entityManager->persist($e);
    return $entities;
}

function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function fetchAndAddSeasonsAndEpisodes(EntityManagerInterface $entityManager, $serie, $s = 1)
{
    $data = fetchImdbAPI($serie->getImdb(), $s);
    dump($data);
    $season = new Season();
    $season->setNumber($s)->setSeries($serie);
    foreach ($data->Episodes as $ep) {
        $new_ep = new Episode();
        $new_ep
            ->setTitle($ep->Title)
            ->setImdb($ep->imdbID)
            ->setDate(validateDate($ep->Released) ? new DateTime($ep->Released) : null)
            ->setImdbrating((float) $ep->imdbRating)
            ->setNumber($ep->Episode)
            ->setSeason($season);

        $entityManager->persist($new_ep);
        $season->addEpisode($new_ep);
    }
    $entityManager->persist($season);
    if ($data->totalSeasons > $s)
        return [$season, ...fetchAndAddSeasonsAndEpisodes($entityManager, $serie, $s + 1)];
    return [$season];
}

function addSerieFromIMDB(EntityManagerInterface $entityManager, $imdb_id, $ytb_trailer = null)
{
    $data = fetchImdbAPI($imdb_id);
    dump($data);

    $new_serie = new Series();
    $years = explode('-', $data->Year);
    $new_serie
        ->setYearStart((int) $years[0])
        ->setYearEnd((int) count($years) > 1 ? $years[1] : null)
        ->setPlot($data->Plot)
        ->setPoster(file_get_contents($data->Poster))
        ->setImdb($imdb_id)
        ->setDirector($data->Director)
        ->setAwards($data->Awards)
        ->setTitle($data->Title);

    // Add genres
    foreach (explodeAndInstanciate($entityManager, ", ", $data->Genre, Genre::class) as $genre) {
        $new_serie->addGenre($genre);
        $genre->addSeries($new_serie);
    }

    // Add countries
    foreach (explodeAndInstanciate($entityManager, ", ", $data->Country, Country::class) as $country) {
        $new_serie->addCountry($country);
        $country->addSeries($new_serie);
    }

    // Add actors
    foreach (explodeAndInstanciate($entityManager, ", ", $data->Actors, Actor::class) as $actor) {
        $new_serie->addActor($actor);
        $actor->addSeries($new_serie);
    }

    // Add external ratings
    foreach ($data->Ratings as $er) {
        $new_er = new ExternalRating();
        $new_er->setSource(
            $entityManager
                ->getRepository(ExternalRatingSource::class)
                ->findOneBy(['name' => $er->Source]) ?? new ExternalRatingSource($er->Source)
        )
            ->setValue($er->Value)
            ->setSeries($new_serie);
        $entityManager->persist($new_er);
        $new_serie->addExternalRating($new_er);
    }

    fetchAndAddSeasonsAndEpisodes($entityManager, $new_serie);

    if (isset($ytb_trailer))
        $new_serie->setYoutubeTrailer($ytb_trailer);

    $entityManager->persist($new_serie);
    $entityManager->flush();
    return $new_serie;
}

class AdminController extends AbstractController
{

    #[Route('/admin', name: 'admin')]
    public function admin(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user == null)
            return $this->redirectToRoute('index');
        if ($user->getAdmin() == false)
            return $this->redirectToRoute('index', array('toasterr' => 'You are not admin'));

        try {
            $imdb_id = $request->get('imdb_id');
            if ($request->isMethod("post") && isset($imdb_id)) {
                $new_serie = addSerieFromIMDB($entityManager, $imdb_id, $request->get('ytb_trailer'));
                return $this->redirectToRoute('series_show', array('id' => $new_serie->getId(), 'toast' => 'Serie added with success!'));
            }
        } catch (\Throwable $th) {
            return $this->redirectToRoute('admin', array('toasterr' => "Error : " . $th->getMessage()));
        }

        $reviews = $entityManager
            ->createQueryBuilder()
            ->select('r')
            ->from(Rating::class, 'r')
            ->orderBy('r.date', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->render('pages/users/admin.html.twig', [
            'reviews' => $reviews
        ]);
    }
}
