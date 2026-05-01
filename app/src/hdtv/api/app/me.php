<?php
dispatch('/api/app/me', function () {
    $token = hdtv::checkToken();
    Imx\headers::json();
    $user = Imx\db::dataQuery("select 
    users.user_id,
    users.name,
    users.email,
    0 as superadmin,
    users.store_id,
    users.shift_id,
    stores.name as store,
    profiles.name as profile,
    stores.tax
    from users 
    left join stores on stores.store_id = users.store_id
    left join profiles on profiles.profile_id = users.profile_id
    
    where token ='$token'
    ");
    $user['superadmin'] = 0;
    if($user['profile'] == "Super Admin")
        $user['superadmin'] = 1;
    return json_encode($user, JSON_PRETTY_PRINT);
});
