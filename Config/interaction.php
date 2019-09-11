<?php

return [

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Interaction model shipped with this package.
     */
    'table_name' => 'interaction_log',

    /*
     * This is the database connection that will be used by the migration and
     * the Interaction model shipped with this package.
     */
    'database_connection' => env('INTERACTION_LOG_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),

];