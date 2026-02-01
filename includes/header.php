<?php ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BETHEL LABS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
    <?php require_once __DIR__ . '/../config/Csrf.php'; Csrf::init(); ?>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm" role="navigation" aria-label="Main navigation">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <picture>
                        <!-- SVG preferred -->
                        <source srcset="../images/logo.svg" type="image/svg+xml">
                        <!-- High-DPI fallback -->
                        <img src="../images/logo.jpg" srcset="../images/logo@2x.jpg 2x, ../images/logo.jpg 1x" alt="BETHEL LABS logo" height="40" class="me-2 rounded-circle border border-white logo-img" loading="lazy">
                    </picture>
                    <span class="ms-1">BETHEL LABS</span>
                </a>

                <!-- Offcanvas toggle for mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainNavOffcanvas" aria-controls="mainNavOffcanvas" aria-label="Afficher le menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Desktop navigation -->
<div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav nav-links mx-auto">
                        <?php
                        // Obtenir le nom du fichier actuel
                        $currentPage = basename($_SERVER['PHP_SELF']);

                        // Définir les pages de navigation
                        $pages = [
                            ['name' => 'Accueil', 'href' => 'index.php'],
                            ['name' => 'À propos', 'href' => 'about.php'],
                            ['name' => 'Services', 'href' => 'services.php'],
                            // ['name' => 'Formations', 'href' => 'formations.php'],
                            // ['name' => 'Concours', 'href' => 'concours.php'],
                            ['name' => 'Portfolio', 'href' => 'portfolio.php'],
                            ['name' => 'Actualités', 'href' => 'news.php']
                        ];

                        // Afficher les liens de navigation (centrés)
                        foreach ($pages as $page) {
                            $isActive = ($currentPage === $page['href']) ? 'active' : '';
                            echo '<li class="nav-item"><a class="nav-link ' . $isActive . '" href="' . $page['href'] . '">' . htmlspecialchars($page['name']) . '</a></li>';
                        }
                        ?>
                    </ul>

                    <div class="nav-actions d-flex align-items-center ms-3">
                        <button class="btn btn-search-icon ms-2 d-none d-md-inline-flex" type="button" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Ouvrir la recherche">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>

                        <ul class="navbar-nav ms-3 align-items-center">
                            <?php if (!empty(
                                session_get_cookie_params() /* placeholder to keep alignment for phpstorm*/
                            )): ?>
                            <?php endif; ?>

                            <?php if (!empty($_SESSION['logged_in_user'])): ?>
                                <li class="nav-item dropdown ms-2">
                                    <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                        <li><a class="dropdown-item" href="profile.php">Mon profil</a></li>
                                        <li><a class="dropdown-item" href="logout.php">Se déconnecter</a></li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item ms-2"><a class="nav-link btn btn-link text-white" href="login.php">Se connecter</a></li>
                                <li class="nav-item ms-1"><a class="nav-link btn btn-outline-light btn-sm" href="inscription.php?type=client">S'inscrire</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Offcanvas (mobile) -->
                <div class="offcanvas offcanvas-start bg-primary text-white" tabindex="-1" id="mainNavOffcanvas" aria-labelledby="mainNavOffcanvasLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="mainNavOffcanvasLabel">
                            <picture>
                                <source srcset="images/logo.svg" type="image/svg+xml">
                                <img src="images/logo.jpg" srcset="images/logo@2x.jpg 2x, images/logo.jpg 1x" height="30" class="me-2 rounded-circle border logo-img" alt="BETHEL LABS logo">
                            </picture>
                            BETHEL LABS
                        </h5>
                        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav mb-3">
                            <?php
                            foreach ($pages as $page) {
                                $isActive = ($currentPage === $page['href']) ? 'active' : '';
                                echo '<li class="nav-item"><a class="nav-link py-2 ' . $isActive . '" href="' . $page['href'] . '">' . $page['name'] . '</a></li>';
                            }
                            ?>
                        </ul>

                        <form class="d-flex mb-3" action="search.php" method="post" role="search" aria-label="Rechercher">
                            <input class="form-control form-control-sm me-2" type="search" placeholder="Rechercher..." aria-label="Rechercher" name="q">
                            <?php echo Csrf::inputField(); ?>
                            <button class="btn btn-sm btn-light" type="submit"><i class="fa fa-search"></i></button>
                        </form>

                        <a href="contact.php" class="btn btn-warning btn-sm w-100 mb-3">Contact</a>

                        <div>
                            <?php if (!empty($_SESSION['logged_in_user'])): ?>
                                <a href="profile.php" class="d-block text-white mb-2">Mon profil</a>
                                <a href="logout.php" class="d-block text-white">Se déconnecter</a>
                            <?php else: ?>
                                <a href="login.php" class="d-block text-white mb-2">Se connecter</a>
                                <a href="inscription.php?type=client" class="d-block text-white">S'inscrire</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Search Modal (POST) -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-body p-3">
                <form action="search.php" method="post" class="d-flex" role="search" aria-label="Recherche">
                  <input class="form-control form-control-lg me-2" type="search" name="q" placeholder="Rechercher..." aria-label="Rechercher" required>
                  <?php echo Csrf::inputField(); ?>
                  <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-search"></i></button>
                </form>
              </div>
            </div>
          </div>
        </div>

    </header>