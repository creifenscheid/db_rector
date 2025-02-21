# TYPO3 Extension "DB Rector"

> **Note: As of version 13.0.0, this extension uses [typo3-fractor](https://github.com/andreaswolf/fractor-typo3-fractor).**

## Disclaimer
It is strongly recommended **NOT** to run this extension **in production environments**!

## Installation

This **extension must be installed via Composer**, e.g., `composer req --dev creifenscheid/db-rector`, to install typo3-fractor and get it running. While you can download it from the [TYPO3 Extension Repository](https://extensions.typo3.org/extension/db_rector/), **it still needs to be installed via Composer**.

## What does it do
This extension acts as an adapter to run typo3-fractor in the TYPO3 backend, enabling the refactoring of TypoScript stored in `sys_template.config`.

## Features
- Backend module to:
  - View all TypoScript stored in the database
  - Run typo3-fractor on individual or all database entries
  - Review the results of the typo3-fractor process (including a diff view)
  - Apply typo3-fractor results to the corresponding sys_template record
  - Roll back to the original TypoScript
- Security
  - The backend module is only active in the TYPO3 development context by default

### Note
If the `sys_template` record is modified after the fractor process or after applying the fractor result, the corresponding fractor model will be reset.<br>
This allows the updated sys_template TypoScript to be processed again.

### Known working setups

* DDEV based environments

### Known not working setups

* MacOS + MAMP

## Configuration
### Extension configuration
| Parameter | Default | Optional | Description                                                                  |
|:----------|:--------|:---------|:-----------------------------------------------------------------------------|
|ignoreTYPO3Context|false|yes| If set to `true`, the TYPO3 installation context will be ignored—this is not recommended. |

### Rector configuration
To configure typo3-fractor, a file named `fractor.php` is required.
This file is generated semi-automatically by copying a predefined template into the working folder.

Since only TypoScript refactoring is needed, the fractor configuration file remains small and simple.

The following configuration parameters are defined dynamically:

| Parameter  | Value(s)                                                                                                   | Description                           |
|------------|------------------------------------------------------------------------------------------------------------|---------------------------------------|
| sets       | 2 defined "Typo3LevelSetList" sets<ul><li>the previous TYPO3 version</li><li>the current TYPO3 version</li></ul> | Rule sets to apply to the target code. |

## Support
I don’t want your money or anything else.
I’m doing this for fun, with passion, and to improve my coding skills.
I always welcome feedback and constructive criticism.
If you’d like to contribute, feel free to do so.<br><br>
**Thank you!**
