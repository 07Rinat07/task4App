<?php

use App\Flash;

$title         = 'Register';
$flashMessages = Flash::consume();

ob_start();
?>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-3 text-center">Create an account</h1>

                    <form method="post" action="index.php?page=register">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <div class="form-text">
                                По условию задачи допускается любой непустой пароль (даже один символ).
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>

                    <p class="mt-3 mb-0 text-center">
                        Already have an account?
                        <a href="index.php?page=login">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php
$content      = ob_get_clean();
$currentUser  = $currentUser ?? null;

include __DIR__ . '/layout.php';
