<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CIS - CNF Integrated System</title>
  <style>
    body {
      margin: 0;
      font-family: 'Arial', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f3f3f3;
      overflow: hidden;
    }
    
    .container {
      position: relative;
      text-align: center;
      color: white;
      width: 90%;
      max-width: 900px;
    }
    
    .background {
      position: absolute;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: linear-gradient(135deg, #11cb21 0%, #2575fc 100%);
      clip-path: polygon(0 0, 100% 0, 100% 50%, 0 100%);
      z-index: -1;
      animation: backgroundAnimation 10s infinite alternate;
    }
    
    @keyframes backgroundAnimation {
      0% {
        clip-path: polygon(0 0, 100% 0, 100% 50%, 0 100%);
      }
      100% {
        clip-path: polygon(0 50%, 100% 100%, 100% 50%, 0 0);
      }
    }
    
    .title {
      font-size: 120px;
      color: white;
      text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      margin: 0;
      animation: fadeIn 2s ease-in-out;
    }
    
    .subtitle {
      font-size: 24px;
      color: #ffffff;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
      margin-top: -20px;
      margin-bottom: 50px;
      animation: fadeIn 3s ease-in-out;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .buttons-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }
    
    .btn {
      padding: 15px 30px;
      font-size: 18px;
      font-weight: bold;
      border: none;
      border-radius: 30px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 200px;
    }
    
    .btn-manufacturing {
      background: white;
      color: #2575fc;
    }
    
    .btn-kayu {
      background: #2ecc71;
      color: white;
    }
    
    .btn-kpi {
      background: #e74c3c;
      color: white;
    }
    
    .btn:hover {
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
      transform: scale(1.05);
    }
    
    .btn-manufacturing:hover {
      background: #2575fc;
      color: white;
    }
    
    .btn-kayu:hover {
      background: #27ae60;
      color: white;
    }
    
    .btn-kpi:hover {
      background: #c0392b;
      color: white;
    }
    
    .btn-icon {
      margin-right: 10px;
      font-size: 20px;
    }
    
    @media (max-width: 768px) {
      .title {
        font-size: 80px;
      }
      
      .subtitle {
        font-size: 20px;
        margin-bottom: 30px;
      }
      
      .btn {
        min-width: 180px;
        padding: 12px 25px;
      }
      
      .buttons-container {
        gap: 15px;
      }
    }
    
    @media (max-width: 480px) {
      .title {
        font-size: 60px;
      }
      
      .subtitle {
        font-size: 18px;
        margin-bottom: 25px;
      }
      
      .btn {
        min-width: 160px;
        padding: 10px 20px;
        font-size: 16px;
      }
      
      .buttons-container {
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <div class="container">
    <div class="background"></div>
    <h1 class="title">CIS</h1>
    <p class="subtitle">CNF Integrated System</p>
    
    <div class="buttons-container">
      <a href="<?=base_url('login')?>" class="btn btn-manufacturing">
        <i class="fas fa-industry btn-icon"></i>
        Manufacturing
      </a>
      
      <a href="https://kayu.cnf-cis.online/" target="_blank" class="btn btn-kayu">
        <i class="fas fa-tree btn-icon"></i>
        Stock Kayu
      </a>
      
      <a href="https://kpi.cnf-cis.online/" target="_blank" class="btn btn-kpi">
        <i class="fas fa-chart-line btn-icon"></i>
        KPI
      </a>
    </div>
  </div>

  <script>
    // Tambahkan efek interaktif saat hover
    document.addEventListener('DOMContentLoaded', function() {
      const buttons = document.querySelectorAll('.btn');
      
      buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
          this.style.transition = 'all 0.2s ease';
        });
        
        button.addEventListener('mouseleave', function() {
          this.style.transition = 'all 0.3s ease';
        });
      });
      
      // Animasi tambahan untuk tombol saat halaman dimuat
      setTimeout(() => {
        const buttonsContainer = document.querySelector('.buttons-container');
        buttonsContainer.style.opacity = '0';
        buttonsContainer.style.transform = 'translateY(20px)';
        buttonsContainer.style.animation = 'fadeIn 1s ease-in-out forwards';
        buttonsContainer.style.animationDelay = '0.5s';
      }, 100);
    });
  </script>
</body>
</html>