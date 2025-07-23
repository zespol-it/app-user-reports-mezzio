<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Entity\Education;
use App\Handler\EducationHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EducationHandlerTest extends TestCase
{
    private EducationHandler $handler;
    private EntityManager|MockObject $entityManager;
    private EntityRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(EntityRepository::class);
        
        $this->handler = new EducationHandler($this->entityManager);
    }

    public function testCreateEducationReturns201WithEducationData(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Education::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('POST')
                          ->withParsedBody(['name' => 'Wyższe']);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Wyższe', $data['name']);
    }

    public function testCreateEducationReturns400WhenNameMissing(): void
    {
        $request = new ServerRequest();
        $request = $request->withMethod('POST')
                          ->withParsedBody([]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Name is required', $data['error']);
    }

    public function testGetEducationsReturns200WithAllEducations(): void
    {
        $educations = [
            (new Education())->setName('Podstawowe'),
            (new Education())->setName('Średnie'),
            (new Education())->setName('Wyższe')
        ];

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Education::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($educations);

        $request = new ServerRequest();
        $request = $request->withMethod('GET');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertCount(3, $data);
        $this->assertEquals('Podstawowe', $data[0]['name']);
        $this->assertEquals('Średnie', $data[1]['name']);
        $this->assertEquals('Wyższe', $data[2]['name']);
    }

    public function testGetEducationByIdFromUrlAttributeReturns200WithEducationData(): void
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

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withAttribute('id', 1);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Wyższe', $data['name']);
    }

    public function testGetEducationByIdFromQueryParamReturns200WithEducationData(): void
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

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams(['id' => 1]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Wyższe', $data['name']);
    }

    public function testUpdateEducationWithIdInBodyReturns200WithUpdatedData(): void
    {
        $education = new Education();
        $education->setName('Stare Wykształcenie');

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
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('PUT')
                          ->withParsedBody([
                              'id' => 1,
                              'name' => 'Nowe Wykształcenie'
                          ]);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Nowe Wykształcenie', $data['name']);
    }

    public function testUpdateEducationWithIdInUrlAttributeReturns200WithUpdatedData(): void
    {
        $education = new Education();
        $education->setName('Stare Wykształcenie');

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
            ->method('flush');

        $request = new ServerRequest();
        $request = $request->withMethod('PUT')
                          ->withAttribute('id', 1)
                          ->withParsedBody(['name' => 'Nowe Wykształcenie']);

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Nowe Wykształcenie', $data['name']);
    }

    public function testDeleteEducationWithIdInQueryParamReturns200WithSuccessMessage(): void
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
            ->method('remove')
            ->with($this->isInstanceOf(Education::class));

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
        $this->assertEquals('Education deleted successfully', $data['message']);
    }

    public function testDeleteEducationWithIdInUrlAttributeReturns200WithSuccessMessage(): void
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
            ->method('remove')
            ->with($this->isInstanceOf(Education::class));

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
        $this->assertEquals('Education deleted successfully', $data['message']);
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

    public function testGetEducationByIdReturns404WhenEducationNotFound(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Education::class)
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
        $this->assertEquals('Education not found', $data['error']);
    }

    public function testUpdateEducationReturns404WhenEducationNotFound(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Education::class)
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
        $this->assertEquals('Education not found', $data['error']);
    }

    public function testDeleteEducationReturns404WhenEducationNotFound(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Education::class)
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
        $this->assertEquals('Education not found', $data['error']);
    }

    public function testUpdateEducationReturns400WhenIdMissing(): void
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

    public function testDeleteEducationReturns400WhenIdMissing(): void
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