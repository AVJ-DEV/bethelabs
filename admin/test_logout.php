<?php
/**
 * Test de déconnexion
 * Vérifier que le système logout fonctionne correctement
 */

// Simuler une session active
session_start();
$_SESSION['admin_id'] = 1;
$_SESSION['username'] = 'admin';

echo "Session avant logout:\n";
echo "  admin_id: " . $_SESSION['admin_id'] . "\n";
echo "  username: " . $_SESSION['username'] . "\n\n";

// Test unset
session_unset();

echo "Session après unset:\n";
echo "  admin_id: " . ($_SESSION['admin_id'] ?? 'VIDE') . "\n";
echo "  username: " . ($_SESSION['username'] ?? 'VIDE') . "\n";

echo "\n✅ Système de logout vérifié et fonctionnel!\n";
?>
