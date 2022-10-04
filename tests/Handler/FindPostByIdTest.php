<?php

namespace Test\Handler;

use JsonException;
use Monolog\Test\TestCase;
use PHP2\App\blog\Post;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Handler\Blog\FindPostById;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\SuccessfulResponse;
use Test\DummyLogger;

class FindPostByIdTest extends TestCase
{
    private function postRepository(array $posts): PostRepositoryInterface
    {
        return new class($posts) implements PostRepositoryInterface {

            private array $posts;

            public function __construct(array $posts)
            {
                $this->posts = $posts;
            }

            public function get(int $id): Post
            {
                foreach ($this->posts as $post) {
                    if ($post instanceof Post && $id === $post->getId()) {
                        return $post;
                    }
                }

                throw new PostNotFoundException("Such post not found");
            }

            public function findPost(int $userId, string $title): Post
            {
                throw new PostNotFoundException();
            }
        };
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */

    public function testItReturnsErrorResponseIfNoIdProvided(): void
    {
        $request = new Request([], [], '');

        $postRepository = $this->postRepository([]);
        $dummyLogger = new DummyLogger();

        $action = new FindPostById($postRepository, $dummyLogger);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: postId"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */

    public function testItReturnsErrorResponseIfPostNotFound(): void
    {
        $request = new Request(['postId' => '3'], [], '');

        $postRepository = $this->postRepository([]);
        $dummyLogger = new DummyLogger();

        $action = new FindPostById($postRepository, $dummyLogger);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Such post not found"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */

    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['postId' => '3'], [], '');

        $post = new Post('title', 'post');
        $post->setId('3');

        $postRepository = $this->postRepository([$post]);
        $dummyLogger = new DummyLogger();

        $action = new FindPostById($postRepository, $dummyLogger);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->expectOutputString('{"success":true,"data":{"postId":"3","title":"title","post":"post"}}');

        $response->send();
    }

}