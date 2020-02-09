<?php


namespace App\Services;


class PaginateCarousel
{

    public function getAllPages(array $cards, int $total, $cardsPerPage) {

        // pagination dans le carousel
        define('VALEUR_ACCEPTEES', [30, 50, 75, 100, 120, 150]);

        if ($cardsPerPage == NULL || !(in_array($cardsPerPage, VALEUR_ACCEPTEES)) ) {
            $cardsPerPage = 50;
        }

        $every_pages = [];
        $debut_tranche = 0;
        $remaining_cards = $total;
        $pageNmb = (int)($total / $cardsPerPage);

        if ($total % $cardsPerPage != 0) {
            $pageNmb++;
        }
        for ($i = 0; $i < $pageNmb; $i++) {
            if ($remaining_cards < $cardsPerPage) {
                $every_pages[$i] = array_slice($cards, $debut_tranche, $remaining_cards);
            }
            else {
                $every_pages[$i] = array_slice($cards, $debut_tranche, $cardsPerPage);
                $remaining_cards -= $cardsPerPage;
            }
            $debut_tranche +=$cardsPerPage;
        }

        return $every_pages;

    }
    public function countCardRarities($cards) {
        // on compte les raretés
        $total = [
            'all'           => count($cards),
            'common'        => 0,
            'uncommon'      => 0,
            'rare'          => 0,
            'mythic'        => 0,
            'timeshifted'   => 0,
            'masterpiece'   => 0
        ];

        foreach ($cards as $key) {
            switch ($key['cardRarity']['name']) {
                case 'Common':
                    $total['common']++;
                    break;
                case 'Uncommon':
                    $total['uncommon']++;
                    break;
                case 'Rare':
                    $total['rare']++;
                    break;
                case 'Muthic Rare':
                    $total['mythic']++;
                    break;
                case 'Timeshifted':
                    $total['timeshifted']++;
                    break;
                case 'Masterpiece':
                    $total['masterpiece']++;
                    break;
                default:
                    break;
            }
        }
        return $total;
    }

}