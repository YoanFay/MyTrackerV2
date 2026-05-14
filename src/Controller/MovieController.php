<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{

    /**
     * @param MovieRepository $movieRepository
     * @param int             $id
     *
     * @return Response
     */
    #[Route('/movie/{id}', name: 'movie_details')]
    public function details(
        MovieRepository $movieRepository,
        int             $id,
    ): Response
    {

        /** @var Movie $movie */
        $movie = $movieRepository->find($id);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }
}
