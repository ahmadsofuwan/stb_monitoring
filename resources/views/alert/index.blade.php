<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Maintenance - IT Team</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated gradient background */
        .gradient-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #0f172a, #1e293b, #0f172a);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Grid pattern overlay */
        .grid-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(56, 189, 248, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Glowing orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: float 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            top: -200px;
            left: -200px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(45deg, #06b6d4, #3b82f6);
            bottom: -250px;
            right: -250px;
            animation-delay: 7s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(45deg, #8b5cf6, #ec4899);
            top: 50%;
            right: 10%;
            animation-delay: 14s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(50px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-30px, 30px) scale(0.9);
            }
        }

        /* Main container */
        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 1100px;
            padding: 0 40px;
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 12px 30px;
            border-radius: 50px;
            margin-bottom: 40px;
            animation: fadeInDown 0.8s ease-out;
            backdrop-filter: blur(10px);
        }

        .status-dot {
            width: 12px;
            height: 12px;
            background: #3b82f6;
            border-radius: 50%;
            animation: pulse-dot 2s ease-in-out infinite;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.8);
        }

        @keyframes pulse-dot {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.2);
            }
        }

        .status-text {
            color: #60a5fa;
            font-size: 1.1em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Main icon */
        .icon-wrapper {
            margin-bottom: 50px;
            animation: fadeInScale 1s ease-out 0.2s both;
        }

        .main-icon {
            width: 160px;
            height: 160px;
            margin: 0 auto;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 
                0 20px 60px rgba(59, 130, 246, 0.4),
                0 0 100px rgba(139, 92, 246, 0.2);
            position: relative;
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .main-icon::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
            border-radius: 30px;
            opacity: 0.5;
            filter: blur(20px);
            z-index: -1;
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .main-icon svg {
            width: 80px;
            height: 80px;
            fill: white;
        }

        /* Title */
        h1 {
            font-size: 5em;
            font-weight: 800;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            letter-spacing: -2px;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .subtitle {
            font-size: 2em;
            color: #94a3b8;
            margin-bottom: 50px;
            font-weight: 600;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        /* IT Team card */
        .it-team-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 24px;
            padding: 50px 60px;
            margin: 50px auto;
            max-width: 800px;
            backdrop-filter: blur(20px);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: fadeInUp 0.8s ease-out 0.8s both;
        }

        .team-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.4);
        }

        .team-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .team-title {
            font-size: 2.2em;
            color: #fff;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .team-message {
            font-size: 1.5em;
            color: #cbd5e1;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        /* Progress bar */
        .progress-container {
            margin: 40px 0;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .progress-text {
            font-size: 1.2em;
            color: #94a3b8;
            font-weight: 600;
        }

        .progress-bar {
            height: 8px;
            background: rgba(148, 163, 184, 0.2);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #06b6d4, #3b82f6);
            background-size: 200% 100%;
            border-radius: 10px;
            animation: progressMove 2s ease-in-out infinite;
            width: 100%;
        }

        @keyframes progressMove {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }

        /* Warning box */
        .warning-box {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.1), rgba(239, 68, 68, 0.1));
            border: 2px solid rgba(251, 146, 60, 0.3);
            border-radius: 16px;
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 25px;
            margin-top: 40px;
            animation: fadeInUp 0.8s ease-out 1s both;
        }

        .warning-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #fb923c, #ef4444);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(251, 146, 60, 0.4);
        }

        .warning-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .warning-content {
            text-align: left;
            flex: 1;
        }

        .warning-title {
            font-size: 1.6em;
            color: #fbbf24;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .warning-text {
            font-size: 1.3em;
            color: #fcd34d;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
            animation: fadeIn 0.8s ease-out 1.2s both;
        }

        .footer-text {
            font-size: 1.3em;
            color: #64748b;
            margin-bottom: 20px;
        }

        .clock {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(30, 41, 59, 0.5);
            padding: 15px 30px;
            border-radius: 50px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            backdrop-filter: blur(10px);
        }

        .clock svg {
            width: 20px;
            height: 20px;
            fill: #60a5fa;
        }

        .clock-text {
            font-size: 1.2em;
            color: #94a3b8;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive untuk TV besar */
        @media screen and (min-width: 1920px) {
            h1 { font-size: 6em; }
            .subtitle { font-size: 2.5em; }
            .team-title { font-size: 2.8em; }
            .team-message { font-size: 1.8em; }
            .it-team-card { padding: 70px 80px; }
        }

        @media screen and (min-width: 3840px) {
            h1 { font-size: 8em; }
            .subtitle { font-size: 3.5em; }
            .team-title { font-size: 3.5em; }
            .team-message { font-size: 2.2em; }
            .main-icon { width: 240px; height: 240px; }
            .main-icon svg { width: 120px; height: 120px; }
        }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="gradient-bg"></div>
    <div class="grid-overlay"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Main content -->
    <div class="container">
        <!-- Status badge -->
        <div class="status-badge">
            <div class="status-dot"></div>
            <span class="status-text">Sedang Dalam Perbaikan</span>
        </div>

        <!-- Main icon -->
        <div class="icon-wrapper">
            <div class="main-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79 2.73 2.71 7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58 3.51-3.47 9.14-3.47 12.65 0L21 3v7.12zM12.5 8v4.25l3.5 2.08-.72 1.21L11 13V8h1.5z"/>
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h1>MAINTENANCE</h1>
        <div class="subtitle">Perbaikan Sistem Sedang Berlangsung</div>

        <!-- IT Team card -->
        <div class="it-team-card">
            <div class="team-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            </div>
            <div class="team-title">Team IT Sedang Bekerja</div>
            <div class="team-message">
                Tim teknisi IT kami sedang melakukan perbaikan dan pemeliharaan pada TV ini untuk memastikan performa optimal dan kualitas layanan terbaik untuk Anda.
            </div>

            <!-- Progress bar -->
            <div class="progress-container">
                <div class="progress-label">
                    <span class="progress-text">Progress Perbaikan</span>
                    <span class="progress-text">⚙️ Processing...</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
            </div>
        </div>

        <!-- Warning -->
        <div class="warning-box">
            <div class="warning-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5.99L19.53 19H4.47L12 5.99M12 2L1 21h22L12 2zm1 14h-2v2h2v-2zm0-6h-2v4h2v-4z"/>
                </svg>
            </div>
            <div class="warning-content">
                <div class="warning-title">⚠️ MOHON TIDAK DIGANGGU</div>
                <div class="warning-text">Teknisi sedang bekerja - Harap bersabar</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">Terima kasih atas pengertian dan kesabaran Anda</div>
            <div class="clock">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
                <span class="clock-text" id="clock">00:00:00</span>
            </div>
        </div>
    </div>

    <script>
        // Real-time clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>