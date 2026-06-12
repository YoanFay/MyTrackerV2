<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Serie;
use App\Entity\User;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

final class SerieController extends AbstractController
{

    /**
     * @param SerieRepository $serieRepository
     * @param int             $id
     *
     * @return Response
     */
    #[Route('/serie/{id}', name: 'serie_details', requirements: ['id' => '\d+'])]
    public function details(
        SerieRepository       $serieRepository,
        EpisodeShowRepository $episodeShowRepository,
        Session               $session,
        int                   $id,
    ): Response
    {

        /** @var Serie $serie */
        $serie = $serieRepository->find($id);

        /** @var User $user */
        $user = $this->getUser();

        $countEpisodeShow = 0;
        $totalDuration = 0;

        $episodeList = [];

        $episodeShows = $episodeShowRepository->getShowBySerie($serie, $user);

        foreach ($episodeShows as $episodeShow){

            $episode = $episodeShow->getEpisode();

            if (!isset($episodeList[$episode->getSeasonNumber()])) {
                $episodeList[$episode->getSeasonNumber()] = [];
            }

            if (!isset($episodeList[$episode->getSeasonNumber()][$episode->getEpisodeNumber()])) {
                $episodeList[$episode->getSeasonNumber()][$episode->getEpisodeNumber()] = $episode;
            }

            $countEpisodeShow++;
            $totalDuration += $episode->getDuration();
        }

        /*foreach ($serie->getEpisodes() as $episode) {
            if (!isset($episodeList[$episode->getSeasonNumber()])) {
                $episodeList[$episode->getSeasonNumber()] = [];
            }

            $episodeList[$episode->getSeasonNumber()][$episode->getEpisodeNumber()] = $episode;

            foreach ($episode->getEpisodeShows() as $show) {
                $countEpisodeShow++;
                $totalDuration += $episode->getDuration();
            }
        }*/

        ksort($episodeList);

        foreach ($episodeList as &$episodes) {
            ksort($episodes);
        }
        unset($episodes);

        $studios = [];
        $producers = [];
        $networks = [];

        foreach ($serie->getInvolvedSerieCompanies()->getValues() as $company) {

            if ($company->isStudio()) {
                $studios[] = $company->getCompany();
            } else if ($company->isProducer()) {
                $producers[] = $company->getCompany();
            } else if ($company->isNetwork()) {
                $networks[] = $company->getCompany();
            }

        }

        $tvdbTagList = [];

        foreach ($serie->getTvdbTags()->getValues() as $tvdbTag) {

            $tvdbTagType = $tvdbTag->getTvdbTagType()->getNameEng();

            if (!isset($tvdbTagList[$tvdbTagType])) {
                $tvdbTagList[$tvdbTagType] = [
                    'tvdbTagType' => $tvdbTag->getTvdbTagType(),
                    'tag' => []
                ];
            }

            $tvdbTagList[$tvdbTagType]['tag'][] = $tvdbTag;

        }

        $back = $session->get('backRouteDetails');

        if (!$back) {

            $episodes = end($episodeList);
            $lastEpisode = end($episodes)->getEpisodeShows()->getValues();
            $lastShow = end($lastEpisode)->getShowDate();

            $back = $this->generateUrl('history_date', ['year' => $lastShow->format('Y'), 'month' => $lastShow->format('m')]);
        }

        return $this->render('serie/details.html.twig', [
            'serie' => $serie,
            'countEpisodeShow' => $countEpisodeShow,
            'totalDuration' => $totalDuration,
            'studios' => $studios,
            'producers' => $producers,
            'networks' => $networks,
            'tvdbTagList' => $tvdbTagList,
            'episodeList' => $episodeList,
            'back' => $back,
        ]);
    }
}
