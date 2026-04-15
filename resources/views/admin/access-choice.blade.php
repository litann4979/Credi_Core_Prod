<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Workspace</title>
    <link rel="icon" type="image/png" href="{{ asset('logo1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#fff7ed] text-slate-800">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-orange-300/40 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-amber-300/35 blur-3xl"></div>

        <main class="relative z-10 mx-auto flex min-h-screen max-w-5xl items-center px-6 py-10">
            <div class="w-full">
                <div class="mb-8 text-center">
                    <div class="mb-3 flex justify-center">
                        <img id="accessChoiceKredipalLogo" src="{{ asset('storage/kredipalfinallogo.png') }}" alt="Kredipal Logo" class="h-12 w-auto object-contain">
                    </div>
                    <div class="mx-auto mb-4 inline-flex items-center rounded-full border border-orange-200 bg-white px-4 py-1.5 text-xs font-semibold text-orange-700 shadow-sm">
                        Kredipal Admin Access
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Choose where you want to continue</h1>
                    <p class="mt-3 text-sm text-slate-600 sm:text-base">
                        Admin Panel is for internal management. TV Mode opens a restricted live dashboard without admin navigation.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <a href="{{ route('admin.dashboard') }}"
                       class="group flex h-full flex-col rounded-2xl border border-orange-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-[0_16px_40px_-22px_rgba(249,115,22,0.55)]">
                        <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-700">
                            <span class="text-lg">🛡️</span>
                        </div>
                        <h2 class="text-xl font-semibold text-slate-900">Open Admin Panel</h2>
                        <p class="mt-2 text-sm text-slate-600">
                            Full admin navigation with users, leads, reports, and system controls.
                        </p>
                        <span class="mt-auto inline-flex w-fit items-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-3 py-1.5 pt-1.5 text-sm font-semibold text-white">
                            Continue to internal console
                        </span>
                    </a>

                    <form method="POST" action="{{ route('admin.access-choice.live-tv') }}" target="_blank" rel="noopener" class="h-full">
                        @csrf
                        <button type="submit"
                                class="group flex h-full w-full flex-col rounded-2xl border border-orange-200 bg-white p-6 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-[0_16px_40px_-22px_rgba(249,115,22,0.55)]">
                            <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-700">
                                <span class="text-lg">📺</span>
                            </div>
                            <h2 class="text-xl font-semibold text-slate-900">Open User Live Dashboard (TV Mode)</h2>
                            <p class="mt-2 text-sm text-slate-600">
                                Signed, standalone dashboard route with no admin sidebar and no direct admin menu access.
                            </p>
                            <span class="mt-auto inline-flex w-fit items-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-3 py-1.5 pt-1.5 text-sm font-semibold text-white">
                                Launch TV-safe dashboard
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logo = document.getElementById('accessChoiceKredipalLogo');
            if (!logo) return;

            function cleanCheckerBackground(imgEl) {
                if (!imgEl || imgEl.dataset.cleaned === '1') return;

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                if (!ctx) return;

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
                        data[i + 3] = 0;
                    } else if (isGray && r > 120 && g > 120 && b > 120) {
                        data[i + 3] = Math.max(0, a - 80);
                    }
                }

                ctx.putImageData(imageData, 0, 0);
                imgEl.src = canvas.toDataURL('image/png');
                imgEl.dataset.cleaned = '1';
            }

            if (logo.complete) {
                cleanCheckerBackground(logo);
            } else {
                logo.addEventListener('load', function () {
                    cleanCheckerBackground(logo);
                }, { once: true });
            }
        });
    </script>
</body>
</html>
