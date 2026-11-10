/* =========================================================
   CAREERAI - MAIN JAVASCRIPT
   ========================================================= */

(function () {
    'use strict';

    /* =============================
       1. SMOOTH SCROLLING
       ============================= */
    function initSmoothScroll() {
        var links = document.querySelectorAll('a[href^="#"]');
        links.forEach(function (link) {
            link.addEventListener('click', function (event) {
                var targetId = link.getAttribute('href');
                if (targetId === '#' || targetId.length < 2) return;
                var targetElement = document.querySelector(targetId);
                if (targetElement) {
                    event.preventDefault();
                    var headerOffset = 72;
                    var elementPosition = targetElement.getBoundingClientRect().top;
                    var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                    closeMobileMenu();
                }
            });
        });
    }

    /* =============================
       2. MOBILE MENU TOGGLE
       ============================= */
    var navToggle = null;
    var navLinks = null;

    function initMobileMenu() {
        navToggle = document.querySelector('.nav-toggle');
        navLinks = document.querySelector('.nav-links');
        if (!navToggle || !navLinks) return;

        navToggle.addEventListener('click', function () {
            var isOpen = navLinks.classList.contains('is-open');
            if (isOpen) { closeMobileMenu(); } else {
                navLinks.classList.add('is-open');
                navToggle.setAttribute('aria-expanded', 'true');
            }
        });

        document.addEventListener('click', function (event) {
            if (!navLinks.classList.contains('is-open')) return;
            var isClickInsideMenu = navLinks.contains(event.target);
            var isClickOnToggle = navToggle.contains(event.target);
            if (!isClickInsideMenu && !isClickOnToggle) closeMobileMenu();
        });
    }

    function closeMobileMenu() {
        if (!navToggle || !navLinks) return;
        navLinks.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
    }

    /* =============================
       3. ANIMATED COUNTER
       ============================= */
    function initCounters() {
        var statValues = document.querySelectorAll('.preview-stat-card .stat-value');
        if (statValues.length === 0) return;
        statValues.forEach(function (el) {
            var finalText = el.textContent.trim();
            var finalNumber = parseInt(finalText, 10);
            if (isNaN(finalNumber)) return;
            el.textContent = '0%';
            animateCounter(el, finalNumber);
        });
    }

    function animateCounter(element, target) {
        var duration = 1200;
        var startTime = null;
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.round(eased * target);
            element.textContent = current + '%';
            if (progress < 1) window.requestAnimationFrame(step);
        }
        window.requestAnimationFrame(step);
    }

    /* =============================
       4. PASSWORD SHOW / HIDE TOGGLE
       ============================= */
    function initPasswordToggles() {
        var toggles = document.querySelectorAll('.password-toggle');
        toggles.forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-target');
                var input = document.getElementById(targetId);
                if (!input) return;
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                button.textContent = isHidden ? 'Hide' : 'Show';
            });
        });
    }

    /* =============================
       5. PASSWORD STRENGTH (Register page)
       ============================= */
    function initPasswordStrength() {
        var passwordInput = document.getElementById('password');
        var submitBtn = document.getElementById('register-submit');
        if (!passwordInput) return;

        var rules = {
            length:  { el: document.getElementById('rule-length'),  test: function (v) { return v.length >= 8; } },
            upper:   { el: document.getElementById('rule-upper'),   test: function (v) { return /[A-Z]/.test(v); } },
            lower:   { el: document.getElementById('rule-lower'),   test: function (v) { return /[a-z]/.test(v); } },
            digit:   { el: document.getElementById('rule-digit'),   test: function (v) { return /[0-9]/.test(v); } },
            special: { el: document.getElementById('rule-special'), test: function (v) { return /[^A-Za-z0-9]/.test(v); } }
        };

        function validate() {
            var value = passwordInput.value;
            var allValid = true;
            Object.keys(rules).forEach(function (key) {
                var rule = rules[key];
                if (!rule.el) return;
                var passed = rule.test(value);
                rule.el.classList.toggle('valid', passed);
                if (!passed) allValid = false;
            });
            if (submitBtn) submitBtn.disabled = !allValid;
        }
        passwordInput.addEventListener('input', validate);
        validate();
    }

    /* =============================
       6. LIVE CLOCK
       ============================= */
    function initLiveClock() {
        var clockEl = document.getElementById('live-clock');
        if (!clockEl) return;

        function updateClock() {
            var now = new Date();
            var timeString = now.toLocaleTimeString([], { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
            clockEl.textContent = timeString;
        }

        setInterval(updateClock, 1000);
        updateClock();
    }

    /* =============================
       7. INITIALIZE EVERYTHING
       ============================= */
    document.addEventListener('DOMContentLoaded', function () {
        initSmoothScroll();
        initMobileMenu();
        initCounters();
        initPasswordToggles();
        initPasswordStrength();
        initLiveClock();
    });

})();