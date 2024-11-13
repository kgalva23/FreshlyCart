<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

function uploadToS3($filePath, $key) {
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => 'us-east-2',
        'credentials' => [
            'key'    => 'AKIAZXBZN6J234JX46Q2',
            'secret' => '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',
        ],
    ]);

    try {
        $result = $s3->putObject([
            'Bucket' => 'team15project',
            'Key'    => $key,
            'SourceFile' => $filePath,
        ]);

        return true;
    } catch (AwsException $e) {
        // output error message if fails
        return false;
    }
}

?>