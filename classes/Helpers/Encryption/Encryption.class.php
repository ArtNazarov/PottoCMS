<?php

define('ciph', "AES-128-CTR");
define('encryption_iv', '1234567891011121');
define('decryption_iv', '1234567891011121');
define('ciph_opt', 0);


class Encryption
{
	private $lib = 'ocrypt';
	
    function encrypt_data($key, $text){
      switch ($this->lib){
		  case 'ocrypt' : {
			  return $this->o_encrypt_data($key, $text);
		  };
		  case 'mcrypt' : {
			  return $this->m_encrypt_data($key, $text);
		  };
	  };
     }

    function decrypt_data($key, $text){
		switch ($this->lib){
			case 'ocrypt' : {
				return $this->o_decrypt_data($key, $text);
			};
			case 'mcrypt' : {
				return $this->m_decrypt_data($key, $text);
			};
		};
    }
	
	private function m_encrypt_data($key, $text){
		  $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
        return $encrypted_text;
	}
	
	private function m_decrypt_data($key, $text){
		 global $encryptionkey;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
        return $decrypted_text;
	}
	
	private function o_encrypt_data($key, $text){
		return openssl_encrypt($text, ciph, 
            $key, ciph_opt, encryption_iv);
	}
	private function o_decrypt_data($key, $text){
		return openssl_decrypt ($text, ciph,  
        $key, ciph_opt, decryption_iv);
	}
}