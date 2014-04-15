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
   */
  public function __construct(Api $api)
  {
    $this->api = $api;
  }

  /**
   * Check file status.
   * Return array of json data
   *
   * @param string $file_id
   * @return array
   */
  public function status($token)
  {
    $data = array(
        'token' => $token,
    );
    $ch = $this->__initRequest('status', $data);
    $this->__setHeaders($ch);
    $data = $this->__runRequest($ch);
    return $data;
  }

  /**
   * Upload file from url and get File instance
   *
   * @param string $url An url of file to be uploaded.
   * @return File
   */
  public function fromUrl($url, $check_status = true, $timeout = 1, $max_attempts = 5)
  {
    $data = array(
        '_' => time(),
        'source_url' => $url,
        'pub_key' => $this->api->getPublicKey(),
    );
    $ch = $this->__initRequest('from_url', $data);
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
        if ($attempts == $max_attempts) {
          throw new \Exception('Cannot store file, max attempts reached, upload is not successful');
        }
        sleep($timeout);
        $attempts++;
      }
    } else {
      return $token;
    }
    $file_id = $data->file_id;

    return new File($file_id, $this->api);
  }

  /**
   * Upload file from local path.
   *
   * @param string $path
   * @param string $mime_type
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
    $file_id = $data->file;
    return new File($file_id, $this->api);
  }

  /**
   * Upload file from file pointer
   *
   * @param resourse $fp
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
   * Init upload request and return curl resource
   *
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
   * Set request type for curl resrouce
   *
   * @param resource $ch
   * @return void
   */
  private function __setRequestType($ch)
  {
    curl_setopt($ch, CURLOPT_POST, true);
  }

  /**
   * Set all the headers for request and set returntrasfer.
   *
   * @param resource $ch. Curl resource.
   * @return void
   */
  private function __setHeaders($ch)
  {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'User-Agent: ' . $this->api->ua,
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
   * @throws Exception
   * @return array
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
