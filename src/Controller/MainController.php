<?php

namespace App\Controller;

use App\Repository\TweetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(TweetRepository $tweetRepository): Response
    {
        $tweets = $tweetRepository->findBy([],['dateCreated'=>"DESC"],10,0);
        return $this->render('main/home.html.twig', [
            'tweets' => $tweets,
        ]);
    }
}
