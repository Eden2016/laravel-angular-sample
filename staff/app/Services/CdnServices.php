<?php
namespace App\Services;

class CdnServices
{
    public $s3 = null;
    public $bucket = null;

    public static $instance = null;

    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new CdnServices();
        }

        return self::$instance;
    }

    public function __construct()
    {
        //Initialize Amazon S3 SDK
        $this->s3       = \AWS::createClient('s3');
        $this->bucket   = getenv('CDN_BUCKET_NAME');
    }

    /**
     * Uploads an image to Amazon S3
     *
     * @param string $image
     *
     * @return Aws\Waiter
     */
    public static function uploadImage($image)
    {
        $instance = self::instance();

        $extensionExplode = explode(".", $image);
        $extension = strtolower(end($extensionExplode));

        switch ($extension) {
            case "png":
                $mime = "image/png";
                break;
            case "jpg":
            case "jpeg":
                $mime = "image/jpeg";
                break;
        }

        $contents = file_get_contents(public_path('uploads').'/'.$image);
        
        return $instance->putObject($image, $contents, $mime);
    }

    /**
     * Puts objects into the S3 storage
     *
     * @param string $fileName
     * @param string $contents
     * @param string $contentType
     *
     * @return Aws\Waiter
     */
    public function putObject($fileName, $contents, $contentType="application/json") {
        $this->s3->putObject([
            'ACL'           => 'public-read',
            'Bucket'        => $this->bucket, // REQUIRED
            'Key'           => $fileName, // REQUIRED
            'Body'          => $contents,
            'ContentType'   => $contentType,
        ]);

        return $this->s3->getWaiter('ObjectExists', array(
                'Bucket' => $this->bucket,
                'Key'    => $fileName
            ));
    }

    /**
     * Gets and object from Amazon S3 storage
     *
     * @var string $key
     *
     * @return string|bool
     */
    public function getObject($key)
    {
        $object = $this->s3->getObject([
            'Bucket' => $this->bucket, // REQUIRED
            'Key' => $key,
        ]);

        if ($object) {
            return $object['Body'];
        } else {
            return false;
        }
    }
}