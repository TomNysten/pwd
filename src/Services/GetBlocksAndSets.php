<?php


namespace App\Services;

use App\Repository\SetsRepository;

class GetBlocksAndSets
{
    private $setRepository;

    /**
     * GetBlocksAndSets constructor.
     * @param $setRepository
     */
    public function __construct(SetsRepository $setRepository)
    {
        $this->setRepository = $setRepository;
    }

    public function getAll ():array {

        $blocksAndSets = $this->setRepository->findAllWithBlocks();

        $block_name_now    = "vide";
        $block_name_before = "vide";
        $block_now = [
            'name' => "",
            'year' => "",
            'content' => []
        ];
        $final_list = [];
        $cpt_list    = 0;
        $cpt_content = 0;


        /* Initialisation */
        $block_name_before = $blocksAndSets[0]['block']['name'];
        $block_now['name'] = $blocksAndSets[0]['block']['name'];
        $block_now['year'] = $blocksAndSets[0]['block']['year'];

        foreach ($blocksAndSets as $key => $value) {
            $block_name_now = $value['block']['name'];

            /* si le nom du block du set actuel est différent, c'est qu'on
            entre dans un nouveau block, donc on pousse le précédent et on commence
            à encoder un nouveau block dans la liste */

            if ($block_name_now != $block_name_before) {

                // on ajoute le block content les sets à la liste
                $final_list[$cpt_list] = $block_now;
                // on augmente le compteur de la liste
                $cpt_list++;
                // on remet à 0 le compteur des sets, vu qu'il sagit du premier encodé
                $cpt_content = 0;

                // on démarre le remplissage d'un nouveau block
                $block_now['name'] = $block_name_now;
                $block_now['year'] = $value['block']['year'];
            }
            $block_now['content'][$cpt_content]['name']  = $value['name'];
            $block_now['content'][$cpt_content]['icon']  = $value['icon'];
            $cpt_content++;

            $block_name_before = $value['block']['name'];
        }

        return $final_list;
    }

}