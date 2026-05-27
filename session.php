<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

// Handle actions
if (is_post()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'set') {
        $_SESSION['game'] = 'Cricket';
        $_SESSION['color'] = 'Black';
        set_flash('Session variables successfully set!');
    } elseif ($action === 'destroy') {
        unset($_SESSION['game']);
        unset($_SESSION['color']);
        set_flash('Session variables successfully cleared!', 'error');
    }
    redirect_to('session.php');
}

render_header('Session Demo');
?>
<!-- Header & Breadcrumbs -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Session Demonstration</span>
    </div>
</div>

<div class="cards" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Set Session Card -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border-color); padding: 24px;">
        <div>
            <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 12px; font-weight: 600;">
                Step 1: Set Session Variables
            </h3>
            <p style="font-size: 1rem; color: var(--text-muted); margin-bottom: 20px;">
                Store data in the server-side session memory. This data will persist across different pages until the session is destroyed or expires.
            </p>
            <div class="card" style="padding: 12px; margin-bottom: 20px; border: 1px solid var(--border-color); background: #f8fafc; border-radius: var(--radius-sm); box-shadow: none;">
                <code style="display: block; font-size: 0.85rem; color: var(--primary);">$_SESSION['game'] = 'Cricket';</code>
                <code style="display: block; font-size: 0.85rem; color: var(--primary); margin-top: 4px;">$_SESSION['color'] = 'Black';</code>
            </div>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="set">
            <button type="submit" class="btn-primary" style="margin-top: 0; width: 100%;">Set Variables</button>
        </form>
    </div>

    <!-- Read Session Card -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border-color); padding: 24px;">
        <div>
            <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 12px; font-weight: 600;">
                Step 2: Read Session Variables
            </h3>
            <p style="font-size: 1rem; color: var(--text-muted); margin-bottom: 20px;">
                Retrieve and display the active session data from memory.
            </p>
            <div class="card" style="padding: 16px; margin-bottom: 20px; border: 1px solid var(--border-color); background: #f8fafc; border-radius: var(--radius-md); box-shadow: none;">
                <p style="margin: 4px 0; font-size: 0.95rem;">
                    <strong>Game:</strong> 
                    <span style="color: var(--primary); font-weight: 600;">
                        <?= isset($_SESSION['game']) ? h($_SESSION['game']) : '<span style="color: var(--danger);">Not Set</span>' ?>
                    </span>
                </p>
                <p style="margin: 4px 0; font-size: 0.95rem; margin-top: 8px;">
                    <strong>Color:</strong> 
                    <span style="color: var(--primary); font-weight: 600;">
                        <?= isset($_SESSION['color']) ? h($_SESSION['color']) : '<span style="color: var(--danger);">Not Set</span>' ?>
                    </span>
                </p>
            </div>
        </div>
        <a href="session.php" class="btn-success" style="display: block; text-decoration: none; text-align: center; line-height: 24px; margin-top: 0; width: 100%;">Refresh Values</a>
    </div>

    <!-- Destroy Session Card -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border-color); padding: 24px;">
        <div>
            <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 12px; font-weight: 600;">
                Step 3: Destroy Session Variables
            </h3>
            <p style="font-size: 1rem; color: var(--text-muted); margin-bottom: 20px;">
                Remove session data from server-side memory. This simulates logging out or clearing user state.
            </p>
            <div class="card" style="padding: 12px; margin-bottom: 20px; border: 1px solid var(--border-color); background: #f8fafc; border-radius: var(--radius-sm); box-shadow: none;">
                <code style="display: block; font-size: 0.85rem; color: var(--danger);">unset($_SESSION['game']);</code>
                <code style="display: block; font-size: 0.85rem; color: var(--danger); margin-top: 4px;">unset($_SESSION['color']);</code>
            </div>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="destroy">
            <button type="submit" class="btn-primary" style="margin-top: 0; background: var(--danger); width: 100%;">Clear Session</button>
        </form>
    </div>
</div>

<div class="card" style="margin-top: 30px; padding: 24px; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
    <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 12px; font-weight: 600;">
        Active Session Metadata
    </h3>
    <table class="data-table" style="margin-top: 12px; width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); overflow: hidden;">
        <thead>
            <tr style="background: #f8fafc;">
                <th style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); font-size: 0.8rem; text-transform: uppercase; font-weight: 600; color: #475569; text-align: left;">Session Attribute</th>
                <th style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); font-size: 0.8rem; text-transform: uppercase; font-weight: 600; color: #475569; text-align: left;">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); text-align: left;"><strong>Session ID (PHPSESSID)</strong></td>
                <td style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); text-align: left;"><code style="font-size: 0.9rem; color: var(--text-muted);"><?= session_id() ?></code></td>
            </tr>
            <tr>
                <td style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); text-align: left;"><strong>Current User Email</strong></td>
                <td style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); text-align: left;"><code style="font-size: 0.9rem; color: var(--primary);"><?= h($_SESSION['user_email'] ?? 'Not Logged In') ?></code></td>
            </tr>
            <tr>
                <td style="padding: 14px 18px; text-align: left;"><strong>Current User Name</strong></td>
                <td style="padding: 14px 18px; text-align: left;"><?= h($_SESSION['user_name'] ?? 'Guest') ?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php render_footer(); ?>
