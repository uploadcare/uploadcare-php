<?php
namespace Uploadcare;

class Uploader
{
  /**
   * Base upload host
   *
   * @var string
   */
  private $host = 'upload.uploadcare.com';

  /**
   * Api instance
   *
   * @var Api
   */
  private $api = null;

  /**
   * Constructor
   * @param Api $api
   */
  public function __construct(Api $api)
  {
    $this->api = $api;
  }

  /**
   * Check file status.
   * Return array of json data
   *
   * @param string $token
   * @return object
   */
  public function status($token)
  {
    $data = array(
        'token' => $token,
    );
    $ch = $this->__initRequest('from_url/status', $data);
    $this->__setHeaders($ch);
    $data = $this->__runRequest($ch);
    return $data;
  }

  /**
   * Upload file from url and get File instance
   *
   * @param string $url An url of file to be uploaded.
   * @param boolean $check_status Wait till upload is complete
   * @param int $timeout Wait $timeout seconds between status checks
   * @param int $max_attempts Check status no more than $max_attempts times
   * @return File|string
   * @throws \Exception
   */
  public function fromUrl($url, $check_status = true, $timeout = 1, $max_attempts = 5)
  {
    $requestData = array(
        '_' => time(),
        'source_url' => $url,
        'pub_key' => $this->api->getPublicKey(),
    );
    $ch = $this->__initRequest('from_url', $requestData);
    $this->__setHeaders($ch);

    $data = $this->__runRequest($ch);
    $token = $data->token;

    if ($check_status) {
      $success = false;
      $attempts = 0;
      while (!$success) {
        $data = $this->status($token);
        if ($data->status == 'success') {
          $success = true;
        }
        if ($data->status == 'error') {
          throw new \Exception('Upload is not successful: ' . $data->error);
        }
        if ($attempts == $max_attempts) {
          throw new \Exception('Max attempts reached, upload is not successful');
        }
        sleep($timeout);
        $attempts++;
      }
    } else {
      return $token;
    }
    $uuid = $data->uuid;

    return new File($uuid, $this->api);
  }

  /**
   * Upload file from local path.
   *
   * @param string $path
   * @param string|bool $mime_type
   * @return File
   */
  public function fromPath($path, $mime_type = false)
  {
    if (function_exists('curl_file_create')) {
      if($mime_type) {
        $f = curl_file_create($path, $mime_type);
      } else {
        $f = curl_file_create($path);
      }
    } else {
      if($mime_type) {
        $f = '@' . $path . ';type=' . $mime_type;
      } else {
        $f = '@' . $path;
      }
    }

    $data = array(
      'UPLOADCARE_PUB_KEY' => $this->api->getPublicKey(),
      'file' => $f,
    );
    $ch = $this->__initRequest('base');
    $this->__setRequestType($ch);
    $this->__setData($ch, $data);
    $this->__setHeaders($ch);

    $data = $this->__runRequest($ch);
    $uuid = $data->file;
    return new File($uuid, $this->api);
  }

  /**
   * Upload file from file pointer
   *
   * @param resource $fp
   * @return File
   */
  public function fromResource($fp)
  {
    $tmpfile = tempnam(sys_get_temp_dir(), 'ucr');
    $temp = fopen($tmpfile, 'w');
    while (!feof($fp)) {
      fwrite($temp, fread($fp, 8192));
    }
    fclose($temp);
    fclose($fp);

    return $this->fromPath($tmpfile);
  }

  /**
   * Upload file from string using mime-type.
   *
   * @param string $content
   * @param string $mime_type
   * @return File
   */
  public function fromContent($content, $mime_type)
  {
    $tmpfile = tempnam(sys_get_temp_dir(), 'ucr');
    $temp = fopen($tmpfile, 'w');
    fwrite($temp, $content);
    fclose($temp);

    return $this->fromPath($tmpfile, $mime_type);
  }

  /**
   * Create group from array of File objects
   *
   * @param array $files
   * @return Group
   */
  public function createGroup($files)
  {
    $data = array(
      'pub_key' => $this->api->getPublicKey(),
    );
    /**
     * @var File $file
     */
    foreach ($files as $i => $file) {
      $data["files[$i]"] = $file->getUuid();
    }


    $ch = $this->__initRequest('group');
    $this->__setRequestType($ch);
    $this->__setData($ch, $data);
    $this->__setHeaders($ch);

    $resp = $this->__runRequest($ch);
    $group = $this->api->getGroup($resp->id);
    return $group;
  }

  /**
   * Init upload request and return curl resource
   *
   * @param $type
   * @param array $data
   * @return resource
   */
  private function __initRequest($type, $data = null)
  {
    $url = sprintf('https://%s/%s/', $this->host, $type);
    if (is_array($data)) {
      $url = sprintf('%s?%s', $url, http_build_query($data));
    }
    $ch = curl_init($url);
    return $ch;
  }

  /**
   * Set request type for curl resource
   *
   * @param resource $ch
   * @return void
   */
  private function __setRequestType($ch)
  {
    curl_setopt($ch, CURLOPT_POST, true);
  }

  /**
   * Set all the headers for request and set return transfer.
   *
   * @param resource $ch. Curl resource.
   * @return void
   */
  private function __setHeaders($ch)
  {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'User-Agent: ' . $this->api->getUserAgent(),
    ));
  }

  /**
   * Set data to be posted on request
   *
   * @param resource $ch. Curl resource
   * @param array $data
   * @return void
   */
  private function __setData($ch, $data = array())
  {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  }

  /**
   * Run prepared curl request.
   * Throws Exception of not 200 http code
   *
   * @param resource $ch. Curl resource
   * @throws \Exception
   * @return object
   */
  private function __runRequest($ch)
  {
    $data = curl_exec($ch);
    $ch_info = curl_getinfo($ch);
    if ($data === false) {
      throw new \Exception(curl_error($ch));
    }
    elseif ($ch_info['http_code'] != 200) {
      throw new \Exception('Unexpected HTTP status code ' . $ch_info['http_code'] . '.' . curl_error($ch));
    }
    curl_close($ch);
    return json_decode($data);
  }
}
