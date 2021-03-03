<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{
    /**
     * ****************************** LOCAL ***********************************
     * Database name
     * @var string
     */
    //local   ----  online

    const ENVI = 'online';
    const LIENS = 'http://localhost/timeflow/elearning-pronote/files/';
    const LIENS_ONLINE = 'https://ent.atlantique-ibs.com/files/';

    //GESTION INFORMATION DE BASE DONNEE AUTO
    //NB EN LOCAL: Creer  virtualhost == localhost.eschool
    const PROTOC_LIGNE = 'https';
    const PROTOC_LOCAL = 'http';

    /** 
     * ****************************** LOCAL ***********************************
     * Database host 
     * @var string
     */
    //your-database-host
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    //your-database-name
    const DB_NAME = 'tf_ugesnew';

    /**
     * Database user
     * @var string
     */
    //your-database-user
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    //your-database-password
    const DB_PASSWORD = '';

    /******************************** ONLINE ***********************************
     * Database host
     * @var string
     */
    //your-database-host
    const DB_HOST_ONLINE = 'localhost';

    /**
     * Database name
     * @var string
     */
    //your-database-name
    const DB_NAME_ONLINE = 'etspkgjv_eschool_aibs';
    
     
    /**          
     * Database user
     * @var string
     */
    //your-database-user
    //const DB_USER_ONLINE = 'etspkgjv_aibs_admin';
    const DB_USER_ONLINE = 'etspkgjv_ngee_admin';

    /**
     * Database password
     * @var string
     */
    //your-database-password
    //const DB_PASSWORD_ONLINE = 'bs9lDj~Oh75Z';
    const DB_PASSWORD_ONLINE = '8IyMwl((5594';
    
    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
}
