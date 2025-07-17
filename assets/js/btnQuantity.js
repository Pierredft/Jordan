
    document.addEventListener('DOMContentLoaded', function() {
        const decrementBtn = document.getElementById('decrement');
        const incrementBtn = document.getElementById('increment');
        const quantityInput = document.getElementById('quantity');

        decrementBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value, 10);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });

        incrementBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value, 10);
            quantityInput.value = value + 1;
        });

        // Animation d'entrée pour les éléments
        const elements = document.querySelectorAll('.product-image-section, .product-details-section');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            setTimeout(() => {
                el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 200);
        });
    });
