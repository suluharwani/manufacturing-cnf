<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embed 192.168.2.133</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .description {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
        }
        
        .iframe-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 10px;
        }
        
        .iframe-wrapper {
            position: relative;
            width: 100%;
            height: 70vh;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.2rem;
            color: #666;
        }
        
        .error-message {
            display: none;
            text-align: center;
            padding: 20px;
            color: #d9534f;
            background-color: #f8d7da;
            border-radius: 8px;
            margin: 20px;
        }
        
        .controls {
            display: flex;
            justify-content: center;
            padding: 15px;
            gap: 10px;
            background-color: #f8f9fa;
            border-top: 1px solid #eaeaea;
        }
        
        button {
            padding: 10px 20px;
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .info-panel {
            padding: 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eaeaea;
        }
        
        .info-panel h3 {
            margin-bottom: 10px;
            color: #4b6cb7;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
            }
            
            button {
                width: 100%;
            }
            
            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Embed Konten dari 192.168.2.133</h1>
            <p>Halaman ini menampilkan konten dari server lokal</p>
        </header>
        
        <div class="description">
            <p>Iframe di bawah ini menampilkan konten dari alamat <strong>https://www.hik-connect.com/views/login/index.html#/login</strong>. Jika halaman tidak muncul, pastikan server lokal sedang aktif dan dapat diakses.</p>
        </div>
        
        <div class="iframe-container">
            <div class="iframe-wrapper">
                <div class="loading" id="loadingIndicator">Memuat konten...</div>
                <iframe 
                    src="https://www.hik-connect.com/views/login/index.html#/login" 
                    title="Konten dari 192.168.2.133"
                    id="contentFrame"
                    onload="document.getElementById('loadingIndicator').style.display='none';"
                    onerror="showError()">
                </iframe>
            </div>
        </div>
        
        <div class="error-message" id="errorMessage">
            <h3>Gagal Memuat Konten</h3>
            <p>Konten dari https://www.hik-connect.com/views/login/index.html#/login tidak dapat dimuat. Pastikan:</p>
            <ul>
                <li>Server pada alamat 192.168.2.133 sedang aktif</li>
                <li>Anda terhubung ke jaringan yang benar</li>
                <li>Alamat tersebut dapat diakses dari perangkat Anda</li>
            </ul>
        </div>
        
        <div class="controls">
            <button onclick="refreshFrame()">Muat Ulang</button>
            <button onclick="openInNewTab()">Buka di Tab Baru</button>
        </div>
        
        <div class="info-panel">
            <h3>Informasi</h3>
            <p>Alamat IP 192.168.2.133 adalah alamat privat yang biasanya digunakan dalam jaringan lokal. Halaman ini hanya akan berfungsi jika Anda terhubung ke jaringan yang sama dengan server tersebut.</p>
        </div>
        
        <footer>
            <p>Halaman Embed &copy; 2023 | Dibuat untuk keperluan demonstrasi</p>
        </footer>
    </div>

    <script>
        function refreshFrame() {
            document.getElementById('loadingIndicator').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            const frame = document.getElementById('contentFrame');
            frame.src = frame.src;
        }
        
        function openInNewTab() {
            window.open('https://www.hik-connect.com/views/login/index.html#/login', '_blank');
        }
        
        function showError() {
            document.getElementById('loadingIndicator').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'block';
        }
        
        // Coba tampilkan error setelah waktu tunggu tertentu
        setTimeout(() => {
            if (document.getElementById('loadingIndicator').style.display !== 'none') {
                showError();
            }
        }, 10000);
    </script>
</body>
</html>