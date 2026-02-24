    </main>
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo APP_NAME; ?></h3>
                    <p>Votre boulangerie artisanale du village</p>
                </div>
                <div class="footer-section">
                    <h4>Horaires</h4>
                    <p>Lun-Ven: 7h00 - 20h00<br>
                    Samedi: 7h00 - 21h00<br>
                    Dimanche: 7h00 - 19h00</p>
                </div>
                <div class="footer-section">
                    <h4>Informations</h4>
                    <ul>
                        <li><a href="<?php echo PUBLIC_URL; ?>/informations.php">Modalités de livraison</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/informations.php">Adresse</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/informations.php">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    <script src="<?php echo ASSETS_WEB_PATH; ?>/js/main.js"></script>
</body>
</html>
