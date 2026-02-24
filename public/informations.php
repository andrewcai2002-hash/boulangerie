<?php
$page_title = 'Informations pratiques';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Informations pratiques</h1>

    <div class="info-layout">
        <!-- Adresse -->
        <section class="info-section">
            <h2>Notre adresse</h2>
            <div class="info-content">
                <p><?php echo APP_NAME; ?><br>
                123, rue de la Boulangerie<br>
                75000 Paris<br>
                France</p>
                <p><strong>Téléphone:</strong> 01 23 45 67 89<br>
                <strong>Email:</strong> contact@boulangerie.local</p>
            </div>
        </section>

        <!-- Horaires -->
        <section class="info-section">
            <h2>Horaires d'ouverture</h2>
            <div class="info-content">
                <table class="hours-table">
                    <tr>
                        <td><strong>Lundi</strong></td>
                        <td>7h00 - 20h00</td>
                    </tr>
                    <tr>
                        <td><strong>Mardi</strong></td>
                        <td>7h00 - 20h00</td>
                    </tr>
                    <tr>
                        <td><strong>Mercredi</strong></td>
                        <td>7h00 - 20h00</td>
                    </tr>
                    <tr>
                        <td><strong>Jeudi</strong></td>
                        <td>7h00 - 20h00</td>
                    </tr>
                    <tr>
                        <td><strong>Vendredi</strong></td>
                        <td>7h00 - 20h00</td>
                    </tr>
                    <tr>
                        <td><strong>Samedi</strong></td>
                        <td>7h00 - 21h00</td>
                    </tr>
                    <tr>
                        <td><strong>Dimanche</strong></td>
                        <td>7h00 - 19h00</td>
                    </tr>
                </table>
            </div>
        </section>

        <!-- Livraison -->
        <section class="info-section">
            <h2>Modalités de livraison</h2>
            <div class="info-content">
                <h3>Zones desservies</h3>
                <p>Nous livrons dans toute l'Île-de-France et les zones limitrophes. 
                Veuillez vérifier que votre adresse est dans notre zone de livraison.</p>

                <h3>Délais de livraison</h3>
                <ul>
                    <li><strong>Commandes avant 10h:</strong> Livraison le jour même (16h-19h)</li>
                    <li><strong>Commandes entre 10h-16h:</strong> Livraison le jour même (19h-20h)</li>
                    <li><strong>Commandes après 16h:</strong> Livraison le lendemain (9h-12h)</li>
                </ul>

                <h3>Frais de livraison</h3>
                <p>Frais de livraison gratuits pour toute commande supérieure à 50€.<br>
                Pour les commandes inférieures à 50€: 5€ de frais de livraison.</p>

                <h3>Paiement</h3>
                <p>Le paiement se fait à la livraison, en espèces ou par carte bancaire.</p>
            </div>
        </section>

        <!-- Contact -->
        <section class="info-section">
            <h2>Nous contacter</h2>
            <div class="info-content">
                <p><strong>Par téléphone:</strong> 01 23 45 67 89<br>
                <strong>Par email:</strong> contact@boulangerie.local<br>
                <strong>Disponibilité:</strong> Lundi au dimanche, pendant les heures d'ouverture</p>

                <p>Vous avez une question? N'hésitez pas à nous contacter. 
                Nous vous répondrons dans les plus brefs délais.</p>
            </div>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
