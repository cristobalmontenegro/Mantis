<?php

function isUserTOTPConfigured($userId) {
    $t_user_id = (int)$userId;
    $t_result = db_query(
        "SELECT user_id FROM " . plugin_table("totp") . " WHERE user_id = " . db_param(),
        array($t_user_id)
    );
    return db_num_rows($t_result) > 0;
}

function retrieveTOTPForUser($userId) {
    $t_user_id = (int)$userId;
    $t_result = db_query(
        "SELECT secret_key FROM " . plugin_table("totp") . " WHERE user_id = " . db_param() . " LIMIT 1",
        array($t_user_id)
    );

    if ($row = db_fetch_array($t_result)) {
        return $row["secret_key"];
    }
    return "";
}

# --- Backup Codes ---

function generateBackupCodes($userId) {
    $t_user_id = (int)$userId;
    $t_count = (int)plugin_config_get('backup_codes_count');
    $t_codes = array();

    // Clear old codes
    db_query(
        "DELETE FROM " . plugin_table("backup_codes") . " WHERE user_id = " . db_param(),
        array($t_user_id)
    );

    for ($i = 0; $i < $t_count; $i++) {
        $t_code = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        $t_code_formatted = substr($t_code, 0, 5) . '-' . substr($t_code, 5, 5);
        $t_hash = password_hash($t_code_formatted, PASSWORD_DEFAULT);

        db_query(
            "INSERT INTO " . plugin_table("backup_codes") . " (user_id, code_hash, used) VALUES (" . db_param() . ", " . db_param() . ", 0)",
            array($t_user_id, $t_hash)
        );

        $t_codes[] = $t_code_formatted;
    }

    return $t_codes;
}

function verifyBackupCode($userId, $code) {
    $t_user_id = (int)$userId;
    $t_code = trim(strtoupper($code));

    $t_result = db_query(
        "SELECT id, code_hash FROM " . plugin_table("backup_codes") . " WHERE user_id = " . db_param() . " AND used = 0",
        array($t_user_id)
    );

    while ($row = db_fetch_array($t_result)) {
        if (password_verify($t_code, $row["code_hash"])) {
            db_query(
                "UPDATE " . plugin_table("backup_codes") . " SET used = 1 WHERE id = " . db_param(),
                array($row["id"])
            );
            return true;
        }
    }
    return false;
}

function getBackupCodesRemaining($userId) {
    $t_user_id = (int)$userId;
    $t_result = db_query(
        "SELECT COUNT(*) as cnt FROM " . plugin_table("backup_codes") . " WHERE user_id = " . db_param() . " AND used = 0",
        array($t_user_id)
    );
    $row = db_fetch_array($t_result);
    return (int)$row["cnt"];
}

# --- Rate Limiting ---

function checkRateLimit($userId, $ipAddress) {
    $t_user_id = (int)$userId;
    $t_ip = (string)$ipAddress;
    $t_max = (int)plugin_config_get('max_failed_attempts');
    $t_duration = (int)plugin_config_get('lockout_duration');
    $t_now = time();

    // Clean old entries
    db_query(
        "DELETE FROM " . plugin_table("failed_attempts") . " WHERE attempted_at < " . db_param(),
        array($t_now - $t_duration)
    );

    $t_result = db_query(
        "SELECT COUNT(*) as cnt FROM " . plugin_table("failed_attempts") . " WHERE user_id = " . db_param() . " AND ip_address = " . db_param() . " AND attempted_at > " . db_param(),
        array($t_user_id, $t_ip, $t_now - $t_duration)
    );
    $row = db_fetch_array($t_result);
    $t_attempts = (int)$row["cnt"];

    if ($t_attempts >= $t_max) {
        return false;
    }
    return true;
}

function recordFailedAttempt($userId, $ipAddress) {
    $t_user_id = (int)$userId;
    $t_ip = (string)$ipAddress;
    $t_now = time();

    db_query(
        "INSERT INTO " . plugin_table("failed_attempts") . " (user_id, ip_address, attempted_at) VALUES (" . db_param() . ", " . db_param() . ", " . db_param() . ")",
        array($t_user_id, $t_ip, $t_now)
    );
}

function clearFailedAttempts($userId) {
    $t_user_id = (int)$userId;
    db_query(
        "DELETE FROM " . plugin_table("failed_attempts") . " WHERE user_id = " . db_param(),
        array($t_user_id)
    );
}

# --- Remember Device ---

function rememberDevice($userId, $days) {
    $t_user_id = (int)$userId;
    $t_days = (int)$days;
    $t_hash = bin2hex(random_bytes(32));
    $t_expires = time() + ($t_days * 86400);

    // Clean old devices
    db_query(
        "DELETE FROM " . plugin_table("remembered_devices") . " WHERE user_id = " . db_param() . " AND expires_at < " . db_param(),
        array($t_user_id, time())
    );

    db_query(
        "INSERT INTO " . plugin_table("remembered_devices") . " (user_id, device_hash, expires_at) VALUES (" . db_param() . ", " . db_param() . ", " . db_param() . ")",
        array($t_user_id, $t_hash, $t_expires)
    );

    return $t_hash;
}

function isDeviceRemembered($userId, $hash) {
    $t_user_id = (int)$userId;
    $t_hash = (string)$hash;
    $t_now = time();

    $t_result = db_query(
        "SELECT id FROM " . plugin_table("remembered_devices") . " WHERE user_id = " . db_param() . " AND device_hash = " . db_param() . " AND expires_at > " . db_param(),
        array($t_user_id, $t_hash, $t_now)
    );
    return db_num_rows($t_result) > 0;
}
