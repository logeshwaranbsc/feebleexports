<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Media;

class FileStorageService
{
    /**
     * Get local storage directory status and metadata.
     */
    public static function getStatus(): array
    {
        Database::loadEnv();

        $uploadDir = self::getUploadDir();
        $isWritable = is_dir($uploadDir) ? is_writable($uploadDir) : (is_dir(dirname($uploadDir)) && is_writable(dirname($uploadDir)));
        $fileCount = 0;
        $totalBytes = 0;

        if (is_dir($uploadDir)) {
            $files = array_diff(scandir($uploadDir), ['.', '..']);
            foreach ($files as $file) {
                $filePath = $uploadDir . '/' . $file;
                if (is_file($filePath)) {
                    $fileCount++;
                    $totalBytes += filesize($filePath);
                }
            }
        }

        return [
            'configured' => true,
            'storage' => 'Local File Storage',
            'upload_dir' => $uploadDir,
            'is_writable' => $isWritable,
            'file_count' => $fileCount,
            'total_size' => $totalBytes
        ];
    }

    /**
     * Get absolute local uploads directory path.
     */
    public static function getUploadDir(): string
    {
        if (defined('PUBLIC_PATH') && is_dir(PUBLIC_PATH)) {
            return PUBLIC_PATH . '/uploads';
        }
        if (defined('BASE_PATH')) {
            if (is_dir(BASE_PATH . '/public')) {
                return BASE_PATH . '/public/uploads';
            }
            return BASE_PATH . '/uploads';
        }
        return dirname(__DIR__, 2) . '/public/uploads';
    }

    /**
     * Upload an uploaded file array ($_FILES['file']) directly to local server file storage (/public/uploads/).
     */
    public static function uploadFile(array $file, string $folder = 'uploads'): array
    {
        Database::loadEnv();

        if (empty($file) || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
            $maxSize = ini_get('upload_max_filesize') ?: '25M';
            $errorMap = [
                UPLOAD_ERR_INI_SIZE   => "The selected file is too large! It exceeds your PHP server limit of {$maxSize}.",
                UPLOAD_ERR_FORM_SIZE  => "The file exceeds the maximum allowed size specified in the form.",
                UPLOAD_ERR_PARTIAL    => "The file was only partially uploaded. Please try uploading again.",
                UPLOAD_ERR_NO_FILE    => "No file was selected for upload.",
                UPLOAD_ERR_NO_TMP_DIR => "Server temporary directory is missing.",
                UPLOAD_ERR_CANT_WRITE => "Server failed to write file to disk.",
                UPLOAD_ERR_EXTENSION  => "Upload blocked by a PHP extension."
            ];
            $errorMsg = $errorMap[$errCode] ?? 'Upload failed with error code: ' . $errCode;

            return [
                'success' => false,
                'error' => $errorMsg
            ];
        }

        $tmpFilePath = $file['tmp_name'];
        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileSize = $file['size'] ?? filesize($tmpFilePath);
        $mimeType = $file['type'] ?: mime_content_type($tmpFilePath) ?: 'application/octet-stream';

        // Sanitize filename
        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($originalName));
        $cleanName = trim(preg_replace('/-+/', '-', $cleanName), '-');
        if (empty($cleanName)) {
            $cleanName = 'file';
        }

        $uniqueName = $cleanName . '-' . date('YmdHis') . '-' . substr(md5(uniqid()), 0, 5) . ($extension ? '.' . $extension : '');

        $uploadDir = self::getUploadDir();
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return [
                    'success' => false,
                    'error' => 'Unable to create local uploads directory: ' . $uploadDir
                ];
            }
        }

        $targetPath = $uploadDir . '/' . $uniqueName;
        $webPath = '/uploads/' . $uniqueName;

        if (move_uploaded_file($tmpFilePath, $targetPath) || copy($tmpFilePath, $targetPath)) {
            $res = [
                'success' => true,
                'url' => $webPath,
                'filename' => $uniqueName,
                'storage' => 'Local File Storage',
                'message' => 'Successfully uploaded to server file storage (' . $webPath . ').'
            ];

            // Save upload record to Media model
            Media::saveRecord([
                'name' => $file['name'],
                'url' => $webPath,
                'filename' => $uniqueName,
                'path' => 'uploads/' . $uniqueName,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'storage' => 'Local File Storage',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $res;
        }

        return [
            'success' => false,
            'error' => 'Failed to save uploaded file to local server storage directory.'
        ];
    }
}
