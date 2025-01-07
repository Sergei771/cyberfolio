<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détecteur de Type de Hash</title>
</head>
<body>
    <h1>Détecteur de Type de Hash</h1>
    <form method="POST" action="">
        <label for="hash_string">Entrez un hash :</label><br>
        <input type="text" id="hash_string" name="hash_string" required><br><br>
        <button type="submit">Détecter</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $hash_string = escapeshellarg($_POST["hash_string"]);

        // Exécution du script Python
        $command = "python3 hash_detector.py $hash_string";
        $output = shell_exec($command);

        if ($output === null) {
            echo "<p>Erreur lors de l'exécution du script Python.</p>";
        } else {
            echo "<h2>Type détecté : " . htmlspecialchars($output) . "</h2>";
        }
    }
    ?>
</body>
</html>
