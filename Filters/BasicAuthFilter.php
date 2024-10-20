<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class BasicAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $Authorization = $request->getServer("HTTP_AUTHORIZATION");
        //chack authentication header
        if(!$Authorization){
            return Services::response()->setStatusCode(401)->setJSON([
                "status" => false,
                "message" => "UnAuthorized access"
            ]);
        }

        //verify Authorization header patern
        $AuthorizationStringParts = explode(" ", $Authorization); //["Basic", "username:password"]

        if(count($AuthorizationStringParts) !==2|| $AuthorizationStringParts[0] !== "Basic"){
            return Services::response()->setStatusCode(401)->setJSON([
                "status" => false,
                "message" => "Invalid Authorization header"
            ]);
        }
        //verify username and password

        list($username, $password) = explode(":", base64_decode($AuthorizationStringParts[1]));

        //Username: CI$_APIS_ADMIN
        //password: <admin#123>
        if($username !== "CI4_APIS_ADMIN" || $password !== "admin#123"){
                   
                return Services::response()->setStatusCode(401)->setJSON([
                    "status" => false,
                    "message" => "Invalid username or password"
                ]);
    }
}
    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}