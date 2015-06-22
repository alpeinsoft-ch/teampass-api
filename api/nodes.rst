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
            "id": "87",
            "title": "тест",
            "type": "FOLDER",
            "access": "W",
            "complication": "90",
            "descendants": [
                {
                    "description": "",
                    "email": null,
                    "id": "48",
                    "password": "ceenaipa",
                    "title": "хей хо",
                    "type": "PASSWORD",
                    "url": null,
                    "username": "тест"
                }
            ]
        },
        ...
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
        "id": "85",
        "title": "newtestfder",
        "type": "FOLDER",
        "access": "W",
        "complication": "80",
        "descendants": [
            {
                "id": "88",
                "title": "CreateTestFolder2",
                "type": "FOLDER",
                "access": "W",
                "complication": "0",
                "descendants": []
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
        "id": "88",
        "title": "CreateTestFolder",
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
        "id": "85",
        "title": "newtestfder",
        "type": "FOLDER",
        "access": "W",
        "complication": "80",
        "descendants": [
            {
                "id": "88",
                "title": "CreateTestFolder2",
                "type": "FOLDER",
                "access": "W",
                "complication": "0",
                "descendants": []
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
