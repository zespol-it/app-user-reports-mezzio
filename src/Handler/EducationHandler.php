<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Education;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EducationHandler implements RequestHandlerInterface
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        
        return match ($method) {
            'GET' => $this->handleGet($request),
            'POST' => $this->handlePost($request),
            'PUT' => $this->handlePut($request),
            'DELETE' => $this->handleDelete($request),
            default => new JsonResponse(['error' => 'Method not allowed'], 405),
        };
    }

    private function handleGet(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();
        $id = $attributes ?? ($queryParams['id'] ?? null);

        if ($id) {
            return $this->getEducation((int) $id);
        }

        return $this->getEducations();
    }

    private function handlePost(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        
        if (!$data || !isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], 400);
        }

        $education = new Education();
        $education->setName($data['name']);

        $this->entityManager->persist($education);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $education->getId(),
            'name' => $education->getName(),
        ], 201);
    }

    private function handlePut(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $attributes = $request->getAttribute('id');
        $id = $attributes ?? ($data['id'] ?? null);

        if (!$id) {
            return new JsonResponse(['error' => 'ID is required'], 400);
        }

        $education = $this->entityManager->getRepository(Education::class)->find($id);
        if (!$education) {
            return new JsonResponse(['error' => 'Education not found'], 404);
        }

        if (isset($data['name'])) {
            $education->setName($data['name']);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $education->getId(),
            'name' => $education->getName(),
        ]);
    }

    private function handleDelete(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();
        $id = $attributes ?? ($queryParams['id'] ?? null);

        if (!$id) {
            return new JsonResponse(['error' => 'ID is required'], 400);
        }

        $education = $this->entityManager->getRepository(Education::class)->find($id);
        if (!$education) {
            return new JsonResponse(['error' => 'Education not found'], 404);
        }

        $this->entityManager->remove($education);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Education deleted successfully']);
    }

    private function getEducation(int $id): ResponseInterface
    {
        $education = $this->entityManager->getRepository(Education::class)->find($id);
        
        if (!$education) {
            return new JsonResponse(['error' => 'Education not found'], 404);
        }

        return new JsonResponse([
            'id' => $education->getId(),
            'name' => $education->getName(),
        ]);
    }

    private function getEducations(): ResponseInterface
    {
        $educations = $this->entityManager->getRepository(Education::class)->findAll();
        
        $result = [];
        foreach ($educations as $education) {
            $result[] = [
                'id' => $education->getId(),
                'name' => $education->getName(),
            ];
        }

        return new JsonResponse($result);
    }
} 