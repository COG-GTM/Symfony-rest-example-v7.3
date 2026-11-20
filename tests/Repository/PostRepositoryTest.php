<?php

namespace App\Tests\Repository;

use App\Entity\PostFactory;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostRepositoryTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;

    private PostRepository $postRepository;

    protected function setUp(): void
    {
        // (1) boot the Symfony kernel
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //(2) use static::getContainer() to access the service container
        $container = static::getContainer();

        //(3) get PostRepository from container.
        $this->postRepository = $container->get(PostRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }


    public function testCreatePost(): void
    {
        // persist entities with EntityManager
        $entity = PostFactory::create("test post", "test content");
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->assertNotNull($entity->getId());

        // query this post by PostRepository
        $byId = $this->postRepository->findOneBy(["id" => $entity->getId()]);
        $this->assertEquals("test post", $byId->getTitle());
        $this->assertEquals("test content", $byId->getContent());
    }

    public function testFindByKeywordCountsAllMatchesAcrossPages(): void
    {
        $marker = uniqid('kw');
        for ($i = 0; $i < 5; $i++) {
            $this->entityManager->persist(PostFactory::create("{$marker} post {$i}", "content {$i}"));
        }
        $this->entityManager->persist(PostFactory::create('other post', "mentions {$marker} in content"));
        $this->entityManager->persist(PostFactory::create('unrelated post', 'unrelated content'));
        $this->entityManager->flush();

        $page = $this->postRepository->findByKeyword($marker, 2, 3);
        $this->assertSame(6, $page->getTotalElements());
        $this->assertCount(3, $page->getContent());

        $empty = $this->postRepository->findByKeyword(uniqid('none'));
        $this->assertSame(0, $empty->getTotalElements());
        $this->assertCount(0, $empty->getContent());
    }

}
