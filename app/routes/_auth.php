<?php

app()->match('POST', '/auth/login', "AuthController@login");
app()->match('POST', '/auth/register', "AuthController@register");
