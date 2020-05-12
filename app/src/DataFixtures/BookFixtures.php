<?php
/**
 * Book fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Persistence\ObjectManager;

/**
 * Class BookFixtures.
 */
class BookFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param \Doctrine\Persistence\ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(10, 'books', function ($i) {
            $book = new Book();
            $book->setTitle($this->faker->sentence);
            $book->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $book->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $book->setDescription($this->faker->text);

            return $book;
        });

        $manager->flush();
    }
}
