<?php

use App\Flash;

/** @var array<int, array<string, mixed>> $users */
/** @var array<string, mixed> $currentUser */

$title         = 'User management';
$flashMessages = Flash::consume();

ob_start();
?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">User list</h1>
                <div class="input-group" style="max-width: 260px;">
                    <span class="input-group-text">Filter</span>
                    <input type="text"
                           class="form-control"
                           id="filterInput"
                           placeholder="Type to filter by name or e-mail">
                </div>
            </div>

            <form method="post" action="index.php?page=bulk_action" id="usersForm">
                <!-- Тулбар над таблицей -->
                <div class="btn-toolbar mb-3" role="toolbar">
                    <div class="btn-group me-2" role="group">
                        <button type="submit"
                                name="action"
                                value="block"
                                class="btn btn-outline-danger btn-sm"
                                title="Block selected users">
                            Block
                        </button>
                        <button type="submit"
                                name="action"
                                value="unblock"
                                class="btn btn-outline-success btn-sm"
                                title="Unblock selected users">
                            Unblock
                        </button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="submit"
                                name="action"
                                value="delete"
                                class="btn btn-outline-secondary btn-sm"
                                title="Delete selected users">
                            Delete
                        </button>
                        <button type="submit"
                                name="action"
                                value="delete_unverified"
                                class="btn btn-outline-secondary btn-sm"
                                title="Delete only unverified users">
                            Delete unverified
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="usersTable">
                        <thead>
                        <tr>
                            <th scope="col">
                                <!-- Чекбокс без текста в заголовке — select all -->
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th scope="col">Name</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Status</th>
                            <th scope="col">Last login</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           name="selected[]"
                                           value="<?= (int) $user['id'] ?>"
                                           class="row-checkbox">
                                </td>
                                <td><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></td>
                                <td>
                                    <?php
                                    $status     = $user['status'];
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'active') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status === 'blocked') {
                                        $badgeClass = 'bg-danger';
                                    } elseif ($status === 'unverified') {
                                        $badgeClass = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($status, ENT_QUOTES) ?>
                                </span>
                                </td>
                                <td>
                                    <?php if (!empty($user['last_login_at'])): ?>
                                        <?= htmlspecialchars($user['last_login_at'], ENT_QUOTES) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var filterInput = document.getElementById('filterInput');
            var table       = document.getElementById('usersTable');
            var rows        = table.querySelectorAll('tbody tr');

            filterInput.addEventListener('input', function () {
                var filterValue = this.value.toLowerCase();

                rows.forEach(function (row) {
                    var nameCell  = row.children[1].textContent.toLowerCase();
                    var emailCell = row.children[2].textContent.toLowerCase();

                    if (nameCell.indexOf(filterValue) !== -1 || emailCell.indexOf(filterValue) !== -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            var selectAllCheckbox = document.getElementById('selectAll');
            var rowCheckboxes     = document.querySelectorAll('.row-checkbox');

            selectAllCheckbox.addEventListener('change', function () {
                rowCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        })();
    </script>
<?php
$content      = ob_get_clean();
$currentUser  = $currentUser ?? null;

include __DIR__ . '/layout.php';
