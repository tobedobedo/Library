<?php


namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\PublishingHouse;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\PublishingHouseRepository;
use Doctrine\ORM\EntityManagerInterface;

class PublishingHouseService
{
    private EntityManagerInterface $em;

    /**
     * PublishingHouseService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createPubHouse(string $name, string $address, ?int $idBook)
    {
        // создать экземпляр и ему присвоить значения в соответствующие поля

        if ($idBook >= 1){

            /** @var BookRepository $repository */
            $repository = $this->em->getRepository(Book::class);
            $book = $repository->find($idBook);

        }

        $pubHouse = new PublishingHouse();
        $pubHouse->setName($name);
        $pubHouse->setAddress($address);
        $pubHouse->addBook($book ?? null);

        $this->em->persist($pubHouse);
        $this->em->flush();
    }

    public function editPubHouse(int $idPubHouse, string $name, string $address)
    {
        /** @var PublishingHouseRepository $repository */
        $repository = $this->em->getRepository(PublishingHouse::class);
        $pubHouse = $repository->find($idPubHouse);

        if ($pubHouse != null) {
            $pubHouse->setName($name);
            $pubHouse->setAddress($address);

            $this->em->flush();
        }
    }

    public function removePubHouseByNameAndAddress(array $information)
    {

        {
            /** @var PublishingHouseRepository $repository */
            if (isset($information['name']) && isset($information['address'])) {
                $repository = $this->em->getRepository(PublishingHouse::class);
                $pubHouses = $repository->findBy([
                    'name' => $information['name'],
                    'address' => $information['address']
                ]);
            }

            if (isset($pubHouses) && $pubHouses != null) {
                /** @var PublishingHouse $pubHouse */
                foreach ($pubHouses as $pubHouse) {
                    $books = $pubHouse->getBooks();

                    /** @var Book $book */
                    foreach ($books as $book) {
                        $this->em->remove($book);
                    }
                    $this->em->remove($pubHouse);
                }

                $this->em->flush();
            }
        }
    }

    public function getPubHouses(array $data): array
    {
        /** @var PublishingHouseRepository $repository */
        $repository = $this->em->getRepository(PublishingHouse::class);
        return $repository->findByFilters($data);
    }
}