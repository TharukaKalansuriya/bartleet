<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BC Agro-Tronics - Under Maintenance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8faf5;
            color: #333;
            line-height: 1.6;
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .background-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627,25.73 C47.528,22.647 39.93,20.438 32.112,20.438 C24.294,20.438 16.696,22.647 9.597,25.73 C2.498,28.812 0.323,34.213 3.406,41.312 C6.489,48.411 11.89,50.586 18.989,47.503 C26.088,44.42 33.686,42.211 41.504,42.211 C49.322,42.211 56.92,44.42 64.019,47.503 C71.118,50.586 76.519,48.411 79.602,41.312 C82.685,34.213 61.726,28.812 54.627,25.73 Z' fill='%2395c97a' fill-opacity='0.06' fill-rule='evenodd'/%3E%3C/svg%3E");
            z-index: 1;
        }
        
        .logo {
            margin-bottom: 30px;
            display: inline-block;
            padding: 15px 25px;
            border-radius: 8px;
            background: linear-gradient(135deg, #b71c1c 0%, #d32f2f 100%);
            box-shadow: 0 6px 12px rgba(183, 28, 28, 0.2);
        }
        
        .logo-text {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 1px;
            text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.3);
        }
        
        .maintenance-icon-container {
            margin: 30px 0;
            position: relative;
            display: inline-block;
        }
        
        .maintenance-icon {
            width: 150px;
            height: 150px;
            background-color: #ffffff;
            border-radius: 50%;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        
        .maintenance-icon-bg {
            position: absolute;
            top: -15px;
            left: -15px;
            right: -15px;
            bottom: -15px;
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
            border-radius: 50%;
            z-index: 1;
            opacity: 0.8;
        }
        
        h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #b71c1c;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .content-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #b71c1c;
        }
        
        p {
            font-size: 17px;
            margin-bottom: 20px;
            color: #444;
        }
        
        .highlight {
            color: #4caf50;
            font-weight: 600;
        }
        
        .progress-container {
            width: 100%;
            background-color: #e8f5e9;
            border-radius: 30px;
            margin: 30px 0;
            padding: 5px;
            overflow: hidden;
            box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .progress-bar {
            height: 20px;
            background: linear-gradient(90deg, #b71c1c 0%, #d32f2f 100%);
            border-radius: 30px;
            width: 70%;
            position: relative;
            animation: progress-animation 3s ease-in-out infinite alternate;
        }
        
        .progress-bar:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
            animation: progress-stripe 1s linear infinite;
        }
        
        @keyframes progress-animation {
            0% {
                width: 30%;
            }
            100% {
                width: 70%;
            }
        }
        
        @keyframes progress-stripe {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 20px 0;
            }
        }
        
        .machine-icon {
            color: #4caf50;
            margin: 0 5px;
            animation: rotate 10s linear infinite;
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .tea-leaves {
            color: #8bc34a;
            margin: 0 5px;
            animation: sway 3s ease-in-out infinite alternate;
        }
        
        @keyframes sway {
            from {
                transform: rotate(-5deg);
            }
            to {
                transform: rotate(5deg);
            }
        }
        
        .contact-info {
            margin: 40px 0 20px;
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .contact-title {
            font-size: 18px;
            font-weight: 600;
            color: #4caf50;
            margin-bottom: 10px;
        }
        
        .contact-methods {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 15px;
        }
        
        .contact-method {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .contact-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #b71c1c;
            color: #ffffff;
            border-radius: 50%;
        }
        
        .email {
            color: #b71c1c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .email:hover {
            color: #4caf50;
            text-decoration: underline;
        }
        
        .estimated-time {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f1f8e9;
            border-radius: 30px;
            margin: 20px 0;
            font-weight: 600;
            color: #4caf50;
            border: 1px dashed #8bc34a;
        }
        
        .footer {
            margin-top: 40px;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #777;
            position: relative;
            z-index: 3;
        }
        
        .tea-plantation {
            position: relative;
            width: 100%;
            height: 120px;
            background-color: rgba(76, 175, 80, 0.1);
            margin-top: 40px;
            overflow: hidden;
            z-index: 1;
        }
        
        .tea-bush {
            position: absolute;
            bottom: 0;
            width: 40px;
            height: 40px;
            background-color: #4caf50;
            border-radius: 50% 50% 0 0;
        }
        
        @media screen and (max-width: 768px) {
            .container {
                padding: 30px 15px;
            }
            
            .content-container {
                padding: 30px 20px;
            }
            
            .logo-text {
                font-size: 28px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            .maintenance-icon {
                width: 120px;
                height: 120px;
            }
            
            .contact-methods {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>
    
    <div class="container">
        <div class="logo">
            <div class="logo-text">BC Agro-Tronics</div>
        </div>
        <br>
        <div class="maintenance-icon-container">
            <div class="maintenance-icon-bg"></div>
            <div class="maintenance-icon">
                <svg width="100" height="100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" stroke="#b71c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        
        <h1>Enhancing Your Tea Plantation Experience</h1>
        
        <div class="content-container">
            <p>Our website is currently undergoing scheduled maintenance to bring you an improved platform for all your <span class="highlight">tea plantation machinery</span> needs.</p>
            
            <p>
                <i class="fas fa-cog fa-spin machine-icon"></i>
                We're upgrading our systems to serve you better
                <i class="fas fa-leaf tea-leaves"></i>
            </p>
            
            <div class="progress-container">
                <div class="progress-bar"></div>
            </div>
            
            <div class="estimated-time">
                <i class="far fa-clock"></i> Estimated completion time: 24 hours
            </div>
            
            <p>Thank you for your patience as we work to enhance your experience with BC Agro-Tronics.</p>
        </div>
        
        <div class="contact-info">
            <div class="contact-title">Need immediate assistance?</div>
            <p>Our support team is still available during this maintenance period.</p>
            
            <div class="contact-methods">
                <div class="contact-method">
                    <div class="contact-icon">
                        <i class="far fa-envelope"></i>
                    </div>
                    <a href="mailto:info@bcagrotronics.com" class="email">info@bartleet.com</a>
                </div>
                
                <div class="contact-method">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <span>+94 11 2422331</span>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2025 BC Agro-Tronics. All rights reserved.</p>
            <p>Specialists in Advanced Tea Plantation Machinery</p>
        </div>
    </div>
    
    <!-- Tea plantation visual elements -->
    <div class="tea-plantation">
        <script>
            function createTeaBushes() {
                const plantation = document.querySelector('.tea-plantation');
                const screenWidth = plantation.offsetWidth;
                
                // Clear existing tea bushes
                plantation.innerHTML = '';
                
                // Calculate how many bushes can fit with proper spacing
                const bushSize = 40;
                const bushSpacing = 30;
                const totalBushWidth = bushSize + bushSpacing;
                const maxBushes = Math.floor(screenWidth / totalBushWidth);
                
                // Create evenly spaced bushes
                for (let i = 0; i < maxBushes; i++) {
                    const teaBush = document.createElement('div');
                    teaBush.className = 'tea-bush';
                    
                    // Calculate position with spacing
                    const leftPosition = i * totalBushWidth + bushSpacing/2;
                    
                    // Vary sizes slightly but prevent overlap
                    const sizeVariation = Math.random() * 10; // +/- 10px variation
                    const size = bushSize + sizeVariation;
                    
                    teaBush.style.left = `${leftPosition}px`;
                    teaBush.style.width = `${size}px`;
                    teaBush.style.height = `${size}px`;
                    teaBush.style.backgroundColor = i % 2 === 0 ? '#4caf50' : '#8bc34a';
                    
                    plantation.appendChild(teaBush);
                }
            }
            
            // Initial creation
            window.addEventListener('load', createTeaBushes);
            
            // Re-create on window resize
            window.addEventListener('resize', createTeaBushes);
        </script>
    </div>
</body>
</html>