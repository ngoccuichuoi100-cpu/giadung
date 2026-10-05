import './bootstrap';

// --- 3D INTERACTIVE TILT MOTION FOR PRODUCT CARDS ---
document.addEventListener('DOMContentLoaded', () => {
    const init3DTilt = () => {
        const cards = document.querySelectorAll('.product-card-3d, [data-tilt-3d]');
        
        cards.forEach(card => {
            if (card.classList.contains('brand-coverflow-card') || card.closest('.brand-coverflow-container')) return;
            if (card.dataset.tiltInitialized) return;
            card.dataset.tiltInitialized = "true";

            card.style.transition = 'transform 0.15s ease-out, box-shadow 0.15s ease-out';
            card.style.transformStyle = 'preserve-3d';
            
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = ((y - centerY) / centerY) * -7;
                const rotateY = ((x - centerX) / centerX) * 7;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.02, 1.02, 1.02)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        });
    };

    init3DTilt();
});
