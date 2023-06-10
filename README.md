## TYPO3 Extension "DB Rector"

## What does it do
This extension is an adapter to run typo3-rector in TYPO3 backend to update the typoscript stored in the database in field:config of table:sys_template.

To do so, the content is stored in a temporary file, which is handed over to typo3-rector.
The result is read into a db model, which can be viewed and applied in the backend module.

## Features
- backend module to
  - view all typoscript stored in the db
  - run typo3-rector on single db entries
  - apply typo3-rector result to original db entries
- security: backend module is only active in TYPO3 context „development“ - can be deactivated via extension configuration

## Configuration
### Extension configuration
| Parameter | Default | Optional | Description                                                                  |
|:----------|:--------|:---------|:-----------------------------------------------------------------------------|
|ignoreTYPO3Context|false|yes| If set to true, the context of the TYPO3 installation is going to be ignored |

### Rector configuration
To configure rector a file named rector.php is required.<br>
This file is generated more or less automatically.<br>
There is some sort of template, which is copied into the working folder /var/db_rector/.<br>
<br>
Hence we just want to refactor typoscript and tsconfig, the rector config file is kept short.<br>
<br>
The following configuration parameter are defined dynamically:

| Parameter  | Value(s)                                                                                                   | Description                           |
|------------|------------------------------------------------------------------------------------------------------------|---------------------------------------|
| phpVersion | The php version of the TYPO3 installation                                                                  | target version to support             |
| sets       | 2 defined "UP_TO_TYPO3" sets<ul><li>the previous TYPO3 version</li><li>the current TYPO3 version</li></ul> | Rule sets to run on the targeted code |

## Installation

Install this extension via `composer req creifenscheid/db-rector` or download it from the [TYPO3 Extension Repository](https://extensions.typo3.org/extension/db_rector/) and activate
the extension in the Extension Manager of your TYPO3 installation.

## Know working setups

* DDEV (TYPO3 11, TYPO3 12)

## Known not working setups

* MacOS + MAMP

## Support
I don't want your money or anything else.
I am doing this for fun, with heart and to improve my coding skills.
Constructive critisism is very welcome.
If you want to contribute, feel free to do so.
Thank you!
