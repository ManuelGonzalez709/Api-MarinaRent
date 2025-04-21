<?php

return [

    /*
     * Las rutas a las que se les permitirán solicitudes CORS. El valor '*' permite todas las rutas.
     */
    'paths' => ['*'],

    /*
     * Los métodos HTTP que se permitirán. El valor '*' permite todos los métodos.
     */
    'allowed_methods' => ['*'],

    /*
     * Los orígenes que están permitidos para las solicitudes. El valor '*' permite todos los orígenes.
     */
    'allowed_origins' => ['*'],

    /*
     * Los patrones de orígenes permitidos. Esto puede ser útil si deseas permitir orígenes específicos mediante patrones de URL.
     */
    'allowed_origins_patterns' => [],

    /*
     * Los encabezados que pueden ser utilizados en la solicitud. El valor '*' permite todos los encabezados.
     */
    'allowed_headers' => ['*'],

    /*
     * Los encabezados que serán expuestos en la respuesta.
     */
    'exposed_headers' => [],

    /*
     * El tiempo máximo que el navegador puede almacenar la configuración CORS en caché. El valor '0' significa sin caché.
     */
    'max_age' => 0,

    /*
     * Si se permiten credenciales (como cookies o cabeceras de autenticación). Si tu API utiliza autenticación con cookies, deberías dejar esto en true.
     */
    'supports_credentials' => true,

];
