<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{

    /**
     * @param MovieRepository $movieRepository
     * @param int             $id
     *
     * @return Response
     */
    #[Route('/movie/{id}', name: 'movie_details', requirements: ['id' => '\d+'])]
    public function details(
        MovieRepository $movieRepository,
        Session         $session,
        int             $id,
    ): Response
    {

        /** @var Movie $movie */
        $movie = $movieRepository->find($id);

        $back = $session->get('backRouteDetails');

        if (!$back) {

            $showList = $movie->getMovieShows()->getValues();
            $lastShow = end($showList)->getShowDate();

            $back = $this->generateUrl('history', ['year' => $lastShow->format('Y'), 'month' => $lastShow->format('m')]);
        }

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
            'back' => $back,
        ]);
    }
}
