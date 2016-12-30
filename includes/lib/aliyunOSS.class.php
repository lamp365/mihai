<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/12/29
 * Time: 15:34
 */


require_once WEB_ROOT . '/includes/TopSdk.php';
use OSS\OssClient;
use OSS\Core\OssException;
/**
 * Class Common
 *
 * 示例程序【Samples/*.php】 的Common类，用于获取OssClient实例和其他公用方法
 */
class aliyunOSS
{
    const endpoint        = 'oss-cn-shanghai.aliyuncs.com';
    const accessKeyId     = 'Ft6j1i6G9kTWBzQl';
    const accessKeySecret = 'VrTlrpC7izOK5aq7CNLY0uKehbzw93';
    const bucket          = 'dayblog';
    private static $self  = NULL;
    /**
     * 根据Config配置，得到一个OssClient实例
     *
     * @return OssClient 一个OssClient实例
     */
    static public function getOssClient()
    {
        if (is_null(self::$self)) {
            try {
                self::$self = new OssClient(self::accessKeyId, self::accessKeySecret, self::endpoint, false);
            } catch (OssException $e) {
                printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
                printf($e->getMessage() . "\n");
                return null;
            }
        }
        return self::$self;
    }

    /*******************************************************************************/
    /**********************   bucket操作     *******************************/
    /*******************************************************************************/

    /**
     * 工具方法，创建一个存储空间，如果发生异常直接exit
     */
    static public function createBucket($bucket)
    {
        $ossClient = self::getOssClient();
        if (is_null($ossClient)) exit(1);
        $acl = OssClient::OSS_ACL_TYPE_PUBLIC_READ;
        try {
            return $ossClient->createBucket($bucket, $acl);
        } catch (OssException $e) {

            $message = $e->getMessage();
            if (\OSS\Core\OssUtil::startsWith($message, 'http status: 403')) {
                echo "Please Check your AccessKeyId and AccessKeySecret" . "\n";
                exit(0);
            } elseif (strpos($message, "BucketAlreadyExists") !== false) {
                echo "Bucket already exists. Please check whether the bucket belongs to you, or it was visited with correct endpoint. " . "\n";
                exit(0);
            }
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
    }
    /**
     * 列出用户所有的Bucket
     * @return null
     */
    public static function listBuckets()
    {
        $ossClient = self::getOssClient();
        $bucketList = null;
        try {
            $bucketListInfo = $ossClient->listBuckets();
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }

        $bucketList = $bucketListInfo->getBucketList();
       /* foreach ($bucketList as $bucket) {
            print($bucket->getLocation() . "\t" . $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
        }*/
        return $bucketList;
    }

    /**
     *  判断Bucket是否存在
     * @param string $bucket 存储空间名称
     */
    public static function doesBucketExist($bucket)
    {
        $ossClient = self::getOssClient();
        try {
            $res = $ossClient->doesBucketExist($bucket);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
        return $res;
    }
    /**
     * 获取bucket的acl配置
     * @param string $bucket 存储空间名称
     * @return null
     */
    public static function getBucketAcl($bucket)
    {
        $ossClient = self::getOssClient();
        try {
            $res = $ossClient->getBucketAcl($bucket);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
       return $res;
    }
    /**
     * 设置bucket的acl配置
     * @param string $bucket 存储空间名称
     * @return null
     */
    public static function putBucketAcl($bucket)
    {
        $ossClient = self::getOssClient();
        $acl = OssClient::OSS_ACL_TYPE_PRIVATE;
        try {
            $res = $ossClient->putBucketAcl($bucket, $acl);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
       return $res;
    }

    /*******************************************************************************/
    /**********************   文件上传和下载 操作     *******************************/
    /*******************************************************************************/

    /**
     * 把本地变量的内容存到文件  如果是远程图片，则把图片变成二进制流，在写入文件中
     *
     * 简单上传,上传指定变量的内存值作为object的内容
     * @param string $fileName 要存储文件名称  当上传远程图片时可以不给名字 会与远程名字同名
     * @param string $content  要写入的内容
     * @return null
     */
    public static function putObject($content,$fileName='')
    {
        $extentionArr = array( 'gif', 'jpg', 'jpeg', 'png');
        $extention = pathinfo($content, PATHINFO_EXTENSION);
        if(in_array($extention,$extentionArr)){
            $picName = pathinfo($content,PATHINFO_BASENAME);
            $content = file_get_contents($content);
            if(empty($fileName)){
                $fileName = $picName;
            }
        }
        $ossClient = self::getOssClient();
        $options = array();
        try {
            $res = $ossClient->putObject(self::bucket, $fileName, $content, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
        return $res;
    }

    /**
     * 上传指定的本地文件内容
     * @param string $new_fileName 给文件重命名
     * @param string $filePath     文件路劲
     * @return null
     */
    public static function uploadFile($filePath,$new_fileName)
    {
        $ossClient = self::getOssClient();
        $options = array();

        try {
           $res =  $ossClient->uploadFile(self::bucket, $new_fileName, $filePath, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
        return $res;
    }

    /**
     * 列出Bucket内所有文件, 注意如果符合条件的文件数目超过设置的max-keys， 用户需要使用返回的nextMarker作为入参，通过
     * @param string $prefix 模糊查询
     * @return null
     */
    public static function listObjects($prefix = '')
    {
        $ossClient = self::getOssClient();
        $prefix = $prefix;  //限定返回的Object 必须以Prefix作为前缀。注意使用prefix查询时，返回的key中仍会包含Prefix。
        $delimiter = '/';  //用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现Delimiter字符之间的Object作为一组元素
        $nextMarker = '';  //设定结果从Marker之后按字母排序的第一个开始返回。
        $maxkeys = 1000;  //限定此次返回Object的最大数，如果不设定，默认为100，MaxKeys取值不能大于1000。
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );
        try {
            $listObjectInfo = $ossClient->listObjects(self::bucket, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }

        $objectList = $listObjectInfo->getObjectList(); // 文件列表

       /* if (!empty($objectList)) {
            foreach ($objectList as $objectInfo) {
                print($objectInfo->getKey() . "\n");
            }
        }*/
        return $objectList;
    }

    /**
     * 获取object的内容
     * @param string $fileName 获取文件的内容，如果是图片，则得到二进制流 不用绝对地址，按照bucket相对路劲
     * @return null
     */
    public static function getObject($fileName,$options = array())
    {
        $ossClient = self::getOssClient();
        try {
            $content = $ossClient->getObject(self::bucket, $fileName, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
//        $content = getimagesize($content);  得到图片具体信息
        return $content;
    }

    /**
     * get_object_to_local_file
     *
     * 获取object
     * 将object下载到指定的文件  少用，还不是很清楚，怎么用
     * @param string $fileName 获取文件的内容，如果是图片，则得到二进制流 不用绝对地址，按照bucket相对路劲
     * $localfile 本地文件名
     * @return null
     */
    public static function getObjectToLocalFile($fileName,$localfile)
    {
        $ossClient = self::getOssClient();
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile,
        );

        try {
            $res = $ossClient->getObject(self::bucket, $fileName, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
        return $res;
    }

    /**
     * 删除object
     * @param string $fileName 不用绝对地址，按照bucket相对路劲
     * $fileName也可以是数组形式 那么就是批量删除
     * @return null
     */
    public static  function deleteObject($fileName)
    {
        $ossClient = self::getOssClient();
        try {
            $res = $ossClient->deleteObject(self::bucket, $fileName);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
        return $res;
    }


    /**
     * 判断object是否存在
     * @param string $fileName 不用绝对地址，按照bucket相对路劲
     * @return null
     */
    public static function doesObjectExist($fileName)
    {
        $ossClient = self::getOssClient();
        try {
            $exist = $ossClient->doesObjectExist(self::bucket, $fileName);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return '';
        }
        return $exist;
    }


}