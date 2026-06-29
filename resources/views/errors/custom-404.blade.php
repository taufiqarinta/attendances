<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kobin Admin Portal</title>
  <link rel="shortcut icon" href="https://web.kobin.co.id/assets/compiled/png/kobin-icon.png">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(180deg, #a40618, #480004);
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      color: #fff;
	  
	  background-image: url(https://web.kobin.co.id/assets/compiled/png/background.png);
      background-size: cover;
      height: 100dvh;
      margin: 0;
    }

    .wrapper {
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      animation: fadeIn 1.2s ease-in-out;
      max-width: 420px;
      width: 100%;
      padding: 40px 30px;
      /* background: rgba(255,255,255,0.08); */
      border-radius: 24px;
      backdrop-filter: blur(14px);
      /* box-shadow: 0 20px 40px rgba(0,0,0,0.35); */
      z-index: 2;
    }

    .logo {
      margin-bottom: 20px;
    }

    h2 {
      font-size: 24px;
      margin-bottom: 12px;
      font-weight: 600;
      line-height: 1.2; /* Tambahkan ini untuk menjaga spasi */
    }

    p {
      font-size: 15px;
      opacity: 0.85;
      margin-bottom: 26px;
      line-height: 1.6;
    }

    a {
      display: inline-block;
      padding: 12px 34px;
      background: linear-gradient(135deg, #a40618, #480004);
      color: #fff;
      text-decoration: none;
      border-radius: 30px;
      font-weight: 600;
      font-size: 16px; /* Tambahkan ukuran font explicit */
      transition: all 0.3s ease;
    }

    a:hover {
      transform: translateY(-2px) scale(1.04);
      box-shadow: 0 0px 5px rgba(255,255,255,0.45);
    }

    .bubble {
      position: absolute;
      bottom: -150px;
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.12);
      border-radius: 50%;
      animation: rise 22s infinite ease-in;
      z-index: 1;
    }

    .bubble:nth-child(1) { left: 12%; animation-duration: 18s; width: 28px; height: 28px; }
    .bubble:nth-child(2) { left: 28%; animation-duration: 26s; }
    .bubble:nth-child(3) { left: 45%; animation-duration: 20s; width: 52px; height: 52px; }
    .bubble:nth-child(4) { left: 65%; animation-duration: 24s; }
    .bubble:nth-child(5) { left: 82%; animation-duration: 30s; width: 64px; height: 64px; }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes rise {
      0% { transform: translateY(0) scale(0.8); opacity: 0; }
      10% { opacity: 1; }
      100% { transform: translateY(-1300px) scale(1.25); opacity: 0; }
    }

    @media (max-width: 480px) {
      h2 { font-size: 22px; }
      p { font-size: 14px; }
      a { font-size: 14px; } /* Tambahkan ini untuk responsif */
    }

    /* Tambahan untuk memastikan konsistensi */
    #lottie-container {
      width: 220px;
      height: 220px;
      margin: 0 auto;
    }
  </style>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
</head>
<body>

  <!-- Background bubbles -->
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>

  <div class="wrapper">
    <div class="container">
      <div class="logo">
        <div id="lottie-container"></div>
      </div>

      <!-- Pastikan teks ini sama persis dengan kode awal -->
      <h2>Oops!<br/>Halaman tidak ditemukan</h2>
      <p>Halaman yang anda cari mungkin sudah dipindahkan atau tidak tersedia.</p>
      <a href="{{ url('/welcome') }}">Kembali ke Beranda</a>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const container = document.getElementById('lottie-container');
      
      try {
        if (typeof lottie !== 'undefined') {
          lottie.loadAnimation({
            container: container,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset("assets/lottie/error.json") }}'
          });
        } else {
          throw new Error('Lottie not loaded');
        }
      } catch (error) {
        console.warn('Lottie error:', error);
        container.innerHTML = `
          <img 
            src="https://web.kobin.co.id/assets/compiled/png/kobin-icon.png" 
            alt="Error Icon" 
            style="width:220px;height:220px;object-fit:contain;"
          />
        `;
      }
    });
  </script>
</body>
</html>