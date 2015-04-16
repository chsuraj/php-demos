<?php

if (!function_exists('generate_webtoken')) {

    /**
     * generate_webtoken
     * 
     * @param String $client_id
     * @param Array $credientials
     * $credientials = array(
     *      'api_key' => SOME_KEY,
     *      'api_secret' => SOME_SECRET,
     *      'user_agent' => USER_AGENT_INFO,
     *      'sess_exp_limit' => SESSION_EXP_LIMIT,
     * );
     * @param Array $options
     * $options = array(
     *      'user_agent' => USER_AGENT_INFO,
     *      'sess_exp_limit' => SESSION_EXP_LIMIT,
     * );
     * @param String $salt
     * @return Mixed Array/String
     * 
     * $client_id = 'SOME_CLIENT_ID';
     * $credientials = array(
     *  'api_key' => 'SOME_API_KEY',
     *  'api_secret' => 'SOME_API_SECRET',
     * );
     * $options = array(
     *  sess_exp_limit' => 600,
     *  user_agent' => 'some-valid-user-agent',
     * );
     * $token = generate_webtoken($client_id, $credientials, $options);
     * Above example will return something like following
     * 
     * MTQyOTE4MzE2N0BANjdjMjE1M2RhNmIyOTc5ZTZlZjFiNWE1NDdmMTk4ODlhMzA4MGJmZDM0N2I0NjNiMjA4NTJiNTg3ZTcwZjQ0OUBAMTQyOTE4Mzc2Nw==
     */
    function generate_webtoken($client_id, array $credientials = array(), array $options = array(), $delimeter = '@@') {
        $api_key = isset($credientials['api_key']) ? $credientials['api_key'] : false;
        $api_secret = isset($credientials['api_secret']) ? $credientials['api_secret'] : false;
        /**
         * Validate api credientails
         */
        if (!$api_key || !$api_secret) {
            return array(
                'tokenError' => 1,
                'errorMsg' => 'Invalid credientials.',
            );
        }
        $sess_exp_limit = isset($options['sess_exp_limit']) ? $options['sess_exp_limit'] : 3600;
        $user_agent = isset($options['user_agent']) ? $options['user_agent'] : false;
        /**
         * Valdiate user agent
         */
        if (!$user_agent) {
            return array(
                'tokenError' => 2,
                'errorMsg' => 'Un-Indetified user agent.',
            );
        }
        /**
         * Prepare token hash
         */
        $token_hash = crypt(
                $client_id . $delimeter .
                md5($api_key . $api_secret)
                , $api_secret
        );

        /**
         * Return final encoded web token
         */
        return base64_encode(
                time() . $delimeter
                . hash_hmac(
                        'sha256'
                        , json_encode(http_build_query(array('client_id' => $client_id, 'token_hash' => $token_hash)))
                        , $api_secret)
                . $delimeter
                . strtotime("+{$sess_exp_limit} seconds")
        );
    }

}

if (!function_exists('validate_webtoken')) {

    /**
     * validate_webtoken
     * 
     * @param String $client_id
     * @param String $webtoken     
     * @param Array $credientials
     * $credientials = array(
     *      'api_key' => SOME_KEY,
     *      'api_secret' => SOME_SECRET,
     *      'user_agent' => USER_AGENT_INFO,
     *      'sess_exp_limit' => SESSION_EXP_LIMIT,
     * );
     * @param type $delimeter
     * @return Array
     * 
     * $client_id = 'SOME_CLIENT_ID';
     * $credientials = array(
     *  'api_key' => 'SOME_API_KEY',
     *  'api_secret' => 'SOME_API_SECRET',
     * );
     * $options = array(
     *  sess_exp_limit' => 600,
     *  user_agent' => 'some-valid-user-agent',
     * );
     * $token = generate_webtoken($client_id, $credientials, $options);
     * $webtoken = 'MTQyOTE4MzE2N0BANjdjMjE1M2RhNmIyOTc5ZTZlZjFiNWE1NDdmMTk4ODlhMzA4MGJmZDM0N2I0NjNiMjA4NTJiNTg3ZTcwZjQ0OUBAMTQyOTE4Mzc2Nw==';
     * $validate = validate_webtoken($client_id, $webtoken, $credientials);
     */
    function validate_webtoken($client_id, $webtoken, array $credientials = array(), $delimeter = '@@') {
        /**
         * Decode the hash and validate the keys of token
         */
        $decoded_token = explode($delimeter, base64_decode($webtoken));
        if (sizeof($decoded_token) < 3) {
            return array(
                'tokenError' => 5,
                'errorMsg' => 'Invalid webtoken.',
            );
        }


        $api_key = isset($credientials['api_key']) ? $credientials['api_key'] : false;
        $api_secret = isset($credientials['api_secret']) ? $credientials['api_secret'] : false;
        /**
         * Validate api credientails
         */
        if (!$api_key || !$api_secret) {
            return array(
                'tokenError' => 1,
                'errorMsg' => 'Invalid credientials.',
            );
        }
        /**
         * Prepare token hash
         */
        $token_hash = crypt(
                $client_id . $delimeter .
                md5($api_key . $api_secret)
                , $api_secret
        );

        /**
         * Prepare token hash to compare with existing webtoken
         */
        $_hash = hash_hmac(
                'sha256'
                , json_encode(
                        http_build_query(
                                array(
                                    'client_id' => $client_id,
                                    'token_hash' => $token_hash
                                )
                        )
                )
                , $api_secret
        );


        /**
         * Validate token
         */
        if ($decoded_token[1] !== $_hash) {
            return array(
                'tokenError' => 3,
                'errorMsg' => 'Invalid token provided.',
            );
        } elseif (($decoded_token[2] - strtotime("now")) < 0) {
            return array(
                'tokenError' => 4,
                'errorMsg' => 'Token has been expired.',
            );
        }

        return array(
            'status' => 'Active',
        );
    }

}