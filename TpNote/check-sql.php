<?php
require_once 'src/Models/Database.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h2>Requête SQL directe</h2>";

$query = "SELECT m.*, u.nom as auteur_nom 
          FROM messages m 
          LEFT JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id 
          ORDER BY m.date_publication DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p><strong>Nombre de résultats:</strong> " . count($messages) . "</p>";

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Titre</th><th>Contenu</th><th>Auteur</th><th>Date</th></tr>";
foreach ($messages as $msg) {
    echo "<tr>";
    echo "<td>" . $msg['message_id'] . "</td>";
    echo "<td>" . htmlspecialchars($msg['titre']) . "</td>";
    echo "<td>" . htmlspecialchars(substr($msg['contenu'], 0, 50)) . "...</td>";
    echo "<td>" . htmlspecialchars($msg['auteur_nom']) . "</td>";
    echo "<td>" . $msg['date_publication'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><a href='?action=messages'>← Retour</a>";
?>
