<?php
/**
 * Copyright (c) 2016 Micorosft Corporation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Nagesh Tembhurnikar <nagesh@introp.net>
 * @license MIT
 * @copyright (C) 2016 onwards Microsoft Corporation (http://microsoft.com/)
 */
namespace microsoft\aadphp\samples;

require(__DIR__ . '/../../vendor/autoload.php');

class sdsapi
{
    /** @var \microsoft\aadphp\AAD\token A token object representing all token information to be used for this client. */
    protected $token;

    /** @var \microsoft\aadphp\HttpClientInterface An HTTP client to use for communication. */
    protected $httpclient;

    /** @var \microsoft\aadphp\samples\sqlite Database instance to update the tokens if needed */
    protected $db;

    /** @var int The ID of the user for which api is getting called. */
    protected $userid;

    const BASE_URL = 'https://graph.windows.net';

    //This can be tenant id or domain name or tenant alias
    const TENANT = 'cdsync66.onmicrosoft.com';

    /**
     * Constructor.
     *
     * @param string $token The access token.
     * @param \microsoft\aadphp\HttpClientInterface $httpclient The httpclient.
     * @param \microsoft\aadphp\samples\sqlite $db The database instance to update the token in database.
     * @param string $userid The user id.
     */
    public function __construct(
        \microsoft\aadphp\AAD\token $token,
        \microsoft\aadphp\HttpClientInterface $httpclient,
        \microsoft\aadphp\samples\sqlite $db,
        $userid
    ) {
        $this->token = $token;
        $this->httpclient = $httpclient;
        $this->db = $db;
        $this->userid = $userid;
    }

    /**
     * Determine whether the supplied token is valid, and refresh if necessary.
     *
     * @return bool true if valid token or refreshed, false if invalid or couldn't refresh.
     */
    protected function checktoken()
    {
        if ($this->token->is_expired() === true) {
            $token = $this->token->refresh();
            if ($token) {
                $this->db->update_ad_user($token, $this->userid);
                return true;
            } else {
                // Could not refresh token.
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Make an API call.
     *
     * @param string $url for rest call .
     * @param array $params Additional params for the request.
     * @return array $response
     * $response['success'] bool Success/Failure.
     * $response['value'] Array if successful, String message if not.
     */
    public function apicall($url, $params = '')
    {
        $response = array();
        $response['success'] = false;

        $tokenvalid = $this->checktoken();
        if (!$tokenvalid) {
            $response['value'] = "The access token to fetch sds data may be expired or not present.
                Please login through Microsoft account using method Auth Code or Credentials.";
            return $response;
        }

        $options = array();
        $headers = array();
        $headers[] = "Authorization: Bearer " . $this->token->get_token();
        $options['headers'] = $headers;

        $response_str = $this->httpclient->get($url, $params, $options);

        try {
            $response['value'] = $this->process_sdsapi_response($response_str);
            $response['success'] = true;
            return $response;
        } catch (\Exception $e) {
            $response['value'] = "SDS data does not exist.";
            return $response;
        }
    }

    /**
     * Processes API responses.
     *
     * @param string $response The raw response from an API call.
     * @param array $expectedstructure A structure to validate.
     * @return array|null Array if successful, null if not.
     */
    private function process_sdsapi_response($response, array $expectedstructure = array())
    {
        $result = @json_decode($response, true);

        if (empty($result) || !is_array($result)) {
            throw new \Exception('Error : Bad api call');
        }
        if (isset($result['odata.error'])) {
            if (isset($result['odata.error']['message']) && isset($result['odata.error']['message']['value'])) {
                $apierrormessage = $result['odata.error']['message']['value'];
                throw new \Exception(htmlentities($apierrormessage));
            } else {
                throw new \Exception('Error : Bad api call');
            }
        }
        if (isset($result['error'])) {
            if (isset($result['error']['message'])) {
                $apierrormessage = 'Unknown error, check logs for more information.';
                if (is_string($result['error']['message'])) {
                    $apierrormessage = $result['error']['message'];
                } else {
                    if (is_array($result['error']['message']) && isset($result['error']['message']['value'])) {
                        $apierrormessage = $result['error']['message']['value'];
                    }
                }
                throw new \Exception('Error : ' . htmlentities($apierrormessage));
            } else {
                throw new \Exception('Error : Bad api call');
            }
        }

        foreach ($expectedstructure as $key => $val) {
            if (!isset($result[$key])) {
                $errmsg = 'Invalid structure received. No "' . $key . '"';
                throw new \Exception('Error : Bad api call' . $errmsg);
            }
            if ($val !== null && $result[$key] !== $val) {
                throw new \Exception('Invalid structure received.');
            }
        }

        return $result;
    }

    /**
     * Get all schools that exist in the Azure Active Directory tenant.
     *
     * @return array $response
     * $response['success'] bool Success/Failure.
     * $response['value'] Array if successful, String message if not.
     */
    public function get_schools()
    {
        $url = $this::BASE_URL . "/" . $this::TENANT . "/administrativeUnits?api-version=beta&\$filter=extension_fe2174665583431c953114ff7268b7b3_Education_ObjectType%20eq%20'School'";
        $response = $this->apicall($url);
        return $response;
    }
}
