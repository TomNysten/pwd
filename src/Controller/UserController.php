<?php


namespace App\Controller;

use App\Entity\Cards;
use App\Entity\CollectionContent;
use App\Entity\Comments;
use App\Entity\Users;
use App\Entity\WishlistContent;
use App\Entity\Wishlists;
use App\Form\CommentFormType;
use App\Form\NewWishlistFormType;
use App\Form\UpdateUserFormType;
use App\Form\UpdateUserImageType;
use App\Form\UsersType;
use App\Repository\CardsRepository;
use App\Repository\CollectionContentRepository;
use App\Repository\CollectionsRepository;
use App\Repository\CommentsRepository;
use App\Repository\SetsRepository;
use App\Repository\UsersRepository;
use App\Repository\WishlistContentRepository;
use App\Repository\WishlistsRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Sodium\add;

class UserController extends AbstractController
{

    /**
     * @Route("Users/{userName}/profile", name="profile")
     * @param String $userName
     * @param UsersRepository $user_repo
     * @param CommentsRepository $com_repo
     * @param CollectionContentRepository $collec_repo
     * @param WishlistsRepository $wish_repo
     * @param WishlistContentRepository $wish_con_repo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploader $uploader
     * @return Response
     * @throws Exception
     */
    public function userProfile(String $userName,
                                UsersRepository $user_repo, CommentsRepository $com_repo, CollectionContentRepository $collec_repo, WishlistsRepository $wish_repo,
                                WishlistContentRepository $wish_con_repo,
                                Request $request, EntityManagerInterface $manager, FileUploader $uploader) : Response {

        $user = $user_repo->findOneBy(['username' => $userName]);
        $user_connected = $this->getUser();

        if ($user_connected != NULL) {
            if ($userName == $user_connected->getUsername())
                 {$at_home = true;}
            else {$at_home = false;}
        }
        else     {$at_home = false;}

        $coms_by_user   = $com_repo->findAllWithLocation($userName);
        $coms_on_user   = $com_repo->findBy(['postedOnUser' => $user->getId()], ['postedAt' => 'DESC'], 10, null);

        $user_stats['comments']['total']    = $com_repo->count(['auteur' => $user]);
        $user_stats['collection']['total']  = $collec_repo->count(['fromCollection' => $user->getCollection()]);
        $user_stats['wishlist']['nmb']      = $wish_repo->count(['users' => $user->getId()]);
        $user_stats['wishlist']['total']    = $wish_con_repo->count(['fromWishlist' => $user->getDefaultWishlist()]);

        $new_com = new Comments();
        $form    = $this->createForm(CommentFormType::class, $new_com);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            $new_com->setPostedOnUser($user);
            $new_com->setPostedAt(new \DateTime('now'));
            $new_com->setAuteur($user_connected);
            $new_com->setDeleted(false);
            $new_com->setValidated(false);

            $manager->persist($new_com);
            $manager->flush();
            return $this->redirectToRoute('profile', [
                'userName' => $userName
            ]);
        }

        $form_user_img = $this->createForm(UpdateUserImageType::class, $user_connected);
        $form_user_img->handleRequest($request);

        if ($form_user_img->isSubmitted() && $form_user_img->isValid() ) {
            $new_image = $form_user_img->get('image')->getData();
            if ($new_image) {
                $old_image = $user_connected->getImage();
                $new_image_name = $uploader->upload($new_image);
                $user_connected->setImage($new_image_name);
                $manager->persist($user_connected);
                $manager->flush();
                if ($old_image != 'default.jpg') {
                    unlink("C:/Users/kevin/Code/Symfony/01_test/public/assets/images/users/".$old_image);
                }
                $this->addFlash('success', 'Image de profil mise à jour.');
            }
            else {
                $this->addFlash('error', 'L\'upload a échoué.');
            }
        }

        return $this->render('user/user-profile.html.twig', [
            'coms_by_user'      => $coms_by_user,
            'coms_on_user'      => $coms_on_user,
            'user'              => $user,
            'user_stats'        => $user_stats,
            'at_home'           => $at_home,
            'form'              => $form->createView(),
            'form_user_img'     => $form_user_img->createView()
        ]);
    }

    /* ================================================================ */

    /**
     * @Route("Users/{userName}/profile/update", name="updateUserData")
     * @param String $userName
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function updateUserData(String $userName, EntityManagerInterface $manager, Request $request) : Response {
        $user = $this->getUser();

        if ($user->getUsername() == $userName) {
            $status = true;

            $form = $this->createForm(UpdateUserFormType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute('profile', [
                    'userName'=> $user->getUsername()
                ]);
            }
        }
        else {
            $status = false;
            $form = NULL;
        }

        if ($status == true) {
            $form = $form->createView();
        }
        return $this->render('user/user-profile-update.html.twig', [
            'form' => $form,
            'status' => $status
        ]);
    }

    /* ================================================================ */

    /**
     * @Route("Users/{userName}/wishlists/", name="wishlists")
     * @param String $userName
     * @param WishlistsRepository $wish_repo
     * @param UsersRepository $user_repo
     * @param WishlistContentRepository $wish_con_repo
     * @param Request $request
     * @return Response
     */
    public function userWishlists (String $userName,
                                   WishlistsRepository $wish_repo, UsersRepository $user_repo, WishlistContentRepository $wish_con_repo,
                                   Request $request) : Response {
        $user      = $user_repo->findOneBy(['username' => $userName]);
        $wishlists = $wish_repo->findBy(['users' => $user->getId()]);

        $wish_id = $request->query->get('wishlist');

        if ($wish_id !== NULL) {
            $wishlist_now = $wish_repo->findOneBy(['id' => $wish_id]);
            $wish_content = $wish_con_repo->findBy(['fromWishlist' => $wish_id]);
            $cardsPerPage = 50;
        }
        else {
            $wish_content = NULL;
            $wishlist_now = NULL;
            $cardsPerPage = 50;
        }

        return $this->render('user/user-wishlists.html.twig', [
            'wishlist_now' => $wishlist_now,
            'wishlists' => $wishlists,
            'cards' => $wish_content,
            'cardsPerPage' => $cardsPerPage,
            'wishId' => $wish_id
        ]);
    }

    /**
     * @Route("Users/{userName}/collection", name="collection")
     * @param String $userName
     * @param CollectionContentRepository $col_cont_repo
     * @param SetsRepository $set_repo
     * @param Request $request
     * @param CardsRepository $card_repo
     * @return Response
     */
    public function userCollection(String $userName, CollectionContentRepository $col_cont_repo, SetsRepository $set_repo, Request $request, CardsRepository $card_repo) : Response {
        $ext_content    = $col_cont_repo->countCardsInExtFromUserCollection($userName);
        $userColId      = $this->getUser()->getCollection();

        $ext_needed     = $request->query->get('extension');
        $order          = $request->query->get('orderby');
        $cardsPerPage   = $request->query->get('cardsPerPage');

        $total = [
            'cards' => $col_cont_repo->count(['fromCollection' => $userColId]),
        ];

        if ($ext_needed != NULL) {
            $cards  = $col_cont_repo->findByUserAndSetName($userColId, $ext_needed);
            $ext    = $set_repo->findOneBy(["name" => $ext_needed]);
            $total['cards_in_ext'] = $card_repo->count(['cardSet' => $ext->getId()]);
            $total['youOwn']       = count($cards);
        }
        else {
            $cards = NULL;
            $ext   = NULL;
        }

        return $this->render('user/user-collection.html.twig', [
            'ext_content'  => $ext_content,
            'cards'        => $cards,
            'ext'          => $ext,
            'cardsPerPage' => $cardsPerPage,
            'total'        => $total,
            'col_id'       => $userColId->getId()
        ]);
    }

    /**
     * @Route("Users/{userName}/wishlists/new", name="wishlist_new")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param String $userName
     * @return Response
     */
    function newWishlist(String $userName, Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'vous devez être connecté pour ajouter une wishlist !');
            $this->redirectToRoute('profile', [
                'userName' => $user->getUsername()
            ]);
        }
        else {
            if ($user->getUsername() != $userName) {
                $this->addFlash('error', 'Vous ne pouvez pas créer de wishlist pour quelqu\'un d\'autre !');
                $this->redirectToRoute('profile', [
                    'userName' => getUsername()
                ]);
            }
            else {
                $new_wishlist = new Wishlists();
                $form = $this->createForm(NewWishlistFormType::class, $new_wishlist);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid() ) {
                    $new_wishlist->setUsers($user);

                    $manager->persist($new_wishlist);
                    $manager->flush();
                    return $this->redirectToRoute('wishlists', [
                        'userName' => $userName
                    ]);
                }
                return $this->render('user/user-new-wishlist.html.twig', [
                    'form' => $form->createView()
                ]);
            }
        }
    }

    /**
     * @Route("Users/{userName}/wishlists/makeDefault/{id}", name="wishlist_default")
     * @param String $userName
     * @param int $id
     * @param EntityManagerInterface $manager
     * @param WishlistsRepository $wish_repo
     * @return Response
     */
    public function makeWishlistDefault(String $userName, int $id, EntityManagerInterface $manager, WishlistsRepository $wish_repo) {
        $user = $this->getUser();
        $default_wish = $user->getDefaultWishlist();
        $default_wish = $default_wish->getId();


        if ($id == $default_wish) {
            $this->addFlash('error', 'Cette wishlist est déjà celle par défaut.');
            return $this->redirectToRoute('wishlists', [
                'userName' => $userName
            ]);
        }
        else {
            $wishlist = $wish_repo->find($id);
            $user->setDefaultWishlist($wishlist);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash("Success", "Cette wishlist est désormais celle par défaut.");
            return $this->redirectToRoute('wishlists', [
                'userName' => $userName
            ]);
        }
    }

    /**
     * @Route("Users/{userName}/wishlists/delete/{id}", name="delete_wishlist")
     * @param String $userName
     * @param int $id
     * @param EntityManagerInterface $manager
     * @param WishlistsRepository $wish_repo
     * @return Response
     */
    public function deleteWishlist(String $userName, int $id, EntityManagerInterface $manager, WishlistsRepository $wish_repo) {
        $wishlist = $wish_repo->find($id);
        $user = $this->getUser();
        $default_wish = $user->getDefaultWishlist();
        $default_wish = $default_wish->getId();

        if ($id == $default_wish) {
            $this->addFlash("error", "Vous ne pouvez pas supprimer la wishlist par défaut.");
            return $this->redirectToRoute('wishlists', [
                'userName' => $userName
            ]);
        }
        else {
            $manager->remove($wishlist);
            $manager->flush();
            $this->addFlash('success', 'wishlist supprimée avec succès.');
            return $this->redirectToRoute('wishlists', [
                'userName' => $userName
            ]);
        }

    }

    /**
     * @Route("Users/{userName}/collection/reset/{id}", name="reset_collection")
     * @param String $userName
     * @param int $id
     * @param EntityManagerInterface $manager
     * @param CollectionContentRepository $col_con_repo
     * @param CollectionsRepository $col_repo
     * @return Response
     */
    public function resetCollection(String $userName, int $id, EntityManagerInterface $manager, CollectionContentRepository $col_con_repo, CollectionsRepository $col_repo) {
        $user = $this->getUser();
        $the_collection = $col_repo->find($id);

        if ($user != NULL && $userName == $user->getUsername()) {
            $cards = $col_con_repo->findBy(['fromCollection' => $the_collection]);
            foreach ($cards as $card) {
                $manager->remove($card);
            }
            $manager->flush();
        }

        return $this->redirectToRoute('collection', [
            'userName' => $userName
        ]);
    }
    /* ===========================================================================
	* 								   A J A X
	* ============================================================================ */

    /**
     * @Route("Users/collection/add/{card}", name="collec_add_cards")
     * @param Cards $card
     * @param CardsRepository $card_repo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function addToCollection(Cards $card, CardsRepository $card_repo,
                                    Request $request, EntityManagerInterface $manager, CollectionContentRepository $col_con_repo) {
        $user = $this->getUser();
        $card_repo->find($card);

        if (!$user) {
            return $this->json ([
                'code'    => 403,
                'message' => 'Connectez vous pour réaliser cette action.'
            ], 403);
        }
        else {
            $quantity = $request->request->get('quantity');
            $already_exist = $col_con_repo->findOneBy([
                'cards' => $card,
                'fromCollection' => $user->getCollection()]
            );
            if ($already_exist) {
                $old_quantity = $already_exist->getQuantity();
                $old_quantity += $quantity;
                $already_exist->setQuantity($old_quantity);

                $manager->persist($already_exist);
                $manager->flush();
                return $this->json ([
                    'code'     => 200,
                    'message'  => 'Carte mise à jour dans votre collection',
                    'quantity' => $old_quantity
                ], 200);
            }
            else {
                $new_card = new CollectionContent();
                $new_card->setFromCollection($user->getCollection());
                $new_card->setQuantity($quantity);
                $new_card->setCards($card);

                $manager->persist($new_card);
                $manager->flush();
                return $this->json ([
                    'code'    => 200,
                    'message' => 'Carte ajoutée à votre collection',
                ], 200);
            }
        }
    }

    /**
     * @Route("Users/collection/remove/{card}", name="collec_remove_cards")
     * @param Cards $card
     * @param CardsRepository $card_repo
     * @param CollectionContentRepository $col_content_repo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function removeFromCollection (Cards $card, CardsRepository $card_repo, CollectionContentRepository $col_content_repo,
                                          Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();
        $card_repo->find($card);

        if (!$user) {
            return $this->json ([
                'code'    => 403,
                'message' => 'Connectez vous pour réaliser cette action.',
            ], 403);
        }
        else {
            $quantity = $request->request->get('quantity');
            $card_in_col = $col_content_repo->findOneBy([
                'cards' => $card,
                'fromCollection' => $user->getCollection()
            ]);

            if ($card_in_col != NULL) {
                $cards_nmb = $card_in_col->getQuantity();
                if ($cards_nmb <= $quantity) {
                    $manager->remove($card_in_col);
                    $manager->flush();
                    return $this->json ([
                        'code'           => 200,
                        'message'        => 'La carte a bien été retirée de la collection.',
                        'removed'        => true
                    ], 200);
                }
                else {
                    $new_quantity = $cards_nmb - $quantity;
                    $card_in_col->setQuantity($new_quantity);
                    $manager->persist($card_in_col);
                    $manager->flush();
                    return $this->json ([
                        'code'           => 200,
                        'message'        => 'La carte a bien été mise à jour dans la collection.',
                        'quantity'   => $new_quantity
                    ], 200);
                }
            }
            else {
                return $this->json ([
                    'code'           => 200,
                    'message'        => 'La carte n\'a pas été trouvée dans la collection !',
                ], 200);
            }

        }
    }

    /**
     * @Route("Users/wishlists/add/{card}", name="wish_add_cards")
     * @param Cards $card
     * @param CardsRepository $card_repo
     * @param WishlistContentRepository $wish_con_repo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function addToWishlist(Cards $card, CardsRepository $card_repo, WishlistContentRepository $wish_con_repo,
                                  Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();
        $card_repo->find($card);

        if (!$user) {
            return $this->json ([
                'code'    => 403,
                'message' => 'Connectez-vous pour effectuer cette action.'
            ], 403);
        }
        else {
            $quantity    = $request->request->get('quantity');
            $target      = $request->request->get('wishlist');

            /* test pour vérifier si une wishlist spécifique a été ciblée ou si c'est celle par défaut */

            if ($target != NULL) {$wish_target = $target;}
            else                 {$wish_target = $user->getDefaultWishlist();}

            $already_exist = $wish_con_repo->findOneBy([
                'cards' => $card,
                'fromWishlist' => $wish_target
            ]);
            if ($already_exist) {
                $old_quantity = $already_exist->getQuantity();
                $old_quantity += $quantity;
                $already_exist->setQuantity($old_quantity);

                $manager->persist($already_exist);
                $manager->flush();
                return $this->json ([
                    'code'     => 200,
                    'message'  => 'Carte mise à jour dans votre wishlist',
                    'quantity' => $old_quantity
                ], 200);
            }
            else {
                $new_card = new WishlistContent();
                $new_card->setFromWishlist($user->getDefaultWishlist());
                $new_card->setQuantity($quantity);
                $new_card->setCards($card);

                $manager->persist($new_card);
                $manager->flush();
                return $this->json ([
                    'code'    => 200,
                    'message' => 'La carte a bien été ajoutée à la wishlist'
                ], 200);
            }
        }
    }

    /**
     * @Route("Users/wishlists/remove/{card}", name="wish_remove_cards")
     * @param Cards $card
     * @param CardsRepository $card_repo
     * @param WishlistContentRepository $wish_con_repo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function removeFromWishlist (Cards $card, CardsRepository $card_repo, WishlistContentRepository $wish_con_repo,
                                          Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();
        $card_repo->find($card);

        if (!$user) {
            return $this->json ([
                'code'    => 403,
                'message' => 'Connectez vous pour réaliser cette action.'
            ], 403);
        }
        else {
            $quantity = $request->request->get('quantity');
            $target   = $request->request->get('wishlist');

            /* test pour vérifier si une wishlist spécifique a été ciblée ou si c'est celle par défaut */
            if ($target != NULL) {$wish_target = $target;}
            else                 {$wish_target = $user->getDefaultWishlist();}

            $card_in_wish = $wish_con_repo->findOneBy([
                'cards' => $card,
                'fromWishlist' => $wish_target
            ]);

            if ($card_in_wish != NULL) {
                $cards_nmb = $card_in_wish->getQuantity();
                if ($cards_nmb <= $quantity) {
                    $manager->remove($card_in_wish);
                    $manager->flush();
                    return $this->json ([
                        'code'           => 200,
                        'message'        => 'La carte a bien été retirée de la wishlist.',
                        'removed'        => true
                    ], 200);
                }
                else {
                    $new_quantity = $cards_nmb - $quantity;
                    $card_in_wish->setQuantity($new_quantity);
                    $manager->persist($card_in_wish);
                    $manager->flush();
                    return $this->json ([
                        'code'           => 200,
                        'message'        => 'La carte a bien été mise à jour dans la wishlist.',
                        'quantity'       => $new_quantity
                    ], 200);
                }
            }
            else {
                return $this->json ([
                    'code'           => 200,
                    'message'        => 'La carte n\'a pas été trouvée dans la wishlist !',
                ], 200);
            }

        }
    }

    /* ====================================================================
                                   C R U D
    ======================================================================= */

    /**
     * @Route("/Admin/users/index", name="users_index", methods={"GET"})
     * @param UsersRepository $usersRepository
     * @return Response
     */
    public function index(UsersRepository $usersRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $usersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/Admin/users/show/{id}", name="users_show", methods={"GET"})
     * @param Users $user
     * @return Response
     */
    public function show(Users $user): Response
    {
        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/Admin/users/new", name="users_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('admin/users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/users/edit/{id}", name="users_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Users $user
     * @return Response
     */
    public function edit(Request $request, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form['roles']->getData();
            $new_role = json_encode($role);
            $user->setRole($role);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/users/delete/{id}", name="users_delete", methods={"DELETE"})
     * @param Request $request
     * @param Users $user
     * @return Response
     */
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index');
    }
}