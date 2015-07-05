Folders
=======

Getting all folders
-------------------

In order to get all folder with subfolders and keys available in Teampass for current user you can call the following GET request:

.. code-block:: text

    [GET] /api/v1/nodes

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    [
        {
            "id": "1",
            "title": "first folder",
            "type": "FOLDER",
            "access": "W",
            "complication": "0",
            "descendants": [
                {
                    "id": "3",
                    "title": "subfolder",
                    "type": "FOLDER",
                    "access": "R",
                    "complication": "0",
                    "descendants": []
                },
                {
                    "id": "2",
                    "title": "first key",
                    "type": "PASSWORD",
                    "email": "demo@pass.securium.ch",
                    "url": "https://pass.securium.ch",
                    "password": "ZGVtbw==",
                    "username": "ZGVtby51c2Vy",
                    "description": ""
                }
            ]
        },
        {
            "id": "2",
            "title": "second folder",
            "type": "FOLDER",
            "access": "W",
            "complication": "0",
            "descendants": [
                {
                    "id": "1",
                    "title": "second key",
                    "type": "PASSWORD",
                    "email": "demo@pass.securium.ch",
                    "username": "ZGVtby5tYW5hZ2Vy",
                    "password": "ZGVtbw==",
                    "url": "https://pass.securium.ch",
                    "description": "",
                }
            ]
        }
    ]

Getting a single folder
-----------------------

You can view a single folder with subfolders and keys by executing the following request:

.. code-block:: text

    [GET] /api/v1/node/{id}

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": "2",
        "title": "second folder",
        "type": "FOLDER",
        "access": "W",
        "complication": "0",
        "descendants": [
            {
                "id": "1",
                "title": "second key",
                "type": "PASSWORD",
                "url": "https://pass.securium.ch",
                "username": "ZGVtby5tYW5hZ2Vy",
                "password": "ZGVtbw==",
                "email": "demo@pass.securium.ch",
                "description": ""
            }
        ]
    }

Create a folder
----------------

To create a new folder, you can execute the following request:

.. code-block:: text

    [POST] /api/v1/node

Parameters
~~~~~~~~~~

title
    title folder
complication
    complexity folder, [0, 25, 50, 60, 70, 80, 90]
folder:
    id parent folder

Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": "2",
        "title": "second folder",
        "type": "FOLDER",
        "access": "W",
        "complication": "0",
        "descendants": []
    }

Updating a folder
-----------------

You can update an existing folder using PUT method:

.. code-block:: text

    [PUT] /api/v1/node/{id}

Parameters
~~~~~~~~~~

title
    title folder
complication
    complexity folder, [0, 25, 50, 60, 70, 80, 90]
folder:
    id parent folder

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": "2",
        "title": "second folder rename",
        "type": "FOLDER",
        "access": "W",
        "complication": "0",
        "descendants": [
            {
                "id": "1",
                "title": "second key",
                "type": "PASSWORD",
                "url": "https://pass.securium.ch",
                "username": "ZGVtby5tYW5hZ2Vy",
                "password": "ZGVtbw==",
                "email": "demo@pass.securium.ch",
                "description": ""
            }
        ]
    }

Deleting a folder
------------------

You can delete a folder from the system by making the following DELETE call:

.. code-block:: text

    [DELETE] /api/v1/node/{id}

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
