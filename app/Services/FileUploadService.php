<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailListImport;
use App\Models\EmailList;

class FileUploadService
{
    /**
     * Process uploaded file and create email list
     */
    public function processFile($file, $name, $userId)
    {
        // Validate file
        $validator = Validator::make(['file' => $file], [
            'file' => 'required|file|mimes:xlsx,csv,json|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->first()
            ];
        }

        // Generate unique file path
        $fileName = $file->getClientOriginalName();
        $fileType = $file->getClientOriginalExtension();
        $uniqueName = Str::random(40) . '.' . $fileType;
        $filePath = 'email_lists/' . $uniqueName;

        // Store file
        $file->storeAs('public', $filePath);

        // Create email list record
        $emailList = EmailList::create([
            'name' => $name,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'status' => 'processing',
            'user_id' => $userId,
        ]);

        try {
            // Process file based on type
            $result = $this->processFileContent($file, $fileType, $emailList);

            // Update email list with results
            $emailList->update([
                'total_emails' => $result['total'],
                'valid_emails' => $result['valid'],
                'invalid_emails' => $result['invalid'],
                'invalid_emails_details' => $result['invalid_details'],
                'status' => $result['success'] ? 'completed' : 'failed',
                'error_message' => $result['success'] ? null : $result['error'],
            ]);

            return [
                'success' => true,
                'email_list' => $emailList,
                'stats' => [
                    'total' => $result['total'],
                    'valid' => $result['valid'],
                    'invalid' => $result['invalid'],
                ]
            ];

        } catch (\Exception $e) {
            // Update email list with error
            $emailList->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'errors' => $e->getMessage()
            ];
        }
    }

    /**
     * Process file content based on file type
     */
    protected function processFileContent($file, $fileType, $emailList)
    {
        $emails = [];
        $invalidDetails = [];

        switch ($fileType) {
            case 'xlsx':
                $emails = $this->processExcelFile($file);
                break;
            case 'csv':
                $emails = $this->processCsvFile($file);
                break;
            case 'json':
                $emails = $this->processJsonFile($file);
                break;
            default:
                throw new \Exception('Unsupported file type');
        }

        // Validate emails
        $validEmails = [];
        $invalidEmails = [];

        foreach ($emails as $emailData) {
            $email = trim($emailData['email'] ?? $emailData);
            $name = trim($emailData['name'] ?? '');

            if ($this->isValidEmail($email)) {
                $validEmails[] = [
                    'email' => $email,
                    'name' => $name ?: null,
                    'email_list_id' => $emailList->id,
                    'tracking_token' => Str::random(64),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $invalidEmails[] = $email;
                $invalidDetails[] = [
                    'email' => $email,
                    'name' => $name,
                    'reason' => 'Invalid email format'
                ];
            }
        }

        // Save valid emails to database
        if (!empty($validEmails)) {
            \App\Models\EmailRecipient::insert($validEmails);
        }

        return [
            'success' => true,
            'total' => count($emails),
            'valid' => count($validEmails),
            'invalid' => count($invalidEmails),
            'invalid_details' => $invalidDetails,
        ];
    }

    /**
     * Process Excel file
     */
    protected function processExcelFile($file)
    {
        $import = new EmailListImport();
        Excel::import($import, $file);
        return $import->getEmails();
    }

    /**
     * Process CSV file
     */
    protected function processCsvFile($file)
    {
        $emails = [];
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            throw new \Exception('Unable to open CSV file');
        }

        // Read header
        $header = fgetcsv($handle);
        $emailColumn = $this->findEmailColumn($header);
        $nameColumn = $this->findNameColumn($header);

        // Read data
        while (($data = fgetcsv($handle)) !== false) {
            if (!empty($data[$emailColumn])) {
                $emails[] = [
                    'email' => $data[$emailColumn],
                    'name' => $nameColumn !== null ? $data[$nameColumn] : '',
                ];
            }
        }

        fclose($handle);
        return $emails;
    }

    /**
     * Process JSON file
     */
    protected function processJsonFile($file)
    {
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON file');
        }

        $emails = [];

        if (is_array($data)) {
            foreach ($data as $item) {
                if (is_array($item)) {
                    // Array of objects with email and name
                    $emails[] = [
                        'email' => $item['email'] ?? '',
                        'name' => $item['name'] ?? '',
                    ];
                } elseif (is_string($item)) {
                    // Array of email strings
                    $emails[] = [
                        'email' => $item,
                        'name' => '',
                    ];
                }
            }
        } elseif (is_object($data) && isset($data->emails)) {
            // Object with emails array
            foreach ($data->emails as $email) {
                $emails[] = is_string($email) ? $email : (array) $email;
            }
        }

        return $emails;
    }

    /**
     * Find email column in CSV header
     */
    protected function findEmailColumn($header)
    {
        foreach ($header as $index => $column) {
            $column = strtolower(trim($column));
            if (in_array($column, ['email', 'email_address', 'mail', 'e-mail'])) {
                return $index;
            }
        }
        return 0; // Default to first column
    }

    /**
     * Find name column in CSV header
     */
    protected function findNameColumn($header)
    {
        foreach ($header as $index => $column) {
            $column = strtolower(trim($column));
            if (in_array($column, ['name', 'full_name', 'first_name', 'last_name', 'fullname'])) {
                return $index;
            }
        }
        return null; // No name column found
    }

    /**
     * Validate email format
     */
    protected function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
