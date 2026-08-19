<?php

function isUserTOTPConfigured($userId) {
    $t_user_id = (int)$userId;
    $data = db_query("SELECT user_id FROM " . plugin_table("totp") . " WHERE user_id = $t_user_id");
    return db_num_rows($data) > 0;
}

function retrieveTOTPForUser($userId) {
    $t_user_id = (int)$userId;
    $query = "SELECT secret_key FROM " . plugin_table("totp") . " WHERE user_id = $t_user_id LIMIT 1";
    $result = db_query($query);

    if ($row = db_fetch_array($result)) {
        return $row["secret_key"];
    }
    return "";
}
