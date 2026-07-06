{{-- New design theme: violet/green dark crypto theme ported from laravel-crypto-investment-design --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --color-ink: #05060a;
        --color-panel: #0b0e15;
        --color-panel-2: #10141d;
        --color-card: #121722;
        --color-line: #1e2530;
        --color-brand: #b57bff;
        --color-brand-2: #7c3aed;
        --color-accent: #22e58a;
    }

    .font-display { font-family: "Space Grotesk", ui-sans-serif, system-ui, sans-serif; }
    .font-sans-new { font-family: "Inter", ui-sans-serif, system-ui, sans-serif; }

    /* Signature helpers */
    .brand-gradient {
        background: linear-gradient(92deg, #b57bff 0%, #7c3aed 55%, #22e58a 130%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .glass {
        background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
        -webkit-backdrop-filter: blur(14px);
        backdrop-filter: blur(14px);
    }
    .glow-brand {
        box-shadow: 0 0 0 1px rgba(181,123,255,0.25), 0 20px 60px -18px rgba(124,58,237,0.55);
    }
    .glow-accent {
        box-shadow: 0 0 0 1px rgba(34,229,138,0.25), 0 20px 60px -18px rgba(34,229,138,0.5);
    }
    .brand-btn {
        background-image: linear-gradient(to right, #b57bff, #7c3aed);
        box-shadow: 0 10px 30px -10px rgba(124,58,237,0.6);
        transition: transform .2s ease;
    }
    .brand-btn:hover { transform: scale(1.03); }
    .grid-bg {
        background-image:
            linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
        background-size: 48px 48px;
        -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, #000 40%, transparent 100%);
        mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, #000 40%, transparent 100%);
    }

    /* Keyframes */
    @keyframes float-y { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }
    @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    @keyframes fade-up { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes spin-slow { to { transform: rotate(360deg); } }

    .animate-float { animation: float-y 6s ease-in-out infinite; }
    .animate-marquee { animation: marquee 32s linear infinite; }
    .animate-fade-up { animation: fade-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .animate-spin-slow { animation: spin-slow 40s linear infinite; }

    /* New-design scrollbar (dark) */
    .newtheme-scroll::-webkit-scrollbar { width: 10px; height: 10px; }
    .newtheme-scroll::-webkit-scrollbar-track { background: #0a0d14; }
    .newtheme-scroll::-webkit-scrollbar-thumb { background: #232a36; border-radius: 999px; }
    .newtheme-scroll::-webkit-scrollbar-thumb:hover { background: #333c4c; }
</style>
