#v2.9.1

* Extra check on "Block Config" page, in case Packages get deleted by hand with Block Types still assigned to them (by ID);
* Labels can be clicked within composer now too (for="" was having the wrong ID attached);
* Added more unique class name for "Date Time" field type field, in case of multiple blocks within a composer form;
* Developers: Rewrite from 'YOUR_BLOCK_HANDLE' to $this->btHandle within 'controller.php' file (where possible) for asset registering/requiring;

#v2.9.0

* Added "Default Value" field for "Select" field type, which will pre-populate the field with the given value (by array key);
* Added "Placeholder" field for "Email" field type, which will add a placeholder to the field within the form (i.e. to give a description/an example what to input in the field);

#v2.8.5

* Developers: Rewritten old array syntax (array()) to new array syntax ([]);
* Developers: Sorted the "use" statements at the top of the controller.php file;

#v2.8.4

* Code cleaning: The "Cache block output lifetime" ($btCacheBlockOutputLifetime) was always set in the controller, also if the field was left empty (defaulted to 0);
* Code cleaning: The $helpers array was always set in the controller, although it's value could be the same as the defaults (so not needed to include in the file);
* Code cleaning: The $btExportFileColumns & $btExportPageColumns arrays were always set in the controller, although if it's empty it is not needed to include in the file;
* Code cleaning: The "getBlockTypeDescription" function was always set in the controller, also if the "Description" field was left empty (so not needed to include this function in the file);

#v2.8.3

* Fixed issue with "Content" field type, when used in "Repeatable" field type - for concrete5 version 5.7.x (using Redactor editor) - which wouldn't load the user-selected plugins (only the default ones);

#v2.8.2

* Fixed issue with "Date Time" field type, when used in a composer form it wouldn't work (for non-repeatable items) - because of other ID's being assigned to the field by the concrete5 core;

#v2.8.1

* Changed "CCM_DISPATCHER_FILENAME" JavaScript variable to "CCM_APPLICATION_URL", to have a call to the full URL (in case of https);

#v2.8.0

* Added a "Settings" page (Dashboard - Stacks & Blocks - Block Designer - Settings) to configure some settings for Block Designer;
* Added a label behind each field with the contents of the "Label" field, which will be displayed when collapsing fields. This makes it easier to see which field you're looking at (instead of only just the field type + the number);
* Added an asterisk (*) behind each required field for both Field types and fields available under the tabs on the left (Basics, Interface, Advanced, Assets);
* Added a placeholder for the "Handle" and "Name" fields in the "Basics" tab to make it easier to spot what to enter;
* Added a red circle with an "R" character in it before the "Repeatable for" label, to make it more obvious this is for the "Repeatable" field type;
* "Repeatable for" field will only display when there is one or more "Repeatable" fields added (since you can only use it in this situation);
* Fixed "Database decimals" field for the "Number" field type, where it would always require a value (instead of defaulting to 2);
* Updated Dutch language;
* Removed package's config.json file for sorting/ordering field types, since this has now been moved to the Dashboard GUI (this has nothing to do with config.json files created with Block Types);

#v2.7.1

* Added an extra set of blacklisted block handles ('double');
* Developers: Field types can set and get variables using "setFieldTypeVariable" and "getFieldTypeVariable" of the BlockDesignerProcessor class;

#v2.7.0

* Added "Thumbnail handle" field for the "Image" field type, making it possible to output images with a thumbnail handle entered under "Thumbnails" (/index.php/dashboard/system/files/thumbnails) using its given width/height;

#v2.6.3

* Fixed issue with "Date Time" field type, when used in "Repeatable" field type and "Hide the seconds picker" + "Hide the minutes picker" where both checked;

#v2.6.2

* Fixed issue with "WYSIWYG" field type, when used in "Repeatable" field type and removed a repeatable item (CKEditor instances should be destroyed when removing a "Repeatable" item);

#v2.6.1

* Fixed issue with "WYSIWYG" field type, when used in "Repeatable" field type (missing backslash, "\$1" instead of "\\$1" in generated auto.js file);

#v2.6.0

* Updated "WYSIWYG" field type, to support CKEditor in concrete5 version 8.x for the "Repeatable" field type;

#v2.5.3

* Minor HTML/CSS changes to add some spacing above "Add a field" and remove spacing below "Developed by";

#v2.5.2

* Added an extra set of blacklisted block handles ('condition');
* Fixed missing "Remove" (delete) icon in version 8.0.0 and above (to remove added fields) in the "Block Designer" dashboard page;

#v2.5.1

* Minor code updates for the generated form.php file;
* Developers: Added "use AssetList" statement/alias instead of full namespace;
* Developers: Added "getBtExportPageColumn" function to field types, which gives the ability to add an array of slugs to add to the $btExportPageColumns variable of the block controller (to be used with fields that refer to a page object) - added in "Link" field type;
* Developers: Edited the "getBtExportFileColumn" function of field types, which gives the ability to add an array of slugs to add to the $btExportFileColumns variable of the block controller (to be used with fields that refer to a file object) - added in "File" and "Image" field types;
* Developers: Shortened code used in "File" field type;
* Developers: Replaced "ucFirst" with "ucfirst" in the "Image" field type, as this function should not be called this way;

#v2.5.0

* Added Japanese language - thanks Katz Ueno (katzueno);

#v2.4.3

* Fix: Usage of 2 or more "Link" field type instances in a repeatable block would result in multiple page selectors for each field (thanks Guido);

#v2.4.2

* Fixed a possible issue with the "File" field type, where if a file got deleted that is already selected within a block, could cause an error (for retrieving the "relativePath" of the non-object);
* Removed the .po/.pot translation files, can be send on request to translate this package into other languages (to minimize package size);
* Developers: changed all _("Access Denied.") into "Access Denied." at the top of PHP files;

#v2.4.1

* Updated "Number" field type to better handle fallback values and empty values;

#v2.4.0

* Removed deprecated "isError" function on file objects (for the "Image" field type);
* Selected pages in the "Link" field type, will only have their anchor/link appear when there is no error and not in the trash (previous checks could pass when no page was found or if it was in the trash); 
* Placeholder field option added for "Number" field type;
* Fallback value field option added for "Number" field type (when a field is being left blank, this value will be used);

#v2.3.0

* Added ability to not "Convert special characters to HTML entities" using concrete5's h() function for the "Text Area" and "Text Box" field types;
* Added "Assets" tab in the backend "Block Designer" page and moved fields "View CSS" and "View JavaScript" to this tab;
* Updated .pot file;
* Updated Dutch language;
* Developers: Removed controller.php's protected "fieldTypes" function, which was only in place to generate the to be translated strings. Added "getFieldName" function to the needed field types instead (Code, Color Picker, Date Time, Number & Text Box);

#v2.2.3

* Fixed issue with "WYSIWYG" field type when used with multiple "Repeatable" field types;

#v2.2.2

* Minor HTML/CSS changes for the "Repeatable" field type, to work nicely with concrete5 - version 8 as well;

#v2.2.1

* Added an extra set of blacklisted block handles ('column');
* Minor HTML/CSS changes for the Block Designer backend, to work nicely with concrete5 - version 8 as well;
* Updated output for the "Link" field type, when chosen to make use of the "Title" field;
* Improved indenting on generated code for "Link" & "URL" field types;

#v2.2.0

* Added support for the "Field to use as title" field, used within the "Repeatable" field type (only available with Block Designer Pro);

#v2.1.2

* Added an extra set of blacklisted block handles ('bigint', 'int', 'smallint', 'tinyint', 'bit', 'decimal', 'numeric', 'float', 'real', 'smalldatetime', 'char', 'varchar', 'nchar', 'nvarchar', 'ntext', 'binary', 'varbinary');

#v2.1.1

* Fixed issue when only using "Static HTML" field types or starting with one within a Block Type;

#v2.1.0

* Rewritten deprecated code "View::url" into "URL::to";
* Rewritten full class names/namespaces to aliases (i.e. \File to File);
* Removed style/CSS code within form.php to "auto.css", to separate CSS from the HTML;
* Developers: Updated Handlebars.js to the latest available version - 4.0.5;

#v2.0.0

* Added Dutch translation;
* Added .pot file to be able to create other translations;
* Improved indenting on generated code;
* Making Block Designer smarter, by only adding composer code when absolutely necessary - essentially stripping code where not needed;
* Following "coding style guide" PSR-2 more than before;
* Changed a couple translatable strings to be using single quotes instead of double quotes;
* Making generated files cleaner, especially when it comes down to registering and requiring assets;
* Added ability to use concrete5's translate (t('Translate')) function on select choices for the "Select" field type - check the available checkbox to use this ability;
* Fixed issue with "Select" field type within a "Repeatable" field type (only when used to build the PHP switch);
* Developers: Added ability to not escape text boxes/text areas, within a "Repeatable" item;
* Developers: Added smart function "getAssets" to field types for requiring and registering assets in the controller's functions (see DateTime field type for a comprehensive example);
* Developers: Removed unnecessary (empty) template files which where used to build the block type (along with some extra code);
* Developers: Removed unnecessary use statements within single pages "Block Config" & "Block Order";

#v1.4.0

* Code cleanup & following "coding style guide" PSR-2 more than before;
* Developers: Removed unnecessary use statements '\File', 'Page', 'Loader', 'URL', '\Concrete\Core\Editor\Snippet' & 'Sunra\PhpSimple\HtmlDomParser' within "WYSIWYG" field type;
* Developers: Included "use Database" statement instead of calling full namespace;
* Developers: Replaced deprecated "get", "GetAll", "execute" & "getRow" functions from the Database class (to "connection", "fetchAll", "executeQuery" & "fetchAssoc");

#v1.3.13

* Fixed issue with the "Image" field type when used in composer;

#v1.3.12

* Added an extra blacklisted block handle ('fulltext');
* Added "Description" field (below "Slug"), which enables you to add a description to an input. A question mark will get appended to the label and the entered description will show on hover;

#v1.3.11

* Fixed issue with "Image" field type, when used with a repeatable item (only happened in combination with "Responsive image");

#v1.3.10

* Added an extra set of blacklisted block handles ('btignorepagethemegridframeworkcontainer', 'pkg', 'asc' & 'desc', 'option');
* Fixes issue when a file got deleted in the file manager, which was selected in some field within a block, it prevented from opening the edit dialog (because of thrown errors);
* Surrounded entered labels with the concrete5 translation function (t("Label name")), in order to have translated labels on multilingual sites - for validation functions;
* The label "-- None --" in a non-required select field, will now be using the translation function to render "None" in the current interface language;
* Fixed issue with the "File" field type, when used multiple times with a "Repeatable" field;

#v1.3.9

* Modified searchable content for the "Repeatable" field type for Block Designer Pro (repeatable field types weren't returning repeatable content at all);
* Added searchable content for the "Number" & "YouTube" field type;

#v1.3.8

* Added ability to enter a "Default block type set". This will default install the block type into the given set, also after uninstalling the block type at some point;
* Minor CSS update for backend interface (file inputs);

#v1.3.7

* Fixed when using the "Repeatable" field type in Block Designer Pro, duplicate IDs were generated (tabs) and caused to not run properly with multiple same block types in composer;
* Fixed issue with the "WYSIWYG" field type when adding this to a non-repeatable field type AND a repeatable field type together;
* Developers: Added a 'ft_count_repeatable' key to the "data" array, to distinguish if an item has been used for repeatable items or non-repeatable items;

#v1.3.6

* Added option "Responsive image" for the "Image" field type, which uses the concrete5 image helper the same as the core "image" block does;
* Added a "Ignore Page Theme Grid Framework Container" field under the "Advanced" tab. This will allow you to ignore the "container" for a Block Type, declared in the grid framework;

#v1.3.5

* Fixed a bug when "CSS & JavaScript Cache" was enabled - the dashboard pages of Block Designer wouldn't work, because of minifying HandlebarsJS code;
* Developers: Updated Handlebars.js to the latest available version - v4.0.4;

#v1.3.4

* Fixed a bug in the "Color Picker" field type, where the input/text field was not editable;
* Moved requireAsset function call for "core/file-manager" to edit/add functions for "Image" and "File" field type, this will prevent loading unneeded .js/.css files;

#v1.3.3

* Fixed a bug where "WYSIWYG" field types in a repeatable group would cause problems;

#v1.3.2

* Fixed a bug where multiple "Image" field types in a repeatable group would cause problems;

#v1.3.1

* Fixed a bug where multiple "Date Time" field types in a repeatable group would cause problems;

#v1.3.0

* Auto loading classes in the "src" directory for the "upgrade" function too (to fix issues with Mac OSX upon upgrading);
* Fixed issue where WYSIWYG content was not being correctly translated back into HTML when in edit mode;

#v1.2.13

* Fixed a bug where a WYSIWYG field type would get rendered multiple times if added more than once in a repeatable group;
* Build in a fix for the "Image" field type, where it checks if a file exists (only needed for repeatable function) - this due to a core c5 bug;

#v1.2.12

* Fixed issue where repeatable items for the "Link" field type would always try and save the title field, with the "Hide title field" option checked;
* Fixed issue where repeatable items for the "Link" field type would always require the link, with the "Required?" option non-checked;
* Fixed issue where repeatable items for the "Link" field type would make weird PHP in the view.php file, causing the block not to render at all;
* Build in a fix for the "File" field type, where it checks if a file exists (only needed for repeatable function) - this due to a core c5 bug;
* Config.json generated files now include packages versions of Block Designer & Block Designer Pro (for analyzing purposes);

#v1.2.11

* Added a border around the "Stacks" field type's multiple select (upon adding/editing in frontend mode);
* Wrapper HTML open/close are not getting trimmed anymore, in case extra (white) spaces are required by the user (only if there really is content in it, only white space does not count);
* Added ability to only output the source (path) of the image (image field type), by checking a checkbox - may come in handy for inline background styling;
* Updated "Parsedown" library in markdown field type from version 1.5.3 to 1.6.0;

#v1.2.10

* Fixed a bug for the WYSIWYG field type when saving in composer (data was not saved in the database);
* Reduced the margin between fields (backend interface to create a block type);
* Added ability to collapse/expand created fields, to make it easier to rearrange fields (backend interface to create a block type);
* Added ability to collapse all/expand all created fields with one click (backend interface to create a block type);
* Added ability to scroll to top with one click, instead of using your scroll wheel or the scroll bar (backend interface to create a block type);
* Developers: Updated Handlebars.js to the latest available version - v4.0.2;

#v1.2.9

* Added ability to have one or multiple classes attached to the "Link" its anchor, as available for the URL field type too;
* Better check if a page is set and found for the "Link" field type (in view.php);
* Minor CSS update for backend interface;

#v1.2.8

* Build in a check if a class of a field type really exists, before trying to call this class. This in case of (hidden) directories like .svn within the src/FieldType directory;

#v1.2.7

* Added "protected $pkg = false;" to the block's controller.php file, to easily package a block;
* Update for "Date Time" & "Stacks" field type to be compatible with Package Designer (https://www.concrete5.org/marketplace/addons/package-designer/);

#v1.2.6

* Fixed a bug where if only a static HTML field type was entered, an error was thrown;
* Minor CSS update for backend interface;
* "Class(es)" option field for Email, File, Image, URL & YouTube now also allow underscores;
* Developers: Added the option to have an "on_start" function for a field type, which passes the (entered) field data as an array (without slug);
* Developers: Base fields for a field type are now wrapped in a div with class "base-fields";

#v1.2.5

* Fixed the "Link" field for the image field type when chosen to be repeatable;
* File field type is now repeatable (if Block Designer Pro 1.1.0 or higher is installed);
* Color picker field type is now repeatable (if Block Designer Pro 1.1.0 or higher is installed);

#v1.2.4

* Added ability to open link in a new window (target="_blank") for image field type's "Link" field (page/url);
* Updated core code parts to be used with field types;
* Added ability to hide title field for the "URL" field type - this means no title is shown (empty anchor tag) and no title field is available;
* Fixed issue where the auto.js file was not being loaded when in composer form;
* Developers: Added function "getFieldsRequired" for field types, which can return values to be inserted into the $btFieldsRequired array;

#v1.2.3

* Fixed issues with OSX for Concrete5 versions 5.7.4.x and lower, all-round fix - do install the newest Pro version too if purchased, because that one will not work when it's not up to date;

#v1.2.2

* Fixed wrapper HTML open/close not being rendered for the "Link" field type;
* Changed information text "semicolons" to "colons" for field type "Select" (Set your own array key for values, by using 2 semicolons (" :: ") on each line - extra spaces required);
* Added ability to echo the "key" or "value" of a selected "Select" field type's option, instead of building a PHP switch as the only possibility;
* Updated WYSIWYG field type to use the Core editor instead of pasting in the Javascript, this will make your editor have all the plugins as with the normal "Content" block type;

#v1.2.1

* Fixed 5.7.5 (RC1) issue "Class 'Concrete\Package\BlockDesigner\Src\BlockDesignerProcessor' not found";

#v1.2.0

* Static HTML field description updated, to let users know they can paste in other scripting languages (CSS/JS/PHP) too;
* Upon clicking "Make the block!", the ajax post will not send the block handle in the URL instead of in the data object - this due to the fact when SEO "trailing_slash" was set on true, it was not being posted (possible C5 bug);
* Fixed issue where wrapper HTML open/close wouldn't show for the file & stacks field types;
* Added ability to hide title field for the "Link" field type - this means the page title/name is always being shown and no alternate title field is available;
* Added ability "New line to blank rule"/nl2br for the textarea field type;
* Updated "Parsedown" library in markdown field type from version 1.5.1 to 1.5.3;
* Updated stacks field type to only write select2 (sortable) CSS once - if multiple stack field types are being used in a block;
* Field types now use the field function "$view->field('fieldName')" in their form.php file, instead of a normal string i.e. 'fieldName';
* Developers: Complete field types rewrite from classes to Namespaces;
* Developers: Renamed field type function "viewContents" to "getViewContents";
* Developers: Renamed field type function "viewFunctionContents" to "getViewFunctionContents";
* Developers: Renamed field type function "formContents" to "getFormContents";
* Developers: Renamed field type function "extraFunctionsContents" to "getExtraFunctionsContents";
* Developers: Renamed field type function "on_startFunctionContents" to "getOnStartFunctionContents";
* Developers: Renamed field type function "validateFunctionContents" to "getValidateFunctionContents";
* Developers: Renamed field type function "editFunctionContents" to "getEditFunctionContents";
* Developers: Renamed field type function "deleteFunctionContents" to "getDeleteFunctionContents";
* Developers: Renamed field type function "duplicateFunctionContents" to "getDuplicateFunctionContents";
* Developers: Renamed field type function "addFunctionContents" to "getAddFunctionContents";
* Developers: Renamed field type function "saveFunctionContents" to "getSaveFunctionContents";
* Developers: Renamed field type function "autoJsContents" to "getAutoJsContents";
* Developers: Renamed field type function "dbFields" to "getDbFields";
* Developers: Renamed field type function "dbTables" to "getDbTables";
* Developers: Added function "getBtExportTables" for field types, which can return an array of tables (to be used with the Concrete5 export);
* Developers: Added function "getExtraOptions" for field types, which can return extra HTML (usage of Handlebars.js is available) for each and every added field;
* Developers: Updated "getDbTables" (previously named "dbTables" function) functionality in field types;
* Developers: Triggering event "remove" upon removing/deleting a field (.content-field);
* Developers: Triggering event "create" upon creating/adding a field (.content-field);
* Developers: Triggering event "complete" upon filling all fields (.content-fields);
* Developers: Field type "link" rewrite within "getViewContents" function -> Loader::helper("navigation")->getLinkToCollection($linkToC) to $linkToC->getCollectionLink();
* Developers: Field type "link" rewrite within "getFormContents" function -> Loader::helper("form/page_selector") to Core::make("helper/form/page_selector");
* Developers: Moved "$this->requireAsset()" for field type "stacks" to the add/edit functions instead of on_start;
* Developers: Moved field type field_options.php's files to "views" folder (i.e. view/FieldType/ColorPickerFieldType/field_options.php);
* Developers: Field types' variables are now protected instead of public;
* Developers: Field types can now require a specific Concrete5 version because of dependencies (usage: protected $appVersionRequired = '5.7.3.1'). If the version does not match, the field type won't be available until the required Concrete5 version is installed;

#v1.1.1

* Added a set of blacklisted block handles (mostly words which have special meaning in PHP, see: http://php.net/manual/en/reserved.keywords.php);
* Removed 4 pixels border (bottom) on the responsive tabs - this was causing inconsistent padding on the tabs;
* Fixed if entered JavaScript in the "View JavaScript" field, that Block Designer will write to view.js instead of auto.js;
* Developers: Field types can now return JavaScript being written to the "auto.js" file;
* Developers: Field types can now require a specific Block Designer version because of dependencies (usage: public $pkgVersionRequired = '1.1.1'). If the version does not match, the field type won't be available until the required version is installed;

#v1.1.0

* Extra blacklisted slug "description" - using this slug for a field would cause SQL issues;
* Added ability to enter rows of colors (unlimited) for the color picker's palette - this slides down when checking the checkbox "Show a palette";
* Fixed select field type's keys being rendered as plain text if keys were set using "concrete5 :: Concrete5 CMS 5.7";
* Fixed select field type's key being rendered as original key number minus 1 (-1), when a select field was not set on "required";
* Fixed Block Configs not showing up on the "dashboard/blocks/block_designer/block_config" page - only happened with identical block type names;
* Developers: Added ability to auto load field_css.css within the field types' "elements" directory (if exists);
* Developers: Removed color picker's JavaScript indenting;
* Developers: Updated Handlebars.js to the latest available version - v3.0.3;

#v1.0.11

* Added a "View CSS" field under the "Advanced" tab. This will allow you to input some CSS to be copied into a view.css file;
* Added a "View Javascript" field under the "Advanced" tab. This will allow you to input some Javascript to be copied into a auto.js file;
* Added ability to have an URL Link on the "image" field type, next to the existing Page Link;

#v1.0.10

* Replaced jquery.responsiveTabs.min.js with a non minified version - 5.7.4 was causing issues for some strange reason;

#v1.0.9

* Brought back bootstrap fonts (bootstrap.fonts.css and "fonts" directory), as there is no Concrete5 asset (yet);

#v1.0.8

* Field type icon fix for Concrete5 5.7.4.x and higher (based on the release candidate of 5.7.4);

#v1.0.7

* Better field type sorting in the "Add a field" section - only visible when names of field types differ from directory name or when pulled from more directories (read: other package(s));
* Cleaned HTML if class for field type "Youtube" was empty (a superfluous empty attribute "class" would be attached to the iFrame);
* Removed 1 (of 2) extra "End of Line" being generated after each field, resulting in a more compact view.php file;
* Developers: registered new handlebarsjs helper "select_multiple" - this for select input fields with the "multiple" attribute;
* Developers: Removed "fieldOptionsJavascript" function for field types and instead autoloads field_javascript.js within the field types' "elements" directory (if exists);

#v1.0.6

* Added ability to enter an email subject for the "email" field type (only when outputted as mailto anchor);
* When caching CSS and Javascript, form validator library could not load the "file" and "date" validator which in turn caused to not validate at all. This is fixed by requiring the file.js with requireAsset;
* Block Configurations can now be loaded by going to "Stacks & Blocks" - "Block Designer" - "Block Config". This page will have the available block types clickable (only when the config.json file exists);
* Block Types within a package that do have a config.json file (and the package is installed), can now also be loaded;

#v1.0.5

* Colorpicker field type now uses Concrete5/core spectrum.css and spectrum.js files (related CSS/JS files deleted);
* Bootstrap Fonts (glyphicons) replaced with Font Awesome (all CSS related files deleted);
* Remaining "renderjs" related files and lines deleted, because this was completely rewritten to "handlebarsjs";
* Caching CSS and JavaScript fix - when this function was turned on (yoursite.com/dashboard/system/optimization/cache), some jQuery functions where not executed, resulting in a non-working page;
* Email field type can now be outputted as anchor with "mailto" functionality + ability to add one or multiple classes to this anchor;
* Parsedown library update (original) - https://github.com/erusev/parsedown;
* Fixed error notice if multiple blocks used the "markdown" field type on the same page;
* Markdown now returns full HTML as "searchable content" instead of plain markdown;

#v1.0.4

* New field type: stacks;
* Clean(er) output of "db.xml" file;
* Extra blacklisted slugs ('file', 'bid', 'bttable', 'helpers', 'btfieldsrequired', 'btinterfacewidth', 'btinterfaceheight', 'btcacheblockrecord', 'btcacheblockoutput', 'btcacheblockoutputonpost', 'btcacheblockoutputforregisteredusers', 'btcacheblockoutputlifetime', 'bthandle', 'btname', 'btexportpagecolumns', 'btexportfilecolumns', 'btexportpagetypecolumns', 'btexportpagefeedcolumns', 'btwrapperclass'), which will cause problems with Concrete5 core, searching for a different (edit) file or overwriting/using wrong data;
* Email field type fix where if no email is being entered, it would give you the notice of an invalid email being entered - so blank values are possible from now on;
* Developers: "getSearchableContent" now needs to return lines of PHP where searchable values can be stored into the "$content" array (like so: $content[] = $this->theFieldSlug);
* Developers: new field type function: "deleteFunctionContents" and "duplicateFunctionContents";
* Developers: "view", "delete" and "duplicate" functions always load the database in a "$db" variable by default (if there were lines returned by field types for these functions);
* Developers: "on_start" function always load the asset list instance in a "$al" variable by default (if there were lines returned by field types for this function);
* Developers: blockType table name passed along to each function of a field type;
* Developers: removed field types' "dbXML" function and replaced this with a "dbFields" function, which will return an array of fields instead of a string of the XML field;
* Developers: new function "dbTables" which will give functionality to create one or multiple tables within a field type (the foundation for so called "entries" like the image_slider block has);

#v1.0.3

* New field type: color_picker;
* Fixed "Block type <handle> does not exist (anymore)." upon direct installing block types on live servers - live it would search for Core blocks instead of "Application" blocks;
* Fixed class names not being escaped in the URL field type because of double quoted string (class="your-class" instead of class=\"your-class\") - thank you "nickratering" for mentioning this;
* Developers: Edited "date_time" field type's inline CSS/JS to Concrete5 "requireAsset" standards;
* Developers: The block handle (block_handle) will now be passed to field types within the "data" array;
* Developers: Added "on_startFunctionContents" function for field types, i.e. for asset registering;
* Developers: Added "defined or die statement" to Parsedown library - within "markdown" fieldtype;

#v1.0.2

* New field type: file;
* New field type: youtube;
* Full rewrite from renderjs to Handelebars(js), which is much more powerful - will help A LOT when writing field options for each field type;
* "Text box", "Select" and "URL" field type options/fields now wrapped in div with class "content-field-options";
* Added the ability to enter (a) class name(s) to your "URL" field type links;

#v1.0.1.1

* WYSIWYG field type fix should really work now (was a long day I guess);

#v1.0.1

* Label for (clickable) fix for "Cache block output lifetime" field in "Advanced" tab;
* Placeholder field option added for "Text box" field type;
* Fallback value field option added for "Text box" field type (when a field is being left blank, this value will be used - usefull for i.e. "read more" text);
* Removed extra white spaces in front of "use" statements (controller.php);
* WYSIWYG field type fix - content was not being saved because of recent ID prefixing;

#v1.0.0

* New field type: markdown;
* Added ability to have a page (link) anchor on the "image" field type and have one or multiple classes attached to this anchor;
* Date_time change to not include CSS/JS on default (as files within js/css directories will automatically get included by Concrete5) by renaming folders;
* Last version number got stuck on 0.9.8, so that may have caused to keep showing the "update addon" button, sorry;
* No more digits allowed in block handle, because namespaces can not (always) use them;
* Removed extra functions (translateFromEditMode) from WYSIWYG field type and rewrote those functions to use the core functions of the "LinkAbstractor" class;
* Prefixed the id attribute of the WYSIWYG field type with "wysiwyg-ft-" in order to maintain all editor functions - thank you "j3ns" for mentioning this;
* Renamed "Cache" tab name to "Advanced", because this tab will have more fields as of now;
* Added an optional "Table prefix" field in the (recently renamed) "Advanced" tab, which will create a table like this "bt<prefix><handle>" (only if the field is filled of course);

#v0.9.9

* New field type: code;
* Removed indenting/spaces before/after prefix/suffix of fields, in order to have everything appended after eachother (needed in some cases);
* Updated link field type validation - thank you "yulolimum" for mentioning possible flaws;
* Sanitize (htmlspecialchars()) output for text_box, text_area and email field types to reduce the risks of XSS (cross site scripting);
* Newly created blocks with a dash (-) used in the handle would result in errors. Dashes get converted into underscores (_) now and multiple/double underscores will be replaced by a single underscore;

#v0.9.8

* Ability to enter class(es) to be added to your image field type;
* Ability to make a thumbnail of your image field type, by entering the wanted width and height;
* Ability to crop your image field type (only possible when making it a thumbnail);

#v0.9.7

* New field type: email;
* New field type: date_time;
* Added ability to sort field types on the block designer index page by various functions, standard sorting changed to alphabetical instead of user defined key sorting (uksort);
* Upon adding a new field when creating a new block, first visible "input", "textarea" or "select" (dropdown) will get focused;
* Animation added for adding fields when creating a block, so they don't magically appear in the list;
* Animation added for deleting fields when creating a block, so they don't magically disappear from the list;
* Better error handling and notification upon installing block types using the build in "Direct install" functionality;
* Fixed a bug when a non-existing field type was being posted (should not happen though, when not manipulating HTML or deleting field types);
* Fixed forgotten t() function for error messages "One or multiple fields are required to build a block." and "-- None --";
* Fixed a bug where the "delete this block type folder" link wouldn't get the correct path when loading a config of a block;
* Code cleaning and optimization to increase speed for both client-side and server-side;
* Developers: New "copyFiles" function for field types, which gives you the option to copy files from [source] to [target] (with [mode]) - only executed when no field type errors available;

#v0.9.6

* Fixed a bug where ft_count for each field type would always reset to 0 over and over (causing code to double include, for example the WYSIWYG field);
* New field type: number;
* JavaScript optimization;
* Minified all packaged CSS to speed up loading and standalone bootstrap fonts and responsive tabs CSS file to be re-used with other pages/field types;
* When changing the field order (sorting fields), the placeholder has the height of the currently dragging item instead of a fixed height which caused the page to change in height;
* Fix when dragging/sorting the first field, it would get a margin at the top as well;
* Fixed bug where all divs with "alert" classes would get instantly hidden within the div .block-designer-container;
* Colored options block for fields that have too many options (number);
* More space/margin added between fields in the block designer configuration page;
* Select field type update: set your own array key for values, by using 2 semicolons (" :: ") on each line - extra spaces required;
* Renamed "Image" field under the interface tab to "Icon" in order to better explain it's use;
* Developers: New "fieldOptionsJavascript" function for field types, which gives you the option to pass in field type specific Javascript;
* Developers: New "getFieldNote" function for field types, which gives you the option to add a note (i.e. telling the user "You need a Twitter account first" for a specific field)
* Developers: Rewrote old if(): endif: statements to {} in each field type's "viewContents" function;

#v0.9.5

* Each field type has it's own icon.png file, which will show up in the "Add a field" section. This makes it easier to see what to click, instead of text links;
* After submitting the form with (processed) errors, the form gets fully populated with the given values and fields;
* After having created a block with the block designer, go to `index.php/dashboard/blocks/block_designer/config/[YOUR_BLOCK_HANDLE_HERE]` to reload all of the entered values. This way you can create a similar block or add another field and delete the old block very FAST;
* Developers: field type "validate" function now returns true OR a string, where everything else but true will be an error string. Creating a block won't succeed and give a message with the returned string;
* Developers: field type "getSearchableContent" function added to be able to return some (unprocessed) PHP. See the "text_box" field type for usage;
* Developers: each field type function with $data array passed along, will now have each value directly in the array. Upon new field type creation, do not use these standard keys: row_id, slug, required, prefix, suffix, label, ft_count;

#v0.9.1 - v0.9.4

* Minor bugfixes and code improvements

#v0.9.0

* Initial Release