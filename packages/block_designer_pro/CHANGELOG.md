#v2.8.5

* Fixed issue with "Boolean" field type, when used in "Repeatable" field type and "Default value" field was set, the default value wouldn't be set;
* Developers: Rewritten old array syntax (array()) to new array syntax ([]);

#v2.8.4

* Fixed issue with "Smart Link" field type, where the "Use download link" option for "File" wouldn't work for non-repeatable items;

#v2.8.3

* Added Version 4.7 option for the "Font Awesome" field type;

#v2.8.2

* Added an asterisk (*) behind each required field for Field types (affects "Express Entry" and "Page Attribute");
* Added missing ID on "Repeatable for" label, which would prevent the select from selecting when clicking on the label;

#v2.8.1

* Fixed issue with "Smart Link" field type, where used with multiple "Repeatable" field types, only the first Repeatable would work;

#v2.8.0

* New field type: File Set;
* Removed unneeded "uninstall" function within controller.php;

#v2.7.2

* Fixed issue with "Smart Link" field type, where it couldn't save when a non-page link type had been selected in a repeatable field type;
* Made "Smart Link" field type code a bit smarter, where it doesn't include "use" statements for classes that will not be used because of excluded link options;

#v2.7.1

* Fixed issue with "WYSIWYG" field type, when used in "Repeatable" field type and removed a repeatable item (CKEditor instances should be destroyed when removing a "Repeatable" item);

#v2.7.0

* Added "Relative URL" option to the "Smart Link" field type;
* Fixed issue where "Smart Link" field type would still save the "Title" value although the link was set to "-- None --" and therefore should not keep this value in the database;

#v2.6.4

* Fixed issue with "Express Entry" field type - mostly when NOT used in "Repeatable" field type;

#v2.6.3

* Fixed issue with "WYSIWYG" field type, when used in "Repeatable" field type and removed a repeatable item (CKEditor instances should be destroyed when removing a "Repeatable" item);
* Developers: Repeatable field type now also calls the "getRepeatableDeleteItemJS" function from used field types, outputted before actually deleting a repeatable item;

#v2.6.2

* Fixed issue with usage of multiple "Smart Link" field type instances when used with "Repeatable", which caused multiple "Page selectors" to show;

#v2.6.1

* Fixed issue with usage of multiple "Smart Link" field type instances when used with "Repeatable", which caused duplicate code/function and errors in the dashboard/website;

#v2.6.0

* Developers: Repeatable field type now also calls the "getFormContentsPre" function from used field types, outputted before a repeatable item starts outputting the main repeatable container;
* CKEditor will now be used in concrete5 version 8.x for "WYSIWYG" fields, used in the "Repeatable" field (created Block Types work backwards compatible);

#v2.5.2

* Fixed issue with "Express Entry" field type, where assets were not being loaded (thanks "nishantl" for providing a bug fix);

#v2.5.1

* Fixed issue with "Smart Link" field type when used with "Repeatable", where it was trying to save values from excluded link options causing to show errors and not save the block contents;

#v2.5.0

* New field type: Smart Link;
* Small code improvements for the "Repeatable" field type (requires Block Designer 2.5.1 due to the made changes);

#v2.4.1

* Removed the .po/.pot translation files, can be send on request to translate this package into other languages (to minimize package size);
* Developers: changed all _("Access Denied.") into "Access Denied." at the top of PHP files;

#v2.4.0

* New field type: Express Entry (only available for concrete5 version 8.0.0a3 and up);
* Updated .pot file;
* Updated Dutch language;
* Developers: Removed a couple of field type titles (from within controller.php's "fieldTypes" function) to the field type themselves;

#v2.3.0

* New field type: Block ID;
* Updated .pot file;
* Updated Dutch language;
* Added missing space after "row" for the "Repeatable" field type;
* Added option to have new entries (for the "Repeatable" field type) be added at the top, instead of at the bottom (upon clicking 'Add Entry');
* Removed unused "html.sortable.min.js" file within the "Repeatable" field type;

#v2.2.1

* Minor HTML/CSS changes for the Block Designer backend, to work nicely with concrete5 - version 8 as well;

#v2.2.0

* Added ability to use a field as title for the "Repeatable" field type, using the "Field to use as title" field/dropdown. This will replace "xxx row #2" with the value of the chosen field (when not empty) for a repeatable item and will make it very easy to identify an item;
* Moved "row" string to a translatable string (used in the "Repeatable" field type), to support multilingual installs;
* Updated .pot file;
* Updated Dutch language;
* Updated "FontAwesome" field type to be able to use FontAwesome version 4.6;

#v2.1.1

* Fixed issue with "Font Awesome" field type, where it wasn't showing any options in the dropdown;
* Added ability to "Shuffle items" for the "Repeatable" field type;

#v2.1.0

* Updated "FontAwesome" field type to be able to use FontAwesome version 4.4 and 4.5;
* Fixed issue with "Country" field type within a "Repeatable" field type;
* Better indenting of code for extra functions, being used by "Repeatable" field type;

#v2.0.0

* Added Dutch translation;
* Added .pot file to be able to create other translations;
* Improved indenting on generated code;
* Making use of smarter functions, shipped with Block Designer version 2.0.0, to generate cleaner files/code;
* Updated handlebarsjs within Repeatable field type to version 4.0.4 (same as Block Designer itself is using);
* Fixed issue with "Code" field type within a "Repeatable" field type (handlebarsjs was always escaping the value, which converts everything to HTML entities);
* Fixed issue with "Country" field type within a "Repeatable" field type, where if "Required" was not checked, there was no option to leave the select "blank";
* Developers: User field type now uses the core alias of \Concrete\Core\User\UserList (simply: UserList);

#v1.2.0

* Included "use Database" statement instead of calling full namespace - version 1.4.0 of Block Designer required for the Repeatable field type;
* Added confirmation message upon clicking the delete/x-button for a repeatable item;
* Tabs within the add/edit form, are now using concrete5's translate (t()) function - was just a non-translated title/string before;
* Developers: Following "coding style guide" PSR-2 more than before;
* Developers: Replaced deprecated "get" and "GetAll" functions from the Database class (to "connection" and "fetchAll");

#v1.1.10

* Changed the "Page Attribute" field type to always use the "display" function of the attribute in question, instead of returning "raw" values like arrays and objects;

#v1.1.9

* Compatibility for the "Description" field for all available field types - feature introduced in Block Designer v1.3.12;

#v1.1.8

* Surrounded entered labels with the concrete5 translation function (t("Label name")), in order to have translated labels on multilingual sites - for validation functions;
* Developers: Moved the registering of the "Repeatable" assets to the add/edit functions, as these are the only ones using it;

#v1.1.7

* "Repeatable" field type will now also return "searchable content" (be sure to upgrade to version 1.3.9 - or higher - of Block Designer);
* Edited field types with searchable content, to support the repeatable option;

#v1.1.6

* Fixed when using the "Repeatable" field type in Block Designer Pro, duplicate IDs were generated (repeatable container) and caused to not run properly with multiple same block types in composer;

#v1.1.5

* Field type "FontAwesome" now also repeatable;
* Developers: Repeatable field type now also calls the "getDuplicateFunctionContents" & "getAutoJsContents" functions from used field types;
* Developers: Minor update to the repeatable field for the "edit" function;

#v1.1.4

* Fixed Ooyala field type to be repeatable, as it used identical id's on elements to populate with the Ooyala video (which did not work);
* Updated Ooyala field type's icon to match the Ooyala icon;
* Added (optional) ability to collapse/expand items for fields created with the "Repeatable" field type, in order to easier rearrange items;
* Minor CSS update to the repeatable items (frontend);
* Upon clicking "Add Entry" (for repeatable items - frontend), you will scroll automatically to the newly added entry and the first input/select/textarea will have focus to start entering data;
* Extra "Add Entry" button at the bottom (for repeatable items - frontend), this way there is no need to scroll to the top every time;
* Developers: added a "key" in the view file when looping repeatable items (field type "Repeatable"), which is unique and starts at 0;
* Developers: Updated Handlebars.js for the Repeatable field type to the latest available version - v4.0.2;

#v1.1.3

* New field type: ooyala;
* Fixed a jQuery bug where the "Repeatable for" field would be added multiple times for specific field types.

#v1.1.2

* New field type: page_attribute;
* Moved the javascript notice for both Facebook and Twitter Timeline inside the field options div;
* Fix: Use javascript to move the "Repeatable for" form group to the very bottom of a field - some field types have this above the normal existing options for some reason;
* Update for "Repeatable" field type to be compatible with Package Designer (https://www.concrete5.org/marketplace/addons/package-designer/);
* "Class(es)" option field for SoundCloud, Vevo & Vimeo now also allow underscores;

#v1.1.1

* New field type: SoundCloud (also repeatable);
* Field type "user" now also repeatable;
* Developers: replaced "htmlspecialchars" function with concrete5's "h" function to sanitize;

#v1.1.0

* Added (repeat) functionality for repeatable field type (boolean, country, facebook_page, quick_list, twitter_timeline, vevo & vimeo);

#v1.0.2

* Compatibility with Block Designer v1.2.3, because of changed namespaces to make Block Designer OSX compatible on Concrete 5 5.7.4.x and below;

#v1.0.1

* New field type: quick_list;

#v1.0.0

* New field type: font_awesome;
* Fixed issue with user field type, where if not required you always had to choose one of the available users;
* Changed fallback width for Vimeo field type from 800 (px) to 100% (only applicable if no width specified by the user);
* Developers: shortened Vevo & Vimeo field type's "getViewFunctionContents" function to have cleaner and faster code;
* Developers: complete field types rewrite from classes to Namespaces;

#v0.9.2

* Updated the "Twitter Timeline" field type, by adding an info block right under the created label telling you have to enter a "Twitter Widget ID";

#v0.9.1

* Although this package can run without "Block Designer", an exception will be thrown to install "Block Designer" first upon installing this package without having "Block Designer" installed;

#v0.9.0

* Initial Release