<?php
/**
 * Student Management System
 * Flash Notification System
 * 
 * Purpose: Allows PHP controllers to queue one-time status alerts (success,
 * danger, warning, info) that are displayed on the next page view and then
 * automatically discarded from memory.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sets a flash message to be displayed on the next request.
 *
 * @param string $type Bootstrap alert type ('success', 'danger', 'warning', 'info')
 * @param string $message The alert text message
 * @return void
 */
function setFlash(string $type, string $message): void {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Checks if there are any pending flash messages.
 *
 * @return bool
 */
function hasFlash(): bool {
    return !empty($_SESSION['flash_messages']);
}

/**
 * Retrieves and clears all queued flash messages.
 *
 * @return array
 */
function getFlash(): array {
    if (!hasFlash()) {
        return [];
    }
    $messages = $_SESSION['flash_messages'];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Renders queued flash messages as dismissible Bootstrap 5 alerts.
 *
 * @return void
 */
function renderFlash(): void {
    if (!hasFlash()) {
        return;
    }

    $messages = getFlash();
    foreach ($messages as $flash) {
        $alertType = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8');
        $alertMsg  = htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');
        
        $icon = "bi-info-circle-fill";
        if ($alertType === 'success') {
            $icon = "bi-check-circle-fill";
        } elseif ($alertType === 'danger') {
            $icon = "bi-exclamation-triangle-fill";
        } elseif ($alertType === 'warning') {
            $icon = "bi-exclamation-circle-fill";
        }

        echo "
        <div class='alert alert-{$alertType} alert-dismissible fade show shadow-sm' role='alert'>
            <i class='bi {$icon} me-2'></i>
            <span>{$alertMsg}</span>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
}
