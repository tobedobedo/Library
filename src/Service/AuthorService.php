<?php


namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

class AuthorService
{
    private EntityManagerInterface $em;
    private AuthorRepository $authorRepository;

    /**
     * BookService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityManager em */
        $this->em = $em;
        $this->authorRepository = $em->getRepository(Author::class);
    }

    /**
     * @param string $name
     * @param string $surname
     * @param int|null $idBook
     */
    public function createNewAuthor(string $name, string $surname, ?int $idBook)
    {
        if ($idBook >= 1){

            /** @var BookRepository $repository */
            $repository = $this->em->getRepository(Book::class);
            $book = $repository->find($idBook);

        }

        $author = new Author();
        $author->setName($name);
        $author->setSurname($surname);
        $author->addBook($book ?? null);

        $this->em->persist($author);
        $this->em->flush();

    }

    /**
     * @param int $idAuthor
     * @param string|null $name
     * @param string|null $surname
     * @param int|null $idBook
     */
    public function editAuthor(int $idAuthor, ?string $name, ?string $surname, ?int $idBook)
    {
        $author = $this->authorRepository->find($idAuthor);

        if ($author != null) {

            if ($name != null) {
                $author->setName($name);
            }

            if ($surname != null) {
                $author->setSurname($surname);
            }

            if($idBook != null){
                /** @var BookRepository $repositoryBook */
                $repositoryBook = $this->em->getRepository(Book::class);
                $book= $repositoryBook->find($idBook);
                $author->addBook($book);
            }

            $this->em->flush();
        } else {
            throw new RuntimeException('Автор не найден', 400);
        }
    }

    /**
     * @param array $data
     */
    public function removeAuthorsByNameAndSurname(array $data)
    {
        if (isset($data['name']) && isset($data['surname'])) {

            $authors = $this->authorRepository->findBy([
                'name' => $data['name'],
                'surname' => $data['surname']
            ]);

            if (isset($authors) && $authors != null) {
                foreach ($authors as $author) {
                    $this->em->remove($author);
                }

                $this->em->flush();
            }
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function getAuthors(array $data): array
    {
        /** @var AuthorRepository $repository */
        $repository = $this->em->getRepository(Author::class);
        return $repository->findByFilters($data);
    }
}