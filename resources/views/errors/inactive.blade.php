<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Accès Refusé — TransMada</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary:    #001c47;
            --secondary:  #2894FF;
            --danger:     #ef4444;
            --danger-bg:  #fee2e2;
            --text-muted: #6b7280;
            --border:     #e5e7eb;
            --surface:    #f9fafb;
        }

        body {
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(ellipse at 60% 0%, #0a3270 0%, #001c47 55%, #000e26 100%);
            padding: 24px;
        }

        /* ── Floating particles ── */
        .particles {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: .07;
            background: var(--secondary);
            animation: float linear infinite;
        }
        @keyframes float {
            from { transform: translateY(110vh) scale(.6); opacity: 0; }
            10%  { opacity: .1; }
            90%  { opacity: .07; }
            to   { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ── Card ── */
        .card {
            position: relative;
            z-index: 1;
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            box-shadow: 0 32px 64px rgba(0,0,0,.4), 0 0 0 1px rgba(255,255,255,.06);
            animation: card-in .45s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes card-in {
            from { opacity: 0; transform: translateY(24px) scale(.97); }
            to   { opacity: 1; transform: none; }
        }

        .card-accent {
            height: 5px;
            background: linear-gradient(90deg, var(--secondary) 0%, #5eb0ff 100%);
        }

        .card-body { padding: 44px 40px 40px; text-align: center; }

        /* ── Icon ── */
        .icon-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--danger-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            position: relative;
        }
        .icon-ring::before {
            content: '';
            position: absolute;
            inset: -7px;
            border-radius: 50%;
            border: 2px solid var(--danger);
            opacity: .22;
            animation: pulse-ring 2.4s ease-out infinite;
        }
        @keyframes pulse-ring {
            0%   { transform: scale(.85); opacity: .3; }
            70%  { transform: scale(1.18); opacity: 0; }
            100% { transform: scale(1.18); opacity: 0; }
        }
        .icon-ring svg { width: 38px; height: 38px; color: var(--danger); }

        /* ── Text ── */
        .badge {
            display: inline-block;
            padding: 4px 13px;
            border-radius: 20px;
            background: rgba(40,148,255,.10);
            color: var(--secondary);
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
            line-height: 1.25;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: .93rem;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        /* ── Info block ── */
        .info-block {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 28px;
            text-align: left;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .85rem;
            padding: 5px 0;
        }
        .info-row + .info-row { border-top: 1px solid var(--border); margin-top: 9px; padding-top: 14px; }
        .info-label { font-weight: 600; color: var(--primary); }
        .info-value { color: var(--text-muted); font-family: monospace; font-size: .82rem; }

        /* ── Buttons ── */
        .btn {
            display: block;
            width: 100%;
            padding: 13px 24px;
            border-radius: 10px;
            font-family: inherit;
            font-size: .93rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .2s;
            text-decoration: none;
            text-align: center;
            line-height: 1.4;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #1a7ee0 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(40,148,255,.32);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(40,148,255,.44); }
        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1.5px solid var(--border);
            margin-top: 10px;
        }
        .btn-ghost:hover { background: var(--surface); color: var(--primary); border-color: #9ca3af; }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* ── Footer ── */
        .card-footer { text-align: center; font-size: .78rem; color: #9ca3af; margin-top: 26px; }

        /* ══════════════════════════════
           MODAL
        ══════════════════════════════ */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,10,28,.6);
            backdrop-filter: blur(5px);
            z-index: 100;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 0 12px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s;
        }
        .modal-backdrop.open { opacity: 1; pointer-events: all; }

        .modal {
            background: #fff;
            border-radius: 20px 20px 0 0;
            width: 100%;
            max-width: 520px;
            padding: 28px 36px 40px;
            transform: translateY(50px);
            transition: transform .32s cubic-bezier(.22,1,.36,1);
            max-height: 93vh;
            overflow-y: auto;
        }
        .modal-backdrop.open .modal { transform: translateY(0); }

        .modal-handle {
            width: 40px; height: 4px;
            background: var(--border);
            border-radius: 2px;
            margin: 0 auto 22px;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .modal-title { font-size: 1.12rem; font-weight: 700; color: var(--primary); }

        .modal-close {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: var(--surface);
            color: var(--text-muted);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
            flex-shrink: 0;
        }
        .modal-close:hover { background: var(--border); color: var(--primary); }

        .modal-desc { font-size: .875rem; color: var(--text-muted); margin-bottom: 22px; line-height: 1.65; }

        /* ── Form ── */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 6px;
            letter-spacing: .03em;
        }
        .form-control {
            width: 100%;
            padding: 11px 13px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: .9rem;
            color: #111827;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(40,148,255,.13);
        }
        textarea.form-control { resize: vertical; min-height: 110px; }
        .form-hint { font-size: .77rem; color: var(--text-muted); margin-top: 4px; }

        /* ── Success ── */
        .success-view {
            display: none;
            text-align: center;
            padding: 12px 0 8px;
        }
        .check-icon {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
        }
        .success-view h3 { font-size: 1.1rem; font-weight: 700; color: var(--primary); margin-bottom: 8px; }
        .success-view p  { font-size: .9rem; color: var(--text-muted); line-height: 1.6; }

        @media (max-width: 480px) {
            .card-body { padding: 36px 22px 30px; }
            .form-row  { grid-template-columns: 1fr; }
            .modal     { padding: 24px 20px 36px; }
        }
    </style>
</head>
<body>

    <div class="particles" id="particles"></div>

    <div class="card">
        <div class="card-accent"></div>
        <div class="card-body">

            <div class="badge">Accès restreint</div>

            <div class="icon-ring">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>

            <h1>Compte Désactivé</h1>

            <p class="subtitle">
                Votre accès à ce compte a été suspendu.<br>
                Contactez notre équipe pour rétablir votre accès.
            </p>

            @if(($tenantId ?? null) || ($domain ?? null))
            <div class="info-block">
                @if($tenantId ?? null)
                <div class="info-row">
                    <span class="info-label">ID Client</span>
                    <span class="info-value">{{ $tenantId }}</span>
                </div>
                @endif
                @if($domain ?? null)
                <div class="info-row">
                    <span class="info-label">Domaine</span>
                    <span class="info-value">{{ $domain }}</span>
                </div>
                @endif
            </div>
            @endif

            <button class="btn btn-primary btn-icon" onclick="openModal()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
                Contacter le Support
            </button>

            <a href="/" class="btn btn-ghost">Retour à l'accueil</a>

            <div class="card-footer">
                &copy; {{ date('Y') }} TransMada &mdash; Tous droits réservés
            </div>
        </div>
    </div>

    <!-- ══ MODAL ══ -->
    <div class="modal-backdrop" id="modalBackdrop" onclick="handleBackdropClick(event)">
        <div class="modal" id="modal">
            <div class="modal-handle"></div>

            <!-- Form -->
            <div id="formView">
                <div class="modal-header">
                    <span class="modal-title">Contacter le Support</span>
                    <button class="modal-close" onclick="closeModal()" aria-label="Fermer">
                        <svg width="13" height="13" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="1" y1="1" x2="13" y2="13"/><line x1="13" y1="1" x2="1" y2="13"/>
                        </svg>
                    </button>
                </div>

                <p class="modal-desc">
                    Décrivez votre situation et notre équipe vous répondra dans les plus brefs délais.
                </p>

                <form id="contactForm" onsubmit="handleSubmit(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fname">Prénom</label>
                            <input id="fname" type="text" class="form-control" placeholder="Jean" required>
                        </div>
                        <div class="form-group">
                            <label for="lname">Nom</label>
                            <input id="lname" type="text" class="form-control" placeholder="Dupont" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cemail">Adresse e-mail</label>
                        <input id="cemail" type="email" class="form-control" placeholder="vous@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Objet</label>
                        <input id="subject" type="text" class="form-control"
                            value="Demande de réactivation{{ isset($tenantId) ? ' — ' . $tenantId : '' }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="cmessage">Message</label>
                        <textarea id="cmessage" class="form-control"
                            placeholder="Décrivez votre problème ou votre demande…" required></textarea>
                        <p class="form-hint">Soyez le plus précis possible pour accélérer le traitement.</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-icon" id="submitBtn">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                        </svg>
                        Envoyer le message
                    </button>
                </form>
            </div>

            <!-- Success -->
            <div class="success-view" id="successView">
                <div class="check-icon">
                    <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                </div>
                <h3>Message envoyé !</h3>
                <p>Notre équipe vous répondra sous 24 h.<br>Merci de votre patience.</p>
                <button class="btn btn-ghost" style="margin-top:20px;max-width:200px;margin-inline:auto;" onclick="closeModal()">Fermer</button>
            </div>
        </div>
    </div>

    <script>
        /* Particles */
        (function () {
            const c = document.getElementById('particles');
            for (let i = 0; i < 18; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                const s = Math.random() * 220 + 60;
                p.style.cssText = `width:${s}px;height:${s}px;left:${Math.random()*100}%;bottom:${Math.random()*-30}%;animation-duration:${Math.random()*20+18}s;animation-delay:${Math.random()*-30}s;`;
                c.appendChild(p);
            }
        })();

        /* Modal */
        function openModal() {
            document.getElementById('modalBackdrop').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('modalBackdrop').classList.remove('open');
            document.body.style.overflow = '';
        }
        function handleBackdropClick(e) {
            if (e.target === document.getElementById('modalBackdrop')) closeModal();
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        /* Submit — ouvre le client mail avec les données pré-remplies */
        function handleSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<svg width="15" height="15" style="animation:spin .8s linear infinite" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Envoi…';
            btn.disabled = true;

            const subject = encodeURIComponent(document.getElementById('subject').value);
            const body    = encodeURIComponent(
                `Prénom : ${document.getElementById('fname').value}\nNom    : ${document.getElementById('lname').value}\nEmail  : ${document.getElementById('cemail').value}\n\n${document.getElementById('cmessage').value}`
            );

            window.location.href = `mailto:support@transmada.com?subject=${subject}&body=${body}`;

            setTimeout(() => {
                document.getElementById('formView').style.display   = 'none';
                document.getElementById('successView').style.display = 'block';
            }, 700);
        }
    </script>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</body>
</html>
