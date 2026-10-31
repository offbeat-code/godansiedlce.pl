<?php
/**
 * Module: Contact
 *
 * @package Godan
 */

$id_module   = $module['id_module'] ?? '';
$tagline     = $module['tagline'] ?? '';
$headline    = $module['headline'] ?? '';
$description = $module['description'] ?? '';
$benefits    = $module['benefits'] ?? [];
?>

<section <?= $id_module ? 'id="' . esc_attr($id_module) . '" ' : ''; ?>class="contact">
    <div class="contact__confetti">
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
        <div class="contact__confetti-piece"></div>
    </div>

    <div class="container">
        <div class="contact__inner">
            <div class="contact__info">
                <?php if ($tagline): ?>
                    <span class="contact__tagline tagline"><?= esc_html($tagline); ?></span>
                <?php endif; ?>

                <?php if ($headline): ?>
                    <h2 class="contact__headline headline"><?= esc_html($headline); ?></h2>
                <?php endif; ?>

                <?php if ($description): ?>
                    <p class="contact__description description"><?= wp_kses_post($description); ?></p>
                <?php endif; ?>

                <?php if ($benefits): ?>
                    <ul class="contact__benefits">
                        <?php foreach ($benefits as $benefit):
                            $icon = $benefit['icon'] ?? 'circle-check';
                            $text = $benefit['text'] ?? '';

                            if (!$text) continue;
                        ?>
                            <li class="contact__benefit">
                                <i class="contact__benefit-icon" data-lucide="<?= esc_attr($icon); ?>"></i>
                                <span><?= esc_html($text); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="contact__form-card">
                <form class="contact__form" id="contact-form">
                    <div class="contact__form-row">
                        <div class="contact__form-group">
                            <label for="contact-name" class="contact__label">Imię i nazwisko</label>
                            <input type="text" id="contact-name" name="name" class="contact__input" required>
                        </div>

                        <div class="contact__form-group">
                            <label for="contact-phone" class="contact__label">Telefon</label>
                            <input type="tel" id="contact-phone" name="phone" class="contact__input">
                        </div>
                    </div>

                    <div class="contact__form-group">
                        <label for="contact-email" class="contact__label">E-mail</label>
                        <input type="email" id="contact-email" name="email" class="contact__input" required>
                    </div>

                    <div class="contact__form-group">
                        <label for="contact-message" class="contact__label">Wiadomość</label>
                        <textarea id="contact-message" name="message" class="contact__input contact__textarea" rows="4" required></textarea>
                    </div>

                    <div class="contact__honeypot" aria-hidden="true">
                        <label for="contact-website">Website</label>
                        <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="contact__consent">
                        <label class="contact__consent-label">
                            <input type="checkbox" id="contact-privacy" name="privacy_consent" class="contact__consent-checkbox" required>
                            <span class="contact__consent-checkmark"></span>
                            <span class="contact__consent-text">
                                Akceptuję <a href="<?= esc_url(get_privacy_policy_url()); ?>" target="_blank" rel="noopener">politykę prywatności</a> i wyrażam zgodę na przetwarzanie moich danych osobowych w celu odpowiedzi na zapytanie.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="contact__submit btn btn--primary">
                        <i data-lucide="send"></i>
                        Wyślij wiadomość
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var nameInput = document.getElementById('contact-name');
    var emailInput = document.getElementById('contact-email');
    var phoneInput = document.getElementById('contact-phone');

    if (nameInput) {
        nameInput.addEventListener('input', function(e) {
            var value = e.target.value;
            value = value.replace(/[^a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s\-]/g, '');
            value = value.replace(/(^|\s|-)([a-ząćęłńóśźż])/g, function(match, separator, letter) {
                return separator + letter.toUpperCase();
            });
            e.target.value = value;
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', function(e) {
            var value = e.target.value.trim();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value && !emailRegex.test(value)) {
                e.target.setCustomValidity('Podaj prawidłowy adres e-mail');
            } else {
                e.target.setCustomValidity('');
            }
        });
        emailInput.addEventListener('input', function(e) {
            e.target.setCustomValidity('');
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            var value = e.target.value;
            var cursorPos = e.target.selectionStart;
            var oldLength = value.length;
            var hasPlus = value.startsWith('+');
            var digits = value.replace(/\D/g, '').substring(0, 15);

            var formatted = '';
            if (hasPlus && digits.length > 0) {
                if (digits.length <= 3) {
                    formatted = '+' + digits;
                } else {
                    var prefix = digits.substring(0, 2);
                    var rest = digits.substring(2);
                    formatted = '+' + prefix;
                    for (var i = 0; i < rest.length; i++) {
                        if (i % 3 === 0) formatted += ' ';
                        formatted += rest[i];
                    }
                }
            } else {
                for (var i = 0; i < digits.length; i++) {
                    if (i > 0 && i % 3 === 0) formatted += ' ';
                    formatted += digits[i];
                }
            }
            e.target.value = formatted;
            var newLength = e.target.value.length;
            var diff = newLength - oldLength;
            e.target.setSelectionRange(cursorPos + diff, cursorPos + diff);
        });

        phoneInput.addEventListener('keydown', function(e) {
            if (e.key === '+' && e.target.selectionStart !== 0) {
                e.preventDefault();
            }
        });
    }

    var contactForm = document.getElementById('contact-form');
    var confetti = document.querySelector('.contact__confetti');

    if (contactForm && typeof godanContact !== 'undefined') {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var submitBtn = contactForm.querySelector('.contact__submit');
            var originalBtnText = submitBtn.innerHTML;

            var existingAlert = contactForm.querySelector('.contact__alert');
            if (existingAlert) existingAlert.remove();

            contactForm.querySelectorAll('.contact__field-error').forEach(function(el) { el.remove(); });
            contactForm.querySelectorAll('.contact__input--error').forEach(function(el) {
                el.classList.remove('contact__input--error');
            });
            contactForm.querySelectorAll('.contact__consent--error').forEach(function(el) {
                el.classList.remove('contact__consent--error');
            });

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="contact__spinner" width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Wysyłanie...';

            var formData = new FormData(contactForm);
            formData.append('action', 'godan_submit_contact');
            formData.append('nonce', godanContact.nonce);

            fetch(godanContact.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    showAlert('success', result.data.message);
                    contactForm.reset();
                    if (confetti) {
                        confetti.classList.add('is-celebrating');
                        setTimeout(function() {
                            confetti.classList.remove('is-celebrating');
                        }, 2000);
                    }
                } else {
                    showAlert('error', result.data.message);
                    if (result.data.errors) {
                        for (var field in result.data.errors) {
                            var message = result.data.errors[field];
                            if (field === 'privacy_consent') {
                                var consentWrapper = contactForm.querySelector('.contact__consent');
                                if (consentWrapper) {
                                    consentWrapper.classList.add('contact__consent--error');
                                    var errorEl = document.createElement('span');
                                    errorEl.className = 'contact__field-error';
                                    errorEl.textContent = message;
                                    consentWrapper.appendChild(errorEl);
                                }
                            } else {
                                var input = contactForm.querySelector('[name="' + field + '"]');
                                if (input) {
                                    input.classList.add('contact__input--error');
                                    var errorEl = document.createElement('span');
                                    errorEl.className = 'contact__field-error';
                                    errorEl.textContent = message;
                                    input.parentNode.appendChild(errorEl);
                                }
                            }
                        }
                    }
                }
            })
            .catch(function() {
                showAlert('error', 'Wystąpił błąd połączenia. Spróbuj ponownie.');
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });

        function showAlert(type, message) {
            var alertEl = document.createElement('div');
            alertEl.className = 'contact__alert contact__alert--' + type;
            var iconPath = type === 'success'
                ? '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><polyline points="22,4 12,14.01 9,11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
                : '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
            alertEl.innerHTML = '<svg class="contact__alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">' + iconPath + '</svg><span>' + message + '</span>';

            var submitBtn = contactForm.querySelector('.contact__submit');
            submitBtn.parentNode.insertBefore(alertEl, submitBtn);
            alertEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
</script>
