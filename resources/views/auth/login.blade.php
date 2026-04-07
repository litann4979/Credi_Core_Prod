<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KrediPal - Login</title>
    <link rel="icon" type="image/png" href="{{ asset('logo1.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --orange: #f97316;
            --orange-dark: #ea580c;
            --bg-light: #f3f4f6;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --success-bg: #ecfdf3;
            --success-border: #bbf7d0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg-light);
            color: var(--text-main);
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 18px;
            padding: 32px 28px;
            box-shadow:
                0 20px 40px rgba(15, 23, 42, 0.08),
                0 1px 3px rgba(15, 23, 42, 0.08);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo-section img {
            display: block;
            margin: 0 auto 16px;
        }

        .logo-section hr {
            border: none;
            border-top: 1px solid var(--border);
            margin: 16px 0 0;
        }

        .login-header {
            text-align: left;
            margin-top: 20px;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .login-header p {
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .success-message,
        .error-message {
            margin-bottom: 16px;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.85rem;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .success-message {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: #14532d;
        }

        .error-message {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: #991b1b;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 10px 38px 10px 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 0.95rem;
            outline: none;
            background-color: #f9fafb;
            transition:
                border-color 0.15s ease,
                box-shadow 0.15s ease,
                background-color 0.15s ease;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-control:focus {
            border-color: var(--orange);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
        }

        .input-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .password-toggle {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9rem;
            color: #9ca3af;
            cursor: pointer;
        }

        .form-options {
            margin-top: 6px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            font-size: 0.85rem;
        }

        .forgot-password {
            color: var(--orange);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            border: none;
            border-radius: 999px;
            padding: 11px 16px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(to right, var(--orange), var(--orange-dark));
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 18px rgba(249, 115, 22, 0.3);
            transition:
                transform 0.12s ease,
                box-shadow 0.12s ease,
                opacity 0.12s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px rgba(249, 115, 22, 0.35);
        }

        .login-btn:active {
            transform: translateY(0);
            box-shadow: 0 6px 12px rgba(249, 115, 22, 0.25);
        }

        /* Right side / testimonial */
        .testimonial-section {
            flex: 1;
            background: linear-gradient(135deg, #ffedd5, #fed7aa);
            position: relative;
            padding: 32px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .testimonial-particles,
        .blue-shape {
            display: none; /* hide extra decoration for a cleaner look */
        }

        .testimonial-container {
            max-width: 460px;
            width: 100%;
            color: #1f2933;
        }

        .testimonial-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px 18px 18px;
            box-shadow:
                0 18px 35px rgba(15, 23, 42, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.7);
            display: none;
            position: relative;
        }

        .testimonial-card.active {
            display: block;
        }

        .profile-photo {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            overflow: hidden;
            border: 2px solid rgba(249, 115, 22, 0.7);
            margin-bottom: 10px;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .quote-icon {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 2rem;
            color: rgba(249, 115, 22, 0.18);
        }

        .testimonial-text {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #374151;
            margin-bottom: 10px;
        }

        .testimonial-author .author-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .testimonial-author .author-title {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .testimonial-dots {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .dot {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            transition: border-color 0.15s ease, transform 0.15s ease;
        }

        .dot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dot.active {
            border-color: var(--orange);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 960px) {
            .login-wrapper {
                flex-direction: column;
            }
            .testimonial-section {
                display: none;
            }
            .login-section {
                padding: 24px 16px;
            }
            .login-container {
                max-width: 420px;
                padding: 24px 20px;
            }
        }

        @keyframes ripple {
            from {
                transform: scale(0);
                opacity: 0.4;
            }
            to {
                transform: scale(2.5);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-section">
            <div class="login-container">
                <div class="logo-section">
                    <img id="kredipalLogo" src="{{ asset('storage/kredipalfinallogo.png') }}" alt="KrediPal Logo" style="width: 180px; height: auto;">
                    <hr>
                </div>

                {{-- <div class="login-header">
                    <h1>Login 👋</h1>
                    <p>Welcome back! Please enter your details to continue.</p>
                </div> --}}

                @if (session('status'))
                    <div class="success-message">
                        <i class="fas fa-check-circle" style="margin-top: 2px;"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle" style="margin-top: 2px;"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="you@example.com"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            >
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="form-options">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="login-btn">
                        <span>Login</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="testimonial-section">
            <div class="testimonial-container">
                <div class="testimonial-card" data-index="0">
                    <div class="profile-photo">
                        <img src="https://cdn.i.haymarketmedia.asia/?n=campaign-india%2Fcontent%2F20241010074955_Untitled+design+(2).jpg" alt="Ratan Tata">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        You can decide how you are working as a team first and then let iTLogistics work around you. 😊
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">Ratan Tata</div>
                        <div class="author-title">Chairman Emeritus of Tata Sons</div>
                    </div>
                </div>

                <div class="testimonial-card" data-index="1">
                    <div class="profile-photo">
                        <img src="https://imageio.forbes.com/specials-images/imageserve/5c7d7829a7ea434b351ba0b6/0x0.jpg" alt="Mukesh Ambani">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        Innovation distinguishes between a leader and a follower. This platform embodies that spirit perfectly. 🚀
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">Mukesh Ambani</div>
                        <div class="author-title">Chairman of Reliance Industries</div>
                    </div>
                </div>

                <div class="testimonial-card" data-index="2">
                    <div class="profile-photo">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/1/11/Narayana_Murthy_CIF_%28cropped%29.JPG" alt="N.R. Narayana Murthy">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        The best way to predict the future is to create it. This tool helps businesses do exactly that. 💪
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">N.R. Narayana Murthy</div>
                        <div class="author-title">Co-founder of Infosys</div>
                    </div>
                </div>

                <div class="testimonial-card" data-index="3">
                    <div class="profile-photo">
                        <img src="https://cdn.britannica.com/74/221774-050-68B15E6F/Indian-businessman-Azim-Premji-2013.jpg" alt="Azim Premji">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        Success is not just about what you accomplish, but what you inspire others to do. This platform inspires excellence. ✨
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">Azim Premji</div>
                        <div class="author-title">Founder Chairman of Wipro</div>
                    </div>
                </div>
                <div class="testimonial-card" data-index="4">
                    <div class="profile-photo">
                        <img src="https://s7d1.scene7.com/is/image/wbcollab/sundar_pichai_google_ceo-1?qlt=75&resMode=sharp2" alt="Sundar Pichai">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        Wear your failure as a badge of honour — every setback carries a lesson to move forward stronger. 🔥
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">Sundar Pichai</div>
                        <div class="author-title">CEO of Google</div>
                    </div>
                </div>
                <div class="testimonial-card" data-index="5">
                    <div class="profile-photo">
                        <img src="https://www.infosys.com/content/dam/infosys-web/en/global-resource/photos/nandan.jpg" alt="Nandan Nilekani">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        The world rewards those who solve real problems — keep focusing on impact, not comfort. 🚀
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">Nandan Nilekani</div>
                        <div class="author-title">Co-founder of Infosys</div>
                    </div>
                </div>

                <div class="testimonial-card" data-index="6">
                    <div class="profile-photo">
                        <img src="https://img.jagranjosh.com/images/2022/March/1432022/Vijay-shekhar-sharma-biography.webp" alt="Vijay Shekhar Sharma">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        Dream big, start small and act fast. The world belongs to the creators — not the spectators. ⚡
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">Vijay Shekhar Sharma</div>
                        <div class="author-title">Founder of Paytm</div>
                    </div>
                </div>

                <div class="testimonial-card" data-index="7">
                    <div class="profile-photo">
                        <img src="https://chanakyauniversity.edu.in/wp-content/uploads/2023/03/Dr.-Kiran-Mazumdar-Shaw.png" alt="Kiran Mazumdar-Shaw">
                    </div>
                    <div class="quote-icon">❝</div>
                    <div class="testimonial-text">
                        Courage is the first ingredient of innovation — breakthroughs happen only when we dare. ✨
                    </div>
                    <div class="testimonial-author">
                        <div class="author-name">Kiran Mazumdar-Shaw</div>
                        <div class="author-title">Founder of Biocon</div>
                    </div>
                </div>

                <div class="testimonial-dots">
                    <div class="dot" data-index="0">
                        <img src="https://cdn.i.haymarketmedia.asia/?n=campaign-india%2Fcontent%2F20241010074955_Untitled+design+(2).jpg" alt="Ratan">
                    </div>
                    <div class="dot" data-index="1">
                        <img src="https://imageio.forbes.com/specials-images/imageserve/5c7d7829a7ea434b351ba0b6/0x0.jpg" alt="Mukesh">
                    </div>
                    <div class="dot" data-index="2">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/1/11/Narayana_Murthy_CIF_%28cropped%29.JPG" alt="Narayana">
                    </div>
                    <div class="dot" data-index="3">
                        <img src="https://cdn.britannica.com/74/221774-050-68B15E6F/Indian-businessman-Azim-Premji-2013.jpg" alt="Azim">
                    </div>
                    <div class="dot" data-index="4">
                        <img src="https://s7d1.scene7.com/is/image/wbcollab/sundar_pichai_google_ceo-1?qlt=75&resMode=sharp2" alt="Sundar">
                    </div>
                    <div class="dot" data-index="5">
                        <img src="https://www.infosys.com/content/dam/infosys-web/en/global-resource/photos/nandan.jpg" alt="Nandan">
                    </div>
                    <div class="dot" data-index="6">
                        <img src="https://img.jagranjosh.com/images/2022/March/1432022/Vijay-shekhar-sharma-biography.webp" alt="Vijay">
                    </div>
                    <div class="dot" data-index="7">
                        <img src="https://chanakyauniversity.edu.in/wp-content/uploads/2023/03/Dr.-Kiran-Mazumdar-Shaw.png" alt="Kiran">
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Remove checkerboard pixels from the uploaded logo preview.
        function cleanCheckerBackground(imgEl) {
            if (!imgEl || imgEl.dataset.cleaned === '1') {
                return;
            }

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                return;
            }

            canvas.width = imgEl.naturalWidth || imgEl.width;
            canvas.height = imgEl.naturalHeight || imgEl.height;
            ctx.drawImage(imgEl, 0, 0);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;

            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                const a = data[i + 3];
                const isGray = Math.abs(r - g) < 12 && Math.abs(g - b) < 12;
                const isLightGray = r > 150 && g > 150 && b > 150;

                if (isGray && isLightGray) {
                    // Remove checker squares while keeping colored logo strokes intact.
                    data[i + 3] = 0;
                } else if (isGray && r > 120 && g > 120 && b > 120) {
                    // Smooth edges near removed pixels.
                    data[i + 3] = Math.max(0, a - 80);
                }
            }

            ctx.putImageData(imageData, 0, 0);
            imgEl.src = canvas.toDataURL('image/png');
            imgEl.dataset.cleaned = '1';
        }

        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        // Testimonial slider (simple)
        let currentTestimonial = 0;
        const testimonials = document.querySelectorAll('.testimonial-card');
        const dots = document.querySelectorAll('.dot');
        const totalTestimonials = testimonials.length;

        function showTestimonial(index) {
            testimonials.forEach((testimonial, i) => {
                testimonial.classList.toggle('active', i === index);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });

            currentTestimonial = index;
        }

        function nextTestimonial() {
            const next = (currentTestimonial + 1) % totalTestimonials;
            showTestimonial(next);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const logoImage = document.getElementById('kredipalLogo');
            if (logoImage) {
                if (logoImage.complete) {
                    cleanCheckerBackground(logoImage);
                } else {
                    logoImage.addEventListener('load', () => cleanCheckerBackground(logoImage), { once: true });
                }
            }

            const randomIndex = Math.floor(Math.random() * totalTestimonials);
            showTestimonial(randomIndex);
        });

        setInterval(nextTestimonial, 5000);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showTestimonial(index);
            });
        });

        // Submit button state
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.querySelector('.login-btn');

        if (loginForm && loginBtn) {
            loginForm.addEventListener('submit', function () {
                loginBtn.style.opacity = '0.85';
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span> Logging in...</span>';
            });
        }
    </script>
</body>
</html>
