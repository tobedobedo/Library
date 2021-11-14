<?php

namespace App\Controller;

use App\Service\BookService;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
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

        try {
            foreach ($body as $book) {
                if (isset($book['name']) && isset($book['year'])) {
                    $bookService->createNewBook($book['name'], $book['year'], $book['idAuthor'] ?? null, $book['idPubHouse'] ?? null);
                } else {
                    throw new RuntimeException('Не указаны название книги или год');
                }
            }
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
     * @Route("/api/edit-book")
     */
    public function editBook(Request $request, BookService $bookService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json,true);

        try {
            if (isset($body['idBook'])) {
                $bookService->editBook($body['idBook'], $body['name'] ?? null, $body['year'] ?? null, $body['idAuthor'] ?? null, $body['idPubHouse'] ?? null);
            } else {
                throw new RuntimeException('Не указан идентификатор книги');
            }
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
