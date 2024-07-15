<?php

app()->match('GET', '/vaults', "VaultController@index");
app()->match('GET', '/vaults/{id}', "VaultController@show");
app()->match('POST', '/vaults', "VaultController@store");
app()->match('PUT', '/vaults/{id}', "VaultController@update");
app()->match('DELETE', '/vaults/{id}', "VaultController@destroy");
