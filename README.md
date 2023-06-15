# TYPO3 Extension "DB Rector"

## Disclaimer
It is recommended to **NOT run this** extension **in productive environments**!

## What does it do
This extension is a wrapper to run typo3-rector in TYPO3 backend to update the typoscript stored in ‚sys_template.config‘.

## Features
- backend module to
  - view all typoscript stored in the db
  - run typo3-rector on single db entries
  - Review the result of the typo3-rector process
  - apply typo3-rector result to the corresponding sys_template record
- security
  - backend module is only active in TYPO3 context „development“ by default

## Installation

Install this extension via `composer req creifenscheid/db-rector` or download it from the [TYPO3 Extension Repository](https://extensions.typo3.org/extension/db_rector/) and activate the extension in the Extension Manager of your TYPO3 installation.

### Known working setups

* DDEV (TYPO3 11, TYPO3 12)

### Known not working setups

* MacOS + MAMP

## Configuration
### Extension configuration
| Parameter | Default | Optional | Description                                                                  |
|:----------|:--------|:---------|:-----------------------------------------------------------------------------|
|ignoreTYPO3Context|false|yes| If set to true, the context of the TYPO3 installation is going to be ignored - this is not recommended |

### Rector configuration
To configure typo3-rector a file named rector.php is required.<br>
This file is generated more or less automatically.<br>
There is some sort of „template“, which is copied into the working folder.<br>
<br>
Hence we just want to refactor typoscript, the rector config file is kept small and simple.<br>
<br>
The following configuration parameter are defined dynamically:

| Parameter  | Value(s)                                                                                                   | Description                           |
|------------|------------------------------------------------------------------------------------------------------------|---------------------------------------|
| phpVersion | The php version of the TYPO3 installation                                                                  | target version to support             |
| sets       | 2 defined "UP_TO_TYPO3" sets<ul><li>the previous TYPO3 version</li><li>the current TYPO3 version</li></ul> | Rule sets to run on the target code |

## Support
I don't want your money or anything else.
I am doing this for fun, with heart and to improve my coding skills.
Constructive critisism is very welcome.
If you want to contribute, feel free to do so.
Thank you!
