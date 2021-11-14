<?php

namespace App\Controller;

use App\Service\BookService;
use App\Service\PublishingHouseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book")
     */
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @param Request $request
     * @param BookService $bookService
     * @return JsonResponse
     * @Route("/api/create-new-book")
     */
    public function createBook(Request $request, BookService $bookService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json, true);

        foreach ($body as $book) {
            if (isset($book['name']) && isset($book['year'])) {
                $bookService->createNewBook($book['name'], $book['year'],$book['idAuthor'], $book['idPubHouse']);
            } else {
                return new JsonResponse(['success' => false]);
            }
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @param BookService $bookService
     * @return JsonResponse
     * @Route("/api/edit-book")
     */
    public function editBook(Request $request, BookService $bookService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json,true);
        if (isset($body['idBook'])) {

            $bookService->editBook($body['idBook'], $body['name'] ?? null, $body['year'] ?? null, $body['idAuthor'] ?? null, $body['idPubHouse'] ?? null);

        }else{
            return new JsonResponse([
                'success' => false,
                'message' => 'Не указан идентификатор книги'
            ]);
        }
        return new JsonResponse([]);
    }

    /**
     * @param Request $request
     * @param BookService $bookService
     * @return JsonResponse
     * @Route("/api/remove-books")
     */
    public function removeBooksByPubHouseName(Request $request, BookService $bookService): JsonResponse
    {
        try {
            $json = $request->getContent();
            $body = json_decode($json,true);

            $bookService->removeBooksByPubHouseName($body['pubHouseName']);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @param BookService $bookService
     * @return JsonResponse
     * @Route("/api/get-all-books")
     */
    public function getAllBooks(Request $request, BookService $bookService): JsonResponse
    {
        try {
            $json = $request->getContent();
            $body = json_decode($json,true);

            return new JsonResponse($bookService->getAllBooks($body ?? []));
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
