const track = document.getElementById("track");

if (track) {
    // DUPLICA CONTENUTO
    track.innerHTML += track.innerHTML;

    let position = 0;
    let speed = 1; // pixel per frame

    function animate() {
        position -= speed;

        // reset seamless
        if (Math.abs(position) >= track.scrollWidth / 2) {
            position = 0;
        }

        track.style.transform = `translate3d(${position}px, 0, 0)`;

        requestAnimationFrame(animate);
    }

    animate();
}

// Animazione mouse card portfolio homepage

document.querySelectorAll('.portfolio-card').forEach(card => {

    const content = card.querySelector('.portfolio-content');

    card.addEventListener('mouseenter', () => {
        content.classList.remove('display-none');
    });

    card.addEventListener('mouseleave', () => {
        content.classList.add('display-none');
    });

});

// apertura e chiusura navbar 

const menuButton = document.getElementById('menu-button');
const navbarContent = document.getElementById('mobile-navbar-content');
const navbarLinks = navbarContent ? navbarContent.querySelectorAll('a') : [];
const gsapInstance = window.gsap;

if (menuButton && navbarContent && gsapInstance) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const animationDuration = reducedMotion ? 0.01 : 0.45;
    const staggerDuration = reducedMotion ? 0 : 0.055;
    let isMenuOpen = false;

    const menuTimeline = gsapInstance.timeline({
        paused: true,
        defaults: {
            ease: 'power3.out',
        },
        onStart: () => {
            navbarContent.classList.remove('navbar-closed');
        },
        onReverseComplete: () => {
            navbarContent.classList.add('navbar-closed');
        },
    });

    gsapInstance.set(navbarContent, {
        autoAlpha: 0,
        y: -18,
    });

    gsapInstance.set(navbarLinks, {
        autoAlpha: 0,
        y: 12,
    });

    menuTimeline
        .to(navbarContent, {
            autoAlpha: 1,
            y: 0,
            duration: animationDuration,
        })
        .to(navbarLinks, {
            autoAlpha: 1,
            y: 0,
            duration: reducedMotion ? 0.01 : 0.32,
            stagger: staggerDuration,
        }, '-=0.25')
        .to(menuButton, {
            rotate: 90,
            duration: reducedMotion ? 0.01 : 0.28,
            transformOrigin: '50% 50%',
        }, 0);

    function openMenu() {
        isMenuOpen = true;
        menuButton.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        menuTimeline.play();
    }

    function closeMenu() {
        isMenuOpen = false;
        menuButton.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        menuTimeline.reverse();
    }

    function toggleMenu() {
        if (isMenuOpen) {
            closeMenu();
            return;
        }

        openMenu();
    }

    menuButton.addEventListener('click', toggleMenu);

    menuButton.addEventListener('keydown', event => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            toggleMenu();
        }
    });

    navbarLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && isMenuOpen) {
            closeMenu();
        }
    });
}

// FAQ accordion servizi

const faqBlock = document.getElementById('faq-block');

if (faqBlock && gsapInstance) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const faqQuestions = faqBlock.querySelectorAll('.faq-question');
    let openFaq = null;

    const faqItems = Array.from(faqQuestions)
        .map(question => {
            const answer = question.nextElementSibling;
            const trigger = question;
            const icon = question.querySelector('.round-pill svg');

            if (!answer || !answer.classList.contains('faq-answer') || !icon) {
                return null;
            }

            trigger.setAttribute('role', 'button');
            trigger.setAttribute('tabindex', '0');
            trigger.setAttribute('aria-expanded', 'false');

            gsapInstance.set(answer, {
                autoAlpha: 0,
                height: 0,
                marginTop: 0,
            });

            gsapInstance.set(icon, {
                rotate: 0,
                transformOrigin: '50% 50%',
            });

            const timeline = gsapInstance.timeline({
                paused: true,
                defaults: {
                    duration: reducedMotion ? 0.01 : 0.35,
                    ease: 'power3.out',
                },
            });

            timeline
                .to(answer, {
                    autoAlpha: 1,
                    height: 'auto',
                    marginTop: '2.5rem',
                })
                .to(icon, {
                    rotate: 45,
                    duration: reducedMotion ? 0.01 : 0.25,
                }, 0);

            return {
                trigger,
                timeline,
            };
        })
        .filter(Boolean);

    function closeFaq(item) {
        item.trigger.setAttribute('aria-expanded', 'false');
        item.timeline.reverse();
    }

    function openFaqItem(item) {
        if (openFaq && openFaq !== item) {
            closeFaq(openFaq);
        }

        item.trigger.setAttribute('aria-expanded', 'true');
        item.timeline.play();
        openFaq = item;
    }

    function toggleFaq(item) {
        if (openFaq === item) {
            closeFaq(item);
            openFaq = null;
            return;
        }

        openFaqItem(item);
    }

    faqItems.forEach(item => {
        item.trigger.addEventListener('click', () => {
            toggleFaq(item);
        });

        item.trigger.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleFaq(item);
            }
        });
    });
}

// Review carousel servizi

document.querySelectorAll('.review-container').forEach(reviewContainer => {
    const track = reviewContainer.querySelector('.review-track');
    const cards = track ? track.querySelectorAll('.review-card') : [];
    const previousButton = reviewContainer.querySelector('.review-arrow-left');
    const nextButton = reviewContainer.querySelector('.review-arrow-right');

    if (!track || cards.length === 0 || !previousButton || !nextButton || !gsapInstance) {
        return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let currentIndex = 0;
    let visibleCards = window.innerWidth > 768 ? 2 : 1;
    let maxIndex = Math.max(cards.length - visibleCards, 0);
    let step = 0;

    previousButton.setAttribute('role', 'button');
    previousButton.setAttribute('tabindex', '0');
    previousButton.setAttribute('aria-label', 'Recensione precedente');
    nextButton.setAttribute('role', 'button');
    nextButton.setAttribute('tabindex', '0');
    nextButton.setAttribute('aria-label', 'Recensione successiva');

    function updateButtons() {
        previousButton.style.opacity = currentIndex === 0 ? '0.35' : '1';
        nextButton.style.opacity = currentIndex === maxIndex ? '0.35' : '1';
    }

    function updateMeasurements() {
        const firstCard = cards[0];
        const gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;

        visibleCards = window.innerWidth > 768 ? 2 : 1;
        maxIndex = Math.max(cards.length - visibleCards, 0);
        currentIndex = Math.min(currentIndex, maxIndex);
        step = firstCard.getBoundingClientRect().width + gap;

        gsapInstance.set(track, {
            x: -currentIndex * step,
        });

        updateButtons();
    }

    function goToReview(index) {
        currentIndex = Math.max(0, Math.min(index, maxIndex));

        gsapInstance.to(track, {
            x: -currentIndex * step,
            duration: reducedMotion ? 0.01 : 0.45,
            ease: 'power3.out',
        });

        updateButtons();
    }

    function handleKeydown(event, direction) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            goToReview(currentIndex + direction);
        }
    }

    previousButton.addEventListener('click', () => {
        goToReview(currentIndex - 1);
    });

    nextButton.addEventListener('click', () => {
        goToReview(currentIndex + 1);
    });

    previousButton.addEventListener('keydown', event => {
        handleKeydown(event, -1);
    });

    nextButton.addEventListener('keydown', event => {
        handleKeydown(event, 1);
    });

    window.addEventListener('resize', updateMeasurements);
    updateMeasurements();
});

// Gallery custom layout

const adaptiveGalleries = document.querySelectorAll('.adaptive-gallery');

if (adaptiveGalleries.length > 0) {
    function updateGalleryImage(image) {
        if (image.naturalWidth === 0 || image.naturalHeight === 0) {
            return false;
        }

        image.classList.toggle('gallery-image-landscape', image.naturalWidth > image.naturalHeight);
        image.classList.toggle('gallery-image-portrait', image.naturalWidth <= image.naturalHeight);

        return true;
    }

    function updateGalleryRows(gallery) {
        const images = Array.from(gallery.querySelectorAll('img'));

        if (images.some(image => image.naturalWidth === 0 || image.naturalHeight === 0)) {
            return;
        }

        images.forEach(image => {
            image.classList.remove('gallery-image-landscape-wide');
        });

        let usedColumns = 0;
        let rowTypes = [];

        images.forEach((image, index) => {
            const isLandscape = image.classList.contains('gallery-image-landscape');
            const nextImage = images[index + 1];
            const nextIsPortrait = nextImage ? nextImage.naturalWidth <= nextImage.naturalHeight : false;
            const shouldPairWithPortrait = isLandscape && (
                (usedColumns === 0 && nextIsPortrait) ||
                (usedColumns === 2 && rowTypes.length === 1 && rowTypes[0] === 'portrait')
            );

            let span = isLandscape ? (shouldPairWithPortrait ? 4 : 3) : 2;

            if (usedColumns + span > 6) {
                usedColumns = 0;
                rowTypes = [];

                const shouldPairFromNewRow = isLandscape && nextIsPortrait;
                span = isLandscape ? (shouldPairFromNewRow ? 4 : 3) : 2;

                if (shouldPairFromNewRow) {
                    image.classList.add('gallery-image-landscape-wide');
                }
            } else if (shouldPairWithPortrait) {
                image.classList.add('gallery-image-landscape-wide');
            }

            usedColumns += span;
            rowTypes.push(isLandscape ? 'landscape' : 'portrait');

            if (usedColumns >= 6) {
                usedColumns = 0;
                rowTypes = [];
            }
        });
    }

    function updateAdaptiveGalleries() {
        adaptiveGalleries.forEach(gallery => {
            gallery.querySelectorAll('img').forEach(image => {
                updateGalleryImage(image);
            });
            updateGalleryRows(gallery);
        });
    }

    adaptiveGalleries.forEach(gallery => {
        gallery.querySelectorAll('img').forEach(image => {
            if (image.complete) {
                updateGalleryImage(image);
                updateGalleryRows(gallery);
                return;
            }

            image.addEventListener('load', () => {
                updateGalleryImage(image);
                updateGalleryRows(gallery);
            });
        });
    });

    window.addEventListener('resize', updateAdaptiveGalleries);
}

// Lightbox gallery

const lightbox = document.querySelector('.gallery-lightbox');
const lightboxImage = lightbox ? lightbox.querySelector('img') : null;
const lightboxClose = lightbox ? lightbox.querySelector('.gallery-lightbox-close') : null;
const lightboxPrevious = lightbox ? lightbox.querySelector('.gallery-lightbox-prev') : null;
const lightboxNext = lightbox ? lightbox.querySelector('.gallery-lightbox-next') : null;
const galleryImages = document.querySelectorAll('.lightbox-gallery img');

if (lightbox && lightboxImage && lightboxClose && lightboxPrevious && lightboxNext && galleryImages.length > 0) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const lightboxGalleryImages = Array.from(galleryImages);
    let activeImage = null;
    let currentImageIndex = 0;

    if (gsapInstance) {
        gsapInstance.set(lightbox, {
            autoAlpha: 0,
        });

        gsapInstance.set(lightboxImage, {
            scale: 0.96,
            autoAlpha: 0,
        });
    }

    function setLightboxImage(index, animate = true) {
        currentImageIndex = (index + lightboxGalleryImages.length) % lightboxGalleryImages.length;
        activeImage = lightboxGalleryImages[currentImageIndex];

        function updateImage() {
            lightboxImage.src = activeImage.currentSrc || activeImage.src;
            lightboxImage.alt = activeImage.alt || '';
        }

        if (gsapInstance && animate && lightbox.classList.contains('is-open')) {
            gsapInstance.killTweensOf(lightboxImage);
            gsapInstance.timeline({
                defaults: {
                    ease: 'power3.out',
                    duration: reducedMotion ? 0.01 : 0.18,
                },
            })
                .to(lightboxImage, {
                    autoAlpha: 0,
                    scale: 0.985,
                    onComplete: updateImage,
                })
                .to(lightboxImage, {
                    autoAlpha: 1,
                    scale: 1,
                });
            return;
        }

        updateImage();
    }

    function openLightbox(index) {
        setLightboxImage(index, false);
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        if (gsapInstance) {
            gsapInstance.killTweensOf([lightbox, lightboxImage]);
            gsapInstance.timeline({
                defaults: {
                    ease: 'power3.out',
                    duration: reducedMotion ? 0.01 : 0.28,
                },
            })
                .to(lightbox, {
                    autoAlpha: 1,
                })
                .to(lightboxImage, {
                    autoAlpha: 1,
                    scale: 1,
                    duration: reducedMotion ? 0.01 : 0.34,
                }, '-=0.12');
            return;
        }

        lightbox.style.opacity = '1';
        lightbox.style.visibility = 'visible';
    }

    function showPreviousImage() {
        setLightboxImage(currentImageIndex - 1);
    }

    function showNextImage() {
        setLightboxImage(currentImageIndex + 1);
    }

    function closeLightbox() {
        if (!lightbox.classList.contains('is-open')) {
            return;
        }

        function resetLightbox() {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            lightboxImage.src = '';

            if (activeImage) {
                activeImage.focus();
            }
        }

        if (gsapInstance) {
            gsapInstance.killTweensOf([lightbox, lightboxImage]);
            gsapInstance.timeline({
                defaults: {
                    ease: 'power3.inOut',
                    duration: reducedMotion ? 0.01 : 0.22,
                },
                onComplete: resetLightbox,
            })
                .to(lightboxImage, {
                    autoAlpha: 0,
                    scale: 0.96,
                })
                .to(lightbox, {
                    autoAlpha: 0,
                }, '-=0.08');
            return;
        }

        lightbox.style.opacity = '0';
        lightbox.style.visibility = 'hidden';
        resetLightbox();
    }

    lightboxGalleryImages.forEach((image, index) => {
        image.setAttribute('tabindex', '0');
        image.setAttribute('role', 'button');
        image.setAttribute('aria-label', image.alt ? `Apri immagine ${image.alt}` : 'Apri immagine');

        image.addEventListener('click', () => {
            openLightbox(index);
        });

        image.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openLightbox(index);
            }
        });
    });

    lightboxClose.addEventListener('click', closeLightbox);
    lightboxPrevious.addEventListener('click', showPreviousImage);
    lightboxNext.addEventListener('click', showNextImage);

    lightbox.addEventListener('click', event => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeLightbox();
        }

        if (!lightbox.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            showPreviousImage();
        }

        if (event.key === 'ArrowRight') {
            showNextImage();
        }
    });
}

// Form contatti multi-step

const contactForm = document.getElementById('contact-form');

if (contactForm) {
    const allSteps = Array.from(contactForm.querySelectorAll('[data-contact-step]'));
    const weddingSteps = Array.from(contactForm.querySelectorAll('[data-wedding-step]'));
    const projectInputs = Array.from(contactForm.querySelectorAll('input[name="tipo_progetto"]'));
    const previousButton = contactForm.querySelector('[data-contact-prev]');
    const nextButton = contactForm.querySelector('[data-contact-next]');
    const submitButton = contactForm.querySelector('[data-contact-submit]');
    const currentStepLabel = contactForm.querySelector('[data-step-current]');
    const totalStepsLabel = contactForm.querySelector('[data-step-total]');
    const progressBar = contactForm.querySelector('.contact-progress-line span');
    const stepsContainer = contactForm.querySelector('.contact-steps');
    const errorMessage = contactForm.querySelector('.contact-error');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let activeSteps = [];
    let currentStepIndex = 0;
    let isAnimating = false;

    function isWeddingSelected() {
        const selectedProject = projectInputs.find(input => input.checked);
        return selectedProject ? selectedProject.value === 'matrimoni' : false;
    }

    function refreshActiveSteps() {
        const includeWeddingSteps = isWeddingSelected();

        weddingSteps.forEach(step => {
            step.disabled = !includeWeddingSteps;
        });

        activeSteps = allSteps.filter(step => includeWeddingSteps || !step.hasAttribute('data-wedding-step'));
    }

    function updateNavigation() {
        const isFirstStep = currentStepIndex === 0;
        const isLastStep = currentStepIndex === activeSteps.length - 1;
        const progress = (currentStepIndex + 1) / activeSteps.length;

        previousButton.hidden = isFirstStep;
        nextButton.hidden = isLastStep;
        submitButton.hidden = !isLastStep;
        currentStepLabel.textContent = currentStepIndex + 1;
        totalStepsLabel.textContent = activeSteps.length;

        if (gsapInstance) {
            gsapInstance.to(progressBar, {
                scaleX: progress,
                duration: reducedMotion ? 0.01 : 0.35,
                ease: 'power3.out',
            });
        } else {
            progressBar.style.transform = `scaleX(${progress})`;
        }
    }

    function showStep(nextIndex, direction = 1) {
        const currentStep = activeSteps[currentStepIndex];
        const nextStep = activeSteps[nextIndex];

        if (!currentStep || !nextStep || isAnimating) {
            return;
        }

        errorMessage.textContent = '';
        isAnimating = true;

        if (!gsapInstance) {
            currentStep.hidden = true;
            currentStep.disabled = true;
            currentStepIndex = nextIndex;
            nextStep.hidden = false;
            nextStep.disabled = false;
            stepsContainer.style.height = `${nextStep.offsetHeight}px`;
            updateNavigation();
            isAnimating = false;
            return;
        }

        nextStep.hidden = false;
        nextStep.disabled = false;

        gsapInstance.set(nextStep, {
            position: 'absolute',
            top: 0,
            left: 0,
            x: direction * 42,
            autoAlpha: 0,
        });

        const nextHeight = nextStep.offsetHeight;

        gsapInstance.timeline({
            defaults: {
                duration: reducedMotion ? 0.01 : 0.38,
                ease: 'power3.inOut',
            },
            onComplete: () => {
                currentStep.hidden = true;
                currentStep.disabled = true;
                gsapInstance.set(nextStep, {
                    clearProps: 'position,top,left,x,opacity,visibility',
                });
                currentStepIndex = nextIndex;
                stepsContainer.style.height = `${nextStep.offsetHeight}px`;
                updateNavigation();
                isAnimating = false;
            },
        })
            .to(currentStep, {
                x: direction * -42,
                autoAlpha: 0,
            })
            .to(stepsContainer, {
                height: nextHeight,
            }, 0)
            .to(nextStep, {
                x: 0,
                autoAlpha: 1,
            }, '-=0.22');
    }

    function validateCurrentStep() {
        const step = activeSteps[currentStepIndex];
        const fields = Array.from(step.querySelectorAll('input, select, textarea'));
        const firstInvalidField = fields.find(field => !field.checkValidity());

        if (!firstInvalidField) {
            errorMessage.textContent = '';
            return true;
        }

        errorMessage.textContent = 'Completa i campi richiesti prima di continuare.';
        firstInvalidField.reportValidity();
        firstInvalidField.focus();
        return false;
    }

    projectInputs.forEach(input => {
        input.addEventListener('change', () => {
            refreshActiveSteps();
            updateNavigation();
        });
    });

    nextButton.addEventListener('click', () => {
        if (validateCurrentStep()) {
            showStep(currentStepIndex + 1, 1);
        }
    });

    previousButton.addEventListener('click', () => {
        showStep(currentStepIndex - 1, -1);
    });

    contactForm.addEventListener('keydown', event => {
        if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' && !submitButton.hidden) {
            return;
        }

        if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
            event.preventDefault();

            if (validateCurrentStep()) {
                showStep(currentStepIndex + 1, 1);
            }
        }
    });

    contactForm.addEventListener('submit', event => {
        if (!validateCurrentStep()) {
            event.preventDefault();
        }
    });

    window.addEventListener('resize', () => {
        if (!isAnimating) {
            stepsContainer.style.height = `${activeSteps[currentStepIndex].offsetHeight}px`;
        }
    });

    refreshActiveSteps();
    allSteps.forEach((step, index) => {
        const isFirstStep = index === 0;
        step.hidden = !isFirstStep;
        step.disabled = !isFirstStep;
    });
    stepsContainer.style.height = `${activeSteps[0].offsetHeight}px`;
    updateNavigation();
}
