<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Entity\User;
use App\Entity\Education;
use App\Handler\UserHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class UserHandlerTest extends TestCase
{
    private UserHandler $handler;
    private EntityManager|MockObject $entityManager;
    private QueryBuilder|MockObject $queryBuilder;
    private Query|MockObject $query;
    private EntityRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class);
        $this->repository = $this->createMock(EntityRepository::class);
        
        $this->handler = new UserHandler($this->entityManager);
    }

    public function testCreateUserReturns201WithUserData(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('POST')
                          ->withParsedBody([
                              'name' => 'Jan Kowalski',
                              'phone_number' => '+48 123 456 789',
                              'address' => 'ul. Testowa 1',
                              'age' => 30
                          ]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Jan Kowalski', $data['name']);
        $this->assertEquals('+48 123 456 789', $data['phone_number']);
        $this->assertEquals('ul. Testowa 1', $data['address']);
        $this->assertEquals(30, $data['age']);
    }

    public function testCreateUserWithEducationReturns201WithEducationData(): void
    {
        $education = new Education();
        $education->setName('Wyższe');

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Education::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($education);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('POST')
                          ->withParsedBody([
                              'name' => 'Jan Kowalski',
                              'phone_number' => '+48 123 456 789',
                              'address' => 'ul. Testowa 1',
                              'age' => 30,
                              'education_id' => 1
                          ]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Jan Kowalski', $data['name']);
        $this->assertNotNull($data['education']);
        $this->assertEquals('Wyższe', $data['education']['name']);
    }

    public function testGetUsersReturns200WithPagination(): void
    {
        $users = [
            (new User())->setName('Jan Kowalski')->setPhoneNumber('123')->setAddress('Test')->setAge(30),
            (new User())->setName('Anna Nowak')->setPhoneNumber('456')->setAddress('Test2')->setAge(25)
        ];

        // Mock count query
        $countQueryBuilder = $this->createMock(QueryBuilder::class);
        $countQuery = $this->createMock(Query::class);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->willReturnOnConsecutiveCalls($this->queryBuilder, $countQueryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('select')
            ->with('u', 'e')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('from')
            ->with(User::class, 'u')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->with('u.education', 'e')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('u.id', 'ASC')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(0)
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with(10)
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn($users);

        $countQueryBuilder
            ->expects($this->once())
            ->method('select')
            ->with('COUNT(u.id)')
            ->willReturnSelf();

        $countQueryBuilder
            ->expects($this->once())
            ->method('from')
            ->with(User::class, 'u')
            ->willReturnSelf();

        $countQueryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->with('u.education', 'e')
            ->willReturnSelf();

        $countQueryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($countQuery);

        $countQuery
            ->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn(2);

        $request = new ServerRequest();
        $request = $request->withMethod('GET');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertCount(2, $data['data']);
    }

    public function testGetUserByIdFromUrlAttributeReturns200WithUserData(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user);

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withAttribute('id', 1);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Jan Kowalski', $data['name']);
        $this->assertEquals('+48 123 456 789', $data['phone_number']);
    }

    public function testGetUserByIdFromQueryParamReturns200WithUserData(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user);

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams(['id' => 1]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Jan Kowalski', $data['name']);
        $this->assertEquals('+48 123 456 789', $data['phone_number']);
    }

    public function testUpdateUserWithIdInBodyReturns200WithUpdatedData(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('PUT')
                          ->withParsedBody([
                              'id' => 1,
                              'name' => 'Jan Nowy',
                              'age' => 35
                          ]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Jan Nowy', $data['name']);
        $this->assertEquals(35, $data['age']);
    }

    public function testUpdateUserWithIdInUrlAttributeReturns200WithUpdatedData(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('PUT')
                          ->withAttribute('id', 1)
                          ->withParsedBody([
                              'name' => 'Jan Nowy',
                              'age' => 35
                          ]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Jan Nowy', $data['name']);
        $this->assertEquals(35, $data['age']);
    }

    public function testDeleteUserWithIdInQueryParamReturns200WithSuccessMessage(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->isInstanceOf(User::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('DELETE')
                          ->withQueryParams(['id' => 1]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('User deleted successfully', $data['message']);
    }

    public function testDeleteUserWithIdInUrlAttributeReturns200WithSuccessMessage(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->isInstanceOf(User::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('DELETE')
                          ->withAttribute('id', 1);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('User deleted successfully', $data['message']);
    }

    public function testMethodNotAllowedReturns405(): void
    {
        $request = new ServerRequest();
        $request = $request->withMethod('PATCH');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(405, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Method not allowed', $data['error']);
    }

    public function testGetUserByIdReturns404WhenUserNotFound(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams(['id' => 999]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('User not found', $data['error']);
    }

    public function testUpdateUserReturns404WhenUserNotFound(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $request = new ServerRequest();
        $request = $request->withMethod('PUT')
                          ->withParsedBody(['id' => 999, 'name' => 'Test']);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('User not found', $data['error']);
    }

    public function testDeleteUserReturns404WhenUserNotFound(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $request = new ServerRequest();
        $request = $request->withMethod('DELETE')
                          ->withQueryParams(['id' => 999]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('User not found', $data['error']);
    }

    public function testUpdateUserReturns400WhenIdMissing(): void
    {
        $request = new ServerRequest();
        $request = $request->withMethod('PUT')
                          ->withParsedBody(['name' => 'Test']);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('ID is required', $data['error']);
    }

    public function testDeleteUserReturns400WhenIdMissing(): void
    {
        $request = new ServerRequest();
        $request = $request->withMethod('DELETE');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('ID is required', $data['error']);
    }
} 