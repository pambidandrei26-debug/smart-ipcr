<?php
/**
 * Secure Error Handler (SEC-005)
 * 
 * Prevents technical details from leaking to users
 * Logs errors securely for debugging
 */

// Don't expose errors in production
$isProduction = (getenv('APP_ENV') === 'production') || !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', 'localhost']);

if ($isProduction) {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
} else {
    // Development mode - show errors
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Custom error handler
set_error_handler(function($severity, $message, $file, $line) use ($isProduction) {
    // Log all errors
    error_log(sprintf(
        "[%s] %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $message,
        $file,
        $line
    ));
    
    // In production, show generic message
    if ($isProduction && !(error_reporting() & $severity)) {
        return false;
    }
    
    // Don't show technical details to users
    if ($isProduction) {
        // Silent for non-fatal errors in production
        return true;
    }
    
    return false; // Use default handler in development
});

// Custom exception handler
set_exception_handler(function($exception) use ($isProduction) {
    // Log exception details
    error_log(sprintf(
        "[%s] Uncaught Exception: %s in %s:%d\nStack trace:\n%s",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));
    
    // Show generic error to user
    http_response_code(500);
    
    if ($isProduction) {
        // Generic production error page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>System Error</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
                .error-container { max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #dc3545; margin-bottom: 20px; }
                p { color: #666; line-height: 1.6; }
                .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
                .btn:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>⚠️ Something Went Wrong</h1>
                <p>We're sorry, but something unexpected happened. Our team has been notified and is working to fix the issue.</p>
                <p>Please try again later or contact the administrator if the problem persists.</p>
                <a href="<?= BASE_URL ?? '/' ?>/dashboard.php" class="btn">Go to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        // Development mode - show detailed error
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Exception</title>
            <style>
                body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
                .exception { background: #252526; padding: 20px; border-radius: 4px; border-left: 4px solid #f44336; }
                h1 { color: #f44336; margin: 0 0 10px 0; }
                .file { color: #4ec9b0; }
                .line { color: #b5cea8; }
                pre { background: #1e1e1e; padding: 15px; border-radius: 4px; overflow-x: auto; }
            </style>
        </head>
        <body>
            <div class="exception">
                <h1>Uncaught Exception</h1>
                <p><strong><?= htmlspecialchars($exception->getMessage()) ?></strong></p>
                <p>
                    <span class="file"><?= htmlspecialchars($exception->getFile()) ?></span>
                    on line <span class="line"><?= $exception->getLine() ?></span>
                </p>
                <h3>Stack Trace:</h3>
                <pre><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
            </div>
        </body>
        </html>
        <?php
    }
    
    exit;
});

// Shutdown handler for fatal errors
register_shutdown_function(function() use ($isProduction) {
    $error = error_get_last();
    
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Log fatal error
        error_log(sprintf(
            "[%s] Fatal Error: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        ));
        
        if ($isProduction) {
            // Clear output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            http_response_code(500);
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>System Error</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
                    .error-container { max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    h1 { color: #dc3545; margin-bottom: 20px; }
                    p { color: #666; line-height: 1.6; }
                </style>
            </head>
            <body>
                <div class="error-container">
                    <h1>⚠️ System Error</h1>
                    <p>A critical error occurred. The technical team has been notified.</p>
                    <p>Please contact the administrator if you continue to see this message.</p>
                </div>
            </body>
            </html>
            <?php
        }
    }
});
