<?php


namespace App\Controller;


use App\Entity\Blocks;
use App\Form\BlocksType;
use App\Repository\BlocksRepository;
use App\Repository\CardsRepository;
use App\Repository\SetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class BlockController extends AbstractController
{
    /**
     * @Route ("/Blocks/{name}", name="this-block")
     * @param String $name
     * @param BlocksRepository $block_repo
     * @param SetsRepository $sets_repo
     * @param CardsRepository $cards_repo
     * @return Response
     */
    public function thisBlock (String $name, BlocksRepository $block_repo, SetsRepository $sets_repo, CardsRepository $cards_repo): Response {
        $this_block     = $block_repo->findOneBy(['name' => $name]);
        $exts           = $sets_repo->findBy(['block' => $this_block]);

        $total = [
            'cards' => 0,
            'ext'   => 0,
        ];
        foreach ($exts as $key => $value) {
            $total['ext']++;
            $total['cards'] += $cards_repo->count(['cardSet' => $value->getId()]);

        }

        return $this->render('pages/this-block.html.twig', [
            'block' => $this_block,
            'exts'  => $exts,
            'total' => $total
        ]);
    }

    /**
     * @Route ("/All-Blocks", name="all-blocks")
     * @param BlocksRepository $block_repo
     * @param Request $request
     * @return Response
     */
    public function allBlocks (BlocksRepository $block_repo, Request $request): Response {
        $blocks = $block_repo->findAllBlocksPaginate($request->query->get('page') ? $request->query->get('page') : 1);

        return $this->render('pages/all-blocks.html.twig', [
            'blocks' => $blocks
        ]);
    }

    /* ====================================================================
                                   C R U D
    ======================================================================= */

    /**
     * @Route("/Admin/blocks/index", name="blocks_index", methods={"GET"})
     * @param BlocksRepository $blocksRepository
     * @return Response
     */
    public function index(BlocksRepository $blocksRepository): Response
    {
        return $this->render('admin/blocks/index.html.twig', [
            'blocks' => $blocksRepository->findAll(),
        ]);
    }

    /**
     * @Route("/Admin/blocks/show/{id}", name="blocks_show", methods={"GET"})
     * @param Blocks $block
     * @return Response
     */
    public function show(Blocks $block): Response
    {
        return $this->render('admin/blocks/show.html.twig', [
            'block' => $block,
        ]);
    }

    /**
     * @Route("/Admin/blocks/new", name="blocks_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $block = new Blocks();
        $form = $this->createForm(BlocksType::class, $block);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($block);
            $entityManager->flush();

            return $this->redirectToRoute('blocks_index');
        }

        return $this->render('admin/blocks/new.html.twig', [
            'block' => $block,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/blocks/edit/{id}", name="blocks_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Blocks $block
     * @return Response
     */
    public function edit(Request $request, Blocks $block): Response
    {
        $form = $this->createForm(BlocksType::class, $block);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blocks_index');
        }

        return $this->render('admin/blocks/edit.html.twig', [
            'block' => $block,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/blocks/delete/{id}", name="blocks_delete", methods={"DELETE"})
     * @param Request $request
     * @param Blocks $block
     * @return Response
     */
    public function delete(Request $request, Blocks $block): Response
    {
        if ($this->isCsrfTokenValid('delete'.$block->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($block);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blocks_index');
    }
}