Other
=====

Generate a password
-------------------

.. code-block:: text

    [GET|POST] /api/v1/password/generate

Parameters
~~~~~~~~~~

upper_case
    Include at least one capital letter in the password, (0|1)
numbers
    Include at least one number in the password, (0|1)
secure
    Generate completely random, hard-to-memorize passwords, (0|1)
symbols
    Include at least one special character in the password, (0|1)
length
    Length of the generated password, by default: 8

Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "password": "aiteizah"
    }

Folder complexity
-------------------

.. code-block:: text

    [GET] /api/v1/password/complication

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "0": "Very weak",
        "25": "Weak",
        "50": "Medium",
        "60": "Strong",
        "70": "Very strong",
        "80": "Heavy",
        "90": "Very heavy"
    }