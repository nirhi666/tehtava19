<?php
// auth.php – sessio & käyttöoikeusportit
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Tarkista onko käyttäjä kirjautunut
 *
 * @return bool true jos kirjautunut, false jos ei
 */
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

/**
 * Palauta kirjautunut käyttäjä
 *
 * @return array{id:int, username:string, role:string}|null
 */
function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Vaadi kirjautuminen
 * Jos ei kirjautunut → ohjaa login.php
 *
 * @return void
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Vaadi admin-rooli
 * Jos ei admin → 403 Forbidden
 *
 * @return void
 */
function require_admin(): void {
    require_login();
    if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
        http_response_code(403);
        echo "<h1>403 Forbidden</h1><p>Vain admin.</p>";
        exit;
    }
}
