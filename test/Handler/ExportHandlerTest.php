<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Entity\User;
use App\Entity\Education;
use App\Handler\ExportHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ExportHandlerTest extends TestCase
{
    private ExportHandler $handler;
    private EntityManager|MockObject $entityManager;
    private QueryBuilder|MockObject $queryBuilder;
    private Query|MockObject $query;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class);
        
        $this->handler = new ExportHandler($this->entityManager);
    }

    public function testExportXlsReturns200WithXlsContent(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $education = new Education();
        $education->setName('Wyższe');
        $user->setEducation($education);

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn([$user]);

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams(['format' => 'xls']);

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('attachment; filename="users_report_', $response->getHeaderLine('Content-Disposition'));
        $this->assertStringContainsString('.xlsx"', $response->getHeaderLine('Content-Disposition'));
    }

    public function testExportPdfReturns200WithPdfContent(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $education = new Education();
        $education->setName('Wyższe');
        $user->setEducation($education);

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn([$user]);

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams(['format' => 'pdf']);

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/pdf', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('attachment; filename="users_report_', $response->getHeaderLine('Content-Disposition'));
        $this->assertStringContainsString('.pdf"', $response->getHeaderLine('Content-Disposition'));
    }

    public function testExportWithFiltersReturns200WithFilteredData(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->exactly(2))
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn([$user]);

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams([
                              'format' => 'xls',
                              'name' => 'Jan',
                              'age' => 30
                          ]);

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testExportWithSortingReturns200WithSortedData(): void
    {
        $user = new User();
        $user->setName('Jan Kowalski');
        $user->setPhoneNumber('+48 123 456 789');
        $user->setAddress('ul. Testowa 1');
        $user->setAge(30);

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->willReturnSelf();

        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn([$user]);

        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams([
                              'format' => 'xls',
                              'sort_by' => 'name',
                              'sort_order' => 'ASC'
                          ]);

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testExportWithInvalidFormatReturns400(): void
    {
        $request = new ServerRequest();
        $request = $request->withMethod('GET')
                          ->withQueryParams(['format' => 'invalid']);

        $response = $this->handler->handle($request);

        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Unsupported format', $data['error']);
    }

    public function testExportWithoutFormatReturns400(): void
    {
        $request = new ServerRequest();
        $request = $request->withMethod('GET');

        $response = $this->handler->handle($request);

        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Format parameter is required', $data['error']);
    }
} 