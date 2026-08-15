import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const revealElements = [...document.querySelectorAll('[data-reveal]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (revealElements.length) {
        if (reducedMotion || ! ('IntersectionObserver' in window)) {
            revealElements.forEach((element) => element.classList.add('is-revealed'));
        } else {
            document.documentElement.classList.add('motion-ready');

            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (! entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                });
            }, {
                rootMargin: '0px 0px -8% 0px',
                threshold: 0.12,
            });

            revealElements.forEach((element) => revealObserver.observe(element));
        }
    }

    const siteHeader = document.querySelector('[data-site-header]');

    if (siteHeader) {
        const navigationLinks = [...siteHeader.querySelectorAll('[data-nav-key]')];
        const availableKeys = new Set(navigationLinks.map((link) => link.dataset.navKey));

        const setActiveNavigation = (key, currentValue = null) => {
            if (! availableKeys.has(key)) {
                return;
            }

            navigationLinks.forEach((link) => {
                const isActive = link.dataset.navKey === key;
                link.classList.toggle('is-active', isActive);

                if (isActive && currentValue) {
                    link.setAttribute('aria-current', currentValue);
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        };

        if (siteHeader.hasAttribute('data-homepage-nav')) {
            const sections = [...document.querySelectorAll('[data-nav-section]')]
                .filter((section) => availableKeys.has(section.dataset.navSection));
            const selectCurrentSection = () => {
                if (window.scrollY < 48) {
                    setActiveNavigation('home', 'location');
                    return;
                }

                const marker = siteHeader.offsetHeight + (window.innerHeight * 0.28);
                const current = sections
                    .filter((section) => section.getBoundingClientRect().top <= marker)
                    .at(-1) ?? sections[0];

                if (current) {
                    setActiveNavigation(current.dataset.navSection, 'location');
                }
            };
            const observer = new IntersectionObserver(selectCurrentSection, {
                rootMargin: `-${siteHeader.offsetHeight}px 0px -55% 0px`,
                threshold: [0, 0.1, 0.5],
            });

            sections.forEach((section) => observer.observe(section));
            navigationLinks.forEach((link) => link.addEventListener('click', () => setActiveNavigation(link.dataset.navKey, 'location')));
            selectCurrentSection();
        }
    }

    const dialog = document.querySelector('[data-gallery-dialog]');

    if (dialog) {
    const image = dialog.querySelector('[data-gallery-dialog-image]');
    const caption = dialog.querySelector('[data-gallery-dialog-caption]');
    const title = dialog.querySelector('[data-gallery-dialog-title]');
    const description = dialog.querySelector('[data-gallery-dialog-description]');
    let lastTrigger = null;

    document.querySelectorAll('[data-gallery-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            lastTrigger = trigger;
            image.src = trigger.dataset.galleryImage;
            image.alt = trigger.dataset.galleryTitle || trigger.getAttribute('aria-label');
            title.textContent = trigger.dataset.galleryTitle || '';
            description.textContent = trigger.dataset.galleryDescription || '';
            title.hidden = ! trigger.dataset.galleryTitle;
            description.hidden = ! trigger.dataset.galleryDescription;
            caption.classList.toggle('hidden', ! trigger.dataset.galleryTitle && ! trigger.dataset.galleryDescription);
            dialog.showModal();
        });
    });

    dialog.querySelector('[data-gallery-close]').addEventListener('click', () => dialog.close());

    dialog.addEventListener('click', (event) => {
        const bounds = dialog.getBoundingClientRect();
        const outside = event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom;

        if (outside) {
            dialog.close();
        }
    });

    dialog.addEventListener('close', () => {
        image.src = '';
        lastTrigger?.focus();
    });
    }

    document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
        const mainImage = gallery.querySelector('[data-product-main-image]');
        const thumbnails = [...gallery.querySelectorAll('[data-product-thumbnail]')];

        if (! mainImage || thumbnails.length < 2) {
            return;
        }

        thumbnails.forEach((thumbnail) => {
            thumbnail.addEventListener('click', () => {
                mainImage.src = thumbnail.dataset.image;

                thumbnails.forEach((item) => {
                    const isActive = item === thumbnail;
                    item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    item.classList.toggle('border-[var(--brand-primary)]', isActive);
                    item.classList.toggle('border-line', ! isActive);
                });
            });
        });
    });
});
