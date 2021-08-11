<?php

namespace App\Controller;


use App\Entity\Tweet;
use App\Form\TweetType;
use App\Repository\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tweet", name="tweet_")
 */
class TweetController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(TweetRepository $tweetRepository): Response
    {
        $tweets =$tweetRepository->findBy([],["dateCreated" => "DESC"]);
        return $this->render('tweet/list.html.twig', [
            'tweets' => $tweets,
        ]);
    }

    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detail(int $id, TweetRepository $tweetRepository): Response
    {
        $tweet =$tweetRepository->find($id);
        if (!$tweet){
            throw $this->createNotFoundException("Ce tweet n'existe pas");
        }
        return $this->render('tweet/detail.html.twig',[
            'tweet'=>$tweet,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $tweet = new Tweet();
        $tweet->setDateCreated(new \DateTime());
        $tweetForm = $this->createForm(TweetType::class,$tweet);
        $tweetForm->handleRequest($request);
        if ($tweetForm->isSubmitted() && $tweetForm->isValid()){
            $entityManager->persist($tweet);
            $entityManager->flush();
            $this->addFlash('success','tweet publié !');
            return $this->redirectToRoute('tweet_detail',['id'=>$tweet->getId()]);
        }
        return $this->render('tweet/create.html.twig',[
            'tweetForm'=>$tweetForm->createView(),
        ]);
    }

    /**
     * @Route ("/delete/{id}",name="delete")
     */
    public function delete(Tweet $tweet,EntityManagerInterface $entityManager){


        $entityManager->remove($tweet);
        $entityManager->flush();

        return $this->redirectToRoute('main');
    }

    public function edit(Tweet $tweet,Request $request): Response
    {
        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tweet_list', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('tweet/edit.html.twig', [
            'tweet' => $tweet,
            'form' => $form,
        ]);
    }
}
