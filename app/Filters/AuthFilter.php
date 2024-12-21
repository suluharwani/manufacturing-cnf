<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('loggedIn')) {
            return redirect()->to('/login');
        }

        // Cek peran pengguna
        if (in_array($arguments[0], ['admin', 'user'])) {
            if (session()->get('role') !== $arguments[0]) {
                return redirect()->to('/dashboard'); // Redirect jika tidak memiliki akses
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response)
    {
        // Do something here
    }
}