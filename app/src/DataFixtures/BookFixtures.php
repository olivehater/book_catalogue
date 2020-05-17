<?php
/**
 * Book fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class BookFixtures.
 */
class BookFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
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
            $book->setCategory($this->getRandomReference('categories'));
            $book->setAuthor($this->getRandomReference('authors'));

            $tags = $this->getRandomReferences(
                'tags',
                $this->faker->numberBetween(0, 5)
            );

            foreach ($tags as $tag) {
                $book->addTag($tag);
            }

            return $book;
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array Array of dependencies
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            AuthorFixtures::class,
            ];
    }
}
