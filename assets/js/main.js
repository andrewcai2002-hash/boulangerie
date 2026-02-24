/**
 * Boulangerie - JavaScript principal
 */

// Confirmation avant suppression
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation pour les clics avec onclick="return confirm(...)"
    // déjà géré en HTML
    
    // Confirmation pour les changements de statut
    const statusSelects = document.querySelectorAll('select[name="nouveau_statut"]');
    statusSelects.forEach(select => {
        select.addEventListener('change', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir changer le statut de cette commande ?')) {
                e.preventDefault();
                this.value = this.dataset.original || this.options[0].value;
            }
        });
    });

    // Mise à jour dynamique du total du panier
    const quantityInputs = document.querySelectorAll('.quantity-small');
    quantityInputs.forEach(input => {
        const originalValue = input.value;
        input.addEventListener('change', function() {
            updateCartTotal();
        });
        
        // Garder la valeur originale pour les annulations
        input.dataset.original = originalValue;
    });

    // Activer la touche Entrée sur les champs de quantité
    quantityInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    });

    // Validation du formulaire d'inscription
    const inscriptionForm = document.querySelector('form');
    if (inscriptionForm && inscriptionForm.action.includes('inscription.php')) {
        inscriptionForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');
            
            if (password && passwordConfirm && password.value !== passwordConfirm.value) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }
        });
    }

    // Format du prix en temps réel
    const priceInputs = document.querySelectorAll('input[name="prix"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
});

/**
 * Met à jour le total du panier
 */
function updateCartTotal() {
    const table = document.querySelector('.cart-table');
    if (!table) return;

    let total = 0;
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const priceCell = row.querySelector('td:nth-child(2)');
        const quantityInput = row.querySelector('input[name*="quantite"]');

        if (priceCell && quantityInput) {
            // Extraire le prix (format: "X,XX €")
            const priceText = priceCell.textContent.trim();
            const price = parseFloat(priceText.replace(',', '.').replace('€', '').trim());
            const quantity = parseInt(quantityInput.value) || 0;

            total += price * quantity;
        }
    });

    // Mettre à jour l'affichage du total
    const totalElement = document.querySelector('.cart-total');
    if (totalElement) {
        const totalFormatted = total.toFixed(2).replace('.', ',') + ' €';
        totalElement.innerHTML = '<strong>Total: ' + totalFormatted + '</strong>';
    }
}

/**
 * Formate un nombre en prix
 */
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

/**
 * Affiche/masque un élément
 */
function toggle(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
}

/**
 * Valide une adresse email
 */
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Soumet un formulaire après confirmation
 */
function submitFormWithConfirmation(formId, message) {
    if (confirm(message)) {
        document.getElementById(formId).submit();
        return true;
    }
    return false;
}

/**
 * Gère la prévisualisation d'images
 */
function previewImage(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (input && preview) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Initialisation pour la prévisualisation d'image produit
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.querySelector('input[name="image"]');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Vérifier le type de fichier
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Type de fichier non autorisé. Acceptés: JPG, PNG, GIF, WebP');
                    this.value = '';
                    return;
                }

                // Vérifier la taille
                if (file.size > 5000000) {
                    alert('Le fichier est trop volumineux (max 5MB).');
                    this.value = '';
                    return;
                }
            }
        });
    }
});
