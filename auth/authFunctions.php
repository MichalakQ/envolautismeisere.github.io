<?php

// Démarre la session si non déjà active
function initialiserSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Vérifie si un utilisateur est connecté
function isLoggedIn() {
    initialiserSession();
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur possède un rôle suffisant
 * Les rôles vont de 1 (admin) à 10 (aucun droit)
 * Retourne true si l'utilisateur a un niveau de droit inférieur ou égal au niveau requis
 */
function hasRole($required_role) {
    initialiserSession();

    if (!isLoggedIn()) return false;

    $role_levels = [
        'admin'      => 1,
        'directeur'  => 2,
        'chef'       => 3,
        'manager'    => 4,
        'encadrant'  => 5,
        'client'     => 6
    ];

    $required_level = $role_levels[$required_role] ?? 10;
    $user_role = $_SESSION['role'] ?? '';
    $user_level = $role_levels[$user_role] ?? 10;

    error_log("Check role : user=$user_level required=$required_level");

    return $user_level <= $required_level;
}

/**
 * Authentifie un utilisateur à partir de son login et mot de passe
 */
function authenticateUser($username, $password, $conn) {
    try {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM employes WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (hash('sha256', $password) === $user['password']) {
                initialiserSession();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                error_log("Authentification réussie pour l'utilisateur $username");
                return true;
            } else {
                error_log("Mot de passe incorrect pour $username");
                return false;
            }
        } else {
            error_log("Utilisateur $username introuvable.");
            return false;
        }

    } catch (PDOException $e) {
        error_log("Erreur d'authentification: " . $e->getMessage());
        return false;
    }
}

/**
 * Déconnecte un utilisateur
 */
function logoutUser() {
    initialiserSession();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"] ?? false, $params["httponly"] ?? false
        );
    }
    session_destroy();
}

/**
 * Redirige si l'utilisateur n'a pas les droits nécessaires
 */
function requireRole($role = null) {
    initialiserSession();

    if (!isLoggedIn()) {
        header("Location: /transgourmet/auth/login.php");
        exit;
    }

    if ($role !== null && !hasRole($role)) {
        $encodedMessage = urlencode("ERREUR : Accès refusé.");
        header("Location: /transgourmet/index.php?message=$encodedMessage");
        exit;
    }
}
?>
