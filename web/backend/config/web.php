<?php
return ['login_max_attempts'=>(int)env('WEB_LOGIN_MAX_ATTEMPTS',5),'login_lock_minutes'=>(int)env('WEB_LOGIN_LOCK_MINUTES',15),'admin_token_ttl'=>(int)env('WEB_ADMIN_TOKEN_TTL',900)];
