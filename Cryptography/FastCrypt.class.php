<?php
/**
 * FastTrack cryptography class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Cryptography;

use FastTrack\ObjectBase;
use FastTrack\Str;
use FastTrack\Convert;
use FastTrack\IO\File;

/**
 * FastTrack cryptography class
 */
class FastCrypt extends ObjectBase {
  /**
   * Compute an init byte that will be the starting offset of the crypt key
   *
   * @param   int[]   $pData          Byte array data to crypt
   * @param   int[]   $pInitVector    Byte array containing the initialisation vector
   * @return  int                     Init byte
   */
  public static function computeInitByte($pData, $pInitVector) {
    $ReturnValue = 0;
    for($i = 0; $i < count($pInitVector); $i++) {
      $MessageByteIdx = $pInitVector[$i] % count($pData);
      $ReturnValue = $ReturnValue ^ $pData[$MessageByteIdx];
    }
    return $ReturnValue;
  }

  /**
   * Compute a strong crypt key
   *
   * @param   int[]   $pKey   Byte array containing the crypt key
   * @param   int[]   $pSalt  Byte array containing the crypt salt key
   * @return  int[]           Byte array containing the crypt strong key
   */
  public static function computeKey($pKey, $pSalt) {
    $TmpKey = $pKey;
    for($i = 1; $i < count($pKey); $i++) {
      $TmpKey = array_merge($TmpKey, array_slice($pKey, $i), array_slice($pKey, 0, $i));
    }
    return FastCrypt::xorCryptBytes($TmpKey, $pSalt);
  }

  /**
   * Decrypt a string using a CryptParameters object
   *
   * @param   string          $pData              Data to decrypt
   * @param   CryptParameters $pCryptParameters   Crypt parameters
   * @return  string                              Decrypted string
   */
  public static function decrypt($pData, $pCryptParameters) {
    if(Str::isNullOrEmpty($pData)) {
      // No data to decrypt, returns an empty string
      return '';
    }

    $FullKey = FastCrypt::computeKey(array_values(unpack('C*', $pCryptParameters->PassPhrase)), array_values(unpack('C*', $pCryptParameters->Salt)));
    $KeyLength = count($FullKey);
    $Data = array_values(unpack('C*', Convert::fromBase64String($pData)));

    // Reads and decrypts the init byte
    $initByte = $Data[0] ^ $FullKey[0];

    // Decrypts the message
    $ReturnValue = FastCrypt::xorCryptBytes(array_slice($Data, 1), $FullKey, $initByte % $KeyLength);

    $ReturnValueStr = '';
    foreach($ReturnValue as $CurrentByte) {
      $ReturnValueStr .= pack('C*', $CurrentByte);
    }

    return $ReturnValueStr;
  }

  /**
   * Decrypt a file using a CryptParameters object
   *
   * @param string            $pInputFileName         Name of the file to decrypt
   * @param string            $pOutputFileName        Name of the decrypted file
   * @param CryptParameters   $pCryptParameters       Crypt parameters
   * @param bool              $pDeleteInputFileAfter  Flag that determines whether we should delete the input file after decryption
   */
  public static function decryptFile($pInputFileName, $pOutputFileName, $pCryptParameters, $pDeleteInputFileAfter = false) {
    // Decrypt and encrypt is the same processing
    FastCrypt::encryptFile($pInputFileName, $pOutputFileName, $pCryptParameters, $pDeleteInputFileAfter);
  }

  /**
   * Encrypt a string using a CryptParameters object
   *
   * @param   string          $pMessage           String to encrypt
   * @param   CryptParameters $pCryptParameters   Crypt parameters
   * @return  string                              Encrypted string
   */
  public static function encrypt($pMessage, $pCryptParameters) {
    if(Str::isNullOrEmpty($pMessage)) {
      // No data to decrypt, returns an empty string
      return '';
    }

    $FullKey = FastCrypt::computeKey(array_values(unpack('C*', $pCryptParameters->PassPhrase)), array_values(unpack('C*', $pCryptParameters->Salt)));
    $KeyLength = count($FullKey);
    $message = array_values(unpack('C*', $pMessage));
    $initVector = array_values(unpack('C*', $pCryptParameters->InitVector));
    $initByte = FastCrypt::computeInitByte($message, $initVector);

    // Encrypts and writes the init byte
    $ReturnValue = [];
    $ReturnValue[] = $initByte ^ $FullKey[0];

    // Encrypts the message
    $ReturnValue = array_merge($ReturnValue, FastCrypt::xorCryptBytes($message, $FullKey, $initByte % $KeyLength));

    $ReturnValueStr = '';
    foreach($ReturnValue as $CurrentByte) {
      $ReturnValueStr .= pack('C*', $CurrentByte);
    }

    return Convert::toBase64String($ReturnValueStr);
  }

  /**
   * Encrypt a file using a CryptParameters object
   *
   * @param string            $pInputFileName         Name of the file to encrypt
   * @param string            $pOutputFileName        Name of the encrypted file
   * @param CryptParameters   $pCryptParameters       Crypt parameters
   * @param bool              $pDeleteInputFileAfter  Flag that determines whether we should delete the input file after encryption
   */
  public static function encryptFile($pInputFileName, $pOutputFileName, $pCryptParameters, $pDeleteInputFileAfter = false) {
    $FullKey = FastCrypt::computeKey(array_values(unpack('C*', $pCryptParameters->PassPhrase)), array_values(unpack('C*', $pCryptParameters->Salt)));
    $KeyLength = count($FullKey);

    // Open the files
    $Fi = new File($pInputFileName, File::MODE_READONLY);
    $Fo = new File($pOutputFileName, File::MODE_WRITEONLY);

    while(!$Fi->EOF) {
      $FiData = $Fi->readBytes($KeyLength);
      $Fo->writeBytes(FastCrypt::xorCryptBytes($FiData, $FullKey));
    }

    $Fi->close();
    $Fo->close();

    // We should delete the input file
    if($pDeleteInputFileAfter === true) {
      File::delete($pInputFileName);
    }
  }

  /**
   * Encrypts a byte array using XOR
   *
   * @param   int[]   $pData      Byte array to encrypt
   * @param   int[]   $pKey       Byte array containing the key
   * @param   int     $pKeyOffset Key starting offset
   * @return  int[]               Byte array containing the encrypted data
   */
  public static function xorCryptBytes($pData, $pKey, $pKeyOffset = 0) {
    $CurrentKeyByte = $pKeyOffset;
    $KeyLength = count($pKey);
    $ReturnValue = [];

    for($i = 0; $i < count($pData); $i++) {
      $ReturnValue[] = $pData[$i] ^ $pKey[$CurrentKeyByte];
      $CurrentKeyByte++;
      if($CurrentKeyByte >= $KeyLength) {
        $CurrentKeyByte = 0;
      }
    }

    return $ReturnValue;
  }
}
