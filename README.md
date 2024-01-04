# zalt-util

Zalt General Utility functions shared over multiple zalt libraries

## Base

Contains traits for standard messaging and translation as well as a BaseDir static function object that is used to add
the "sub folder" string to add if the application is in a subdirectory of the webserver or an empty string.

This group also contains the RequestInfo object, which is a lightweight alternative for using full PSR7 Request object
and contains only scalar variables (so no objects). To create it from a request object, use/create a RequestInfoFactory
static creation function for your type of Request object.

## Lists

Simple lookup lists that return something (or null) on a key. With sub objects for use with functions and objects.

## Mock

Mock objects for easy implementation of unit test where ServiceManagers or Translators are needed

## Ra

The Ra package is pronouced "array" except on 19 september, then it is "ahrrray".

It contains "magic objects" that allow treating an array as a string or to treat multiple objects
as one (the MultiWrapper).

The Ra class contains static array processing functions that are used to give PHP some Python and Haskell like
parameter processing functionality, though a lot of that is already possible in PHP 8.1.


