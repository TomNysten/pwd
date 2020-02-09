<?php


namespace App\Controller;


use App\Entity\Sets;
use App\Form\SetsType;
use App\Repository\CardsRepository;
use App\Repository\SetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExtController extends AbstractController
{
    /**
     * @Route ("/Extensions/{name}", name="this-extension")
     * @param String $name
     * @param SetsRepository $set_repo
     * @param CardsRepository $card_repo
     * @param Request $request
     * @return Response
     */
    public function thisExtension (String $name, SetsRepository $set_repo, CardsRepository $card_repo, Request $request) : Response {
        $order          = $request->query->get('orderby');
        $cardsPerPage   = $request->query->get('cardsPerPage');

        $ext     = $set_repo->findOneBy(["name" => $name]);
        $cards   = $card_repo->findBySetName($name, $order);

        return $this->render('pages/this-extension.html.twig', [
            'ext'          => $ext,
            'cards'        => $cards,
            'cardsPerPage' => $cardsPerPage
        ]);
    }

    /**
     * @Route ("/All-Extensions", name="all-ext")
     * @return Response
     */
    public function allExtension () : Response {

        return $this->render('pages/all-extensions.html.twig', [

        ]);
    }

    /* ====================================================================
                                   C R U D
    ======================================================================= */

    /**
     * @Route("/Admin/extensions/index", name="sets_index", methods={"GET"})
     * @param SetsRepository $setsRepository
     * @return Response
     */
    public function index(SetsRepository $setsRepository): Response
    {
        return $this->render('admin/sets/index.html.twig', [
            'sets' => $setsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/Admin/extensions/show/{id}", name="sets_show", methods={"GET"})
     * @param Sets $set
     * @return Response
     */
    public function show(Sets $set): Response
    {
        return $this->render('admin/sets/show.html.twig', [
            'set' => $set,
        ]);
    }

    /**
     * @Route("/Admin/extensions/new", name="sets_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $set = new Sets();
        $form = $this->createForm(SetsType::class, $set);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($set);
            $entityManager->flush();

            return $this->redirectToRoute('sets_index');
        }

        return $this->render('admin/sets/new.html.twig', [
            'set' => $set,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/extensions/edit/{id}", name="sets_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Sets $set
     * @return Response
     */
    public function edit(Request $request, Sets $set): Response
    {
        $form = $this->createForm(SetsType::class, $set);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sets_index');
        }

        return $this->render('admin/sets/edit.html.twig', [
            'set' => $set,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/extensions/delete{id}", name="sets_delete", methods={"DELETE"})
     * @param Request $request
     * @param Sets $set
     * @return Response
     */
    public function delete(Request $request, Sets $set): Response
    {
        if ($this->isCsrfTokenValid('delete'.$set->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($set);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sets_index');
    }
}