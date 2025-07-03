<?php

if (!class_exists('BuddyMeet_JWT')) {
    class BuddyMeet_JWT {

        private $private_key;
        private $key_id;
        private $app_id;

        public function __construct($app_id, $key_id, $private_key) {
            $this->app_id = $app_id;
            $this->key_id = $key_id;
            $this->private_key = $private_key;
        }

        public function generate_token($user_data, $room_name, $is_moderator = true) {
            if (empty($this->private_key)) {
                return "";
            }

            $header = [
                'kid' => !empty($this->key_id) ? $this->key_id : $this->app_id,
                'typ' => 'JWT',
                'alg' => 'RS256'
            ];

            $current_time = time();
            $payload = [
                'aud' => 'jitsi',
                'iss' => 'chat',
                'iat' => $current_time,
                'exp' => $current_time + (60 * 60),
                'nbf' => $current_time - 5,
                'sub' => $this->app_id,
                'context' => [
                    'features' => [
                        'livestreaming'   => true,
                        'outbound-call'   => true,
                        'sip-outbound-call' => false,
                        'transcription'   => true,
                        'recording'       => true,
                        'flip'            => false,
                    ],
                    'user' => [
                        'hidden-from-recorder' => false,
                        'moderator'            => (bool) $is_moderator,
                        'name'                 => $user_data['name'],
                        'id'                   => $user_data['id'],
                        'avatar'               => $user_data['avatar'],
                        'email'                => $user_data['email'],
                    ],
                ],
                'room' => $room_name,
            ];

            $encoded_header = self::base64url_encode(json_encode($header));
            $encoded_payload = self::base64url_encode(json_encode($payload));

            $signing_input = $encoded_header . '.' . $encoded_payload;
            $signature = '';

            $success = openssl_sign($signing_input, $signature, $this->private_key, 'sha256WithRSAEncryption');

            if (!$success) {
                error_log('OpenSSL signing failed: ' . openssl_error_string());
                return "";
            }

            $encoded_signature = self::base64url_encode($signature);

            return $encoded_header . '.' . $encoded_payload . '.' . $encoded_signature;
        }

        /**
         * Encodes data in a URL-safe Base64 format.
         *
         * @param string $data The data to encode.
         * @return string The Base64Url encoded string.
         */
        private static function base64url_encode($data) {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }
    }
}
