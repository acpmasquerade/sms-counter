<?php

/**
 * The SMSCounter class
 * Inspired by the Javascript library of same name https://github.com/danxexe/sms-counter
 * @author - acpmasquerade <acpamsquerade@gmail.com>
 * @date - 05th March, 2014

 * License Information
 * -------------------------------------------------------------------------------
 * | Permission is hereby granted, free of charge, to any person obtaining a copy
 * | of this software and associated documentation files (the "Software"), to deal
 * | in the Software without restriction, including without limitation the rights
 * | to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * | copies of the Software, and to permit persons to whom the Software is
 * | furnished to do so, subject to the following conditions:
 * |
 * | The above copyright notice and this permission notice shall be included in
 * | all copies or substantial portions of the Software.
 * |
 * | THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * | IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * | FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * | AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * | LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * | OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * | THE SOFTWARE.
 * -------------------------------------------------------------------------------
 */

class SMSCounter{
  
  // character set for GSM 7 Bit charset
  const gsm_7bit_chars = "@£\$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà";
  
  // character set for GSM 7 Bit charset (each character takes two length)
  const gsm_7bitEx_chars = "\\^{}\\\\\\€[~\\]\\|";

  const GSM_7BIT = 'GSM_7BIT';
  const GSM_7BIT_EX = 'GSM_7BIT_EX';
  const UTF16 = 'UTF16';

  /**
   * Regular Expression for 7Bit Chars
   */
  private static function gsm_7bit_regex(){
    return '|^['.self::gsm_7bit_chars.']+$|';
  }

  /**
   * Regular Expression for 7Bit Chars including 7BitEx Chars
   */
  private static function gsm_7bitEx_regex(){
    return('|^['.self::gsm_7bit_chars.self::gsm_7bitEx_chars.']+$|');
  }

  /**
   * Regular Expression for 7BitEx Chars only
   */
  private static function gsm_7bitEx_only_regex(){
    return '|^['.self::gsm_7bitEx_chars.']+$|';
  }

  /** 
   * Regular Expression to remove chars except 7BitEx Chars
   */
  private static function not_gsm_7bitEx_regex(){
    return '|[^'.self::gsm_7bitEx_chars.']+|';
  }

  // message length for GSM 7 Bit charset
  const messageLength_GSM_7BIT = 160;
  // message length for GSM 7 Bit charset with extended characters
  const messageLength_GSM_7BIT_EX = 160;
  // message length for UTF16 charset
  const messageLength_UTF16 = 70;

  // message length for multipart message in GSM 7 Bit encoding
  const multiMessageLength_GSM_7BIT = 153;
  // message length for multipart message in GSM 7 Bit encoding with extended characters
  const multiMessageLength_GSM_7BIT_EX = 153;
  // message length for multipart message in UTF16 encoding
  const multiMessageLength_UTF16 = 67;

  /**
   * function count($text)
   * Detects the encoding, Counts the characters, message length, remaining characters
   * @returns - stdClass Object with params encoding,length,per_message,remaining,messages
   */
  public static function count($text){
    $encoding = self::detect_encoding($text);

    // Assume that the string is UTF-8 and calculate the multibyte string length
    $length = mb_strlen($text, "UTF-8");

    if ( $encoding === self::GSM_7BIT_EX){
      $length_exchars = self::count_gsm_7bitEx($text);
      // Each exchar in the GSM 7 Bit encoding takes one more space
      // Hence the length increases by one char for each of those Ex chars. 
      $length += $length_exchars;
    }

    // Select the per message length according to encoding and the message length
    switch($encoding){
      case self::GSM_7BIT:
        if ( $length > self::messageLength_GSM_7BIT){
          $per_message = self::multiMessageLength_GSM_7BIT;
        }else{
          $per_message = self::messageLength_GSM_7BIT;
        }
      break;

      case self::GSM_7BIT_EX:
        if ( $length > self::messageLength_GSM_7BIT_EX){
          $per_message = self::multiMessageLength_GSM_7BIT_EX;
        }else{
          $per_message = self::messageLength_GSM_7BIT_EX;
        }
      break;

      default:
        if($length > self::messageLength_UTF16){
          $per_message = self::multiMessageLength_UTF16;
        }else{
          $per_message = self::messageLength_UTF16;
        }
      break;
    }

    $messages = ceil($length / $per_message);
    $remaining = ( $per_message * $messages ) - $length ; 

    $returnset = new stdClass();

    $returnset->encoding = $encoding;
    $returnset->length = $length;
    $returnset->per_message = $per_message;
    $returnset->remaining = $remaining;
    $returnset->messages = $messages;

    return $returnset;

  }

  /** 
   * function detect_encoding($text)
   * Detects the encoding of a particular text
   * @return - one of GSM_7BIT, GSM_7BIT_EX, UTF16
   */
  public static function detect_encoding ($text) {

    if(mb_strlen($text) === 0){
      return self::GSM_7BIT;
    }

    if(preg_match(self::gsm_7bit_regex(), $text)){
      return self::GSM_7BIT;
    }
    if(preg_match(self::gsm_7bitEx_regex(), $text)){
      return self::GSM_7BIT_EX;
    }
    else{
      return self::UTF16;
    }
  }

  /**
   * function count_gsm_7bitEx($text)
   * Counts the number of 7BitEx characters in the given string
   * @return - int
   */
  public static function count_gsm_7bitEx($text){
    $replaced_text = preg_replace(self::not_gsm_7bitEx_regex(), '', $text);
    return mb_strlen($replaced_text);
  }

}
