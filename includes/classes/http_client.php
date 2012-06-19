<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

  class httpClient {
    var $url;
    var $reply;
    var $replyString; 
    var $protocolVersion = '1.1';
    var $requestHeaders, $requestBody;
    var $socket = false;

    var $useProxy = false;
    var $proxyHost, $proxyPort;

    function httpClient($host = '', $port = '') {
      if (os_not_null($host)) {
        $this->connect($host, $port);
      }
    }

    function setProxy($proxyHost, $proxyPort) {
      $this->useProxy = true;
      $this->proxyHost = $proxyHost;
      $this->proxyPort = $proxyPort;
    }

    function setProtocolVersion($version) {
      if ( ($version > 0) && ($version <= 1.1) ) {
        $this->protocolVersion = $version;
        return true;
      } else {
        return false;
      }
    }

    function setCredentials($username, $password) {
      $this->addHeader('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
     }

    function setHeaders($headers) {
      if (is_array($headers)) {
        reset($headers);
        while (list($name, $value) = each($headers)) {
          $this->requestHeaders[$name] = $value;
        }
      }
    }

    function addHeader($headerName, $headerValue) {
      $this->requestHeaders[$headerName] = $headerValue;
    }

    function removeHeader($headerName) {
      unset($this->requestHeaders[$headerName]);
    }

    function Connect($host, $port = '') {
      $this->url['scheme'] = 'http';
      $this->url['host'] = $host;
      if (os_not_null($port)) $this->url['port'] = $port;

      return true;
    }

    function Disconnect() {
      if ($this->socket) fclose($this->socket);
    }

    function Head($uri) {
      $this->responseHeaders = $this->responseBody = '';

      $uri = $this->makeUri($uri);

      if ($this->sendCommand('HEAD ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      return $this->reply;
    }

    function Get($url) {
      $this->responseHeaders = $this->responseBody = '';

      $uri = $this->makeUri($url);

      if ($this->sendCommand('GET ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      return $this->reply;
    }

    function Post($uri, $query_params = '') {
      $uri = $this->makeUri($uri);

      if (is_array($query_params)) {
        $postArray = array();
        reset($query_params);
        while (list($k, $v) = each($query_params)) {
          $postArray[] = urlencode($k) . '=' . urlencode($v);
        }

        $this->requestBody = implode('&', $postArray);
      }

      $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');

      if ($this->sendCommand('POST ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      $this->removeHeader('Content-Type');
      $this->removeHeader('Content-Length');
      $this->requestBody = '';

      return $this->reply;
    }

    function Put($uri, $filecontent) {
      $uri = $this->makeUri($uri);
      $this->requestBody = $filecontent;

      if ($this->sendCommand('PUT ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      return $this->reply;
    }

    function getHeaders() {
      return $this->responseHeaders;
    }

    function getHeader($headername) {
      return $this->responseHeaders[$headername];
    }

    function getBody() {
      return $this->responseBody;
    }

    function getStatus() {
      return $this->reply;
    }

    function getStatusMessage() {
      return $this->replyString;
    }

    function sendCommand($command) {
      $this->responseHeaders = array();
      $this->responseBody = '';

      if ( ($this->socket == false) || (feof($this->socket)) ) {
        if ($this->useProxy) {
          $host = $this->proxyHost;
          $port = $this->proxyPort;
        } else {
          $host = $this->url['host'];
          $port = $this->url['port'];
        }

        if (!os_not_null($port)) $port = 80;

        if (!$this->socket = fsockopen($host, $port, $this->reply, $this->replyString)) {
          return false;
        }

        if (os_not_null($this->requestBody)) {
          $this->addHeader('Content-Length', strlen($this->requestBody));
        }

        $this->request = $command;
        $cmd = $command . "\r\n";
        if (is_array($this->requestHeaders)) {
          reset($this->requestHeaders);
          while (list($k, $v) = each($this->requestHeaders)) {
            $cmd .= $k . ': ' . $v . "\r\n";
          }
        }

        if (os_not_null($this->requestBody)) {
          $cmd .= "\r\n" . $this->requestBody;
        }

        $this->requestBody = '';

        fputs($this->socket, $cmd . "\r\n");

        return true;
      }
    }

    function processReply() {
      $this->replyString = trim(fgets($this->socket, 1024));

      if (preg_match('|^HTTP/\S+ (\d+) |i', $this->replyString, $a )) {
        $this->reply = $a[1];
      } else {
        $this->reply = 'Bad Response';
      }

      $this->responseHeaders = $this->processHeader();
      $this->responseBody = $this->processBody();

      return $this->reply;
    }

    function processHeader($lastLine = "\r\n") {
      $headers = array();
      $finished = false;

      while ( (!$finished) && (!feof($this->socket)) ) {
        $str = fgets($this->socket, 1024);
        $finished = ($str == $lastLine);
        if (!$finished) {
          list($hdr, $value) = preg_split('/: /', $str, 2);
          // nasty workaround broken multiple same headers (eg. Set-Cookie headers) @FIXME 
          if (isset($headers[$hdr])) {
            $headers[$hdr] .= '; ' . trim($value);
          } else {
            $headers[$hdr] = trim($value);
          }
        }
      }

      return $headers;
    }

    function processBody() {
      $data = '';
      $counter = 0;

      do {
        $status = socket_get_status($this->socket);
        if ($status['eof'] == 1) {
          break;
        }

        if ($status['unread_bytes'] > 0) {
          $buffer = fread($this->socket, $status['unread_bytes']);
          $counter = 0;
        } else {
          $buffer = fread($this->socket, 128);
          $counter++;
          usleep(2);
        }

        $data .= $buffer;
      } while ( ($status['unread_bytes'] > 0) || ($counter++ < 10) );

      return $data;
    }

    function makeUri($uri) {
      $a = parse_url($uri);

      if ( (isset($a['scheme'])) && (isset($a['host'])) ) {
        $this->url = $a;
      } else {
        unset($this->url['query']);
        unset($this->url['fragment']);
        $this->url = array_merge($this->url, $a);
      }

      if ($this->useProxy) {
        $requesturi = 'http://' . $this->url['host'] . (empty($this->url['port']) ? '' : ':' . $this->url['port']) . $this->url['path'] . (empty($this->url['query']) ? '' : '?' . $this->url['query']);
      } else {
        $requesturi = $this->url['path'] . (empty($this->url['query']) ? '' : '?' . $this->url['query']);
      }

      return $requesturi;
    }
  }
?>