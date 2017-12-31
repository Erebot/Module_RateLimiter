Welcome to the documentation for Erebot_Module_RateLimiter!
===========================================================

Erebot_Module_RateLimiter is a module for `Erebot`_ that can limit the bot's
output rate so as to prevent it from flooding IRC servers.

The algorithm used is really basic and won't protect it against carefully
planned attacks (:abbr:`DoS (Denial of Service)`), but it is still better
than having nothing at all.


Contents:

..  toctree::
    :maxdepth: 2

    Prerequisites
    Configuration
    Usage


Current status on http://travis-ci.org/: |travis|

..  |travis| image:: https://secure.travis-ci.org/Erebot/Erebot_Module_RateLimiter.png
    :alt: UNKNOWN
    :target: https://travis-ci.org/Erebot/Erebot_Module_RateLimiter/

..  _`Erebot`:
    https://www.erebot.net/

.. vim: ts=4 et

