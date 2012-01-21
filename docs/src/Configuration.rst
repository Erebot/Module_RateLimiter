Configuration
=============

..  _`configuration options`:

Options
-------

This module provides several configuration options.

..  table:: Options for |project|

    +-----------+-----------+-----------+-----------------------------------+
    | Name      | Type      | Default   | Description                       |
    |           |           | value     |                                   |
    +===========+===========+===========+===================================+
    | limit     | integer   | n/a       | How many messages may be sent to  |
    |           |           |           | a connection during a period of   |
    |           |           |           | time before the bot starts        |
    |           |           |           | throttling the output rate.       |
    +-----------+-----------+-----------+-----------------------------------+
    | period    | integer   | n/a       | Period of time (in seconds) which |
    |           |           |           | is used to control the output     |
    |           |           |           | rate.                             |
    +-----------+-----------+-----------+-----------------------------------+


Example
-------

In this example, we prevent the bot from sending out more than 4 messages
every 2 seconds.

..  parsed-code:: xml

    <?xml version="1.0"?>
    <configuration
      xmlns="http://localhost/Erebot/"
      version="..."
      language="fr-FR"
      timezone="Europe/Paris"
      commands-prefix="!">

      <modules>
        <!-- Other modules ignored for clarity. -->

        <module name="|project|">
          <param name="limit"  value="4" />
          <param name="period" value="2" />
        </module>
      </modules>
    </configuration>


.. vim: ts=4 et
