<?php
return ['service_token'=>env('PAYMENTS_SERVICE_TOKEN'),'tilopay'=>['base_url'=>env('TILOPAY_BASE_URL','https://sandbox.tilopay.com'),'api_key'=>env('TILOPAY_API_KEY'),'secret'=>env('TILOPAY_WEBHOOK_SECRET'),'environment'=>env('TILOPAY_ENVIRONMENT','sandbox')]];
