Keys
====

Getting a single key
--------------------

You can view a single key by executing the following request:

.. code-block:: text

    [GET] /api/v1/key/{id}

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": "1",
        "title": "second key",
        "type": "PASSWORD",
        "folder": "2",
        "url": "https://pass.securium.ch",
        "password": "ZGVtbw==",
        "username": "ZGVtby5tYW5hZ2Vy",
        "email": "demo@pass.securium.ch",
        "description": ""
    }

Create a key
-------------

To create a new key, you can execute the following request:

.. code-block:: text

    [POST] /api/key

Parameters
~~~~~~~~~~

title
    title for key
folder
    folder id
username
    username, base64 encode string
password
    passwords, base64 encode string
email
    email
url
    url
description
    description for key


Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": "2",
        "title": "first key",
        "type": "PASSWORD",
        "folder": "1",
        "url": "https://pass.securium.ch",
        "password": "ZGVtbw==",
        "username": "ZGVtby51c2Vy",
        "email": "demo@pass.securium.ch",
        "description": ""
    }

Updating a key
------------------

You can update an existing key using PUT:

.. code-block:: text

    [PUT] /api/key/{id}

Parameters
~~~~~~~~~~

title
    title for key
folder
    folder id
username
    username, base64 encode string
password
    passwords, base64 encode string
email
    email
url
    url
description
    description for key


Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": "2",
        "title": "rename first key",
        "type": "PASSWORD",
        "folder": "1",
        "url": "https://pass.securium.ch",
        "password": "ZGVtbw==",
        "username": "ZGVtby51c2Vy",
        "email": "demo@pass.securium.ch",
        "description": ""
    }

Deleting a key
------------------

You can delete a key from the system by making the following DELETE call:

.. code-block:: text

    [DELETE] /api/key/{id}

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
