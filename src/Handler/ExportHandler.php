<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Entity\Education;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ExportHandler implements RequestHandlerInterface
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $format = $queryParams['format'] ?? null;

        if (!$format) {
            return new Response\JsonResponse(['error' => 'Format parameter is required'], 400);
        }

        return match ($format) {
            'xls' => $this->exportToXls($queryParams),
            'pdf' => $this->exportToPdf($queryParams),
            default => new Response\JsonResponse(['error' => 'Unsupported format'], 400),
        };
    }

    private function exportToXls(array $queryParams): ResponseInterface
    {
        $users = $this->getUsersData($queryParams);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Nagłówki
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Imię i Nazwisko');
        $sheet->setCellValue('C1', 'Numer telefonu');
        $sheet->setCellValue('D1', 'Adres');
        $sheet->setCellValue('E1', 'Wiek');
        $sheet->setCellValue('F1', 'Wykształcenie');

        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user['id']);
            $sheet->setCellValue('B' . $row, $user['name']);
            $sheet->setCellValue('C' . $row, $user['phone_number']);
            $sheet->setCellValue('D' . $row, $user['address']);
            $sheet->setCellValue('E' . $row, $user['age']);
            $sheet->setCellValue('F' . $row, $user['education_name'] ?? '');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'users_report_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $tempFile = tempnam(sys_get_temp_dir(), 'xls_');
        $writer->save($tempFile);

        $response = new Response();
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response = $response->withHeader('Content-Length', (string) filesize($tempFile));

        $stream = new Stream(fopen($tempFile, 'r'));
        return $response->withBody($stream);
    }

    private function exportToPdf(array $queryParams): ResponseInterface
    {
        $users = $this->getUsersData($queryParams);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        $html = $this->generatePdfHtml($users);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'users_report_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $response = new Response();
        $response = $response->withHeader('Content-Type', 'application/pdf');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($dompdf->output());
        $stream->rewind();

        return $response->withBody($stream);
    }

    private function getUsersData(array $queryParams): array
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

        $users = $qb->getQuery()->getResult();

        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'phone_number' => $user->getPhoneNumber(),
                'address' => $user->getAddress(),
                'age' => $user->getAge(),
                'education_name' => $user->getEducation() ? $user->getEducation()->getName() : '',
            ];
        }

        return $result;
    }

    private function generatePdfHtml(array $users): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                h1 { text-align: center; color: #333; }
            </style>
        </head>
        <body>
            <h1>Raport Użytkowników</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imię i Nazwisko</th>
                        <th>Numer telefonu</th>
                        <th>Adres</th>
                        <th>Wiek</th>
                        <th>Wykształcenie</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($users as $user) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars((string) $user['id']) . '</td>
                        <td>' . htmlspecialchars($user['name']) . '</td>
                        <td>' . htmlspecialchars($user['phone_number']) . '</td>
                        <td>' . htmlspecialchars($user['address']) . '</td>
                        <td>' . htmlspecialchars((string) $user['age']) . '</td>
                        <td>' . htmlspecialchars($user['education_name']) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }
} 