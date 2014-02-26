<?php
namespace system\exceptions;

class Redirect extends \system\exceptions\BaseException {
  private $url;
  
  public function __construct($url) {
    parent::__construct('Redirecting to <em>@url</em>', array('@url' => $url));
    $this->url = $url;
  }
  
  public function getUrl() {
    return $this->url;
  }
}