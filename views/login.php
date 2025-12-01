<?php

use App\Flash;

$title         = 'Sign in';
$flashMessages = Flash::consume();

ob_start();
?>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-3 text-center">Sign in to the app</h1>

                    <form method="post" action="index.php?page=login">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </form>

                    <p class="mt-3 mb-0 text-center">
                        Don't have an account?
                        <a href="index.php?page=register">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php
$content      = ob_get_clean();
$currentUser  = $currentUser ?? null;

include __DIR__ . '/layout.php';
