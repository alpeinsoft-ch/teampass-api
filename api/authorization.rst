.. index::
   single: Authorization

Authorization
=============

The API supports Basic Authentication as defined in RFC2617 with a few slight differences.

For example, if you’re accessing the API via cURL, the following command would authenticate you if you replace <username> with your username. (cURL will prompt you to enter the password.)

.. code-block:: bash

    $ curl -u <username> https://domain.ltd/api/v1/nodes