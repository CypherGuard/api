<?php

app()->group('/pgp', function () {
    app()->match('GET', '/key', "PgpController@get_public_key");
});
