<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/Admin/home", name="admin_home")
     */
    public function adminHome ()
    {
        return $this->render('admin/admin-home.html.twig', [

        ]);
    }

    /**
     * @Route("/Admin/cards", name="admin_cards")
     */
    public function adminCards ()
    {
        return $this->redirectToRoute('cards_index');
    }

    /**
     * @Route("/Admin/blocks", name="admin_blocks")
     */
    public function adminBlocks ()
    {
        return $this->redirectToRoute('blocks_index');
    }

    /**
     * @Route("/Admin/extensions", name="admin_ext")
     */
    public function adminExt ()
    {
        return $this->redirectToRoute('sets_index');
    }

    /**
     * @Route("/Admin/users", name="admin_users")
     */
    public function adminUsers ()
    {
        return $this->redirectToRoute('users_index');
    }

    /**
     * @Route("/Admin/comments/flagged", name="admin_flagged_coms")
     */
    public function adminFlaggedComs ()
    {
        return $this->redirectToRoute('comments_index');
    }
}
