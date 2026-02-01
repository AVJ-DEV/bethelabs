<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/ErrorHandler.php';
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section class="py-5">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4">Contactez-nous</h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Merci ! Votre message a été envoyé avec succès.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Erreur : <?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <form action="process_contact.php" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="name" name="name" required minlength="3" placeholder="Votre nom">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="votre@email.com">
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required minlength="10" placeholder="Votre message..."></textarea>
                        </div>

                        <?php echo Csrf::inputField(); ?>

                        <button type="submit" class="btn btn-primary">Envoyer le message</button>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-3">
                        <h5 class="mb-3">Infos</h5>
                        <p class="mb-1"><strong>Adresse :</strong> Cotonou, Bénin</p>
                        <p class="mb-1"><strong>Téléphone :</strong> +229 00 00 00 00</p>
                        <p class="mb-0"><strong>Email :</strong> contact@bethelabs.bj</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>