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
