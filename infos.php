<?php
require_once 'includes/config.php';
$pageTitle = 'Informations pratiques';
?>
<?php include 'includes/header.php'; ?>

<h1 class="page-title">Informations pratiques</h1>

<div class="info-grid">
    <div class="info-box">
        <div class="icon">⏰</div>
        <h3>Horaires d'ouverture</h3>
        <ul>
            <li><strong>Lundi :</strong> Fermé</li>
            <li><strong>Mardi – Vendredi :</strong> 7h00 – 19h30</li>
            <li><strong>Samedi :</strong> 7h00 – 19h30</li>
            <li><strong>Dimanche :</strong> 7h00 – 13h00</li>
        </ul>
        <p style="margin-top:12px;font-size:.9rem;color:#888">Fermeture exceptionnelle les jours fériés.</p>
    </div>

    <div class="info-box">
        <div class="icon">📍</div>
        <h3>Nous trouver</h3>
        <p><strong>Adresse :</strong><br>12 rue de la Paix<br>75001 Paris</p>
        <p style="margin-top:10px"><strong>Téléphone :</strong><br>01 23 45 67 89</p>
        <p style="margin-top:10px"><strong>Email :</strong><br>contact@boulangerie-du-village.fr</p>
    </div>

    <div class="info-box">
        <div class="icon">🚚</div>
        <h3>Modalités de livraison</h3>
        <ul>
            <li>Livraison dans un rayon de <strong>10 km</strong></li>
            <li>Du mardi au dimanche</li>
            <li>Créneaux : 8h–12h et 14h–18h</li>
            <li>Commande minimum : <strong>10 €</strong></li>
            <li>Frais de livraison : <strong>2 €</strong></li>
            <li>Paiement <strong>à la livraison</strong> uniquement</li>
        </ul>
    </div>

    <div class="info-box">
        <div class="icon">ℹ️</div>
        <h3>Comment commander ?</h3>
        <ol style="padding-left:20px">
            <li>Créez un compte ou connectez-vous</li>
            <li>Parcourez notre catalogue</li>
            <li>Ajoutez vos produits au panier</li>
            <li>Validez votre commande avec votre adresse</li>
            <li>Payez à la livraison</li>
        </ol>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
