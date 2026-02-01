// Custom JavaScript for interactions
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const name = document.getElementById('name').value;
            const message = document.getElementById('message').value;

            if (!name || !email || !message) {
                alert('Veuillez remplir tous les champs.');
                e.preventDefault();
                return;
            }

            if (!validateEmail(email)) {
                alert('Veuillez entrer un email valide.');
                e.preventDefault();
                return;
            }
        });
    }

    const inscriptionForm = document.getElementById('inscriptionForm');
    if (inscriptionForm) {
        inscriptionForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const formation = document.getElementById('formation').value;

            if (!name || !email || !phone || !formation) {
                alert('Veuillez remplir tous les champs.');
                e.preventDefault();
                return;
            }

            if (!validateEmail(email)) {
                alert('Veuillez entrer un email valide.');
                e.preventDefault();
                return;
            }
        });
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Star Rating System
    const stars = document.querySelectorAll('.star');
    const ratingValue = document.getElementById('ratingValue');
    const ratingText = document.getElementById('ratingText');

    if (stars.length > 0) {
        stars.forEach(star => {
            // Hover effect
            star.addEventListener('mouseover', function() {
                const hoverValue = this.getAttribute('data-value');
                stars.forEach((s, index) => {
                    if (index < hoverValue) {
                        s.classList.add('hover');
                    } else {
                        s.classList.remove('hover');
                    }
                });
            });

            // Click to rate
            star.addEventListener('click', function() {
                const selectedValue = this.getAttribute('data-value');
                ratingValue.value = selectedValue;

                stars.forEach((s, index) => {
                    if (index < selectedValue) {
                        s.classList.add('active');
                        s.classList.remove('hover');
                    } else {
                        s.classList.remove('active');
                    }
                });

                // Update rating text
                const ratings = ['', 'Mauvais', 'Acceptable', 'Bon', 'Très bon', 'Excellent'];
                ratingText.textContent = `Vous avez donné une note de ${selectedValue}/5 - ${ratings[selectedValue]}`;
                ratingText.style.color = '#667eea';
            });
        });

        // Remove hover effect when leaving the rating area
        document.getElementById('starRating').addEventListener('mouseout', function() {
            stars.forEach(s => {
                s.classList.remove('hover');
            });
        });
    }

    // Testimonial Form Validation
    const testimonialForm = document.getElementById('testimonialForm');
    if (testimonialForm) {
        testimonialForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const rating = document.getElementById('ratingValue').value;

            if (!name || !email) {
                alert('Veuillez remplir les champs obligatoires (nom et email).');
                e.preventDefault();
                return;
            }

            if (rating == 0) {
                alert('Veuillez sélectionner une note en cliquant sur les étoiles.');
                e.preventDefault();
                return;
            }

            if (!validateEmail(email)) {
                alert('Veuillez entrer un email valide.');
                e.preventDefault();
                return;
            }

            // Character limit for comment
            const comment = document.getElementById('comment').value;
            if (comment.length > 500) {
                alert('Le commentaire ne peut pas dépasser 500 caractères.');
                e.preventDefault();
                return;
            }
        });
    }

    // Read More functionality for testimonials
    const readMoreButtons = document.querySelectorAll('.read-more-btn');
    readMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const testimonialId = this.getAttribute('data-id');
            const shortComment = document.getElementById('comment-' + testimonialId);
            const fullComment = document.getElementById('full-comment-' + testimonialId);

            if (fullComment.style.display === 'none') {
                // Show full comment
                shortComment.style.display = 'none';
                fullComment.style.display = 'block';
                this.textContent = 'Lire moins';
                this.classList.add('read-less-btn');
            } else {
                // Show short comment
                shortComment.style.display = 'block';
                fullComment.style.display = 'none';
                this.textContent = 'Lire plus';
                this.classList.remove('read-less-btn');
            }
        });
    });

    // Offcanvas animation handlers
    const offcanvasEl = document.getElementById('mainNavOffcanvas');
    if (offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', function() {
            offcanvasEl.classList.add('offcanvas-animate-in');
            // Focus the first link for accessibility after the animation starts
            setTimeout(() => {
                const first = offcanvasEl.querySelector('.nav-link');
                if (first) first.focus();
            }, 180);
        });
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function() {
            offcanvasEl.classList.remove('offcanvas-animate-in');
        });
    }

    // Search suggestions (typeahead)
    const searchInput = document.getElementById('globalSearchInput');
    const suggestionBox = document.getElementById('searchSuggestions');
    let suggestions = [];
    let activeIndex = -1;
    let debounceTimer = null;

    function hideSuggestions() {
        suggestionBox.style.display = 'none';
        suggestionBox.innerHTML = '';
        suggestionBox.setAttribute('aria-hidden', 'true');
        if (searchInput) searchInput.setAttribute('aria-expanded', 'false');
        if (searchInput) searchInput.removeAttribute('aria-activedescendant');
        activeIndex = -1;
    }

    function showSuggestions(items) {
        if (!items || items.length === 0) {
            hideSuggestions();
            return;
        }
        suggestionBox.innerHTML = '';
        items.forEach((it, idx) => {
            const a = document.createElement('a');
            a.className = 'list-group-item list-group-item-action';
            a.setAttribute('role', 'option');
            a.setAttribute('data-idx', idx);
            // assign an id for aria-activedescendant references
            a.id = 'search-suggestion-' + idx;
            a.setAttribute('href', (it.type === 'news') ? ('news.php?id=' + encodeURIComponent(it.id)) : ('portfolio.php?id=' + encodeURIComponent(it.id)));
            a.innerHTML = '<strong>' + escapeHtml(it.label) + '</strong><br><small class="text-muted">' + escapeHtml((it.excerpt || '').substring(0, 80)) + '...</small>';
            a.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });
            suggestionBox.appendChild(a);
        });
        suggestionBox.style.display = 'block';
        suggestionBox.setAttribute('aria-hidden', 'false');
        if (searchInput) searchInput.setAttribute('aria-expanded', 'true');
    }

    function escapeHtml(s) {
        return String(s).replace(/[&"'<>]/g, function(c) { return { '&': '&amp;', '"': '&quot;', "'": '&#39;', '<': '&lt;', '>': '&gt;' }[c]; });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const q = this.value.trim();
            clearTimeout(debounceTimer);
            if (!q) { hideSuggestions(); return; }
            debounceTimer = setTimeout(() => {
                fetch('search_suggest.php?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        suggestions = data;
                        showSuggestions(suggestions);
                    })
                    .catch(err => {
                        console.error('Suggestion error', err);
                        hideSuggestions();
                    });
            }, 220);
        });

        searchInput.addEventListener('keydown', function(e) {
            const items = suggestionBox.querySelectorAll('.list-group-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                items.forEach((it, i) => it.classList.toggle('active', i === activeIndex));
                if (activeIndex >= 0 && items[activeIndex]) {
                    searchInput.setAttribute('aria-activedescendant', items[activeIndex].id);
                    items[activeIndex].scrollIntoView({ block: 'nearest' });
                }
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                items.forEach((it, i) => it.classList.toggle('active', i === activeIndex));
                if (activeIndex >= 0 && items[activeIndex]) {
                    searchInput.setAttribute('aria-activedescendant', items[activeIndex].id);
                    items[activeIndex].scrollIntoView({ block: 'nearest' });
                } else {
                    searchInput.removeAttribute('aria-activedescendant');
                }
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    window.location.href = items[activeIndex].getAttribute('href');
                } else {
                    // fallback to full search page
                    const q = this.value.trim();
                    if (q) window.location.href = 'search.php?q=' + encodeURIComponent(q);
                }
            }
            if (e.key === 'Escape') {
                hideSuggestions();
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                hideSuggestions();
            }
        });
    }

    console.log('BETHEL LABS site loaded');
});