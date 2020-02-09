<?php


namespace App\Controller;


use App\Repository\CardsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route ("/", name="home")
     * @return Response
     */
    public function index () : Response {

        return $this->render('pages/home.html.twig');
    }

    /**
     * @Route("/contact",name="contact")
     * @return Response
     */
    public function contact() : Response
    {
        return $this->render('pages/contact.html.twig');
    }

    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @param CardsRepository $cards_repo
     * @return Response
     */
    public function search(Request $request, CardsRepository $cards_repo) : Response {
        $query = $request->query->get('query');
        $pagination = $request->query->get('page') ? $request->query->get('page') : 1;

        $total = $cards_repo->CountSearchResult($query);
        $cards = $cards_repo->SearchCardsLike($query, $pagination);

        return $this->render('searchs/search-cards-result.html.twig', [
            'cards' => $cards,
            'total' => $total
        ]);
    }

}
