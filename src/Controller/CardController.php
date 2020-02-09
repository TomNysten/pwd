<?php

namespace App\Controller;

use App\Entity\Cards;
use App\Entity\Comments;
use App\Form\CardsType;
use App\Form\CommentFormType;
use App\Repository\CardsRepository;
use App\Repository\CommentsRepository;
use App\Repository\SetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CardController extends AbstractController
{
    /**
     * @Route("/Extensions/{extension}/{id}", name="this-card")
     * @param Request $request
     * @param String $extension
     * @param Int $id
     * @param CardsRepository $card_repo
     * @param SetsRepository $set_repo
     * @param CommentsRepository $com_repo
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    public function thisCard (String $extension, Int $id,
                              CardsRepository $card_repo, SetsRepository $set_repo, CommentsRepository $com_repo,
                              EntityManagerInterface $manager, Request $request) : Response
    {
        $card       = $card_repo->findOneByIdWithData($id);
        $ext        = $set_repo->findOneBy(["name" => $extension]);
        $comments   = $com_repo->findByPostedOnCardWithAuthor($id);
        $user       = $this->getUser();

        $new_com    = new Comments();
        $form = $this->createForm(CommentFormType::class, $new_com);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_com->setPostedOnCard($card);
            $new_com->setPostedAt(new \DateTime('now'));
            $new_com->setAuteur($user);
            $new_com->setDeleted(false);
            $new_com->setValidated(false);

            $manager->persist($new_com);
            $manager->flush();
            return $this->redirectToRoute('this-card', [
                'extension' => $extension,
                'id'        => $id
            ]);
        }

        return $this->render('pages/this-card.html.twig', [
            'card'      => $card,
            'ext'       => $ext,
            'comments'  => $comments,
            'form'      => $form->createView()
        ]);
    }

    /* ====================================================================
                                   C R U D
    ======================================================================= */

    /**
     * @Route("/Admin/cards/index", name="cards_index", methods={"GET"})
     * @param CardsRepository $cardsRepository
     * @param Request $request
     * @return Response
     */
    public function index(CardsRepository $cardsRepository, Request $request): Response
    {
        $pagination = $request->query->get('page') ? $request->query->get('page') : 1;
        return $this->render('admin/cards/index.html.twig', [

            'cards' => $cardsRepository->FindAllWithPagination($pagination)
        ]);
    }

    /**
     * @Route("/Admin/cards/show/{cardId}", name="cards_show", methods={"GET"})
     * @param Cards $card
     * @return Response
     */
    public function show(Cards $card): Response
    {
        return $this->render('admin/cards/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/Admin/cards/new", name="cards_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $card = new Cards();
        $form = $this->createForm(CardsType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->redirectToRoute('cards_index');
        }

        return $this->render('admin/cards/new.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/cards/edit/{cardId}", name="cards_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Cards $card
     * @return Response
     */
    public function edit(Request $request, Cards $card): Response
    {
        $form = $this->createForm(CardsType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cards_index');
        }

        return $this->render('admin/cards/edit.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/cards/delete/{cardId}", name="cards_delete", methods={"DELETE"})
     * @param Request $request
     * @param Cards $card
     * @return Response
     */
    public function delete(Request $request, Cards $card): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getCardId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cards_index');
    }
}
