.. index::
   single: Configuration

Configuration
=============

Config file
-----------

Configuration API stored in a file config.yml

.. code-block:: yaml

    parameters:
        database_driver:        pdo_mysql
        database_host:          127.0.0.1
        database_dbname:        teampass_db
        database_user:          root
        database_password:      ~
        database_prefix:        teampass
        database_charset:       utf8

        locale:                 en
        encoder:                ~
        secret:                 ~

        teampass_salt:          ~

        endpoint:               api
        version:                v1

Parameters
~~~~~~~~~~

database_driver
    database_host, only pdo_mysql
database_host
    database host
database_dbname
    database name
database_user
    database user
database_password
    database password
database_prefix
    prefix for databases, by default = teampass, please check prefix in teampass settings
database_charset
    charset for database, by default = utf8
locale
    locale api, by default = en
encoder
    if you use **PassSecurium Lite**, set lite, default
    if you use **PassSecurium Standalone**, set standalone
secret
    The secret value is a random string of characters, numbers and symbols. use with standalone encoder
teampass_salt
    salt used teampass, please check salt in teampass, salt is defined in file sk.php
endpoint
    endpoint for API, by default = api
version
    api version, by default = v1

Example config for PassSecurium Lite
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    parameters:
        database_driver:        pdo_mysql
        database_host:          127.0.0.1
        database_dbname:        teampass_db
        database_user:          root
        database_password:      root
        database_prefix:        teampass
        database_charset:       utf8
        locale:                 en
        encoder:                lite
        teampass_salt:          RnR5ZHeERVyZx3bShrmp4NyzXw3s
        endpoint:               api
        version:                v1

Example config for PassSecurium Standalone
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    parameters:
        database_driver:        pdo_mysql
        database_host:          127.0.0.1
        database_dbname:        teampass_db
        database_user:          root
        database_password:      root
        database_prefix:        teampass
        database_charset:       utf8
        locale:                 en
        encoder:                standalone
        secret:                 ThisTokenIsNotSoSecretChangeIt
        teampass_salt:          RnR5ZHeERVyZx3bShrmp4NyzXw3s
        endpoint:               api
        version:                v1

Configuring a Web Server
------------------------

The preferred way for the API running in the production environment, you'll need to use a fully-featured web server. This document describes several ways to use API with Apache or Nginx.

Apache
~~~~~~

The **minimum configuration** to get your application running under Apache is:

.. code-block:: apache

    <VirtualHost *:80>
        ServerName domain.tld

        DocumentRoot /var/www/project/web
        <Directory /var/www/project/web>
            AllowOverride All
            Order Allow,Deny
            Allow from All
        </Directory>

        ErrorLog /var/log/apache2/project_error.log
        CustomLog /var/log/apache2/project_access.log combined
    </VirtualHost>

.. warning::

    If your system supports the ``APACHE_LOG_DIR`` variable, you may want
    to use ``${APACHE_LOG_DIR}/`` instead of hardcoding ``/var/log/apache2/``.

.. warning::

    In Apache 2.4, ``Order Allow,Deny`` has been replaced by ``Require all granted``.
    Hence, you need to modify your ``Directory`` permission settings as follows:

    .. code-block:: apache

        <Directory /var/www/project/web>
            Require all granted
            # ...
        </Directory>