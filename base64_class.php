<?php

//thanks to R4SAS
class b32_b64 {
  // I2P uses custom base64 alphabet, defining translation here
  private $b64Trans = array("-" => "+", "~" => "/");
 
  // base32 alpabet
  private $b32alphabet = 'abcdefghijklmnopqrstuvwxyz234567';
 
  /**
   * base32 encoding function.
   * @param string $string
   * @return string
   */
  private function b32encode(string $data): string
  {
    if (empty($data)) {
        return "";
    }
 
    /* Create binary string zeropadded to eight bits. */
    $data = str_split($data);
    $binary = implode("", array_map(function ($character) {
      return sprintf("%08b", ord($character));
    }, $data));
 
    /* Split to five bit chunks and make sure last chunk has five bits. */
    $binary = str_split($binary, 5);
    $last = array_pop($binary);
    if (null !== $last) {
      $binary[] = str_pad($last, 5, "0", STR_PAD_RIGHT);
    }
 
    /* Convert each five bits to Base32 character. */
    $encoded = implode("", array_map(function ($fivebits) {
      $index = bindec($fivebits);
      return $this->b32alphabet[$index];
    }, $binary));
 
    return $encoded;
  }
 
  public function b32from64(string $data): string
  {
    $bytes = base64_decode(strtr($data, $this->b64Trans));
    return $this->b32encode(hash('sha256', $bytes, true));
  }


      public static function isValidBase64(string $data): bool
      {
        $len = strlen($data);
     
        if($len < 516 || $len > 524)
          return false;
     
        /* .i2p in string */
        if(preg_match('/\.i2p/', $data))
          return false;
     
        /* DSA-SHA (?) */
        if($len == 516 && preg_match('/^[a-zA-Z0-9\-~]+AA$/', $data))
          return true;
     
        /* ECDSA or EdDSA */
        if($len == 524 && preg_match('/^[a-zA-Z0-9\-~]+AEAA[Ec]AAA==$/', $data))
          return true;
     
        return false;
      }


}
