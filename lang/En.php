<?php
namespace lang;

class En {
  public static function vocabulary() {
    return array(
      "@lang" => function($params) {
        switch ($params["@lang"]) {
          case "it":
            return "italian";
          case "en":
            return "english";
          case "de":
            return "german";
          case "fr":
            return "french";
        }
      }
    );    
  }
}
?>