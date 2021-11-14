<?php


namespace App\Service;


use App\Entity\Author;
use App\Entity\Book;
use App\Entity\PublishingHouse;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\PublishingHouseRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    private EntityManagerInterface $em;
    private BookRepository $bookRepository;

    /**
     * BookService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->bookRepository = $em->getRepository(Book::class);
    }

    public function createNewBook(string $name, int $year, int $idPubHouse, ?int $idAuthor)
    {
        if ($idAuthor >= 1){

            /** @var AuthorRepository $repository */
            $repository = $this->em->getRepository(Author::class);
            $author = $repository->find($idAuthor);

        }

        if ($idPubHouse >= 1){

            /** @var PublishingHouseRepository $repository */
            $repository = $this->em->getRepository(PublishingHouse::class);
            $pubHouse = $repository->find($idPubHouse);

        }

        $book = new Book();
        $book->setName($name);
        $book->setYear($year);
        $book->addAuthor($author ?? null);
        $book->setPubHouse($pubHouse ?? null);

        $this->em->persist($book);
        $this->em->flush();

    }

    public function editBook(int $idBook, ?string $name, ?int $year, ?int $idPubHouse, ?int $idAuthor)
    {
        $book= $this->bookRepository->find($idBook);

        /** @var AuthorRepository $repositoryAuthor */
        $repositoryAuthor = $this->em->getRepository(Author::class);
        $author = $repositoryAuthor->find($idAuthor);

        /** @var PublishingHouseRepository $repositoryPublishingHouseRepository */
        $repositoryPublishingHouseRepository = $this->em->getRepository(PublishingHouse::class);
        $pubHouse = $repositoryPublishingHouseRepository->find($idPubHouse);

        if ($book != null) {
            $book->setName($name);
            $book->setYear($year);
            $book->setPubHouse($pubHouse);
            $book->addAuthor($author);

            $this->em->flush();
        }
    }

    public function removeBooksByPubHouseName(string $pubHouseName)
    {
        /** @var PublishingHouseRepository $repositoryPublishingHouseRepository */
        $repositoryPublishingHouseRepository = $this->em->getRepository(PublishingHouse::class);

        $pubHouses = $repositoryPublishingHouseRepository->findBy([
            'name' => $pubHouseName
        ]);

        if ($pubHouses != null) {
            $books = [];
            foreach ($pubHouses as $pubHouse) {
                $booksFromPubHouse = $pubHouse->getBooks();
//                $booksFromRepository = $this->bookRepository->findBy([
//                    'pubHouse' => $pubHouse
//                ]);
//                echo gettype($booksFromRepository) . "\n";
                $books = array_merge($books, $booksFromPubHouse);
            }

            foreach ($books as $book) {
                $this->em->remove($book);
            }

            $this->em->flush();
        }

    }

    /**
     * @param array $data
     * @return array
     */
    public function getAllBooks(array $data): array
    {
        /** @var BookRepository $repository */
        $repository = $this->em->getRepository(Book::class);
        return $repository->findByFilters($data);
    }
}