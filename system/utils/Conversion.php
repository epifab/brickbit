<?php
namespace system\utils;

use system\exceptions\ConversionError;

/**
 * Classe di metodi utili per il data layer
 * @author fab
 */
class Conversion {
  public static function realEscapeStrings($x) {
    return DataLayerCore::getInstance()->sqlRealEscapeStrings($x);
  }

  /* -------------------
   * Insieme di valori
   * ------------------- */
   public static function key2Value($x, $set) {
     if (\array_key_exists($x, $set)) {
       return $set[$x];
     } else {
       throw new ConversionError("Valore fuori dal range");
     }
   }

   public static function value2Key($x, $set) {
     foreach ($set as $k => $v) {
       if ($x == $v) {
         return $k;
       }
     }
     throw new ConversionError("Valore fuori dal range");
   }

  /* -------------------
   * VALORI INTERI
   * ------------------- */
  // from DATAEDIT
  public static function string2Integer($x) {
    if ($x === null || empty($x)) {
      return null;
    } else if (preg_match('/^[0-9]+$/', $x) == 1) {
      return (int)$x;
    } else {
      throw new ConversionError("Impossibile convertire la stringa in un numero");
    }
  }
  // to DATAVIEW - to DATAEDIT
  public static function integer2String($x) {
    return $x;
  }
  // conversione da DB a PROG
  public static function sqlString2Integer($x) {
    return Conversion::string2Integer($x);
  }
  // conversione da PROG a DB
  public static function integer2SqlString($x) {
    if ($x === null) {
      return "null";
    }
    return $x;
  }

  /* -------------------
   * VALORI REALI
   * ------------------- */
  // from DATAEDIT
  public static function string2Real($x) {
    if ($x === null || empty($x)) {
      return null;
    } else if (preg_match('/^[0-9]+(?:\\,[0-9]+)?$/', $x) == 1) {
      return (double)(str_replace(',', '.', $x));
    } else if (preg_match('/^[0-9]+(?:\\.[0-9]+)?$/', $x) == 1) {
      return (double)$x;
    } else {
      throw new ConversionError("Impossibile convertire la stringa in un numero");
    }
  }
  // to DATAVIEW - to DATAEDIT
  public static function real2String($x) {
    return str_replace('.',',',(String)$x);
  }
  // conversione da DB a PROG
  public static function sqlString2Real($x) {
    if ($x === null || empty($x)) {
      return null;
    } else if (preg_match('/^[0-9]+(?:\\.[0-9]+)?$/', $x) == 1) {
      return (double)$x;
    } else {
      throw new ConversionError("Impossibile convertire la stringa in un numero");
    }
  }
  // conversione da PROG a DB
  public static function real2SqlString($x) {
    if ($x === null) {
      return "null";
    }
    return $x;
  }

  /* -------------------
   * VALORI BOOLEANI
   * ------------------- */
  // from DATAEDIT
  public static function string2Boolean($x) {
    if ($x == "Y") {
      return true;
    } else {
      return false;
    }
  }
  // to DATAVIEW - to DATAEDIT
  public static function boolean2String($x) {
    if ($x) {
      return "Y";
    } else {
      return "N";
    }
  }
  // conversione da DB a PROG
  public static function sqlString2Boolean($x) {
    return $x == "1";
  }
  // conversione da PROG a DB
  public static function boolean2SqlString($x) {
    if ($x) {
      return "1";
    } else {
      return "0";
    }
  }

  /* -------------------
   * DATA
   * ------------------- */
  // conversione da DB a PROG
  public static function sqlDateString2Time($x) {
    if ($x === null || empty($x)) {
      return null;
    }
    if (!preg_match("/^[1-2][0-9]{3}-[0-1][0-9]-[0-3][0-9]$/", $x)) {
      throw new ConversionError("Impossibile convertire la stringa in una data");
    }
    $y = substr($x,0,4);
    $m = substr($x,5,2);
    $d = substr($x,8,2);
    return mktime(0,0,0,$m,$d,$y);
  }
  // conversione da PROG a DB
  public static function time2SqlDateString($x) {
    if ($x === null) {
      return "null";
    }
    return "'" . date("Y-m-d", $x) . "'";
  }
  // from DATAEDIT
  public static function dateString2Time($x) {
    if ($x === null || empty($x)) {
      return null;
    }
    if (!preg_match("/^[0-3][0-9]\\/[0-1][0-9]\\/[1-2][0-9]{3}$/", $x)) {
      throw new ConversionError("Impossibile convertire la stringa in una data");
    }
    $y = substr($x,6,4);
    $m = substr($x,3,2);
    $d = substr($x,0,2);
    return mktime(0,0,0,$m,$d,$y);
  }
  // to DATAEDIT
  public static function time2DateString_DataEdit($x) {
    if ($x === null) {
      return "";
    }
    return date("d/m/Y", $x);
  }
  // to DATAVIEW
  public static function time2DateString_DataView($x) {
    if ($x === null) {
      return "";
    }
    return date('D, j M Y', $x);
  }

  /* -------------------
   * DATA E ORA
   * ------------------- */
  // conversione da DB a PROG
  public static function sqlDateTimeString2Time($x) {
    if ($x === null || empty($x)) {
      return null;
    }
    if (!preg_match("/^[1-2][0-9]{3}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $x)) {
      throw new ConversionError("Impossibile convertire la stringa in una data");
    }
    $y = substr($x,0,4);
    $m = substr($x,5,2);
    $d = substr($x,8,2);
    $h = substr($x,11,2);
    $i = substr($x,14,2);
    $s = substr($x,17,2);
    return mktime($h,$i,$s,$m,$d,$y);
  }
  // conversione da PROG a DB
  public static function time2SqlDateTimeString($x) {
    if ($x === null) {
      return "null";
    }
    return "'" . date("Y-m-d H:i:s", $x) . "'";
  }
  // from DATAEDIT
  public static function dateTimeString2Time($x) {
    if ($x === null || empty($x)) {
      return null;
    }
    if (!preg_match("/^[0-3][0-9]\\/[0-1][0-9]\\/[1-2][0-9]{3} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $x)) {
      throw new ConversionError("Impossibile convertire la stringa in una data");
    }
    $y = substr($x,6,4);
    $m = substr($x,3,2);
    $d = substr($x,0,2);
    $h = substr($x,11,2);
    $i = substr($x,14,2);
    $s = substr($x,17,2);
    return mktime($h,$i,$s,$m,$d,$y);
  }
  // to DATAEDIT
  public static function time2DateTimeString_DataEdit($x) {
    if ($x === null) {
      return "";
    }
    return date("d/m/Y H:i:s", $x);
  }
  // to DATAVIEW
  public static function time2DateTimeString_DataView($x) {
    if ($x === null) {
      return "";
    }
    return date('D, j M Y, H:i', $x);
  }


  /* -------------------
   * ORA
   * ------------------- */
  // conversione da DB a PROG
  public static function sqlTimeString2Time($x) {
    if ($x === null || empty($x)) {
      return null;
    }
    if (!preg_match("/^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $x)) {
      throw new ConversionError("Impossibile convertire la stringa in una data");
    }
    $h = substr($x,0,2);
    $i = substr($x,3,2);
    $s = substr($x,6,2);
    return mktime($h,$i,$s);
  }
  // conversione da PROG a DB
  public static function time2SqlTimeString($x) {
    if ($x === null) {
      return "null";
    }
    return "'" . date("H:i:s", $x) . "'";
  }
  // from DATAEDIT
  public static function timeString2Time($x) {
    return Conversion::sqlTimeString2Time($x);
  }
  // to DATAEDIT / to DATAVIEW
  public static function time2TimeString($x) {
    if ($x === null) {
      return "";
    }
    return date("H:i:s", $x);
  }

  /* -------------------
   * STRINGHE
   * ------------------- */
  // conversione da PROG a DB
  public static function string2SqlString($x) {
    $x = \stripslashes($x);
    if ($x === null) {
      return "null";
    }
    return "'" . DataLayerCore::getInstance()->sqlRealEscapeStrings($x) . "'";
  }
  // conversione da DB a PROG
  public static function sqlString2String($x) {
    return $x;
  }
  // from DATAEDIT
  public static function dataEditString2String($x) {
    return \stripslashes(\strip_tags($x));
  }
  // to DATAEDIT
  public static function string2String_DataEdit($x) {
//    return htmlentities($x, ENT_COMPAT, "UTF-8");
    return $x;
  }
  // to DATAVIEW
  public static function string2String_DataView($x) {
    return $x;
  }

  /* -------------------
   * RICH TEXT STRINGS
   * ------------------- */
  public static function dataEditString2RichString($x) {
    // ATTENZIONE: rimozione tags potenzialmente dannosi
    $x = strip_tags($x, "<strong><span><p><em><br><ul><ol><li>");
    return $x;
  }
  // to DATAEDIT
  public static function richString2String_DataEdit($x) {
    return $x;
  }
  // to DATAVIEW
  public static function richString2String_DataView($x) {
    return $x;
  }

  public static function time2OnlyTimeString($x) {
    return Conversion::time2TimeString($x);
  }
  public static function time2OnlyDateString($x) {
    return Conversion::time2DateString_DataEdit($x);
  }
}
