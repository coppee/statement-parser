<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\FilesystemService;
use App\Service\Spreadsheet\PhpSpreadsheetSaver;
use App\Service\Spreadsheet\Spreadsheet;
use App\Service\Spreadsheet\SpreadsheetSaveOptions;
use App\Service\Statement\BnpFortisStatementParser;
use App\Service\Statement\Statement;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ParseStatementCommand extends Command
{
    protected static $defaultName = 'statement:parse';

    /** @var LoggerInterface  */
    private $logger;
    /** @var FilesystemService */
    private $filesystemService;
    /** @var SymfonyStyle */
    private $io;
    /** @var string */
    private $inputDirectory;
    /** @var string */
    private $outputDirectory;

    private const DIRECTORY = '/var/app';
    private const OUTPUT_FILENAME = 'statements';

    public function __construct(
        LoggerInterface $logger,
        FilesystemService $filesystemService
    )
    {
        $this->logger = $logger;
        $this->filesystemService = $filesystemService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Parse bank statements PDF.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Statement parser');

        $this->inputDirectory = \getcwd() . self::DIRECTORY . '/input';
        $this->outputDirectory = \getcwd() . self::DIRECTORY . '/output';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->section('Get input files...');
        $files = $this->filesystemService->allInputFiles($this->inputDirectory);
        $this->io->note(\sprintf('"%s" file(s) loaded', \count($files)));

        $this->io->section('Parsing text...');
        $data = $this->parsingText($files);

        $this->io->section('Generating spreadsheet...');
        $this->generatingSpreadsheet($data);

        $this->io->success('SUPER');
        return 0;
    }

    private function parsingText(array $files): array
    {
        $this->io->progressStart(\count($files));

        $data = [];
        $fileNumber = 1;
        foreach ($files as $filePath) {
            $statement = new Statement($filePath);

            $parser = new BnpFortisStatementParser();
            $valueDates = $parser->getValueDates($statement);

            foreach ($valueDates as $valueDate => $content) {
                $operations = $parser->getTransactionsByValueDate($content);
                foreach ($operations as $operation) {
                    $data[] = [
                        'file' => $fileNumber,
                        'id' => $parser->getTransactionNumber($operation),
                        'valueDate' => $valueDate,
                        'amount' => $parser->getTransactionAmount($operation),
                        'type' => $parser->getTransactionType($operation),
                        'description' => $parser->getTransactionDescription($operation),
                        'content' => $parser->getTransactionContent($operation),
                    ];
                }
            }

            $this->io->progressAdvance();
            $fileNumber++;
        }
        $this->io->progressFinish();

        return $data;
    }

    private function generatingSpreadsheet(array $data): void
    {
        $filename = self::OUTPUT_FILENAME . '_' . \date('Ymd-His') . '.xls';
        $spreadsheet = new Spreadsheet($filename, $data);

        $columnNames = [
            "File",
            "ID",
            "Value date",
            "Amount",
            "Type",
            "Description",
            "Content"
        ];

        $columnStyles = [
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'argb' => 'FF666666'
                    ]
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFf4f5f7',
                ],
            ],
        ];

        $options = [
            'columnNames' => $columnNames,
            'columnStyles' => $columnStyles,
            'directoryPath' => $this->outputDirectory,
        ];
        $spreadsheetOptions = new SpreadsheetSaveOptions($options);

        $saver = new PhpSpreadsheetSaver();
        $saver->save($spreadsheet, $spreadsheetOptions);
    }
}
