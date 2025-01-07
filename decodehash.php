<html>

!-- Section Détecteur de Type de Hash --
<section id="hash-detector" class="hash-detector" aria-labelledby="hash-detector-title">
    <div class="container">
        <h2 id="hash-detector-title" class="section-title slide-in">
            Détecteur de Type de Hash
            <span class="section-title__decoration"></span>
        </h2>

        <div class="hash-detector__content">
            <p>Entrez un hash pour détecter son type.</p>

            <!-- Formulaire de saisie du hash -->
            <form method="POST" action="">
                <label for="hash_string">Entrez un hash :</label><br>
                <input type="text" id="hash_string" name="hash_string" required><br><br>
                <button type="submit" class="btn btn-primary">Détecter</button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                // Récupération du hash soumis via le formulaire
                $hash_string = escapeshellarg($_POST["hash_string"]);

                // Exécution du script Python pour détecter le type de hash
                $command = "python3 " . escapeshellarg(__DIR__ . "\app.py") . " $hash_string";

                $output = shell_exec($command);

                // Affichage du résultat
                if ($output === null) {
                    echo "<p>Erreur lors de l'exécution du script Python.</p>";
                } else {
                    echo "<h3>Type détecté : " . htmlspecialchars($output) . "</h3>";
                }
            }
            ?>

        </div>
    </div>
</section>

</html>
