<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Entity\Education;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Handler\UserInputFilter;

class UserHandler implements RequestHandlerInterface
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
            return $this->getUser((int) $id);
        }

        return $this->getUsers($queryParams);
    }

    private function handlePost(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $inputFilter = new UserInputFilter();
        $inputFilter->setData($data);
        if (!$inputFilter->isValid()) {
            return new JsonResponse([
                'error' => 'Validation failed',
                'messages' => $inputFilter->getMessages(),
            ], 400);
        }
        $validData = $inputFilter->getValues();

        $user = new User();
        $user->setName($validData['name']);
        $user->setPhoneNumber($validData['phone_number']);
        $user->setAddress($validData['address']);
        $user->setAge((int) $validData['age']);

        if (!empty($validData['education_id'])) {
            $education = $this->entityManager->getRepository(Education::class)->find($validData['education_id']);
            $user->setEducation($education);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'phone_number' => $user->getPhoneNumber(),
            'address' => $user->getAddress(),
            'age' => $user->getAge(),
            'education' => $user->getEducation() ? [
                'id' => $user->getEducation()->getId(),
                'name' => $user->getEducation()->getName(),
            ] : null,
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

        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $inputFilter = new UserInputFilter();
        $inputFilter->setData($data);
        // W PUT wszystkie pola są opcjonalne, ale jeśli podane, muszą być poprawne
        $inputFilter->setValidationGroup(array_keys($data));
        if (!$inputFilter->isValid()) {
            return new JsonResponse([
                'error' => 'Validation failed',
                'messages' => $inputFilter->getMessages(),
            ], 400);
        }
        $validData = $inputFilter->getValues();

        if (isset($validData['name'])) {
            $user->setName($validData['name']);
        }
        if (isset($validData['phone_number'])) {
            $user->setPhoneNumber($validData['phone_number']);
        }
        if (isset($validData['address'])) {
            $user->setAddress($validData['address']);
        }
        if (isset($validData['age'])) {
            $user->setAge((int) $validData['age']);
        }
        if (isset($validData['education_id'])) {
            $education = $this->entityManager->getRepository(Education::class)->find($validData['education_id']);
            $user->setEducation($education);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'phone_number' => $user->getPhoneNumber(),
            'address' => $user->getAddress(),
            'age' => $user->getAge(),
            'education' => $user->getEducation() ? [
                'id' => $user->getEducation()->getId(),
                'name' => $user->getEducation()->getName(),
            ] : null,
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

        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User deleted successfully']);
    }

    private function getUser(int $id): ResponseInterface
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'phone_number' => $user->getPhoneNumber(),
            'address' => $user->getAddress(),
            'age' => $user->getAge(),
            'education' => $user->getEducation() ? [
                'id' => $user->getEducation()->getId(),
                'name' => $user->getEducation()->getName(),
            ] : null,
        ]);
    }

    private function getUsers(array $queryParams): ResponseInterface
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('u', 'e')
           ->from(User::class, 'u')
           ->leftJoin('u.education', 'e');

        // Filtrowanie
        if (isset($queryParams['name'])) {
            $qb->andWhere('u.name LIKE :name')
               ->setParameter('name', '%' . $queryParams['name'] . '%');
        }

        if (isset($queryParams['phone_number'])) {
            $qb->andWhere('u.phoneNumber LIKE :phone')
               ->setParameter('phone', '%' . $queryParams['phone_number'] . '%');
        }

        if (isset($queryParams['address'])) {
            $qb->andWhere('u.address LIKE :address')
               ->setParameter('address', '%' . $queryParams['address'] . '%');
        }

        if (isset($queryParams['age'])) {
            $qb->andWhere('u.age = :age')
               ->setParameter('age', (int) $queryParams['age']);
        }

        if (isset($queryParams['education_id'])) {
            $qb->andWhere('e.id = :education_id')
               ->setParameter('education_id', (int) $queryParams['education_id']);
        }

        // Sortowanie
        $sortBy = $queryParams['sort_by'] ?? 'id';
        $sortOrder = $queryParams['sort_order'] ?? 'ASC';
        
        $allowedSortFields = ['id', 'name', 'phoneNumber', 'address', 'age'];
        if (in_array($sortBy, $allowedSortFields)) {
            $qb->orderBy('u.' . $sortBy, $sortOrder);
        }

        // Paginacja
        $page = max(1, (int) ($queryParams['page'] ?? 1));
        $limit = max(1, min(100, (int) ($queryParams['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;

        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $users = $qb->getQuery()->getResult();

        // Liczba wszystkich rekordów
        $countQb = $this->entityManager->createQueryBuilder();
        $countQb->select('COUNT(u.id)')
                ->from(User::class, 'u')
                ->leftJoin('u.education', 'e');

        // Zastosuj te same filtry
        if (isset($queryParams['name'])) {
            $countQb->andWhere('u.name LIKE :name')
                    ->setParameter('name', '%' . $queryParams['name'] . '%');
        }

        if (isset($queryParams['phone_number'])) {
            $countQb->andWhere('u.phoneNumber LIKE :phone')
                    ->setParameter('phone', '%' . $queryParams['phone_number'] . '%');
        }

        if (isset($queryParams['address'])) {
            $countQb->andWhere('u.address LIKE :address')
                    ->setParameter('address', '%' . $queryParams['address'] . '%');
        }

        if (isset($queryParams['age'])) {
            $countQb->andWhere('u.age = :age')
                    ->setParameter('age', (int) $queryParams['age']);
        }

        if (isset($queryParams['education_id'])) {
            $countQb->andWhere('e.id = :education_id')
                    ->setParameter('education_id', (int) $queryParams['education_id']);
        }

        $totalCount = $countQb->getQuery()->getSingleScalarResult();

        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'phone_number' => $user->getPhoneNumber(),
                'address' => $user->getAddress(),
                'age' => $user->getAge(),
                'education' => $user->getEducation() ? [
                    'id' => $user->getEducation()->getId(),
                    'name' => $user->getEducation()->getName(),
                ] : null,
            ];
        }

        return new JsonResponse([
            'data' => $result,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int) $totalCount,
                'pages' => (int) ceil($totalCount / $limit),
            ],
        ]);
    }
} 