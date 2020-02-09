<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\FlaggedBy;
use App\Form\CommentsType;
use App\Repository\CardsRepository;
use App\Repository\CommentsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/Extensions/{extension}/{id}/commentaires", name="card-comments")
     * @param int $id
     * @param CommentsRepository $com_repo
     * @param CardsRepository $cards_repo
     * @param Request $request
     * @return Response
     */
    public function cardComments (int $id, CommentsRepository $com_repo, CardsRepository $cards_repo, Request $request)
    {
        $card = $cards_repo->findOneBy(['cardId' => $id]);

        $pagination = $request->query->get('page') ? $request->query->get('page') : 1;
        $coms = $com_repo->findAllFromCardWithPagination($id, $pagination);

        return $this->render('/comment/all-comments.html.twig', [
            'item'     => $card->getCardName(),
            'comments' => $coms
        ]);
    }

    /**
     * @Route("Users/{userName}/profile/commentaires_postés", name="coms_by_user")
     * @param string $userName
     * @param CommentsRepository $com_repo
     * @param Request $request
     * @return Response
     */
    public function comsByUser (string $userName, CommentsRepository $com_repo, Request $request)
    {
        $pagination = $request->query->get('page') ? $request->query->get('page') : 1;
        $coms = $com_repo->findAllFromAuthorWithPagination($userName, $pagination);

        return $this->render('/comment/all-comments.html.twig', [
            'item' => $userName,
            'comments' => $coms,
            'need_location' => true
        ]);
    }

    /**
     * @Route("Users/{userName}/profile/commentaires_reçus", name="coms_on_user")
     * @param string $userName
     * @param CommentsRepository $com_repo
     * @param Request $request
     * @return Response
     */
    public function comsOnUser (string $userName, CommentsRepository $com_repo, Request $request)
    {
        $pagination = $request->query->get('page') ? $request->query->get('page') : 1;
        $coms = $com_repo->findAllOnProfileWithPagination($userName, $pagination);

        return $this->render('/comment/all-comments.html.twig', [
            'item' => $userName,
            'comments' => $coms
        ]);
    }

    /* ============================================================================================= */

    /**
     * @Route("Users/{userName}/comments/delete/{id}", name="delete_com")
     * @param string $userName
     * @param Int $id
     * @param CommentsRepository $com_repo
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function deleteCom (string $userName, Int $id, CommentsRepository $com_repo, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        if ($user != NULL) {
            $com = $com_repo->findOneBy(['id' => $id]);
            $com_auteur = $com->getAuteur();

            if ( ($user->getRoles() == ['ROLE_ADMIN']) || ($user->getUsername() == $com_auteur->getUsername()) ) {
                $com->setDeleted(true);
                $manager->persist($com);
                $manager->flush();
                $this->addFlash('success', 'Commentaire supprimé.');
                if ($user->getRoles() == ['ROLE_ADMIN']) {
                    return $this->redirectToRoute('comments_index', [

                    ]);
                }
                else {
                    return $this->redirectToRoute('profile', [
                        'userName' => $userName
                    ]);
                }
            }
        }
        else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce commentaire.');
            return $this->redirectToRoute('home', [

            ]);
        }
    }

    /**
     * @Route("Users/{userName}/comments/flag/{id}", name="flag_com")
     * @param string $userName
     * @param Int $id
     * @param CommentsRepository $com_repo
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function flagCom (string $userName, Int $id, CommentsRepository $com_repo, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        if ($user != NULL) {
            $com = $com_repo->findOneBy(['id' => $id]);
            $com_auteur = $com->getAuteur();

            if ( ($user->getUsername() == $com_auteur->getUsername()) ) {
                $this->addFlash('error', 'Vous ne pouvez pas flaguer un commentaire si c\'est le vôtre ou que vous êtes admin.');
                return $this->redirectToRoute('home', [

                ]);
            }
            else {
                $flag = new FlaggedBy();
                $flag->setUser($user);
                $flag->setComment($com);
                $manager->persist($flag);
                $manager->flush();
                $this->addFlash('success', 'Commentaire flagué.');

                return $this->redirectToRoute('profile', [
                    'userName' => $userName
                ]);
            }
        }
        else {
            $this->addFlash('error', 'Il faut être logué pour flaguer un commentaire.');
            return $this->redirectToRoute('home', [

            ]);
        }
    }

    /* ====================================================================
                                   C R U D
    ======================================================================= */

    /**
     * @Route("/Admin/comments/index", name="comments_index", methods={"GET"})
     * @param CommentsRepository $commentsRepository
     * @param Request $request
     * @return Response
     */
    public function index(CommentsRepository $commentsRepository, Request $request): Response
    {
        $pagination = $pagination = $request->query->get('page') ? $request->query->get('page') : 1;
        $comments = $commentsRepository->findAllWithPaginationAndFlags($pagination);

        return $this->render('admin/comments/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/Admin/comments/show/{id}", name="comments_show", methods={"GET"})
     * @param Comments $comment
     * @return Response
     */
    public function show(Comments $comment): Response
    {
        return $this->render('admin/comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/Admin/comments/edit/{id}", name="comments_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Comments $comment
     * @return Response
     */
    public function edit(Request $request, Comments $comment): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comments_index');
        }

        return $this->render('admin/comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/comments/delete/{id}", name="comments_delete", methods={"DELETE"})
     * @param Request $request
     * @param Comments $comment
     * @return Response
     */
    public function delete(Request $request, Comments $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comments_index');
    }

    /**
     * @Route("/Admin/comments/validate/{id}", name="comments_validate", methods={"GET"})
     * @param Comments $comment
     * @return Response
     */
    public function validate (Comments $comment): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment->setValidated(true);
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->render('admin/comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

}
