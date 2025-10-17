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
            // Redirect ke halaman login jika session tidak ditemukan atau level tidak ada
            return redirect()->to(base_url('/forbidden'));
        }

        // Mendapatkan level akses pengguna
        $userLevel = (int)$auth['level'];
        
        // Mengambil level yang dibutuhkan dari argumen filter
        // Jika tidak ada argumen, default ke level 1 (Super Admin)
        $requiredLevel = isset($arguments[0]) ? (int)$arguments[0] : 1;

        // Memeriksa apakah level akses pengguna cukup untuk mengakses halaman ini
        if ($userLevel > $requiredLevel) {
            // Jika level pengguna lebih tinggi dari level yang dibutuhkan, akses ditolak
            return redirect()->to(base_url('/forbidden'));
        }

        // Jika level pengguna mencukupi atau lebih tinggi dari yang dibutuhkan, lanjutkan request
        return null;
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
        // Tidak ada proses setelah request (kosong jika tidak ada aksi yang diperlukan)
    }
}
