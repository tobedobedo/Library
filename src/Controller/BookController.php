<?php

namespace App\Controller;

use App\Service\BookService;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * Создание книги
     *
     * В теле ожидается массив книг, для каждой:
     * - name (обязательно)
     * - year (обязательно)
     * - idPubHouse (обязательно)
     * - idAuthor (необязательно)
     *
     * @param Request $request
     * @param BookService $bookService
     * @return JsonResponse
     * @Route ("/api/create-new-book")
     */
    public function createBook(Request $request, BookService $bookService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json, true);

        try {
            foreach ($body as $book) {
                if (isset($book['name']) && isset($book['year'])) {
                    $bookService->createNewBook($book['name'], $book['year'], $book['idPubHouse'] ?? null, $book['idAuthor'] ?? null);
                } else {
                    throw new RuntimeException('Не указаны название книги, издательство или год');
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
     * Редактирование книги
     *
     * В теле ожидается:
     * - idBook (обязательно)
     * - name (необязательно)
     * - year (необязательно)
     * - idAuthor (необязательно)
     * - idPubHouse (необязательно)
     *
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
     * Удаление книг по имени издательства
     *
     * В теле ожидается:
     * - pubHouseName (обязательно)
     *
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
     * Получение книг с их издательствами и авторами
     *
     * Если тело пустое, то будут возвращены все
     *
     * Если нужны конкретные, то в теле ожидается массив фильтров:
     * [
     *   'p.name' => 'Росмэн',
     *   'b.name' => 'Гарри Поттер'
     * ]
     *
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
