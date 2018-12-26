<?php

/**
 * @param string $str
 * @return string
 */
function efb(string $str): string
{
	return html_entity_decode($str, ENT_QUOTES, "UTF-8");
}

/**
 * @link https://stackoverflow.com/a/35155837/7275114
 */
class CurlHelper {
  /**
   * @param array $data POST data. could be multidimensional array
   * @param string $boundary if $boundary == '' it will be generated automatically
   * @return string formatted POST data separeted with boundary like in browser.
   */
  static function prepareMultiPartData($data, &$boundary){
    $boundary = self::_createBoundary($boundary);
    $boundaryMiddle = "--$boundary\r\n";
    $boundaryLast = "--$boundary--\r\n";

    $res = self::_prepareMultipartData($data, ["\r\n"]);
    $res = join($boundaryMiddle, $res) . $boundaryLast;
    return $res;
  }

  static private function _createBoundary($boundaryCustom = '') {
    switch ($boundaryCustom) {
      case '':
        return uniqid('----').uniqid();
      case 'chrome':
      case 'webkit':
        return uniqid('----WebKitFormBoundary').'FxB';
      default:
        return $boundaryCustom;
    }
  }

  static private function _prepareMultipartData($data, $body, $keyTpl = ''){
    $ph = '{key}';
    if ( !$keyTpl ) {
      $keyTpl = $ph;
    }
    foreach ($data as $k => $v) {
      $paramName = str_replace($ph, $k, $keyTpl);
      if ( (class_exists('CURLFile') && $v instanceof \CURLFile) || strpos($v, '@') === 0 ) {
        if (is_string($v)) {
          $buf = strstr($v,'filename=');
          $buf = explode(';', $buf);
          $filename = str_replace('filename=', '', $buf[0]);
          $mimeType = (isset($buf[1])) ? str_replace('type=', '', $buf[1]) : 'application/octet-stream';
        } else {
          $filename = $v->name;
          $mimeType = $v->mime;
        }
        $str = 'Content-Disposition: form-data; name="' . $paramName . '"; filename="' . $filename . "\"\r\n";
        $str .= 'Content-Type: ' . $mimeType . "\r\n\r\n\r\n";
        $body[] = $str;
      } elseif ( is_array($v) ) {
        $body = self::_prepareMultipartData($v, $body, $paramName.'['.$ph.']');
      } else {
        $body[] = 'Content-Disposition: form-data; name="' . $paramName . "\"\r\n\r\n" . $v . "\r\n";
      }
    }
    return $body;
  }
}
