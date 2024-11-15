<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AccessControl implements FilterInterface
{
    /**
     * Method ini dipanggil sebelum request diproses.
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return void|ResponseInterface
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Mengambil data session menggunakan method session()->get()
        $auth = session()->get('auth');  // Ambil session 'auth'
        
        // Memeriksa apakah session 'auth' ada
        if (is_null($auth) || !isset($auth['level'])) {
            return redirect()->to(base_url('/login')); // Redirect ke halaman login jika belum login
        }

        // Mendapatkan level akses pengguna
        $userLevel = $auth['level'];
        
        // Mengambil level yang dibutuhkan dari argumen
        $requiredLevel = isset($arguments[0]) ? (int)$arguments[0] : 1; // Default ke level 1 jika tidak ada

        // Memeriksa apakah level akses pengguna cukup untuk mengakses halaman ini
        if ($userLevel < $requiredLevel) {
            return redirect()->to(base_url('/forbidden')); // Redirect jika akses ditolak
        }
    }

    /**
     * Method ini dipanggil setelah request diproses.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada proses setelah request
    }
}
