<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Media;

class SupabaseStorageService
{
    /**
     * Get current S3/Supabase configuration status.
     */
    public static function getStatus(): array
    {
        Database::loadEnv();

        $endpoint = trim($_ENV['SUPABASE_S3_ENDPOINT'] ?? getenv('SUPABASE_S3_ENDPOINT') ?: '');
        $accessKey = trim($_ENV['SUPABASE_S3_ACCESS_KEY_ID'] ?? getenv('SUPABASE_S3_ACCESS_KEY_ID') ?: '');
        $secretKey = trim($_ENV['SUPABASE_S3_SECRET_ACCESS_KEY'] ?? getenv('SUPABASE_S3_SECRET_ACCESS_KEY') ?: '');
        $bucket = trim($_ENV['SUPABASE_S3_BUCKET'] ?? getenv('SUPABASE_S3_BUCKET') ?: 'feebleexports');
        $region = trim($_ENV['SUPABASE_S3_REGION'] ?? getenv('SUPABASE_S3_REGION') ?: 'us-east-1');
        $supabaseUrl = trim($_ENV['SUPABASE_URL'] ?? getenv('SUPABASE_URL') ?: '');

        $hasS3Keys = !empty($accessKey) && !empty($secretKey) 
            && !str_contains($accessKey, 'YOUR_') && !str_contains($secretKey, 'YOUR_');

        return [
            'configured' => $hasS3Keys,
            'endpoint' => $endpoint,
            'access_key' => !empty($accessKey) ? (str_contains($accessKey, 'YOUR_') ? 'Not set (Placeholder)' : substr($accessKey, 0, 6) . '...') : 'Missing',
            'bucket' => $bucket,
            'region' => $region,
            'supabase_url' => $supabaseUrl
        ];
    }

    /**
     * Upload an uploaded file array ($_FILES['key']) to Supabase S3 or local fallback.
     */
    public static function uploadFile(array $file, string $folder = 'uploads'): array
    {
        Database::loadEnv();

        if (empty($file) || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
            $maxSize = ini_get('upload_max_filesize') ?: '2M';
            $errorMap = [
                UPLOAD_ERR_INI_SIZE   => "The selected file is too large! It exceeds your PHP server limit of {$maxSize}. Please compress your image or select a file under {$maxSize}.",
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

        $uniqueName = $cleanName . '-' . date('YmdHis') . '-' . substr(md5(uniqid()), 0, 5) . '.' . $extension;
        $objectPath = trim($folder, '/') . '/' . $uniqueName;

        $status = self::getStatus();

        if ($status['configured']) {
            $res = self::uploadToS3($tmpFilePath, $objectPath, $mimeType, $fileSize);
        } else {
            // Check for REST Service Key alternative
            $serviceKey = trim($_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? getenv('SUPABASE_SERVICE_ROLE_KEY') ?: '');
            $supabaseUrl = $status['supabase_url'];

            if (!empty($serviceKey) && !str_contains($serviceKey, 'YOUR_') && !empty($supabaseUrl)) {
                $res = self::uploadViaRestApi($tmpFilePath, $objectPath, $mimeType, $supabaseUrl, $status['bucket'], $serviceKey, $fileSize);
            } else {
                $res = self::uploadToLocal($tmpFilePath, $uniqueName, $mimeType, $fileSize);
            }
        }

        if ($res['success']) {
            // Save upload record to Media model
            Media::saveRecord([
                'name' => $file['name'],
                'url' => $res['url'],
                'filename' => $uniqueName,
                'path' => $objectPath,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'storage' => $res['storage'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $res;
    }

    /**
     * Upload binary data directly to Supabase S3 endpoint using AWS SigV4 authorization.
     */
    private static function uploadToS3(string $tmpFilePath, string $objectPath, string $mimeType, int $fileSize): array
    {
        $endpoint = trim($_ENV['SUPABASE_S3_ENDPOINT'] ?? getenv('SUPABASE_S3_ENDPOINT') ?: '');
        $accessKey = trim($_ENV['SUPABASE_S3_ACCESS_KEY_ID'] ?? getenv('SUPABASE_S3_ACCESS_KEY_ID') ?: '');
        $secretKey = trim($_ENV['SUPABASE_S3_SECRET_ACCESS_KEY'] ?? getenv('SUPABASE_S3_SECRET_ACCESS_KEY') ?: '');
        $bucket = trim($_ENV['SUPABASE_S3_BUCKET'] ?? getenv('SUPABASE_S3_BUCKET') ?: 'feebleexports');
        $region = trim($_ENV['SUPABASE_S3_REGION'] ?? getenv('SUPABASE_S3_REGION') ?: 'us-east-1');
        $supabaseUrl = trim($_ENV['SUPABASE_URL'] ?? getenv('SUPABASE_URL') ?: '');

        $parsedUrl = parse_url($endpoint);
        $host = $parsedUrl['host'] ?? '';
        $scheme = $parsedUrl['scheme'] ?? 'https';
        $basePath = rtrim($parsedUrl['path'] ?? '/storage/v1/s3', '/');

        // Path for S3 PUT: /storage/v1/s3/<bucket>/<objectPath>
        $uriPath = $basePath . '/' . $bucket . '/' . ltrim($objectPath, '/');
        $fullUrl = $scheme . '://' . $host . $uriPath;

        $content = file_get_contents($tmpFilePath);
        $payloadHash = hash('sha256', $content);

        $dt = new \DateTime('now', new \DateTimeZone('UTC'));
        $amzDate = $dt->format('Ymd\THis\Z');
        $dateStamp = $dt->format('Ymd');

        // Canonical headers
        $canonicalHeaders = "content-type:" . strtolower($mimeType) . "\n"
            . "host:" . strtolower($host) . "\n"
            . "x-amz-content-sha256:" . $payloadHash . "\n"
            . "x-amz-date:" . $amzDate . "\n";

        $signedHeaders = "content-type;host;x-amz-content-sha256;x-amz-date";

        $canonicalRequest = "PUT\n"
            . $uriPath . "\n"
            . "\n"
            . $canonicalHeaders . "\n"
            . $signedHeaders . "\n"
            . $payloadHash;

        $algorithm = "AWS4-HMAC-SHA256";
        $credentialScope = $dateStamp . "/" . $region . "/s3/aws4_request";
        $stringToSign = $algorithm . "\n"
            . $amzDate . "\n"
            . $credentialScope . "\n"
            . hash('sha256', $canonicalRequest);

        $kDate = hash_hmac('sha256', $dateStamp, "AWS4" . $secretKey, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', "s3", $kRegion, true);
        $kSigning = hash_hmac('sha256', "aws4_request", $kService, true);
        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        $authorizationHeader = $algorithm . " "
            . "Credential=" . $accessKey . "/" . $credentialScope . ", "
            . "SignedHeaders=" . $signedHeaders . ", "
            . "Signature=" . $signature;

        $headers = [
            "Host: " . $host,
            "Content-Type: " . $mimeType,
            "x-amz-content-sha256: " . $payloadHash,
            "x-amz-date: " . $amzDate,
            "Authorization: " . $authorizationHeader
        ];

        $ch = curl_init($fullUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $projectUrl = !empty($supabaseUrl) ? rtrim($supabaseUrl, '/') : ($scheme . '://' . $host);
            $publicUrl = $projectUrl . "/storage/v1/object/public/" . $bucket . "/" . ltrim($objectPath, '/');

            return [
                'success' => true,
                'url' => $publicUrl,
                'filename' => basename($objectPath),
                'storage' => 'Supabase S3',
                'message' => 'Successfully uploaded to Supabase S3 bucket!'
            ];
        }

        error_log("Supabase S3 upload failed (HTTP {$httpCode}): {$response} {$curlError}");

        // Fallback to local if S3 rejects invalid keys or non-existent bucket
        $local = self::uploadToLocal($tmpFilePath, basename($objectPath), $mimeType, $fileSize);
        $local['warning'] = "Supabase S3 upload returned HTTP {$httpCode}. Saved locally to uploads/ while S3 keys in .env are being verified.";
        return $local;
    }

    /**
     * Upload via Supabase REST Storage API.
     */
    private static function uploadViaRestApi(string $tmpFilePath, string $objectPath, string $mimeType, string $supabaseUrl, string $bucket, string $apiKey, int $fileSize): array
    {
        $url = rtrim($supabaseUrl, '/') . "/storage/v1/object/" . $bucket . "/" . ltrim($objectPath, '/');
        $content = file_get_contents($tmpFilePath);

        $headers = [
            "Authorization: Bearer " . $apiKey,
            "apiKey: " . $apiKey,
            "Content-Type: " . $mimeType,
            "x-upsert: true"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $publicUrl = rtrim($supabaseUrl, '/') . "/storage/v1/object/public/" . $bucket . "/" . ltrim($objectPath, '/');
            return [
                'success' => true,
                'url' => $publicUrl,
                'filename' => basename($objectPath),
                'storage' => 'Supabase REST',
                'message' => 'Uploaded to Supabase Storage via REST!'
            ];
        }

        return self::uploadToLocal($tmpFilePath, basename($objectPath), $mimeType, $fileSize);
    }

    /**
     * Local storage fallback handler.
     */
    private static function uploadToLocal(string $tmpFilePath, string $filename, string $mimeType, int $fileSize): array
    {
        $uploadDir = defined('PUBLIC_PATH') ? PUBLIC_PATH . '/uploads' : (is_dir(BASE_PATH . '/public') ? BASE_PATH . '/public/uploads' : dirname(BASE_PATH) . '/public/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $targetPath = $uploadDir . '/' . $filename;
        if (move_uploaded_file($tmpFilePath, $targetPath) || copy($tmpFilePath, $targetPath)) {
            return [
                'success' => true,
                'url' => '/uploads/' . $filename,
                'filename' => $filename,
                'storage' => 'Local Uploads',
                'message' => 'Uploaded to local storage (/uploads/' . $filename . '). Add Supabase S3 keys to .env to stream directly to S3.'
            ];
        }

        return [
            'success' => false,
            'error' => 'Failed to save file to server storage.'
        ];
    }
}
